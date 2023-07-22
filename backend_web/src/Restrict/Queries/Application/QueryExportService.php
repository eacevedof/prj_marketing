<?php
namespace App\Restrict\Queries\Application;

use App\Restrict\Queries\Domain\Events\QueryActionWasCreatedEvent;
use App\Restrict\Queries\Domain\QueryRepository;
use App\Shared\Domain\Bus\Event\IEventDispatcher;
use App\Shared\Infrastructure\Bus\EventBus;
use App\Shared\Infrastructure\Components\Export\CsvComponent;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\ComponentFactory as CF;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Domain\Enums\ExceptionType;

final class QueryExportService extends AppService implements IEventDispatcher
{
    private const LIMIT_PARAMS = 19999;
    private const LIMIT_DOWNLOAD = 1000;
    private string $requuid;
    private array $columns;
    private string $filename;

    public function __construct(array $input)
    {
        $this->_load_input($input);
    }

    private function _load_input(array $input): void
    {
        if (!$input) $this->_exception(__("Empty request"), ExceptionType::CODE_BAD_REQUEST);
        $this->requuid = trim($input["req_uuid"] ?? "");
        if (!$this->requuid) $this->_exception(__("No request id received"), ExceptionType::CODE_BAD_REQUEST);
        $this->columns = $input["columns"] ?? [];
        if (!$this->columns) $this->_exception(__("No request columns received"), ExceptionType::CODE_BAD_REQUEST);
        if (strlen(json_encode($this->columns))> self::LIMIT_PARAMS)
            $this->_exception(__("Request payload is too big"), ExceptionType::CODE_BAD_REQUEST);
        $this->filename = $input["filename"] ?? "export";
    }

    private function _check_permission(): void
    {
        if($this->auth->is_root_super()) return;

        if(!(
            SF::get_auth()->is_user_allowed(UserPolicyType::PROMOTIONS_READ)
            || SF::get_auth()->is_user_allowed(UserPolicyType::PROMOTIONS_WRITE)
        ))
            $this->_exception(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
    }

    private function _transform_by_columns(array &$data): void
    {
        foreach($this->columns as $column => $label)
            $this->columns[$column] = html_entity_decode($label);
        
        $colums = array_keys($this->columns);
        $transformed = [];
        foreach ($data as $row) {
            $tmprow = [];
            foreach ($row as $column => $value) {
                if (!in_array($column, $colums)) continue;
                $tmprow[$this->columns[$column]] = $value;
            }
            if (!$tmprow) continue;
            $transformed[] = $tmprow;
        }
        $data = $transformed;
    }

    private function _dispatch(array $payload): void
    {
        EventBus::instance()->publish(...[
            QueryActionWasCreatedEvent::from_primitives(
                -1,
                [
                    "id_query"=>$payload["id"],
                    "description"=>"excel-export",
                    "params"=> json_encode($this->columns),
                ]
            )
        ]);
    }

    public function __invoke(): void
    {
        $this->_check_permission();
        $iduser = SF::get_auth()->get_user()["id"] ?? -1;
        if (!$query = RF::get(QueryRepository::class)->get_by_uuid_and_iduser($this->requuid, $iduser, ["id","query", "total"]))
            $this->_exception(
                __("Request id {0} not found!", $this->requuid),
                ExceptionType::CODE_NOT_FOUND
            );
        if (($total = (int)$query["total"])>self::LIMIT_DOWNLOAD)
            $this->_exception(
                __("The amount of rows {0} exceed the limit {1}", $total, self::LIMIT_DOWNLOAD),
                ExceptionType::CODE_EXPECTATION_FAILED
            );

        $sql = $query["query"];
        $sql = explode(" LIMIT ", $sql)[0];
        $result = RF::get(QueryRepository::class)->query($sql);
        $this->_transform_by_columns($result);
        $now = date("Y-m-d_H-i-s");
        $this->_dispatch($query);
        CF::get(CsvComponent::class)->download_as_excel("{$this->filename}-$now.xls", $result);
    }
}