<?php

namespace App\Shared\Infrastructure\Components\Export;

final class CsvComponent
{
    public const DELIMITER_SEMICOLON = ";";
    public const DELIMITER_COMMA = ";";
    public const DELIMITER_PIPE = "|";

    public function downloadResponse(
        string $filename,
        array $data,
        string $delimiter = self::DELIMITER_SEMICOLON
    ): void {
        header("Content-Type: application/csv");
        header("Content-Disposition: attachment; filename=\"{$filename}\";");
        $buffer = fopen("php://output", "w");
        foreach ($data as $line) {
            fputcsv($buffer, $line, $delimiter);
        }
        exit;
    }

    public function downloadResponseAsExcel(string $fileName, array $data): void
    {
        $eol = PHP_EOL;
        $fnFilter = function (&$str) {
            if (is_null($str)) $str = "";
            $str = preg_replace("/\t/", "\\t", $str);
            $str = preg_replace("/\r?\n/", "\\n", $str);
            if(strstr($str, "\"")) {
                $str = "\"" . str_replace("\"", "\"\"", $str) . "\"";
            }
        };

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"{$fileName}\";");
        foreach ($data as $i => $row) {
            if ($i === 0) {
                echo implode("\t", array_keys($row)).$eol;
            }
            array_walk($row, $fnFilter);
            echo implode("\t", array_values($row)).$eol;
        }
        exit;
    }
}
