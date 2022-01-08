<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Factories\Specific\ValidatorFactory
 * @file ValidatorFactory.php v1.0.0
 * @date 20-11-2021 00:50 SPAIN
 * @observations
 */
namespace App\Factories\Specific;

use App\Models\AppEntity;
use App\Models\FieldsValidator;

final class ValidatorFactory
{
    public static function get(array $request, AppEntity $entity): FieldsValidator
    {
        return new FieldsValidator($request, $entity);
    }

}//ValidatorFactory
