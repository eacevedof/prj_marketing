<?php
declare(strict_types=1);
use Migrations\AbsMigration;

final class CreateBaseUser extends AbsMigration
{
    private string $tablename = "base_user";

    public function up(): void
    {
        $this->_create_table();
        $this->_initial_load();
    }

    private function _create_table(): void
    {
        $table = $this->table("{$this->tablename}", [
            "engine" => "MyISAM",
            "collation" => "utf8_general_ci",
            "id"=> false,
            "primary_key" => ["id"]
        ]);

        $this->add_sysfields($table);

        $table->addColumn("id", "integer", [
            "limit" => 11,
            "identity" => true,
        ])
        ->addColumn("uuid", "string", [
            "limit" => 50,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("code_erp", "string", [
            "limit" => 25,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("description", "string", [
            "limit" => 250,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("email", "string", [
            "limit" => 100,
            "null" => false,
            "default" => null,
        ])
        ->addColumn("secret", "string", [
            "limit" => 100,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("phone", "string", [
            "limit" => 20,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("fullname", "string", [
            "limit" => 100,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("address", "string", [
            "limit" => 250,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("birthdate", "datetime", [
            "null" => true,
            "default" => null,
            "comment" => "comprobar mayoria de edad",
        ])
        ->addColumn("id_parent", "integer", [
            "limit" => 11,
            "null" => true,
            "default" => null,
            "comment" => "base_user.id quien es su superior"
        ])
        ->addColumn("id_gender", "integer", [
            "limit" => 11,
            "null" => true,
            "default" => null,
            "comment" => "app_array.type=gender"
        ])
        ->addColumn("id_nationality", "integer", [
            "limit" => 11,
            "null" => true,
            "default" => null,
            "comment" => "app_array.type=nationality"
        ])
        ->addColumn("id_country", "integer", [
            "limit" => 11,
            "null" => true,
            "default" => null,
            "comment" => "app_array.type=country"
        ])
        ->addColumn("id_language", "integer", [
            "limit" => 11,
            "null" => false,
            "default" => 1,//ingles
            "comment" => "app_array.type=language id_pk",
        ])
        ->addColumn("id_profile", "integer", [
            "limit" => 11,
            "null" => false,
            "default" => 2,
            "comment" => "base_array.type=profile perfil 1 root, 2 sys admin, 3 business owner, 4 busines manager"
        ])
        ->addColumn("url_picture", "string", [
            "limit" => 100,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("date_validated", "datetime", [
            "null" => true,
            "default" => null,
        ])
        ->addColumn("log_attempts", "integer", [
            "limit" => 5,
            "default" => 0,
        ])
        //esto mejor en preferences
        /*
        ->addColumn("token_reset", "string", [
            "limit" => 250,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("geo_location", "string", [
            "limit" => 500,
            "null" => true,
            "default" => null,
            "comment" => "(x,y,z)"
        ])
        ->addColumn("is_notifiable", "string", [
            "limit" => 4,
            "null" => true,
            "default" => null,
        ])*/
        ->create();
    }

    private function _initial_load(): void
    {
        $secret = "$2y$10\$BEClm.fzRU2shGk5nMLGRe4f0JnkXofGMBkLZ6sC86f8/aeetCMhC";
        $array = [
            [
                //secret: eaf
                "id"=>"1", "email"=>"root@sys.sys", "description"=>"Root One", "secret"=>$secret,
                "fullname" => "Super Root", "uuid"=>"sys000001", "id_gender" => "1", "id_nationality" => "1", "id_country" => "69",
                "id_language" => "2", "id_profile" => "1", "id_parent" => "null"
            ],
            [
                "id"=>"2", "email"=>"sysadm@sys.sys", "description"=>"Sys One", "secret"=>$secret,
                "fullname" => "Root Admin", "uuid"=>"sys000002", "id_gender" => "1", "id_nationality" => "1", "id_country" => "69",
                "id_language" => "2", "id_profile" => "2", "id_parent" => "null"
            ],
            [
                "id"=>"3", "email"=>"bow@bow.com", "description"=>"Business Owner One", "secret"=>$secret,
                "fullname" => "Business Owner Demo", "uuid"=>"demo000001", "id_gender" => "1", "id_nationality" => "1", "id_country" => "69",
                "id_language" => "2", "id_profile" => "3", "id_parent" => "null"
            ],
            [
                "id"=>"4", "email"=>"adm@bow.com", "description"=>"Admin Of Business Owner One", "secret"=>$secret,
                "fullname" => "Business Admin", "uuid"=>"demo000002", "id_gender" => "1", "id_nationality" => "1", "id_country" => "69",
                "id_language" => "2", "id_profile" => "4", "id_parent" => 3
            ],
        ];

        foreach ($array as $item) {
            list(
                "id"=>$id, "email"=>$email, "description"=>$description, "secret"=>$secret, "fullname"=>$fullname,
                "uuid"=>$uuid, "id_gender"=>$idgender, "id_nationality"=>$idnationality, "id_country"=>$idcountry,
                "id_language"=>$idlanguage, "id_profile"=>$idprofile, "id_parent" => $idparent
            ) = $item;

            $sql = "
            INSERT INTO {$this->tablename} 
            (id, `email`,`description`, secret, fullname, uuid, id_gender, id_nationality, id_country, id_language, id_profile, id_parent)
            VALUES($id, '$email', '$description', '$secret','$fullname','$uuid','$idgender','$idnationality','$idcountry','$idlanguage','$idprofile', $idparent)
            ";
            $this->execute($sql);
        }
    }

    public function down(): void
    {
        $this->table($this->tablename)->drop()->save();
    }
}

