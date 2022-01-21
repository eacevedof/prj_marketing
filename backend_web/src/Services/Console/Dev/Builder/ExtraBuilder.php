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

    private function _get_translation(string $key): string
    {
        return "
        msgid \"key\"
        msgstr \"\"
        ";
    }

    private function _build_extra_md(): void
    {
        $skip = [
            "processflag", "insert_platform", "insert_user", "insert_date", "delete_platform", "delete_user"
            , "delete_date", "cru_csvnote", "is_erpsent", "is_enabled", "i", "update_platform", "update_user",
            "update_date"
        ];
        //tags %PO_KEYS%

        $pokeys = [];
        foreach ($this->fields as $field) {
            $fieldname = $field["field_name"];
            if (in_array($fieldname, $skip)) continue;
            $trkey = "tr_$field";
            $pokeys[] = $this->_get_translation($trkey);
        }
        $pokeys = implode("\n", $pokeys);

        $contenttpl = file_get_contents($this->pathtpl);
        $contenttpl = $this->_replace($contenttpl, [
            "%PO_KEYS%" => $pokeys, "%HTML_FIELDS%" => $htmlfields, "%yyy%"=>$firstfield
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