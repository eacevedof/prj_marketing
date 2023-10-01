<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Console\Application\Dev\Builder\ExtraBuilder
 * @file ExtraBuilder.php 1.0.0
 * @date 31-10-2022 17:46 SPAIN
 * @observations
 */

namespace App\Console\Application\Dev\Builder;

final class ExtraBuilder
{
    private string $type;
    private string $pathTpl;
    private string $pathModule;
    private array $aliases;
    private array $fields;
    private array $skipFields;

    public const TYPE_EXTRA_MD = "extra.md";

    public function __construct(
        array  $aliases,
        array  $fields,
        string $pathTpl,
        string $pathModule,
        string $type = self::TYPE_EXTRA_MD
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

    private function _getTranslationIdAndStr(string $key): string
    {
        return "
        msgid \"$key\"
        msgstr \"$key\"
        ";
    }

    private function _buildExtraDataInMd(): void
    {
        //tags %PO_KEYS%
        $pokeys = [];
        foreach ($this->fields as $field) {
            $fieldname = $field["field_name"];
            if (in_array($fieldname, $this->skipFields)) {
                continue;
            }
            $trkey = "tr_$fieldname";
            $pokeys[] = $this->_getTranslationIdAndStr($trkey);
        }
        $pokeys = implode("", $pokeys);
        $contenttpl = file_get_contents($this->pathTpl);
        $contenttpl = $this->_replace($contenttpl, [
            "%PO_KEYS%" => trim($pokeys)
        ]);
        $pathfile = "{$this->pathModule}/{$this->type}";
        file_put_contents($pathfile, $contenttpl);
    }

    public function build(): void
    {
        switch ($this->type) {
            case self::TYPE_EXTRA_MD:
                $this->_buildExtraDataInMd();
                break;
        }
    }
}
