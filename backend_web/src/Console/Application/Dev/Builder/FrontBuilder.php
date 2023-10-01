<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Console\Application\Dev\Builder\FrontBuilder
 * @file FrontBuilder.php 1.0.0
 * @date 31-10-2022 17:46 SPAIN
 * @observations
 */

namespace App\Console\Application\Dev\Builder;

final class FrontBuilder
{
    private string $type;
    private string $pathTpl;
    private string $pathModule;
    private array $aliases;
    private array $fields;
    private array $skipFields;

    public const TYPE_INSERT_JS     = "Xxxs-front/insert.js";
    public const TYPE_INSERT_TPL    = "Xxxs-front/insert.tpl";
    public const TYPE_UPDATE_JS     = "Xxxs-front/update.js";
    public const TYPE_UPDATE_TPL    = "Xxxs-front/update.tpl";
    public const TYPE_INFO_TPL      = "Xxxs-front/info.tpl";
    public const TYPE_INDEX_TPL     = "Xxxs-front/index.tpl";
    public const TYPE_CSS           = "Xxxs-front/xxxs.css";

    public function __construct(
        array  $aliases,
        array  $fields,
        string $pathTpl,
        string $pathModule,
        string $type = self::TYPE_INSERT_JS
    ) {
        $this->pathTpl = $pathTpl;
        $this->pathModule = $pathModule;
        $this->aliases = $aliases;
        $this->fields = $fields;
        $this->type = $type;
        $this->_loadSkipFields();
        ;
    }

    private function _loadSkipFields(): void
    {
        $this->skipFields = [
            "processflag", "insert_platform", "insert_user", "insert_date", "delete_platform", "delete_user"
            , "delete_date", "cru_csvnote", "is_erpsent", "is_enabled", "i", "update_platform", "update_user",
            "update_date"
        ];
    }

    private function _replace(string $content, array $replaces = []): string
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

    private function _createFile(string $pathfile, string $content): void
    {
        //esta creando un modulo por fuera de files por eso peta
        $dirname = dirname($pathfile);
        if (!is_dir($dirname)) { //$r = mkdir($dirname,0777,1);
            exec("mkdir -p $dirname");
        }
        sleep(1);
        $r = file_put_contents($pathfile, $content);
        if ($r === false) {
            exit("ERROR on creation:\n\t$dirname\n\t$pathfile\n");
        }
    }

    private function _getFieldDetails(string $field): array
    {
        $type = array_filter($this->fields, function ($item) use ($field) {
            return $item["field_name"] === $field;
        });
        $type = array_values($type);
        return $type[0] ?? [];
    }

    private function _get_length(string $field): string
    {
        $fielddet = $this->_getFieldDetails($field);
        $length = $fielddet["field_length"] ?? "";
        if (!$length) {
            $length = $fielddet["ntot"] ?? "";
        }
        return $length;
    }

    private function _getJsProperties(string $field): string
    {
        return "_{$field}: {type: String, state:true},";
    }

    private function _getHtmlFields(string $field, string $pos): string
    {
        $len = $this->_get_length($field);
        return "<div class=\"form-group\">
                    <label for=\"$field\">\${this.texts.f{$pos}}</label>
                    <div id=\"field-{$field}\">
                        <input type=\"text\" id=\"{$field}\" .value=\${this._{$field}} class=\"form-control\" maxlength=\"$len\">
                    </div>
                </div>";
    }

    private function _buildInsertJsFile(): void
    {
        //tags %FIELDS%
        $arfields = [];
        foreach ($this->fields as $i => $field) {
            $fieldname = $field["field_name"];
            if (in_array($fieldname, $this->skipFields + ["id","uuid"])) {
                continue;
            }
            $arfields[$i] = $this->_getJsProperties($fieldname);
        }
        $strfields = implode("\n", $arfields);
        $firstfield = $this->fields[array_keys($arfields)[0]]["field_name"];

        $arfields = [];
        $i = 0;
        foreach ($this->fields as $field) {
            $fieldname = $field["field_name"];
            if (in_array($fieldname, $this->skipFields)) {
                continue;
            }
            $pos = sprintf("%02d", $i);
            $arfields[] = $this->_getHtmlFields($fieldname, $pos);
            $i++;
        }
        $htmlfields = implode("\n", $arfields);

        $contenttpl = file_get_contents($this->pathTpl);
        $contenttpl = $this->_replace($contenttpl, [
            "%FIELDS%" => $strfields, "%HTML_FIELDS%" => $htmlfields, "%yyy%" => $firstfield
        ]);

        $pathfile = $this->_replace($this->type);
        $pathfile = "{$this->pathModule}/{$pathfile}";
        $this->_createFile($pathfile, $contenttpl);
    }

    private function _buildUpdateJsFile(): void
    {
        //tags %FIELDS%
        $arfields = [];
        foreach ($this->fields as $i => $field) {
            $fieldname = $field["field_name"];
            if (in_array($fieldname, $this->skipFields)) {
                continue;
            }
            $arfields[$i] = $this->_getJsProperties($fieldname);
        }
        $strfields = implode("\n", $arfields);
        $firstfield = $this->fields[array_keys($arfields)[0]]["field_name"];

        $arfields = [];
        $i = 0;
        foreach ($this->fields as $field) {
            $fieldname = $field["field_name"];
            if (in_array($fieldname, $this->skipFields)) {
                continue;
            }
            $pos = sprintf("%02d", $i);
            $arfields[] = $this->_getHtmlFields($fieldname, $pos);
            $i++;
        }
        $htmlfields = implode("\n", $arfields);

        $contenttpl = file_get_contents($this->pathTpl);
        $contenttpl = $this->_replace($contenttpl, [
            "%FIELDS%" => $strfields, "%HTML_FIELDS%" => $htmlfields, "%yyy%" => $firstfield
        ]);

        $pathfile = $this->_replace($this->type);
        $pathfile = "{$this->pathModule}/{$pathfile}";
        $this->_createFile($pathfile, $contenttpl);
    }

    private function _buildCreateTpl(): void
    {
        //tags %FIELD_LABELS%, %FIELD_KEY_AND_VALUES%
        $trs = [];
        $kvs = [];
        $i = 0;
        foreach ($this->fields as $field) {
            $fieldname = $field["field_name"];
            if (in_array($fieldname, $this->skipFields)) {
                continue;
            }
            $pos = sprintf("%02d", $i);
            $trs[] = "\"f$pos\" => __(\"tr_{$fieldname}\"),";
            $kvs[] = "\"$fieldname\" => \"\",";
            $i++;
        }
        $trs = implode("\n", $trs);
        $kvs = implode("\n", $kvs);

        $contenttpl = file_get_contents($this->pathTpl);
        $contenttpl = $this->_replace($contenttpl, ["%FIELD_LABELS%" => $trs, "%FIELD_KEY_AND_VALUES%" => $kvs]);

        $pathfile = $this->_replace($this->type);
        $pathfile = "{$this->pathModule}/{$pathfile}";
        $this->_createFile($pathfile, $contenttpl);
    }

    private function _buildUpdateTpl(): void
    {
        //tags %FIELD_LABELS%, %FIELD_KEY_AND_VALUES%
        $trs = [];
        $kvs = [];
        $i = 0;
        foreach ($this->fields as $field) {
            $fieldname = $field["field_name"];
            if (in_array($fieldname, $this->skipFields)) {
                continue;
            }
            $pos = sprintf("%02d", $i);
            $trs[] = "\"f$pos\" => __(\"tr_{$fieldname}\"),";
            $kvs[] = "\"$fieldname\" => \$result[\"{$fieldname}\"],";
            $i++;
        }
        $trs = implode("\n", $trs);
        $kvs = implode("\n", $kvs);

        $contenttpl = file_get_contents($this->pathTpl);
        $contenttpl = $this->_replace($contenttpl, ["%FIELD_LABELS%" => $trs, "%FIELD_KEY_AND_VALUES%" => $kvs]);

        $pathfile = $this->_replace($this->type);
        $pathfile = "{$this->pathModule}/{$pathfile}";
        $this->_createFile($pathfile, $contenttpl);
    }

    private function _buildInfoTpl(): void
    {
        //tags %FIELD_KEY_AND_VALUES%
        $kvs = [];
        foreach ($this->fields as $field) {
            $fieldname = $field["field_name"];
            if (in_array($fieldname, $this->skipFields)) {
                continue;
            }
            $kvs[] = "<li><b><?=__(\"tr_{$fieldname}\")?>:</b>&ensp;<span><?=\${$this->aliases["lowered"]}[\"{$fieldname}\"] ?? \"\"?></span></li>";
        }
        $kvs = implode("\n", $kvs);
        $contenttpl = file_get_contents($this->pathTpl);
        $contenttpl = $this->_replace($contenttpl, ["%FIELD_KEY_AND_VALUES%" => $kvs]);

        $pathfile = $this->_replace($this->type);
        $pathfile = "{$this->pathModule}/{$pathfile}";
        $this->_createFile($pathfile, $contenttpl);
    }

    private function _buildCssFile(): void
    {
        $contenttpl = file_get_contents($this->pathTpl);
        $contenttpl = $this->_replace($contenttpl);

        $pathfile = $this->_replace($this->type);
        $pathfile = "{$this->pathModule}/{$pathfile}";
        $this->_createFile($pathfile, $contenttpl);
    }

    private function _buildIndexTpl(): void
    {
        $contenttpl = file_get_contents($this->pathTpl);
        $contenttpl = $this->_replace($contenttpl);

        $pathfile = $this->_replace($this->type);
        $pathfile = "{$this->pathModule}/{$pathfile}";
        $this->_createFile($pathfile, $contenttpl);
    }

    public function build(): void
    {
        switch ($this->type) {
            case self::TYPE_INSERT_JS:
                $this->_buildInsertJsFile();
                break;
            case self::TYPE_UPDATE_JS:
                $this->_buildUpdateJsFile();
                break;
            case self::TYPE_INSERT_TPL:
                $this->_buildCreateTpl();
                break;
            case self::TYPE_UPDATE_TPL:
                $this->_buildUpdateTpl();
                break;

            case self::TYPE_INFO_TPL:
                $this->_buildInfoTpl();
                break;
            case self::TYPE_INDEX_TPL:
                $this->_buildIndexTpl();
                break;
            case self::TYPE_CSS:
                $this->_buildCssFile();
                break;
        }
    }
}
