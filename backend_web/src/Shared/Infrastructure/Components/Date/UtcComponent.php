<?php

namespace App\Shared\Infrastructure\Components\Date;
use \DateTimeZone;
use \DateTime;

final class UtcComponent
{
    public const TZ_UTC = "UTC";
    public const FORMAT_FULL_DT = "Y-m-d H:i:s";
    public const FORMAT_ONLY_DATE = "Y-m-d";

    private function _get_offset_between_zones(string $targettz, string $sourcetz = self::TZ_UTC): int
    {
        $tz0 = new DateTimeZone($sourcetz);
        //$tzTarget = new DateTimeZone("Europe/Madrid");
        $tzTarget = new DateTimeZone($targettz);

        $dt0 = new DateTime("now", $tz0);
        //$dtTarget = new DateTime("now", $tzTarget);
        $secsoffset = $tzTarget->getOffset($dt0);
        return (int) $secsoffset;
    }

    /**
     * de UTC a TZx
     * @param string $utcdt "2022-01-03 10:11:22"
     * @param string $targettz user-timezone
     * @param string $format "Y-m-d H:i:s",
     * @return string
     * @throws \Exception
     */
    public function get_utcdt_in_tz(
        string $utcdt,
        string $targettz=self::TZ_UTC,
        string $format=self::FORMAT_FULL_DT
    ): string
    {
        $source = new DateTime($utcdt, new DateTimeZone(self::TZ_UTC));
        $source->setTimezone(new DateTimeZone($targettz));
        return $source->format($format);
    }

    /**
     * de TZx a UTC
     * Por defecto va de UI => BD. De UTCx a UTC0
     * @param string $sourcedt "2022-01-03 10:11:22"
     * @param string $sourcetz "Europe/Madrid"
     * @param string $targettz "UTC"
     * @param string $format "Y-m-d H:i:s",
     * @return string
     * @throws \Exception
     */
    public function get_dt_into_tz(
        string $sourcedt,
        string $sourcetz,
        string $targettz=self::TZ_UTC,
        string $format=self::FORMAT_FULL_DT
    ): string
    {
        $source = new DateTime($sourcedt, new DateTimeZone($sourcetz));
        $source->setTimezone(new DateTimeZone($targettz));
        return $source->format($format);
    }

    /**
     * current datetime in TZx (default UTC)
     * @param string $timezone
     * @param string $format
     * @return string
     * @throws \Exception
     */
    public function get_nowdt_in_timezone(string $timezone=self::TZ_UTC, string $format=self::FORMAT_FULL_DT): string
    {
        $dt = new DateTime("now", new DateTimeZone($timezone));
        return $dt->format($format);
    }
    
    private function get_dt_by_ip(string $ip, string $format=self::FORMAT_FULL_DT): string
    {
        $timezone = $this->get_timezone_by_ip($ip);
        return $this->get_nowdt_in_timezone($timezone, $format);
    }

    public function get_timezone_by_ip(string $ip): string
    {
        if ($ip === "127.0.0.1") return self::TZ_UTC;
        $info = file_get_contents("http://ip-api.com/json/{$ip}");
        $info = json_decode($info, 1);
        /*
         * para que esto funcione hay que instalar una extension
        $info = geoip_record_by_name($ip);
        $timezone = geoip_time_zone_by_country_and_region($info["country_code"], $info["region"]);
        $info["timezone"] = $timezone;
        */

        return ($info["timezone"] ?? self::TZ_UTC);
    }
    
    public function get_utcdt_into_iptz(string $utcdt, string $ip, string $format=self::FORMAT_FULL_DT): string
    {
        $timezone = $this->get_timezone_by_ip($ip);
        if ($timezone === self::TZ_UTC) return date($format, strtotime($utcdt));
        return $this->get_dt_into_tz($utcdt, self::TZ_UTC, $timezone, $format);
    }
}