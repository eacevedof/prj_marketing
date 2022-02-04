<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Models\ExampleEntity
 * @file ExampleEntity.php v1.0.0
 * @date 29-11-2018 19:00 SPAIN
 * @observations
 */
namespace App\Restrict\Users\Domain;

use App\Shared\Domain\Entities\AppEntity;

final class UserPermissionsEntity extends AppEntity
{
    public function __construct()
    {
        $this->fields = [
            "id" => "id",
            "email" => "eamil",
            "secret" => "password"
        ];
    }

}//UserPermissionsEntity
