<?php
namespace App\Shared\Infrastructure\Components\Export;

final class CsvComponent
{
    public const DELIMITER_SEMICOLON = ";";
    public const DELIMITER_COMMA = ";";
    public const DELIMITER_PIPE = "|";

    public function download(string $filename, array $data, string $delimiter=self::DELIMITER_SEMICOLON): void
    {
        header("Content-Type: application/csv");
        header("Content-Disposition: attachment; filename=\"{$filename}\";");
        $buffer = fopen("php://output", "w");
        foreach ($data as $line)
            fputcsv($buffer, $line, $delimiter);
        exit;
    }

    public function download_as_excel(string $filename, array $data, string $delimiter=self::DELIMITER_SEMICOLON): void
    {
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"{$filename}\";");
        $buffer = fopen("php://output", "w");
        foreach ($data as $line)
            fputcsv($buffer, $line, $delimiter);
        exit;
    }
}