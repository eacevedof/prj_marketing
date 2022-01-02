<?php

namespace App\Components\Response;
use TheFramework\Helpers\HelperJson;

final class ResponseComponent
{
    private array $headers = [];
    private int $code = 0;

    public function json(): HelperJson
    {
        return new HelperJson();
    }

    public function add_header(string $key, string $value=""): self
    {
        $this->headers[] = "$key: $value";
        return $this;
    }

}