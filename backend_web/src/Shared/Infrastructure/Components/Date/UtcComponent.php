<?php

namespace App\Shared\Infrastructure\Components\Date;

use DateTime;
use DateTimeZone;

final class UtcComponent
{
    public const TZ_UTC = "UTC";
    public const FORMAT_FULL_DT = "Y-m-d H:i:s";
    public const FORMAT_ONLY_DATE = "Y-m-d";

    private function _getSecsDiffBetweenTzs(string $targettz, string $sourcetz = self::TZ_UTC): int
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
    public function getUtcDtInTargetTz(
        string $utcdt,
        string $targettz = self::TZ_UTC,
        string $format = self::FORMAT_FULL_DT
    ): string {
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
    public function getSourceDtIntoTargetTz(
        string $sourcedt,
        string $sourcetz,
        string $targettz = self::TZ_UTC,
        string $format = self::FORMAT_FULL_DT
    ): string {
        $source = new DateTime($sourcedt, new DateTimeZone($sourcetz));
        $source->setTimezone(new DateTimeZone($targettz));
        return $source->format($format);
    }

    /**
     * current datetime in TZx (default UTC)
     * @param string $targetTz
     * @param string $format
     * @return string
     * @throws \Exception
     */
    public function getNowDtIntoTargetTz(string $targetTz = self::TZ_UTC, string $format = self::FORMAT_FULL_DT): string
    {
        $dt = new DateTime("now", new DateTimeZone($targetTz));
        return $dt->format($format);
    }

    private function getDateByIp(string $ip, string $format = self::FORMAT_FULL_DT): string
    {
        $timezone = $this->getTimezoneByIp($ip);
        return $this->getNowDtIntoTargetTz($timezone, $format);
    }

    public function getTimezoneByIp(string $ip): string
    {
        if ($ip === "127.0.0.1") {
            return self::TZ_UTC;
        }
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

    public function getUtcDateIntoIpTimeZone(
        string $dtUTC,
        string $targetIp,
        string $format = self::FORMAT_FULL_DT
    ): string {
        $timezoneByIp = $this->getTimezoneByIp($targetIp);
        if ($timezoneByIp === self::TZ_UTC) {
            return date($format, strtotime($dtUTC));
        }
        return $this->getSourceDtIntoTargetTz($dtUTC, self::TZ_UTC, $timezoneByIp, $format);
    }
}
