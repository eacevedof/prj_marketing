<?php
/**
 * eventbus listeners
 */
use App\Shared\Infrastructure\Bus\EventBus;
use App\Restrict\UserPreferences\Application\UserPreferencesInsertService;
use App\Restrict\UserPermissions\Application\UserPermissionsInsertService;
use App\Restrict\PromotionsUi\Application\PromotionUiInsertService;

$bus = EventBus::instance();
$bus->subscribe(new UserPreferencesInsertService());
$bus->subscribe(new UserPermissionsInsertService());
$bus->subscribe(new PromotionUiInsertService());