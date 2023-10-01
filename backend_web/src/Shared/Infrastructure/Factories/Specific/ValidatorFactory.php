<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Shared\Infrastructure\Factories\Specific\ValidatorFactory
 * @file ValidatorFactory.php v1.0.0
 * @date 20-11-2021 00:50 SPAIN
 * @observations
 */

namespace App\Shared\Infrastructure\Factories\Specific;

use App\Shared\Domain\Entities\{AppEntity, FieldsValidator};

final class ValidatorFactory
{
    public static function getFieldValidator(array $request, ?AppEntity $entity = null): FieldsValidator
    {
        return new FieldsValidator($request, $entity);
    }

    public static function getFieldValidatorFromDto(object $requestDto, ?AppEntity $entity = null): FieldsValidator
    {
        return new FieldsValidator($requestDto, $entity);
    }

}//ValidatorFactory
