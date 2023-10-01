<?php

namespace App\Restrict\Queries\Application;

use App\Shared\Domain\Enums\ExceptionType;
use App\Shared\Infrastructure\Bus\EventBus;
use App\Restrict\Auth\Application\AuthService;
use App\Restrict\Queries\Domain\QueryRepository;
use App\Shared\Domain\Bus\Event\IEventDispatcher;
use App\Shared\Infrastructure\Services\AppService;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Infrastructure\Components\Export\CsvComponent;
use App\Restrict\Queries\Domain\Events\QueryActionWasCreatedEvent;
use App\Shared\Infrastructure\Factories\{ComponentFactory as CF, RepositoryFactory as RF, ServiceFactory as SF};

final class QueryExportService extends AppService implements IEventDispatcher
{
    private const LIMIT_PARAMS = 19999;
    private const LIMIT_DOWNLOAD = 1000;
    private string $reqUuid;
    private array $columns;
    private string $filename;

    private ?AuthService $authService;

    public function __construct(array $input)
    {
        $this->_loadInput($input);
    }

    private function _loadInput(array $input): void
    {
        if (!$input) {
            $this->_throwException(__("Empty request"), ExceptionType::CODE_BAD_REQUEST);
        }

        $this->reqUuid = trim($input["req_uuid"] ?? "");
        if (!$this->reqUuid) {
            $this->_throwException(__("No request id received"), ExceptionType::CODE_BAD_REQUEST);
        }

        $this->columns = $input["columns"] ?? [];
        if (!$this->columns) {
            $this->_throwException(__("No request columns received"), ExceptionType::CODE_BAD_REQUEST);
        }

        if (strlen($this->getJson($this->columns)) > self::LIMIT_PARAMS) {
            $this->_throwException(__("Request payload is too big"), ExceptionType::CODE_BAD_REQUEST);
        }

        $this->filename = $input["filename"] ?? "export";
    }

    private function _checkPermissionOrFail(): void
    {
        if ($this->authService->isAuthUserSuperRoot()) {
            return;
        }

        if (!(
            SF::getAuthService()->hasAuthUserPolicy(UserPolicyType::PROMOTIONS_READ)
            || SF::getAuthService()->hasAuthUserPolicy(UserPolicyType::PROMOTIONS_WRITE)
        )) {
            $this->_throwException(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
        }
    }

    private function _applyOnlyAllowedColumns(array &$data): void
    {
        foreach($this->columns as $column => $label) {
            $this->columns[$column] = html_entity_decode($label);
        }

        $columns = array_keys($this->columns);
        $transformed = [];
        foreach ($data as $row) {
            $tmpRow = [];
            foreach ($row as $column => $value) {
                if (!in_array($column, $columns)) {
                    continue;
                }
                $tmpRow[$this->columns[$column]] = $value;
            }
            if (!$tmpRow) {
                continue;
            }
            $transformed[] = $tmpRow;
        }
        $data = $transformed;
    }

    private function _dispatchEvents(array $payload): void
    {
        EventBus::instance()->publish(...[
            QueryActionWasCreatedEvent::fromPrimitives(
                -1,
                [
                    "id_query" => $payload["id"],
                    "description" => "excel-export",
                    "params" => $this->getJson($this->columns),
                ]
            )
        ]);
    }

    private function getJson(mixed $var): string
    {
        return json_encode($var, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public function __invoke(): void
    {
        $this->authService = SF::getAuthService();

        $this->_checkPermissionOrFail();
        $idUser = $this->authService->getAuthUserArray()["id"] ?? -1;
        $query = RF::getInstanceOf(QueryRepository::class)
                    ->getQueryByUuidAndIdUser(
                        $this->reqUuid,
                        $idUser,
                        ["id","query", "total"]
                    );
        if (!$query) {
            $this->_throwException(
                __("Request id {0} not found!", $this->reqUuid),
                ExceptionType::CODE_NOT_FOUND
            );
        }
        if (($total = (int) $query["total"]) > self::LIMIT_DOWNLOAD) {
            $this->_throwException(
                __("The amount of rows {0} exceed the limit {1}", $total, self::LIMIT_DOWNLOAD),
                ExceptionType::CODE_EXPECTATION_FAILED
            );
        }

        $sql = $query["query"];
        $sql = explode(" LIMIT ", $sql)[0];
        $result = RF::getInstanceOf(QueryRepository::class)->query($sql);
        $this->_applyOnlyAllowedColumns($result);
        $now = date("Y-m-d_H-i-s");
        $this->_dispatchEvents($query);

        CF::getInstanceOf(CsvComponent::class)
            ->downloadResponseAsExcel("{$this->filename}-$now.xls", $result);
    }
}
