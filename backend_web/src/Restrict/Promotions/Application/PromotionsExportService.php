<?php
namespace App\Restrict\Promotions\Application;

use App\Restrict\Queries\Domain\QueryRepository;
use App\Shared\Infrastructure\Components\Export\CsvComponent;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\ComponentFactory as CF;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Domain\Enums\ExceptionType;

final class PromotionsExportService extends AppService
{
    private string $requuid;

    public function __construct(array $input)
    {
        $this->requuid = trim($input["req_uuid"] ?? "");
        if (!$this->requuid) $this->_exception(__("No request id received"));
    }

    private function _check_permission(): void
    {
        if(!(
            SF::get_auth()->is_user_allowed(UserPolicyType::PROMOTIONS_READ)
            || SF::get_auth()->is_user_allowed(UserPolicyType::PROMOTIONS_WRITE)
        ))
            $this->_exception(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
    }

    public function __invoke(): void
    {
        $this->_check_permission();
        $iduser = SF::get_auth()->get_user()["id"] ?? -1;

        if (!$query = RF::get(QueryRepository::class)->get_by_uuid_and_iduser($this->requuid, $iduser, ["query"]))
            $this->_exception(
                __("Request id {0} not found!", $this->requuid),
                ExceptionType::CODE_NOT_FOUND
            );

        $result = RF::get(QueryRepository::class)->query($query["query"]);
        //transformar dato por perfil de usuario
        //CF::get(CsvComponent::class)->download("promotions-{$this->requuid}.csv", $result);
        $now = date("Y-m-d_H-i-s");
        CF::get(CsvComponent::class)->download_as_excel("promotions-$now.xls", $result);
    }
}