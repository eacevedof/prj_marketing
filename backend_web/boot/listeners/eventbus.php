<?php
/**
 * eventbus listeners
 */
use App\Shared\Infrastructure\Bus\EventBus;
use App\Restrict\UserPreferences\Application\UserPreferencesInsertEventHandler;
use App\Restrict\UserPermissions\Application\UserPermissionsInsertEventHandler;
use App\Restrict\PromotionsUi\Application\PromotionUiInsertService;
use App\Open\PromotionCaps\Application\PromotionCapSubscriptionEventHandler;
use App\Open\PromotionCaps\Application\PromotionCapActionEventHandler;
use App\Restrict\Promotions\Application\PromotionCountersEventHandler;
use App\Open\PromotionCaps\Application\PromotionSubscriptionNotifierEventHandler;
use App\Open\PromotionCaps\Application\PromotionCapFinishedEventHandler;
use App\Restrict\Queries\Application\QueryEventHandler;
use App\Open\PromotionCaps\Application\ContactEmailSentEventHandler;

$bus = EventBus::instance();
$bus->subscribe(new UserPreferencesInsertEventHandler());
$bus->subscribe(new UserPermissionsInsertEventHandler());
$bus->subscribe(new PromotionUiInsertService());
$bus->subscribe(new PromotionCapSubscriptionEventHandler());
$bus->subscribe(new PromotionCapActionEventHandler());
$bus->subscribe(new PromotionCountersEventHandler());
$bus->subscribe(new PromotionSubscriptionNotifierEventHandler());
$bus->subscribe(new PromotionCapFinishedEventHandler());
$bus->subscribe(new ContactEmailSentEventHandler());
$bus->subscribe(new QueryEventHandler());