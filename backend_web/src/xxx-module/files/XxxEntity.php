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
use App\Enums\EntityType;

final class XxxEntity extends AppEntity
{
    public function __construct()
    {
        $this->fields = %FIELDS%

        $this->pks = [
            "id", "uuid"
        ];

    }// construct

}//XxxEntity
