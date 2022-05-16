<?php

namespace App\Shared\Infrastructure\Components\Date;

use \DateTimeImmutable;

final class DateComponent
{
    public const SOURCE_YMD = "ymd";
    public const SOURCE_DMY = "dmy";
    public const SOURCE_MDY = "mdy";

    public const TIME = "time";
    public const DATE = "date";
    public const DATETIME = "datetime";

    public function get_seconds_between(string $dtlt, string $dtgt): int
    {
        if ($dtlt === $dtgt) return 0;
        $dt1 = (new DateTimeImmutable($dtlt))->getTimestamp();
        $dt2 = (new DateTimeImmutable($dtgt))->getTimestamp();
        return ($dt2 - $dt1);
    }

    public function get_date_only(string $date): string
    {
        $date = trim($date);
        $sep = strstr($date, "T") ? "T" : " ";
        $parts = explode($sep, $date);
        return $parts[0] ?? "";
    }

    public function is_valid(string $date): bool
    {
        if (!$dateonly = $this->get_date_only($date))
            return false;

        list($y, $m, $d) = explode("-", $dateonly);
        return checkdate($m, $d, $y);
    }

    public function explode(string $date, string $format=self::SOURCE_YMD): array
    {
        $sep = strstr($date, "T") ? "T" : " ";
        $parts = explode($sep, $date);
        $date = $parts[0];
        $time = $parts[1] ?? "";

        $sep = strstr($date, "/") ? "/" : "-";
        $parts = explode($sep, $date);

        $ymd = $date;
        if ($format===self::SOURCE_YMD)
            $ymd = [
                "y" => $parts[0],
                "m" => $parts[1],
                "d" => $parts[2],
            ];

        if ($format===self::SOURCE_DMY)
            $ymd = [
                "y" => $parts[2],
                "m" => $parts[1],
                "d" => $parts[0],
            ];

        if ($format===self::SOURCE_MDY)
            $ymd = [
                "y" => $parts[2],
                "m" => $parts[0],
                "d" => $parts[1],
            ];

        $return = [
            "date"=> $ymd,
        ];

        $time = explode(":", $time);
        $return["time"] = [
            "h" => $time[0] ?? "00",
            "i" => $time[1] ?? "00",
            "s" => $time[2] ?? "00",
        ];

        return $return;
    }

    public function to_db(string $date, string $format=self::DATETIME): string
    {
        $clean = str_replace(" ","T", $date);
        $seconds = strtotime($clean);
        switch ($format) {
            case self::DATETIME: return date("Y-m-d H:i:s", $seconds);
            case self::DATE: return date("Y-m-d", $seconds);
            case self::TIME: return date("H:i:s", $seconds);
        }
        return $clean;
    }

    public function get_jsdt(string $dbdt): string
    {
        if (!$dbdt) return $dbdt;
        //$dbdt = substr($dbdt, 0, 16);
        return str_replace(" ","T", $dbdt);
    }

    public function get_dbdt(string $jsdt): string
    {
        if (!$jsdt) return $jsdt;
        if (strlen($jsdt)==16) $jsdt = "$jsdt:00";
        if (strstr($jsdt,"T"))
            $jsdt = str_replace("T"," ", $jsdt);
        return $jsdt;
    }

    public function add_time(string $dt, int $seconds): string
    {
        $newdate = strtotime($dt) + $seconds;
        return date("Y-m-d H:i:s", $newdate);
    }

    public function get_last_hour(string $dt): string
    {
        $sep = strstr($dt, "T") ? "T" : " ";
        $date = explode($sep, $dt)[0];
        return "{$date}{$sep}23:59:59";
    }
}