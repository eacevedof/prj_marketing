<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Checker\Application\CheckerService
 * @file CheckerService.php 1.0.0
 * @date 21-06-2020 20:52 SPAIN
 * @observations
 */
namespace App\Checker\Application;

final class CheckerService
{
    public static function is_valid_url(string $value): bool
    {
        $proto = substr($value, 0, 8);
        if (!(strstr($proto, "http://") || strstr($proto, "https://")))
            return false;

        return filter_var($value, FILTER_VALIDATE_URL);
    }

    public static function is_valid_color(string $hexcolor): bool
    {
        $hexcolor = ltrim($hexcolor, "#");
        if (
            ctype_xdigit($hexcolor) &&
            (strlen($hexcolor) == 6 || strlen($hexcolor) == 3)
        )
            return true;
        return false;
    }

    public static function is_valid_email(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}