<?php

namespace App\Shared\Infrastructure\Components\Date;
use \DateTimeZone;
use \DateTime;

final class UtcComponent
{
    public const DEFAULT_TZ = "Europe/London";
    public const DEFAULT_DT_FORMAT = "Y-m-d H:i:s";

    private function _get_offset_between_zones(string $targettz, string $sourcetz = self::DEFAULT_TZ): int
    {
        $tz0 = new DateTimeZone($sourcetz);
        //$tzTarget = new DateTimeZone("Europe/Madrid");
        $tzTarget = new DateTimeZone($targettz);

        $dt0 = new DateTime("now", $tz0);
        //$dtTarget = new DateTime("now", $tzTarget);
        $secsoffset = $tzTarget->getOffset($dt0);
        return (int) $secsoffset;
    }

    private function get_datetime_by_timezone(string $timezone, string $format=self::DEFAULT_DT_FORMAT): string
    {
        $dt = new DateTime("now", new DateTimeZone($timezone));
        return $dt->format($format);
    }

    private function get_datetime_by_ip(string $ip, string $format=self::DEFAULT_DT_FORMAT): string
    {
        $timezone = $this->get_timezone_by_ip($ip);
        return $this->get_datetime_by_timezone($timezone, $format);
    }

    public function get_timezone_by_ip(string $ip): string
    {
        $info = file_get_contents("http://ip-api.com/json/{$ip}");
        $info = json_decode($info, 1);
        $timezone = $info["timezone"] ?? self::DEFAULT_TZ;
        return $timezone;
    }
}