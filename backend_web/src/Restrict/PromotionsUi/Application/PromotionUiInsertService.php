<?php
namespace App\Restrict\PromotionsUi\Application;

use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Domain\Bus\Event\IEventSubscriber;
use App\Shared\Domain\Bus\Event\IEvent;
use App\Shared\Infrastructure\Factories\EntityFactory as MF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Restrict\Auth\Application\AuthService;
use App\Restrict\Promotions\Domain\PromotionUiEntity;
use App\Restrict\Promotions\Domain\PromotionUiRepository;
use App\Restrict\Promotions\Domain\Enums\PromotionUiType;
use App\Restrict\Promotions\Domain\Events\PromotionWasCreatedEvent;

final class PromotionUiInsertService extends AppService implements IEventSubscriber
{
    private AuthService $auth;
    private PromotionUiEntity $promotionui;
    private PromotionUiRepository $repopromotionuis;

    public function __construct()
    {
        $this->auth = SF::get_auth();
        $this->promotionui = MF::get(PromotionUiEntity::class);
        $this->repopromotionuis = RF::get(PromotionUiRepository::class);
    }

    public function on_event(IEvent $domevent): IEventSubscriber
    {
        if(get_class($domevent)!==PromotionWasCreatedEvent::class) return $this;

        $promotionui = [
            "uuid" => uniqid(),
            "id_owner" => $domevent->id_owner(),
            "id_promotion" => $domevent->aggregate_id(),
            "input_email" => 1,
            "pos_email" => 10,
            "input_name1" => 1,
            "pos_name1" => 20,
            "input_phone1" => 0,
            "pos_phone1" => 30,
            "input_name2" => 0,
            "pos_name2" => 40,
            "input_language" => 0,
            "pos_language" => 50,
            "input_country" => 0,
            "pos_country" => 60,
            "input_birthdate" => 0,
            "pos_birthdate" => 70,
            "input_gender" => 0,
            "pos_gender" => 80,
            "input_address" => 0,
            "pos_address" => 90,
            "input_is_mailing" => 0,
            "pos_is_mailing" => 100,
            "input_is_terms" => 0,
            "pos_is_terms" => 110,
        ];

        $this->promotionui->add_sysinsert($promotionui, $this->auth->get_user()["id"]);
        $this->repopromotionuis->insert($promotionui);
        return $this;
    }
}