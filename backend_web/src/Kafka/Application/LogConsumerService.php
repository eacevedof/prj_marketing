<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Kafka\Application\KafkaReducerService
 * @file KafkaReducerService.php 1.0.0
 * @date 21-06-2020 20:52 SPAIN
 * @observations
 */
namespace App\Kafka\Application;

use App\Shared\Infrastructure\Factories\DbFactory;
use App\Shared\Infrastructure\Factories\Specific\KafkaFactory;
use App\Shared\Infrastructure\Traits\LogTrait;
use PDO;
use TheFramework\Components\Db\ComponentQB;
use TheFramework\Components\Db\Context\ComponentContext;
use RdKafka\Message;

final class LogConsumerService
{
    use LogTrait;

    private function _get_pdo(): ?PDO
    {
        $context = new ComponentContext($_ENV["APP_CONTEXTS"], "c1");
        return DbFactory::get_pdo_by_ctx($context, "db_mypromos_log");
    }

    private function _get_query(): ComponentQB
    {
        $qb = new ComponentQB();
        $qb->set_table("app_log");
        return $qb;
    }

    private function _save(?array $data): void
    {
        if(!$data) return;
        if(!$pdo = $this->_get_pdo())
        {
            $this->logerr("No pdo created");
            return;
        }

        $sqls = [];
        foreach ($data as $message)
        {
            $sql = $this->_get_query()
                ->add_insert_fv("group_type", $message["type"])
                ->add_insert_fv("user_uuid", $message["useruuid"])
                ->add_insert_fv("title", $message["title"])
                ->add_insert_fv("message", $message["message"])
                ->add_insert_fv("timest", $message["timestamp"])
                ->add_insert_fv("code_cache", uniqid())
                ->insert();
            $sql = $sql->sql();
            $sqls[] = $sql;
        }
        $sql = implode(";",$sqls).";";
        $pdo->exec($sql);
    }

    public function __invoke(array $kafkamessages): void
    {
        $parsed = [];
        foreach($kafkamessages as $message)
        {
            if ($message->timestamp === -1) continue;

            $data=[];
            $data["timestamp"] = $message->timestamp;
            $arjson = json_decode($message->payload,1);
            $data = array_merge($data, $arjson);
            $parsed[] = $data;
        }
        $this->_save($parsed);
    }

    public function run(): void
    {
        KafkaFactory::get_consumer()->run($this);
    }
}