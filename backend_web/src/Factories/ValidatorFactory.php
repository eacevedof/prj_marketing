<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Factories\ValidatorFactory 
 * @file ValidatorFactory.php v1.0.0
 * @date 20-11-2021 00:50 SPAIN
 * @observations
 */
namespace App\Factories;

use App\Models\AppModel;
use App\Models\FieldsValidator;

final class ValidatorFactory
{
    public static function get(array $post, AppModel $model): FieldsValidator
    {
        return new FieldsValidator($post, $model);
    }
}//ServiceFactory
