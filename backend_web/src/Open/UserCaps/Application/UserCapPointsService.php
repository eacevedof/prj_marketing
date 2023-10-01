<?php

namespace App\Open\UserCaps\Application;

use App\Shared\Domain\Enums\ExceptionType;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Restrict\BusinessData\Domain\BusinessDataRepository;
use App\Open\PromotionCaps\Domain\PromotionCapUsersRepository;
use App\Open\PromotionCaps\Domain\Errors\PromotionCapException;
use App\Shared\Infrastructure\Components\Date\UtcComponent as UTC;
use App\Shared\Infrastructure\Factories\{ComponentFactory as CF, RepositoryFactory as RF};

final class UserCapPointsService extends AppService
{
    use RequestTrait;

    private BusinessDataRepository $repobusinessdata;
    private PromotionCapUsersRepository $repopromocapuser;

    private array $businesssdata;
    private array $promocapuser;

    public function __construct(array $input)
    {
        if (!$input["businessuuid"]) {
            $this->_promocap_exception(__("No business account provided"), ExceptionType::CODE_BAD_REQUEST);
        }

        if (!$input["capuseruuid"]) {
            $this->_promocap_exception(__("No user provided"), ExceptionType::CODE_BAD_REQUEST);
        }

        $this->input = $input;

        $this->repobusinessdata = RF::getInstanceOf(BusinessDataRepository::class);
        $this->repopromocapuser = RF::getInstanceOf(PromotionCapUsersRepository::class);
    }

    private function _promocap_exception(string $message, int $code = ExceptionType::CODE_INTERNAL_SERVER_ERROR): void
    {
        throw new PromotionCapException($message, $code);
    }

    private function _load_businessdata(): void
    {
        $businessuuid = $this->input["businessuuid"];
        $this->businesssdata = $this->repobusinessdata->getEntityByEntityUuid($businessuuid, ["id", "id_user", "business_name"]);
        if (!$this->businesssdata) {
            $this->_promocap_exception(__("Business account {0} not found!", $businessuuid), ExceptionType::CODE_NOT_FOUND);
        }
    }

    private function _load_promocapuser(): void
    {
        $capuseruuid = $this->input["capuseruuid"];
        $this->promocapuser = $this->repopromocapuser->getEntityByEntityUuid($capuseruuid, ["id", "id_owner", "email", "name1"]);
        if (!$this->promocapuser) {
            $this->_promocap_exception(__("User {0} not found!", $capuseruuid), ExceptionType::CODE_NOT_FOUND);
        }

        if (!$this->promocapuser["email"]) {
            $this->_promocap_exception(__("User {0} without email!", $capuseruuid), ExceptionType::CODE_BAD_REQUEST);
        }

        if ($this->businesssdata["id_user"] !== $this->promocapuser["id_owner"]) {
            $this->_promocap_exception(__("These codes are not congruent!"), ExceptionType::CODE_BAD_REQUEST);
        }
    }

    public function __invoke(): array
    {
        $this->_load_businessdata();
        $this->_load_promocapuser();

        $remoteip = $this->_loadRequestComponentInstance()->getRemoteIp();
        $result = $this->repopromocapuser->getPointsInAccountByEmailAndIdOwner($this->promocapuser["email"], $this->businesssdata["id_user"]);
        $utc = CF::getInstanceOf(UTC::class);
        $result = array_map(function (array $row) use ($utc, $remoteip) {
            return [
                "description" => $row["description"],
                "subscriptionuuid" => $row["uuid"],
                "date_execution" => $utc->getUtcDateIntoIpTimeZone($row["date_execution"], $remoteip, UTC::FORMAT_ONLY_DATE),
                "points" => $row["p"],
            ];
        }, $result);

        $points = array_column($result, "points");
        $points = array_sum($points);
        return [
            "username" => $this->promocapuser["name1"],
            "email" => $this->promocapuser["email"],
            "business_name" => $this->businesssdata["business_name"],
            "result" => $result,
            "total_points" => $points,
        ];
    }
}
