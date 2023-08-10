<?php

namespace App\Shared\Infrastructure\Components\Date;

use DateTimeImmutable;

final class DateComponent
{
    public const SOURCE_YMD = "ymd";
    public const SOURCE_DMY = "dmy";
    public const SOURCE_MDY = "mdy";

    public const TIME = "time";
    public const DATE = "date";
    public const DATETIME = "datetime";

    public function getSecondsBetween(string $dtFrom, string $dtTo): int
    {
        if ($dtFrom === $dtTo) {
            return 0;
        }
        $dt1 = (new DateTimeImmutable($dtFrom))->getTimestamp();
        $dt2 = (new DateTimeImmutable($dtTo))->getTimestamp();
        return ($dt2 - $dt1);
    }

    public function getDateOnly(string $date): string
    {
        $date = trim($date);
        $sep = strstr($date, "T") ? "T" : " ";
        $parts = explode($sep, $date);
        return $parts[0] ?? "";
    }

    public function isValidDate(string $date): bool
    {
        if (!$dateOnly = $this->getDateOnly($date)) {
            return false;
        }

        list($y, $m, $d) = explode("-", $dateOnly);
        return checkdate($m, $d, $y);
    }

    public function getDateExploded(string $date, string $format = self::SOURCE_YMD): array
    {
        $sep = strstr($date, "T") ? "T" : " ";
        $parts = explode($sep, $date);
        $date = $parts[0];
        $time = $parts[1] ?? "";

        $sep = strstr($date, "/") ? "/" : "-";
        $parts = explode($sep, $date);

        $ymd = $date;
        if ($format === self::SOURCE_YMD) {
            $ymd = [
                "y" => $parts[0],
                "m" => $parts[1],
                "d" => $parts[2],
            ];
        }

        if ($format === self::SOURCE_DMY) {
            $ymd = [
                "y" => $parts[2],
                "m" => $parts[1],
                "d" => $parts[0],
            ];
        }

        if ($format === self::SOURCE_MDY) {
            $ymd = [
                "y" => $parts[2],
                "m" => $parts[0],
                "d" => $parts[1],
            ];
        }

        $return = [
            "date" => $ymd,
        ];

        $time = explode(":", $time);
        $return["time"] = [
            "h" => $time[0] ?? "00",
            "i" => $time[1] ?? "00",
            "s" => $time[2] ?? "00",
        ];

        return $return;
    }

    public function getDateInDbFormat(string $date, string $format = self::DATETIME): string
    {
        $clean = str_replace(" ", "T", $date);
        $seconds = strtotime($clean);
        switch ($format) {
            case self::DATETIME: return date("Y-m-d H:i:s", $seconds);
            case self::DATE: return date("Y-m-d", $seconds);
            case self::TIME: return date("H:i:s", $seconds);
        }
        return $clean;
    }

    public function getDateInJsFormat(string $dtDb): string
    {
        if (!$dtDb) {
            return $dtDb;
        }
        //$dbdt = substr($dbdt, 0, 16);
        return str_replace(" ", "T", $dtDb);
    }

    public function getDateInDbFormat00(string $dtJs): string
    {
        if (!$dtJs) {
            return $dtJs;
        }
        if (strlen($dtJs) === 16) {
            $dtJs = "$dtJs:00";
        }
        if (strstr($dtJs, "T")) {
            $dtJs = str_replace("T", " ", $dtJs);
        }
        return $dtJs;
    }

    public function addSecondsToDate(string $dt, int $seconds): string
    {
        $newdate = strtotime($dt) + $seconds;
        return date("Y-m-d H:i:s", $newdate);
    }

    public function getLastSecondInSomeDate(string $dt): string
    {
        $sep = strstr($dt, "T") ? "T" : " ";
        $date = explode($sep, $dt)[0];
        return "{$date}{$sep}23:59:59";
    }
}
