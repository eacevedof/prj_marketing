<?php

namespace App\Shared\Infrastructure\Components\Formatter;

final class TextComponent
{
    public function slug(string $toslug): string
    {
        $toslug = trim($toslug);
        $toslug = preg_replace("/\s+/", " ",$toslug);
        $toslug = $this->_remove_accents($toslug);
        $divider = "-";
        $text = preg_replace("~[^\pL\d]+~u", $divider, $toslug);
        // transliterate
        $text = iconv("utf-8", "us-ascii//TRANSLIT", $text);
        // remove unwanted characters
        $text = preg_replace("~[^-\w]+~", "", $text);
        $text = trim($text, $divider);
        // remove duplicate divider
        $text = preg_replace("~-+~", $divider, $text);
        // lowercase
        $text = strtolower($text);
        return $text;
    }

    private function _remove_accents(string $withaccents): string
    {
        //Reemplazamos la A y a
        $withaccents = str_replace(
            ["Á", "À", "Â", "Ä", "á", "à", "ä", "â", "ª"],
            ["A", "A", "A", "A", "a", "a", "a", "a", "a"],
            $withaccents
        );

        //Reemplazamos la E y e
        $withaccents = str_replace(
            ["É", "È", "Ê", "Ë", "é", "è", "ë", "ê"],
            ["E", "E", "E", "E", "e", "e", "e", "e"],
            $withaccents );

        //Reemplazamos la I y i
        $withaccents = str_replace(
            ["Í", "Ì", "Ï", "Î", "í", "ì", "ï", "î"],
            ["I", "I", "I", "I", "i", "i", "i", "i"],
            $withaccents );

        //Reemplazamos la O y o
        $withaccents = str_replace(
            ["Ó", "Ò", "Ö", "Ô", "ó", "ò", "ö", "ô"],
            ["O", "O", "O", "O", "o", "o", "o", "o"],
            $withaccents );

        //Reemplazamos la U y u
        $withaccents = str_replace(
            ["Ú", "Ù", "Û", "Ü", "ú", "ù", "ü", "û"],
            ["U", "U", "U", "U", "u", "u", "u", "u"],
            $withaccents );

        //Reemplazamos la N, n, C y c
        $withaccents = str_replace(
            ["Ñ", "ñ", "Ç", "ç"],
            ["N", "n", "C", "c"],
            $withaccents
        );

        return $withaccents;
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

    public function get_cancelled_email(string $email): string
    {
        $email = explode("@", $email);
        return "$email@deleted.ddd";
    }
}