<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Traits\RequestTrait
 * @file RequestTrait.php 1.0.0
 * @date 18-11-2021 21:51 SPAIN
 * @observations
 */

namespace App\Shared\Infrastructure\Traits;

use App\Shared\Domain\Enums\RequestType;
use App\Shared\Infrastructure\Factories\ComponentFactory as CF;
use App\Shared\Infrastructure\Components\Request\RequestComponent;

trait RequestTrait
{
    protected ?RequestComponent $requestComponent = null;

    protected function _loadRequestComponentInstance(): RequestComponent
    {
        if (!$this->requestComponent) {
            $this->requestComponent = CF::getInstanceOf(RequestComponent::class);
        }
        return $this->requestComponent;
    }

    protected function _getRequestWithoutOperations(array $request = []): array
    {
        if (!$request) {
            $request = $this->requestComponent->getPost();
        }

        $without = [];
        foreach ($request as $key => $value) {
            //si no empieza con "_xxxx"
            if (substr($key, 0, 1) === "_") {
                continue;
            }

            $tmpValue = is_string($value) && trim($value) === ""
                            ? null
                            : trim($value ?? "")
            ;
            if (strstr($key, "id_") && is_numeric($value)) {
                $tmpValue = (int) $value;
            }
            $without[$key] = $tmpValue;
        }
        return $without;
    }

    protected function _getCsrfTokenFromRequest(array $request = []): string
    {
        if (!$request) {
            $request = $this->requestComponent->getPost();
        }
        return $request[RequestType::CSRF] ?? "";
    }

    private function _getLanguageByHttpAcceptLanguage(): string
    {
        $lang = explode(",", $_SERVER["HTTP_ACCEPT_LANGUAGE"]);
        $lang = $lang[0] ?? "en";
        $lang = strtolower($lang);
        $lang = explode("-", $lang);
        $lang = $lang[0] ?? "en";
        if (!in_array($lang, ["en", "es"]))
            return "en";
        return $lang;
    }

    protected function _loadHttpLanguage(): void
    {
        $_REQUEST["lang"] = $this->_getLanguageByHttpAcceptLanguage();
    }

}//RequestTrait
