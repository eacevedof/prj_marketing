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
}