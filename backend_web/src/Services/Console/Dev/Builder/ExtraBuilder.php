<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Services\Console\Dev\Builder\FrontBuilder
 * @file FrontBuilder.php 1.0.0
 * @date 31-10-2022 17:46 SPAIN
 * @observations
 */
namespace App\Services\Console\Dev\Builder;

final class ExtraBuilder
{
    private string $type;
    private string $pathtpl;
    private string $pathmodule;
    private array $aliases;
    private array $fields;

    public const TYPE_EXTRA_MD = "extra.md";

    public function __construct(array $aliases, array $fields, string $pathtpl, string $pathmodule, string $type=self::TYPE_EXTRA_MD)
    {
       $this->pathtpl = $pathtpl;
       $this->pathmodule = $pathmodule;
       $this->aliases = $aliases;
       $this->fields = $fields;
       $this->type = $type;
    }
    
    private function _replace(string $content, array $replaces=[]): string
    {
        $basic = [
            "Xxxs" => $this->aliases["uppercased-plural"],
            "Xxx" => $this->aliases["uppercased"],
            "xxxs" => $this->aliases["lowered-plural"],
            "xxx" => $this->aliases["lowered"],
            "XXXS" => $this->aliases["uppered-plural"],
        ];
        $basic = $basic + $replaces;
        return str_replace(array_keys($basic), array_values($basic), $content);
    }
    
    private function _get_field_details(string $field): array
    {
       $type = array_filter($this->fields, function ($item) use ($field) {
           return $item["field_name"] === $field;
       });
       $type = array_values($type);
       return $type[0] ?? [];
    }

    private function _build_extra_md(): void
    {
        $skip = [
            "processflag", "insert_platform", "insert_user", "insert_date", "delete_platform", "delete_user"
            , "delete_date", "cru_csvnote", "is_erpsent", "is_enabled", "i", "update_platform", "update_user",
            "update_date"
        ];
        //tags %FIELDS%
        $arfields = [];
        foreach ($this->fields as $i =>$field) {
            $fieldname = $field["field_name"];
            if (in_array($fieldname, $skip)) continue;
            $arfields[$i] = $this->_get_properties_js($fieldname);
        }
        $strfields = implode("\n", $arfields);
        $firstfield = $this->fields[array_keys($arfields)[0]]["field_name"];

        $arfields = [];
        $i = 0;
        foreach ($this->fields as $field) {
            $fieldname = $field["field_name"];
            if (in_array($fieldname, $skip)) continue;
            $pos = sprintf("%02d", $i);
            $arfields[] = $this->_get_html_fields($fieldname, $pos);
            $i++;
        }
        $htmlfields = implode("\n", $arfields);

        $contenttpl = file_get_contents($this->pathtpl);
        $contenttpl = $this->_replace($contenttpl, [
            "%FIELDS%" => $strfields, "%HTML_FIELDS%" => $htmlfields, "%yyy%"=>$firstfield
        ]);
        $pathfile = "{$this->pathmodule}/{$this->type}";
        file_put_contents($pathfile, $contenttpl);
    }

    public function build(): void
    {
        switch ($this->type) {
            case self::TYPE_EXTRA_MD:
                $this->_build_extra_md();
            break;
        }
    }
}