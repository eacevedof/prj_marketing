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
    
    private function _get_properties_js(string $field): string
    {
        return "_{$field}: {type: String, state:true},";
    }

    private function _get_html_fields(string $field, string $pos): string
    {
        return "<div class=\"form-group\">
                    <label for=\"$field\">\${this.texts.f{$pos}}</label>
                    <div id=\"field-{$field}\">
                        <input type=\"text\" id=\"{$field}\" .value=\${this._{$field}} class=\"form-control\">
                    </div>
                </div>";
    }

    private function _build_create_js(): void
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

    private function _build_create_edit_tpl(): void
    {
        $skip = [
            "processflag", "insert_platform", "insert_user", "insert_date", "delete_platform", "delete_user",
            "delete_date", "cru_csvnote", "is_erpsent", "is_enabled", "i", "update_platform", "update_user",
            "update_date"
        ];
        //tags %FIELD_LABELS%, %FIELD_KEY_AND_VALUES%
        $trs = [];
        $kvs = [];
        $i = 0;
        foreach ($this->fields as $field) {
            $fieldname = $field["field_name"];
            if (in_array($fieldname, $skip)) continue;
            $pos = sprintf("%02d", $i);
            $trs[] = "\"f$pos\" => __(\"tr_{$fieldname}\"),";
            $kvs[] = "\"$fieldname\" => \"\",";
            $i++;
        }
        $trs = implode("\n", $trs);
        $kvs = implode("\n", $kvs);

        $contenttpl = file_get_contents($this->pathtpl);
        $contenttpl = $this->_replace($contenttpl, ["%FIELD_LABELS%" => $trs, "%FIELD_KEY_AND_VALUES%" => $kvs]);
        $pathfile = "{$this->pathmodule}/{$this->type}";
        file_put_contents($pathfile, $contenttpl);
    }

    public function build(): void
    {
        switch ($this->type) {
            case self::TYPE_CREATE_JS:
                $this->_build_create_js();
            break;
            case self::TYPE_EDIT_JS:
                $this->_build_edit_js();
            break;
            case self::TYPE_CREATE_TPL:
            case self::TYPE_EDIT_TPL:
                $this->_build_create_edit_tpl();
            break;

            case self::TYPE_INFO_TPL:
                $this->_build_tpl();
            break;
            case self::TYPE_CSS:
                $this->_build_css();
            break;
        }
    }
}