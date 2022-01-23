<?php

namespace App\Components\Date;

final class DateComponent
{
    private string $date1 = "";
    private string $date2 = "";

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

    public function is_valid(): bool
    {
        if (!$this->result = trim($this->date1)) return false;
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

    public function get(): string {return $this->result;}

}