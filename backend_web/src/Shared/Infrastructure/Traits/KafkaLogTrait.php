<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Traits\KafkaLogTrait
 * @file KafkaLogTrait.php 1.0.0
 * @date 01-11-2018 19:00 SPAIN
 * @observations
 */

namespace App\Shared\Infrastructure\Traits;

use App\Shared\Infrastructure\Components\Kafka\ProducerComponent;

trait KafkaLogTrait
{
    protected function log(mixed $mxVar, ?string $title = null): void
    {
        $oLog = new ProducerComponent;
        $oLog->save($mxVar, $title, ProducerComponent::TYPE_SQL);
    }

    protected function logd(mixed $mxVar, ?string $title = null): void
    {
        $oLog = new ProducerComponent;
        $oLog->save($mxVar, $title, ProducerComponent::TYPE_DEBUG);
    }

    protected function logerr(mixed $mxVar, ?string $title = null): void
    {
        $oLog = new ProducerComponent;
        $oLog->save($mxVar, $title, ProducerComponent::TYPE_ERROR);
    }

    protected function logkafka(mixed $mxVar, ?string $title = null): void
    {
        $oLog = new ProducerComponent;
        $oLog->save($mxVar, $title, ProducerComponent::TYPE_KAFKA);
    }

}//KafkaLogTrait
