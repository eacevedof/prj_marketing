<?php
/**
 * eventbus listeners
 */
use App\Shared\Infrastructure\Bus\EventBus;
use App\Restrict\UserPreferences\Application\UserPreferencesInsertService;
use App\Restrict\UserPermissions\Application\UserPermissionsInsertService;
use App\Restrict\PromotionsUi\Application\PromotionUiInsertService;
use App\Open\PromotionCaps\Application\PromotionCapSubscriptionEventHandler;
use App\Open\PromotionCaps\Application\PromotionCapActionEventHandler;
$bus = EventBus::instance();
$bus->subscribe(new UserPreferencesInsertService());
$bus->subscribe(new UserPermissionsInsertService());
$bus->subscribe(new PromotionUiInsertService());
$bus->subscribe(new PromotionCapSubscriptionEventHandler());
$bus->subscribe(new PromotionCapActionEventHandler());