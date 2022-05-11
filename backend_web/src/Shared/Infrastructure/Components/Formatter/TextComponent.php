<?php

namespace App\Shared\Infrastructure\Components\Formatter;

final class TextComponent
{
    private string $text = "";
    private string $result = "";

    public function slug(): string
    {
        $divider = "-";
        $text = preg_replace("~[^\pL\d]+~u", $divider, $this->text);
        // transliterate
        $text = iconv("utf-8", "us-ascii//TRANSLIT", $text);
        // remove unwanted characters
        $text = preg_replace("~[^-\w]+~", "", $text);
        $text = trim($text, $divider);
        // remove duplicate divider
        $text = preg_replace("~-+~", $divider, $text);
        // lowercase
        $text = strtolower($text);
        $this->result = $text;
        return $this->result;
    }

    public function set_text(string $text): self
    {
        $this->text = $text;
        return $this;
    }

    public function get(): string
    {
        return $this->result;
    }

    public function get_random_word(int $charlen=4, int $numbers=2): string
    {
        $chars = "bcdfghjklmnpqrstvxyz";
        $chars = str_split($chars);
        $vocals = "aeiou";
        $vocals = str_split($vocals);

        $word = [];
        for($i=0; $i<$charlen; $i++) {
            $word[] = (($i%2)===0) ? $chars[array_rand($chars)] : $vocals[array_rand($vocals)];
        }

        if(!$numbers) return strtoupper(implode("", $word));
        for($i=0; $i<$numbers; $i++){
            $word[] = rand(0, 9);
        }
        return strtoupper(implode("", $word));
    }

    public function get_csv_cleaned(string $csv): string
    {
        $csv = trim(strtolower($csv));
        if (!strstr($csv, ",")) return $csv;

        $parts = explode(",", $csv);
        $parts = array_map(function (string $string) {
            $string = trim($string);
            return !$string ? null : $string;
        }, $parts);

        $parts = array_filter($parts);
        $parts = array_unique($parts);
        return implode(", ", $parts);
    }
}