<?php

namespace App\Shared\Infrastructure\Helpers;

use BOOT;

final class RoutesHelper
{
    private const FIND_PARAMS_PATTERN = "/[\?|\?int|int]*:[a-z,A-Z]+/";
    private const PATH_ROUTES_FILE = BOOT::PATH_SRC."/Shared/Infrastructure/routes/routes.php";
    private static ?array $routes = null;

    private static function _loadRoutesIfEmpty(): void
    {
        if (is_null(self::$routes)) {
            self::$routes = include(self::PATH_ROUTES_FILE);
        }
    }

    private static function _getParamsFromUrl(string $url): array
    {
        $matches = [];
        preg_match_all(self::FIND_PARAMS_PATTERN, $url, $matches);
        return $matches[0] ?? [];
    }

    public static function getUrlByRouteName(string $routeName, array $args = []): string
    {
        self::_loadRoutesIfEmpty();

        $route = array_filter(self::$routes, function (array $route) use ($routeName) {
            if (!$alias = ($route["name"] ?? "")) {
                return false;
            }
            return trim($alias) === $routeName;
        });
        if (!$route) {
            return "";
        }
        $route = array_values($route);
        $url = $route[0]["url"];
        if (!$args) {
            return $url;
        }

        $params = self::_getParamsFromUrl($url);
        $tags = array_keys($args);
        $tags = array_map(function (string $tagArg) use ($params) {
            $tag = str_starts_with($tagArg, ":") ? $tagArg : ":$tagArg";
            $param = array_filter($params, function (string $param) use ($tag) {
                return strstr($param, $tag);
            });
            $param = array_values($param);
            return $param[0] ?? "";
        }, $tags);

        $values = array_values($args);
        $url = str_replace($tags, $values, $url);
        if (in_array("_nods", array_values($args))) {
            if (str_ends_with($url, "/")) {
                return substr_replace($url, "", -1);
            }
        }
        return $url;
    }
}
