<?php
namespace App\Open\UserCaps\Application;

use App\Open\PromotionCaps\Domain\PromotionCapUsersRepository;
use App\Restrict\Promotions\Domain\PromotionRepository;
use App\Restrict\Promotions\Domain\PromotionUiRepository;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Restrict\BusinessData\Domain\BusinessDataRepository;
use App\Shared\Domain\Enums\ExceptionType;
use App\Open\PromotionCaps\Domain\Errors\PromotionCapException;
use App\Shared\Infrastructure\Traits\RequestTrait;

final class UserCapPointsService extends AppService
{
    use RequestTrait;

    private BusinessDataRepository $repobusinessdata;
    private PromotionCapUsersRepository $repopromocapuser;

    private array $businesssdata;
    private array $promocapuser;

    public function __construct(array $input)
    {
        if (!$input["businessuuid"])
            $this->_promocap_exception(__("No business account provided"), ExceptionType::CODE_BAD_REQUEST);

        if (!$input["capuseruuid"])
            $this->_promocap_exception(__("No user provided"), ExceptionType::CODE_BAD_REQUEST);

        $this->input = $input;

        $this->repobusinessdata = RF::get(BusinessDataRepository::class);
        $this->repopromocapuser = RF::get(PromotionCapUsersRepository::class);
    }

    private function _promocap_exception(string $message, int $code = ExceptionType::CODE_INTERNAL_SERVER_ERROR): void
    {
        throw new PromotionCapException($message, $code);
    }

    private function _load_businessdata(): void
    {
        $businessuuid = $this->input["businessuuid"];
        $this->businesssdata = $this->repobusinessdata->get_by_uuid($businessuuid, ["id", "id_user"]);
        if (!$this->businesssdata)
            $this->_promocap_exception(__("Business account {$businessuuid} not found!"), ExceptionType::CODE_NOT_FOUND);
    }

    private function _load_promocapuser(): void
    {
        $capuseruuid = $this->input["capuseruuid"];
        $this->promocapuser = $this->repopromocapuser->get_by_uuid($capuseruuid, ["id", "id_owner", "email", "name1"]);
        if (!$this->promocapuser)
            $this->_promocap_exception(__("User {$capuseruuid} not found!"), ExceptionType::CODE_NOT_FOUND);

        if ($this->businesssdata["id_user"] !== $this->promocapuser["id_owner"])
            $this->_promocap_exception(__("These codes are not congruent!"), ExceptionType::CODE_BAD_REQUEST);
    }

    public function __invoke(): array
    {
        $this->_load_businessdata();
        $this->_load_promocapuser();

    }
}