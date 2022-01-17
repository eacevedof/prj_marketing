<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Services\Console\Dev\Builder\ModuleService
 * @file ModuleService.php 1.0.0
 * @date 31-10-2022 17:46 SPAIN
 * @observations
 */
namespace App\Services\Console\Dev\Builder;

use App\Enums\EntityType;

final class PhpBuilder
{
    private string $type;
    private string $pathtpl;
    private string $pathmodule;
    private array $aliases;
    private array $fields;

    public const TYPE_ENTITY = "Entity";
    public const TYPE_REPOSITORY = "Repository";
    public const TYPE_CONTROLLER = "Controller";
    public const TYPE_DELETE_SERVICE = "DeleteService";
    public const TYPE_INFO_SERVICE = "InfoService";
    public const TYPE_INSERT_SERVICE = "InsertService";
    public const TYPE_SEARCH_SERVICE = "SearchService";
    public const TYPE_UPDATE_SERVICE = "UpdateService";

    public function __construct(array $aliases, array $fields, string $pathtpl, string $pathmodule, string $type=self::TYPE_ENTITY)
    {
       $this->pathtpl = $pathtpl;
       $this->pathmodule = $pathmodule;
       $this->aliases = $aliases;
       $this->fields = $fields;
       $this->type = $type;
    }

    private function _get_field_details(string $field): array
    {
       $type = array_filter($this->fields, function ($item) use ($field) {
           return $item["field_name"] === $field;
       });
       $type = array_values($type);
       return $type[0] ?? [];
    }

    private function _get_type(string $field): string
    {
        $types = [
           "decimal"    => "EntityType::DECIMAL",
           "varchar"    => "EntityType::STRING",
           "int"        => "EntityType::INT",
           "tinyint"    => "EntityType::INT",
           "datetime"   => "EntityType::DATETIME",
           "date"       => "EntityType::DATE",
        ];

        $fielddet = $this->_get_field_details($field);
        $type = $fielddet["field_type"];
        return $types[$type] ?? "-error-";
    }

    private function _get_length(string $field): string
    {
        $fielddet = $this->_get_field_details($field);
        $length = $fielddet["field_length"] ?? "";
        if (!$length)
            $length = $fielddet["ntot"] ?? "";
        return $length;
    }

    private function _get_field_tpl(string $field): string
    {
        $tr = "tr_$field";
        $type = $this->_get_type($field);
        $length = $this->_get_length($field);
        if ($length) $length = "\"length\" => $length,";

        return "
        \"$field\" => [
            \"label\" => __(\"$tr\"),
            EntityType::REQUEST_KEY => \"$field\",
            \"config\" => [
                \"type\" => $type,
                $length
            ]
        ],
       ";
    }

    private function _get_rule_tpl(string $field): string
    {
        return "
            ->add_rule(\"$field\", \"$field\", function (\$data) {
                return trim(\$data[\"value\"]) ? false : __(\"Empty field is not allowed\");
            })
       ";
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

    private function _build_entity(): void
    {
        $skip = [
            "processflag", "insert_platform", "insert_user", "insert_date", "delete_platform", "delete_user"
            , "delete_date", "cru_csvnote", "is_erpsent", "is_enabled", "i", "update_platform", "update_user",
            "update_date"
        ];
        //tags %FIELDS%
        $arfields = ["["];
        foreach ($this->fields as $field) {
            $fieldname = $field["field_name"];
            if (in_array($fieldname, $skip)) continue;
            $arfields[] = $this->_get_field_tpl($fieldname);
        }
        $arfields[] = "];";
        $strfields = implode("", $arfields);

        $contenttpl = file_get_contents($this->pathtpl);

        $contenttpl = str_replace("%FIELDS%", $strfields, $contenttpl);
        $contenttpl = str_replace("Xxx", $this->aliases["uppercased"], $contenttpl);

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

    private function _build_controller(): void
    {
        $contenttpl = file_get_contents($this->pathtpl);
        $contenttpl = $this->_replace($contenttpl);
        $pathfile = "{$this->pathmodule}/{$this->aliases["uppercased-plural"]}{$this->type}.php";
        file_put_contents($pathfile, $contenttpl);
    }

    private function _build_service(): void
    {
        $contenttpl = file_get_contents($this->pathtpl);
        $contenttpl = $this->_replace($contenttpl);
        $pathfile = "{$this->pathmodule}/{$this->aliases["uppercased-plural"]}{$this->type}.php";
        file_put_contents($pathfile, $contenttpl);
    }

    private function _build_rules_service(): void
    {
        $skip = [
            "processflag", "insert_platform", "insert_user", "insert_date", "delete_platform", "delete_user"
            , "delete_date", "cru_csvnote", "is_erpsent", "is_enabled", "i", "update_platform", "update_user",
            "update_date"
        ];
        //tags: %FIELD_RULES%

        $arfields = [];
        foreach ($this->fields as $field) {
            $fieldname = $field["field_name"];
            if (in_array($fieldname, $skip)) continue;
            $arfields[] = $this->_get_rule_tpl($fieldname);
        }
        $rules = implode("", $arfields);

        $contenttpl = file_get_contents($this->pathtpl);
        $contenttpl = $this->_replace($contenttpl,
            [
                "%FIELD_RULES%" => $rules
            ]);
        $pathfile = "{$this->pathmodule}/{$this->aliases["uppercased-plural"]}{$this->type}.php";
        file_put_contents($pathfile, $contenttpl);
    }

    public function build(): void
    {
        switch ($this->type) {
            case self::TYPE_ENTITY:
                $this->_build_entity();
            break;
            case self::TYPE_REPOSITORY:
                $this->_build_repository();
            break;
            case self::TYPE_CONTROLLER:
                $this->_build_controller();
            break;
            case self::TYPE_INSERT_SERVICE:
            case self::TYPE_UPDATE_SERVICE:
                $this->_build_rules_service();
            break;
            case self::TYPE_DELETE_SERVICE:
            case self::TYPE_INFO_SERVICE:
            case self::TYPE_SEARCH_SERVICE:
                $this->_build_service();
            break;
        }
    }
}