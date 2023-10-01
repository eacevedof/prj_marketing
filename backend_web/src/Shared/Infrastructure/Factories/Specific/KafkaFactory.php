<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Shared\Infrastructure\Factories\Specific\KafkaFactory
 * @file KafkaFactory.php v1.0.0
 * @date 25-06-2021 19:50 SPAIN
 * @observations
 */

namespace App\Shared\Infrastructure\Factories\Specific;

use App\Shared\Infrastructure\Components\Kafka\{ConsumerComponent, ProducerComponent};

final class KafkaFactory
{
    public static function getConsumerInstance(): ConsumerComponent
    {
        return new ConsumerComponent;
    }

    public static function getProducerInstance(): ProducerComponent
    {
        return new ProducerComponent;
    }

}//KafkaFactory
