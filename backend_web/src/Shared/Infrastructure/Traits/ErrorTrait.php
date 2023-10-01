<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Traits\ErrorTrait
 * @file ErrorTrait.php 1.0.0
 * @date 01-11-2018 19:00 SPAIN
 * @observations
 */

namespace App\Shared\Infrastructure\Traits;

trait ErrorTrait
{
    protected array $errors = [];
    protected bool $isError = false;

    protected function _addError(string $message): void
    {
        $this->isError = true;
        $this->errors[] = $message;
    }
    protected function _addErrors(array $messages): void
    {
        $this->isError = true;
        foreach ($messages as $message) {
            $this->errors[] = $message;
        }
    }
    protected function _setErrors(array $errors): void
    {
        $this->isError = true;
        $this->errors = $errors;
    }
    private function _getFlattenedArray(array $array): array
    {
        //de un array asociativo devuelve un array simple
        $flatten = [];
        array_walk_recursive($array, function ($a) use (&$flatten) {
            $flatten[] = $a;
        });
        return $flatten;
    }

    public function isError(): bool
    {
        return $this->isError;
    }

    public function getErrors(bool $doFlatten = false): array
    {
        if (!$doFlatten) {
            return $this->errors;
        }
        return $this->_getFlattenedArray($this->errors);
    }

    public function getErrorByPosition(int $position = 0): mixed
    {
        return $this->errors[$position] ?? null;
    }

}//ErrorTrait
