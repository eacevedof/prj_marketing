<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Console\Application\Dev\Builder\PhpBuilder
 * @file PhpBuilder.php 1.0.0
 * @date 31-10-2022 17:46 SPAIN
 * @observations
 */
namespace App\Console\Application\Dev\Builder;

final class PhpBuilder
{
    private string $type;
    private string $pathtpl;
    private string $pathmodule;
    private array $aliases;
    private array $fields;
    private array $skipfields;

    public const TYPE_ENTITY = "Xxxs-domain/XxxEntity.php";
    public const TYPE_REPOSITORY = "Xxxs-domain/XxxRepository.php";

    public const TYPE_DELETE_CONTROLLER = "Xxxs-controllers/XxxsDeleteController.php";
    public const TYPE_INFO_CONTROLLER   = "Xxxs-controllers/XxxsInfoController.php";
    public const TYPE_INSERT_CONTROLLER = "Xxxs-controllers/XxxsInsertController.php";
    public const TYPE_SEARCH_CONTROLLER = "Xxxs-controllers/XxxsSearchController.php";
    public const TYPE_UPDATE_CONTROLLER = "Xxxs-controllers/XxxsUpdateController.php";

    public const TYPE_DELETE_SERVICE    = "Xxxs-services/XxxsDeleteService.php";
    public const TYPE_INFO_SERVICE      = "Xxxs-services/XxxsInfoService.php";
    public const TYPE_INSERT_SERVICE    = "Xxxs-services/XxxsInsertService.php";
    public const TYPE_SEARCH_SERVICE    = "Xxxs-services/XxxsSearchService.php";
    public const TYPE_UPDATE_SERVICE    = "Xxxs-services/XxxsUpdateService.php";

    public function __construct(array $aliases, array $fields, string $pathtpl, string $pathmodule, string $type=self::TYPE_ENTITY)
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
            "processflag", "insert_platform", "insert_user", "insert_date", "delete_platform", "delete_user",
            "delete_date", "cru_csvnote", "is_erpsent", "is_enabled", "i", "update_platform", "update_user",
            "update_date"
        ];
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
        return "->add_rule(\"$field\", \"$field\", function (\$data) {
                return trim(\$data[\"value\"]) ? false : __(\"Empty field is not allowed\");
            })";
    }

    private function _get_dtcolumn_tpl(string $field): string
    {
        return "->add_column(\"$field\")->add_label(__(\"tr_$field\"))->add_tooltip(__(\"tr_$field\"))";
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


    private function _build_entity(): void
    {
        //tags %FIELDS%
        $arfields = ["["];
        foreach ($this->fields as $field) {
            $fieldname = $field["field_name"];
            if (in_array($fieldname, $this->skipfields)) continue;
            $arfields[] = $this->_get_field_tpl($fieldname);
        }
        $arfields[] = "];";
        $strfields = implode("", $arfields);

        $contenttpl = file_get_contents($this->pathtpl);
        $contenttpl = $this->_replace($contenttpl, ["%FIELDS%" => $strfields]);

        $pathfile = $this->_replace($this->type);
        $pathfile = "{$this->pathmodule}/{$pathfile}";
        $this->_create_file($pathfile, $contenttpl);
    }

    private function _build_repository(): void
    {
        $arfields = [];
        foreach ($this->fields as $field) {
            $fieldname = $field["field_name"];
            if (in_array($fieldname, $this->skipfields)) continue;
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

        $pathfile = $this->_replace($this->type);
        $pathfile = "{$this->pathmodule}/{$pathfile}";
        $this->_create_file($pathfile, $contenttpl);
    }

    private function _build_controller(): void
    {
        $contenttpl = file_get_contents($this->pathtpl);
        $contenttpl = $this->_replace($contenttpl);
        $pathfile = $this->_replace($this->type);
        $pathfile = "{$this->pathmodule}/{$pathfile}";
        $this->_create_file($pathfile, $contenttpl);
    }

    private function _build_search_insert_update_service(): void
    {
        //tags: %FIELD_RULES%
        $arfields = [];
        $columns = [];
        foreach ($this->fields as $field) {
            $fieldname = $field["field_name"];
            if (in_array($fieldname, $this->skipfields)) continue;
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
        $pathfile = $this->_replace($this->type);
        $pathfile = "{$this->pathmodule}/{$pathfile}";
        $this->_create_file($pathfile, $contenttpl);
    }

    private function _build_service(): void
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
            case self::TYPE_ENTITY:
                $this->_build_entity();
            break;
            case self::TYPE_REPOSITORY:
                $this->_build_repository();
            break;

            case self::TYPE_DELETE_CONTROLLER:
            case self::TYPE_INFO_CONTROLLER:
            case self::TYPE_INSERT_CONTROLLER:
            case self::TYPE_UPDATE_CONTROLLER:
            case self::TYPE_SEARCH_CONTROLLER:
                $this->_build_controller();
            break;
            case self::TYPE_SEARCH_SERVICE:
            case self::TYPE_INSERT_SERVICE:
            case self::TYPE_UPDATE_SERVICE:
                $this->_build_search_insert_update_service();
            break;
            case self::TYPE_DELETE_SERVICE:
            case self::TYPE_INFO_SERVICE:
                $this->_build_service();
            break;
        }//swithc (type)

    }//build
}