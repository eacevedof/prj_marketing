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
    private const NAME_PATTERN = "/^([A-Z,a-zÑñáéíóú]+ )+[A-Z,a-zÑñáéíóú]+$|^[A-Z,a-záéíóú]+$/";
    private const ADDRESS_PATTERN = "/^[a-zA-ZÑñáéíóú]+[a-zA-ZÑñáéíóú0-9\s,\.'\-]{3,}[a-zA-Z0-9\.]$/";
    private const PHONE_PATTERN = "/^(\d{3} )+\d+$|^\d{3,}$/";

    public static function isValidUrl(?string $url): bool
    {
        if (!$url) {
            return false;
        }
        $proto = substr($url, 0, 8);
        if (!(strstr($proto, "http://") || strstr($proto, "https://"))) {
            return false;
        }

        return filter_var($url, FILTER_VALIDATE_URL);
    }

    public static function isValidColor(?string $hexColor): bool
    {
        if (!$hexColor) {
            return false;
        }
        $hexColor = ltrim($hexColor, "#");
        if (
            ctype_xdigit($hexColor) &&
            (strlen($hexColor) == 6 || strlen($hexColor) == 3)
        ) {
            return true;
        }
        return false;
    }

    public static function isValidEmail(?string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function isNameFormatOk(?string $name): bool
    {
        $matches = [];
        preg_match(self::NAME_PATTERN, $name, $matches);
        return (bool) ($matches[0] ?? "");
    }

    public static function isAddressFormatOk(?string $address): bool
    {
        $matches = [];
        preg_match(self::ADDRESS_PATTERN, $address, $matches);
        return (bool) ($matches[0] ?? "");
    }

    public static function isBoolean(?string $value): bool
    {
        return in_array($value, ["", "1", "0", 0, 1,null]);
    }

    public static function isPhoneFormatOk(?string $phone): bool
    {
        $matches = [];
        preg_match(self::PHONE_PATTERN, $phone, $matches);
        return (bool) ($matches[0] ?? "");
    }

    public static function isValidDate(?string $date): bool
    {
        if (!$date) {
            return false;
        }
        if (strlen($date) != 10) {
            return false;
        }
        $date = explode("-", $date);
        return checkdate(
            (int) ($date[1] ?? ""),
            (int) ($date[2] ?? ""),
            (int) $date[0]
        );
    }

}
