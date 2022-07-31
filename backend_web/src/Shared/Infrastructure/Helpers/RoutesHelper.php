<?php
namespace App\Shared\Infrastructure\Helpers;

final class RoutesHelper
{
    private const PATH_ROUTES = PATH_SRC."/Shared/Infrastructure/routes/routes.php";
    private static ?array $routes = null;

    private static function _load_routes(): void
    {
        if (is_null(self::$routes))
            self::$routes = include(self::PATH_ROUTES);
    }

    public static function url(string $name, array $args=[]): string
    {
        self::_load_routes();

        $route = array_filter(self::$routes, function (array $route) use ($name) {
            if (!$alias = ($route["name"] ?? "")) return false;
            return trim($alias) === $name;
        });
        if (!$route) return "";
        $route = array_values($route);
        $url = $route[0]["url"];
        if (!$args) return $url;
        $tags = array_keys($args);
        $tags = array_map(function (string $tag){
            return str_starts_with($tag, ":") ? $tag : ":$tag";
        }, $tags);

        $values = array_values($args);
        return str_replace($tags, $values, $url);
    }
}
