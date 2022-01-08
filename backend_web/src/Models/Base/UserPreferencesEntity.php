<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Models\ExampleEntity
 * @file ExampleEntity.php v1.0.0
 * @date 29-11-2018 19:00 SPAIN
 * @observations
 */
namespace App\Models\Base;

use App\Models\AppEntity;

final class UserPreferencesEntity extends AppEntity
{
    public function __construct()
    {
        $this->fields = [
            "id" => "id",
            "id_user" => "id_user",
            "pref_key" => "pref_key",
            "pref_value" => "pref_value"
        ];
    }

}//UserPreferencesEntity
