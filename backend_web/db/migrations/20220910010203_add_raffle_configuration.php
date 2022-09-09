<?php
declare(strict_types=1);
use Migrations\AbsMigration;

final class AddRaffleConfiguration extends AbsMigration
{
    public function up(): void
    {
        $this->_add_columns_to_app_promotion();
        $this->_add_columns_to_app_promotion_cap();
        $this->_initial_load();
    }

    private function _add_columns_to_app_promotion(): void
    {
        $table = $this->table("app_promotion");
        $table->addColumn("date_raffle", "datetime", [
            "null" => false,
            "after" => "is_raffleable",
            "comment" => "depende de is_raffable",
        ])
        ->save();
    }

    private function _add_columns_to_app_promotion_cap(): void
    {
        $table = $this->table("app_promotioncap_subscriptions");
        $table->addColumn("id_raffle", "integer", [
            "limit" => 3,
            "null" => true,
            "default" => null,
            "after" => "id_promouser",
            "comment" => "app_array.type=raffle",
        ])
        ->save();
    }

    private function _initial_load(): void 
    {
        $sqls = [
            "INSERT INTO app_array (is_enabled,TYPE,id_pk,description) VALUES ('1','raffle','1','Winner');",
            "INSERT INTO app_array (is_enabled,TYPE,id_pk,description) VALUES ('1','raffle','2','Alternate 1');",
            "INSERT INTO app_array (is_enabled,TYPE,id_pk,description) VALUES ('1','raffle','3','Alternate 2');",
        ];

        foreach ($sqls as $sql)
            $this->execute($sql);
    }

    public function down(): void
    {
        $this->table($this->tablename)->drop()->save();
    }
}
