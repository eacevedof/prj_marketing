<?php

namespace App\Shared\Infrastructure\Components\Date;

final class DateComponent
{
    public const SOURCE_YMD = "ymd";
    public const SOURCE_DMY = "dmy";
    public const SOURCE_MDY = "mdy";

    public const TIME = "time";
    public const DATE = "date";
    public const DATETIME = "datetime";

    private string $date1 = "";
    private string $date2 = "";

    private array $parts = ["date"=>[], "time"=>""];
    private string $result = "";

    public function is_greater(int $which=1): bool
    {
        $d1 = trim($this->date1);
        $d2 = trim($this->date2);

        return ($which===1)
            ? strtotime($d1) > strtotime($d2)
            : strtotime($d2) > strtotime($d1)
        ;
    }

    public function get_date_only(string $date): string
    {
        $date = trim($date);
        $sep = strstr($date, "T") ? "T" : " ";
        $parts = explode($sep, $date);
        return $parts[0] ?? "";
    }

    public function is_valid(): bool
    {
        if (!$this->result = $this->get_date_only($this->date1))
            return false;

        list($y, $m, $d) = explode("-", $this->result);
        return checkdate($m, $d, $y);
    }

    public function set_date1(string $date): self
    {
        $this->date1 = $date;
        return $this;
    }

    public function set_date2(string $date): self
    {
        $this->date2 = $date;
        return $this;
    }

    public function explode(string $format=self::SOURCE_YMD): self
    {
        $sep = strstr($this->date1, "T") ? "T" : " ";
        $parts = explode($sep, $this->date1);
        $date = $parts[0];
        $time = $parts[1] ?? "";

        $sep = strstr($this->date1, "/") ? "/" : "-";
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

        $this->parts = [
            "date"=> $ymd,
        ];

        $time = explode(":", $time);
        $this->parts["time"] = [
            "h" => $time[0] ?? "00",
            "i" => $time[1] ?? "00",
            "s" => $time[2] ?? "00",
        ];

        return $this;
    }

    public function to_db(string $format=self::DATETIME): self
    {
        $date = ["y" => $this->parts["date"]["y"], "m" => $this->parts["date"]["m"], "d" => $this->parts["date"]["d"]];
        switch ($format) {
            case self::DATETIME:
                $this->result = implode("-", $date)." ".implode(":",$this->parts["time"]);
            break;
            case self::DATE:
                $this->result = implode("-", $date);
            break;
            case self::TIME:
                $this->result = implode(":", $this->parts["time"]);
            break;
        }
        return $this;
    }

    public function to_js(string $format=self::DATETIME): self
    {
        //"Y-m-d\TH:i:s"
        switch ($format) {
            case self::DATETIME:
                $time = $this->parts["time"];
                unset($time["s"]);
                $this->result = implode("-", $this->parts["date"])."T".implode(":",$time);
            break;
            case self::DATE:
                $this->result = implode("-", $this->parts["date"]);
            break;
            case self::TIME:
                $this->result = implode("-", $this->parts["time"]);
            break;
        }
        return $this;
    }

    public function get(): string {return $this->result;}

    public function get_parts(): array {return $this->parts;}

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
}