<?php

namespace App\Components\Response;
use App\Enums\ResponseType;
use TheFramework\Helpers\HelperJson;

final class ResponseComponent
{
    private array $headers = [];
    private int $code = 0;

    public function json(): HelperJson
    {
        return new HelperJson();
    }

}