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

final class FrontBuilder
{
    private string $type;
    private string $pathtpl;
    private string $pathmodule;
    private array $aliases;
    private array $fields;

    public const TYPE_CREATE_JS = "create.js";
    public const TYPE_CREATE_TPL = "create.tpl";
    public const TYPE_EDIT_JS = "edit.js";
    public const TYPE_EDIT_TPL = "edit.tpl";
    public const TYPE_INFO_TPL = "info.tpl";
    public const TYPE_INDEX_TPL = "index.tpl";
    public const TYPE_CSS = "xxxs.css";

    public function __construct(array $aliases, array $fields, string $pathtpl, string $pathmodule, string $type=self::TYPE_CREATE_JS)
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

    private function _get_length(string $field): string
    {
        $fielddet = $this->_get_field_details($field);
        $length = $fielddet["field_length"] ?? "";
        if (!$length)
            $length = $fielddet["ntot"] ?? "";
        return $length;
    }
    
    private function _get_field_create_tpl(string $field): string
    {
        return "_{$field}: {type: String, state:true},";
    }

    private function _build_create_js(): void
    {
        $skip = [
            "processflag", "insert_platform", "insert_user", "insert_date", "delete_platform", "delete_user"
            , "delete_date", "cru_csvnote", "is_erpsent", "is_enabled", "i", "update_platform", "update_user",
            "update_date"
        ];
        //tags %FIELDS%
        foreach ($this->fields as $field) {
            $fieldname = $field["field_name"];
            if (in_array($fieldname, $skip)) continue;
            $arfields[] = $this->_get_field_create_tpl($fieldname);
        }
        $strfields = implode("\n", $arfields);
        $contenttpl = file_get_contents($this->pathtpl);
        $contenttpl = $this->_replace($contenttpl, ["%FIELDS%" => $strfields]);
        $pathfile = "{$this->pathmodule}/{$this->aliases["uppercased"]}{$this->type}.php";
        file_put_contents($pathfile, $contenttpl);
    }

    private function _build_repository(): void
    {
        $skip = [
            "processflag", "insert_platform", "insert_user", "insert_date", "delete_platform", "delete_user"
            , "delete_date", "cru_csvnote", "is_erpsent", "is_enabled", "i", "update_platform", "update_user",
            "update_date"
        ];
        //tags: %TABLE%, %SEARCH_FIELDS%, %INFO_FIELDS%, xxx

        $arfields = [];
        foreach ($this->fields as $field) {
            $fieldname = $field["field_name"];
            if (in_array($fieldname, $skip)) continue;
            $arfields[] = "\"m.$fieldname\"";
        }
        $searchfields = implode(",\n", $arfields);

        $skip = [
            "processflag", "insert_platform", "update_platform", "delete_platform", "cru_csvnote",
            "is_erpsent", "is_enabled", "i"
        ];
        $arfields = [];
        foreach ($this->fields as $field) {
            $fieldname = $field["field_name"];
            if (in_array($fieldname, $skip)) continue;
            $arfields[] = "\"m.$fieldname\"";
        }
        $infofields = implode(",\n", $arfields);

        $contenttpl = file_get_contents($this->pathtpl);
        $contenttpl = $this->_replace($contenttpl,
                        [
                            "%TABLE%"=>$this->aliases["raw"],
                            "%SEARCH_FIELDS%"=> $searchfields,
                            "%INFO_FIELDS%"=> $infofields,
                        ]);

        $pathfile = "{$this->pathmodule}/{$this->aliases["uppercased"]}{$this->type}.php";
        file_put_contents($pathfile, $contenttpl);
    }

    private function _build_js(): void
    {
        $contenttpl = file_get_contents($this->pathtpl);
        $contenttpl = $this->_replace($contenttpl);
        $pathfile = "{$this->pathmodule}/{$this->aliases["uppercased-plural"]}{$this->type}.php";
        file_put_contents($pathfile, $contenttpl);
    }

    private function _build_css(): void
    {
        $skip = [
            "processflag", "insert_platform", "insert_user", "insert_date", "delete_platform", "delete_user"
            , "delete_date", "cru_csvnote", "is_erpsent", "is_enabled", "i", "update_platform", "update_user",
            "update_date"
        ];
        //tags: %FIELD_RULES%

        $arfields = [];
        $columns = [];
        foreach ($this->fields as $field) {
            $fieldname = $field["field_name"];
            if (in_array($fieldname, $skip)) continue;
            $arfields[] = $this->_get_rule_tpl($fieldname);
            $columns[] = $this->_get_dtcolumn_tpl($fieldname);
        }
        $rules = implode("", $arfields);
        $dtcolumns = implode("\n", $columns);

        $contenttpl = file_get_contents($this->pathtpl);
        $contenttpl = $this->_replace($contenttpl,
            [
                "%FIELD_RULES%" => $rules,
                "%DT_COLUMNS%"  => $dtcolumns
            ]);
        $pathfile = "{$this->pathmodule}/{$this->aliases["uppercased-plural"]}{$this->type}.php";
        file_put_contents($pathfile, $contenttpl);
    }


    private function _build_tpl(): void
    {
        $contenttpl = file_get_contents($this->pathtpl);
        $contenttpl = $this->_replace($contenttpl);
        $pathfile = "{$this->pathmodule}/{$this->aliases["uppercased-plural"]}{$this->type}.php";
        file_put_contents($pathfile, $contenttpl);
    }
    
    public function build(): void
    {
        switch ($this->type) {
            case self::TYPE_CREATE_JS:
                $this->_build_create_js();
            break;
            case self::TYPE_CREATE_TPL:

            break;
            case self::TYPE_EDIT_JS:
                $this->_build_js();
            break;

            case self::TYPE_EDIT_TPL:
            case self::TYPE_INFO_TPL:
                $this->_build_tpl();
            break;
            case self::TYPE_CSS:
                $this->_build_css();
            break;
        }
    }
}