<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Restrict\Xxxs\Domain\XxxEntity
 * @file XxxEntity.php v1.0.0
 * @date %DATE% SPAIN
 */
namespace App\Restrict\Xxxs\Domain;

use App\Shared\Domain\Entities\AppEntity;
use App\Shared\Infrastructure\Enums\EntityType;

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
