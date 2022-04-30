<?php
/**
 * eventbus listeners
 */
use App\Shared\Infrastructure\Bus\EventBus;
use App\Restrict\UserPreferences\Application\UserPreferencesInsertService;

$bus = EventBus::instance();
//$bus->subscribe(new UserPreferencesInsertService());