<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name TheApplication\Components\ComponentRouter
 * @file ComponentRouter.php 1.0.0
 * @date 30-07-2022 08:19 SPAIN
 * @observations
 */

namespace TheFramework\Components;

final class ComponentRouter
{
    private string $requestUri;
    private string $pathRoutesFile;
    private array $routesArray;
    private array $requestArray;
    private array $argumentsArray;

    private static array $routes;

    public function __construct(array $routesArray = [], string $pathRoutesFile = "")
    {
        $this->requestUri = $_SERVER["REQUEST_URI"];
        $this->pathRoutesFile = $pathRoutesFile;
        $this->routesArray = $routesArray;
        self::$routes = $routesArray;

        $this->requestArray = [
            "url" => "",
            "url_pieces" => [],
            "get_params" => []
        ];
        $this->_loadRoutesArray();
        $this->_loadRequestArrayPiecesAndParams();
    }

    private function _loadRoutesArray(): void
    {
        if ($this->routesArray || !$this->pathRoutesFile) {
            return;
        }
        $this->routesArray = include($this->pathRoutesFile);
        self::$routes = $this->routesArray;
    }

    private function _loadRequestArrayPiecesAndParams(): void
    {
        $arGetParams = $this->_getGETParamsFromRequestUri($this->requestUri);
        $arUrlPieces = $this->_getUrlPieces($this->requestUri);
        $this->requestArray["url"] = "/".implode("/", $arUrlPieces);
        $this->requestArray["url_pieces"] = $arUrlPieces;
        $this->requestArray["get_params"] = $arGetParams;
    }

    private function _getRouteByExactPieces(): array
    {
        $requri = $this->requestArray["url"];
        $routes = array_filter($this->routesArray, function ($route) use ($requri) {
            return $route["url"] === $requri;
        });
        return reset($routes) ?: [];
    }

    private function _getRouteByPieces(): array
    {
        $allPiecesFound = false;
        foreach($this->routesArray as $i => $arRoute) {
            $sUrl = $arRoute["url"];
            $routesArrayep = $this->_getUrlPieces($sUrl, true);
            $this->argumentsArray = [];
            //compare pieces comprueba todo, tammaño y tags
            $allPiecesFound = $this->_areAllPiecesFoundAndLoadedIntoArumentArray($this->requestArray["url_pieces"], $routesArrayep);
            if ($allPiecesFound) {
                break;
            }
        }

        if ($allPiecesFound) {
            $this->_addToGlobalGETVarEachTag($this->requestArray["url_pieces"], $routesArrayep);
        }

        $r = array_merge(
            $this->routesArray[$i],
            $this->argumentsArray ? ["_args" => $this->argumentsArray] : []
        );
        return $r;
    }

    public function get_rundata(): array
    {
        return $this->_getRouteByExactPieces() ?: $this->_getRouteByPieces();
    }

    private function _doesRouteHaveNullableVars(array $route): bool
    {
        $null = array_filter(
            array_values($route),
            function ($string) {
                return (strstr($string, "?:") || strstr($string, "?int:"));
            }
        );
        return (bool) count($null);
    }

    private function _doRequestAndRouteMatchInNumberOfPieces(array $requestPieces, array $routePieces): bool
    {
        if (($numReqPieces = count($requestPieces)) === ($numRoutePieces = count($routePieces))) {
            return true;
        }
        //esto no está del todo fino ya que no se permitiria varios ?: como partes de la ruta
        if ($this->_doesRouteHaveNullableVars($routePieces) && $numReqPieces === ($numRoutePieces - 1)) {
            return true;
        }
        return false;
    }

    private function _areAllPiecesFoundAndLoadedIntoArumentArray(array $requestArray, array $arRoute): bool
    {
        //true si casan todas las partes y de paso carga los args
        if (!$this->_doRequestAndRouteMatchInNumberOfPieces($requestArray, $arRoute)) {
            return false;
        }

        foreach($arRoute as $i => $sPiece) {
            if ($this->_doesHaveUrlPieceHaveSomeVarArgument($sPiece)) {
                $tag = $this->_getVarArgumentInfo($sPiece);
                $value = $requestArray[$i] ?? null;
                if (!$this->_doesValueIsInTypes($value, $tag["types"])) {
                    return false;
                }
                $this->argumentsArray[$tag["key"]] = trim(urldecode($value ?? ""));
                continue;
            }
            $sReqval = $requestArray[$i];
            if ($sReqval != $sPiece) {
                return false;
            }
        }
        return true;
    }

    private function _addToGlobalGETVarEachTag(array $requestArray, array $arRoute): void
    {
        foreach($arRoute as $i => $sPiece) {
            if (!$this->_doesHaveUrlPieceHaveSomeVarArgument($sPiece)) {
                continue;
            }
            $tag = $this->_getVarArgumentInfo($sPiece);
            $_GET[$tag["key"]] = $requestArray[$i] ?? "";
        }
    }

    private function _doesHaveUrlPieceHaveSomeVarArgument(string $sPiece): bool
    {
        return (
            (strstr($sPiece, "{") && strstr($sPiece, "}")) ||
            strstr($sPiece, ":") ||
            strstr($sPiece, "?:")
        );
    }

    private function _doesValueIsInTypes(mixed $anyValue, array $types): bool
    {
        foreach ($types as $type) {
            if ($type === "int" && is_numeric($anyValue)) {
                return true;
            }
            if ($type === "string" && is_string($anyValue)) {
                return true;
            }
            if ($type === "null" && $anyValue === null) {
                return true;
            }
        }
        return false;
    }

    private function _getVarArgumentInfo(string $sPiece): array
    {
        //restrict/users/:page
        //restrict/users/int:page
        //restrict/users/?:page
        //restrict/users/?int:page

        $parts = explode(":", $sPiece);
        $r = [
            "types" => ["string"],
            "key" => $parts[1]
        ];
        $before = $parts[0];
        switch ($before) {
            case "": return $r;
            case "?":
                $r["types"][] = "null";
                break;
            case "?int":
                $r["types"] = ["null", "int"];
                break;
            case "int":
                $r["types"] = ["int"];
        }
        return $r;
    }

    private function _getGETQueryStringAsArray(string $queryString): array
    {
        $arRet = [];
        $arTmp = explode("&", $queryString);
        foreach($arTmp as $sEq) {
            $arParamVal = explode("=", $sEq);
            $arRet[$arParamVal[0]] = isset($arParamVal[1]) ? $arParamVal[1] : "";
        }
        return $arRet;
    }

    private function _getGETParamsFromRequestUri($sUrl): array
    {
        $arTmp = explode("?", $sUrl);
        if (!isset($arTmp[1])) {
            return [];
        }
        $arParams = $this->_getGETQueryStringAsArray($arTmp[1]);
        return $arParams;
    }

    private function _removeItemsWithEmptyValue(array &$urlPieces): void
    {
        $arNew = [];
        foreach($urlPieces as $i => $sValue) {
            if ($sValue) {
                $arNew[] = $sValue;
            }
        }
        $urlPieces = $arNew;
    }

    private function _getUrlPieces(string $sUrl, bool $haspattern = false): array
    {
        if (!$haspattern) {
            $arTmp = explode("?", $sUrl);
            if (isset($arTmp[1])) {
                $sUrl = $arTmp[0];
            }
        }
        $urlPieces = explode("/", $sUrl);
        $this->_removeItemsWithEmptyValue($urlPieces);
        return $urlPieces;
    }

    public static function get_url(string $name, array $args = []): string
    {
        $route = array_filter(self::$routes, function (array $route) use ($name) {
            if (!$alias = ($route["name"] ?? "")) {
                return false;
            }
            return trim($alias) === $name;
        });
        if (!$route) {
            return "";
        }
        $route = array_values($route);
        $url = $route[0]["url"];
        if (!$args) {
            return $url;
        }
        $tags = array_keys($args);
        $tags = array_map(function (string $tag) {
            return str_starts_with($tag, ":") ? $tag : ":$tag";
        }, $tags);

        $values = array_values($args);
        return str_replace($tags, $values, $url);
    }

}
