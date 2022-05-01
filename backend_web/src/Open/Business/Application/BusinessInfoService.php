<?php
namespace App\Open\Business\Application;

use App\Restrict\Promotions\Domain\PromotionRepository;
use App\Restrict\Promotions\Domain\PromotionUiRepository;
use App\Restrict\Users\Domain\UserRepository;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Restrict\Auth\Application\AuthService;
use App\Restrict\BusinessData\Domain\BusinessDataRepository;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Domain\Enums\ExceptionType;

final class BusinessInfoService extends AppService
{
    private AuthService $auth;
    private array $authuser;

    private UserRepository $repouser;
    private BusinessDataRepository $repobusinessdata;
    private PromotionRepository $repopromotion;
    private PromotionUiRepository $repopromotionui;

    private array $businesssdata;
    private array $promotion;
    private array $promotionui;

    public function __construct(array $input)
    {
        if (!$input["businessslug"]) $this->_exception(__("No business space provided"));
        if (!$input["promotionslug"]) $this->_exception(__("No promotion provided"));

        $this->input = $input;

        $this->repouser = RF::get(UserRepository::class);
        $this->repobusinessdata = RF::get(BusinessDataRepository::class);
        $this->repopromotion = RF::get(PromotionRepository::class);
        $this->repopromotionui = RF::get(PromotionUiRepository::class);
    }

    private function _get_businessdata_by_slug(): array
    {
        $businessslug = $this->input["businessslug"];

    }

    public function __invoke(): array
    {
        return [
            "promotion" => [],
            "businessdata" => [],
            "metadata" => [], //depende si es test o no
        ];
    }
}