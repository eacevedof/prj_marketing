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
    private array $skipfields;

    public const TYPE_INSERT_JS     = "Xxxs-front/insert.js";
    public const TYPE_INSERT_TPL    = "Xxxs-front/insert.tpl";
    public const TYPE_UPDATE_JS     = "Xxxs-front/update.js";
    public const TYPE_UPDATE_TPL    = "Xxxs-front/update.tpl";
    public const TYPE_INFO_TPL      = "Xxxs-front/info.tpl";
    public const TYPE_INDEX_TPL     = "Xxxs-front/index.tpl";
    public const TYPE_CSS           = "Xxxs-front/xxxs.css";

    public function __construct(array $aliases, array $fields, string $pathtpl, string $pathmodule, string $type=self::TYPE_INSERT_JS)
    {
       $this->pathtpl = $pathtpl;
       $this->pathmodule = $pathmodule;
       $this->aliases = $aliases;
       $this->fields = $fields;
       $this->type = $type;
       $this->_load_skip_fields();;
    }
    
    private function _load_skip_fields(): void
    {
        $this->skipfields = [
            "processflag", "insert_platform", "insert_user", "insert_date", "delete_platform", "delete_user"
            , "delete_date", "cru_csvnote", "is_erpsent", "is_enabled", "i", "update_platform", "update_user",
            "update_date"
        ];        
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

    private function _create_file(string $pathfile, string $content):void
    {
        //esta creando un modulo por fuera de files por eso peta
        $dirname = dirname($pathfile);
        if (!is_dir($dirname)) //$r = mkdir($dirname,0777,1);
            exec("mkdir -p $dirname");
        sleep(1);
        $r = file_put_contents($pathfile, $content);
        if ($r === false)
            exit("ERROR on creation:\n\t$dirname\n\t$pathfile\n");
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
        $len = $this->_get_length($field);
        return "<div class=\"form-group\">
                    <label for=\"$field\">\${this.texts.f{$pos}}</label>
                    <div id=\"field-{$field}\">
                        <input type=\"text\" id=\"{$field}\" .value=\${this._{$field}} class=\"form-control\" maxlength=\"$len\">
                    </div>
                </div>";
    }

    private function _build_create_js(): void
    {
        //tags %FIELDS%
        $arfields = [];
        foreach ($this->fields as $i =>$field) {
            $fieldname = $field["field_name"];
            if (in_array($fieldname, $this->skipfields + ["id","uuid"])) continue;
            $arfields[$i] = $this->_get_properties_js($fieldname);
        }
        $strfields = implode("\n", $arfields);
        $firstfield = $this->fields[array_keys($arfields)[0]]["field_name"];

        $arfields = [];
        $i = 0;
        foreach ($this->fields as $field) {
            $fieldname = $field["field_name"];
            if (in_array($fieldname, $this->skipfields)) continue;
            $pos = sprintf("%02d", $i);
            $arfields[] = $this->_get_html_fields($fieldname, $pos);
            $i++;
        }
        $htmlfields = implode("\n", $arfields);

        $contenttpl = file_get_contents($this->pathtpl);
        $contenttpl = $this->_replace($contenttpl, [
            "%FIELDS%" => $strfields, "%HTML_FIELDS%" => $htmlfields, "%yyy%"=>$firstfield
        ]);

        $pathfile = $this->_replace($this->type);
        $pathfile = "{$this->pathmodule}/{$pathfile}";
        $this->_create_file($pathfile, $contenttpl);
    }

    private function _build_update_js(): void
    {
        //tags %FIELDS%
        $arfields = [];
        foreach ($this->fields as $i =>$field) {
            $fieldname = $field["field_name"];
            if (in_array($fieldname, $this->skipfields)) continue;
            $arfields[$i] = $this->_get_properties_js($fieldname);
        }
        $strfields = implode("\n", $arfields);
        $firstfield = $this->fields[array_keys($arfields)[0]]["field_name"];

        $arfields = [];
        $i = 0;
        foreach ($this->fields as $field) {
            $fieldname = $field["field_name"];
            if (in_array($fieldname, $this->skipfields)) continue;
            $pos = sprintf("%02d", $i);
            $arfields[] = $this->_get_html_fields($fieldname, $pos);
            $i++;
        }
        $htmlfields = implode("\n", $arfields);

        $contenttpl = file_get_contents($this->pathtpl);
        $contenttpl = $this->_replace($contenttpl, [
            "%FIELDS%" => $strfields, "%HTML_FIELDS%" => $htmlfields, "%yyy%"=>$firstfield
        ]);

        $pathfile = $this->_replace($this->type);
        $pathfile = "{$this->pathmodule}/{$pathfile}";
        $this->_create_file($pathfile, $contenttpl);
    }

    private function _build_create_tpl(): void
    {
        //tags %FIELD_LABELS%, %FIELD_KEY_AND_VALUES%
        $trs = [];
        $kvs = [];
        $i = 0;
        foreach ($this->fields as $field) {
            $fieldname = $field["field_name"];
            if (in_array($fieldname, $this->skipfields)) continue;
            $pos = sprintf("%02d", $i);
            $trs[] = "\"f$pos\" => __(\"tr_{$fieldname}\"),";
            $kvs[] = "\"$fieldname\" => \"\",";
            $i++;
        }
        $trs = implode("\n", $trs);
        $kvs = implode("\n", $kvs);

        $contenttpl = file_get_contents($this->pathtpl);
        $contenttpl = $this->_replace($contenttpl, ["%FIELD_LABELS%" => $trs, "%FIELD_KEY_AND_VALUES%" => $kvs]);

        $pathfile = $this->_replace($this->type);
        $pathfile = "{$this->pathmodule}/{$pathfile}";
        $this->_create_file($pathfile, $contenttpl);
    }

    private function _build_update_tpl(): void
    {
        //tags %FIELD_LABELS%, %FIELD_KEY_AND_VALUES%
        $trs = [];
        $kvs = [];
        $i = 0;
        foreach ($this->fields as $field) {
            $fieldname = $field["field_name"];
            if (in_array($fieldname, $this->skipfields)) continue;
            $pos = sprintf("%02d", $i);
            $trs[] = "\"f$pos\" => __(\"tr_{$fieldname}\"),";
            $kvs[] = "\"$fieldname\" => \$result[\"{$fieldname}\"],";
            $i++;
        }
        $trs = implode("\n", $trs);
        $kvs = implode("\n", $kvs);

        $contenttpl = file_get_contents($this->pathtpl);
        $contenttpl = $this->_replace($contenttpl, ["%FIELD_LABELS%" => $trs, "%FIELD_KEY_AND_VALUES%" => $kvs]);

        $pathfile = $this->_replace($this->type);
        $pathfile = "{$this->pathmodule}/{$pathfile}";
        $this->_create_file($pathfile, $contenttpl);
    }

    private function _build_info_tpl(): void
    {
        //tags %FIELD_KEY_AND_VALUES%
        $kvs = [];
        foreach ($this->fields as $field) {
            $fieldname = $field["field_name"];
            if (in_array($fieldname, $this->skipfields)) continue;
            $kvs[] = "<li><b><?=__(\"tr_{$fieldname}\")?>:</b>&ensp;<span><?=\${$this->aliases["lowered"]}[\"{$fieldname}\"] ?? \"\"?></span></li>";
        }
        $kvs = implode("\n", $kvs);
        $contenttpl = file_get_contents($this->pathtpl);
        $contenttpl = $this->_replace($contenttpl, ["%FIELD_KEY_AND_VALUES%" => $kvs]);

        $pathfile = $this->_replace($this->type);
        $pathfile = "{$this->pathmodule}/{$pathfile}";
        $this->_create_file($pathfile, $contenttpl);
    }

    private function _build_css(): void
    {
        $contenttpl = file_get_contents($this->pathtpl);
        $contenttpl = $this->_replace($contenttpl);

        $pathfile = $this->_replace($this->type);
        $pathfile = "{$this->pathmodule}/{$pathfile}";
        $this->_create_file($pathfile, $contenttpl);
    }

    private function _build_index_tpl(): void
    {
        $contenttpl = file_get_contents($this->pathtpl);
        $contenttpl = $this->_replace($contenttpl);

        $pathfile = $this->_replace($this->type);
        $pathfile = "{$this->pathmodule}/{$pathfile}";
        $this->_create_file($pathfile, $contenttpl);
    }

    public function build(): void
    {
        switch ($this->type) {
            case self::TYPE_INSERT_JS:
                $this->_build_create_js();
            break;
            case self::TYPE_UPDATE_JS:
                $this->_build_update_js();
            break;
            case self::TYPE_INSERT_TPL:
                $this->_build_create_tpl();
            break;
            case self::TYPE_UPDATE_TPL:
                $this->_build_update_tpl();
            break;

            case self::TYPE_INFO_TPL:
                $this->_build_info_tpl();
            break;
            case self::TYPE_INDEX_TPL:
                $this->_build_index_tpl();
            break;
            case self::TYPE_CSS:
                $this->_build_css();
            break;
        }
    }
}