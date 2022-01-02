<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Models\ExampleModel 
 * @file ExampleModel.php v1.0.0
 * @date 29-11-2018 19:00 SPAIN
 * @observations
 */
namespace App\Models\Base;

use App\Models\AppModel;

final class UserPreferencesModel extends AppModel
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

}//ExampleModel
