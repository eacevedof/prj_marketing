<?php

namespace App\Components\Datatable;

final class Datatable
{
    private array $request;

    public function __construct(array $request)
    {
        $this->request = $request;
    }


}