<?php
/**
 * eventbus listeners
 */
use App\Shared\Infrastructure\Bus\EventBus;
use App\Restrict\UserPreferences\Application\UserPreferencesInsertService;
use \App\Restrict\UserPermissions\Application\UserPermissionsInsertService;
$bus = EventBus::instance();
$bus->subscribe(new UserPreferencesInsertService());
$bus->subscribe(new UserPermissionsInsertService());