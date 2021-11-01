<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Models\ExampleModel 
 * @file ExampleModel.php v1.0.0
 * @date 29-11-2018 19:00 SPAIN
 * @observations
 */
namespace App\Models;

use App\Models\AppModel;

final class BaseUserModel extends AppModel
{
    public int $id;
    public string $email;
    public string $password;

    public function __construct() 
    {
        $this->fields = [
            "id" => ""
        ];
        $this->pks = [];
    }

    
}//ExampleModel
