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
    private string $pathTpl;
    private string $pathModule;
    private array $aliases;
    private array $fields;
    private array $skipFields;

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

    public function __construct(
        array  $aliases,
        array  $fields,
        string $pathTpl,
        string $pathModule,
        string $type = self::TYPE_ENTITY
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
            "processflag", "insert_platform", "insert_user", "insert_date", "delete_platform", "delete_user",
            "delete_date", "cru_csvnote", "is_erpsent", "is_enabled", "i", "update_platform", "update_user",
            "update_date"
        ];
    }

    private function _getFieldDetails(string $field): array
    {
        $type = array_filter($this->fields, function ($item) use ($field) {
            return $item["field_name"] === $field;
        });
        $type = array_values($type);
        return $type[0] ?? [];
    }

    private function _getFieldType(string $field): string
    {
        $types = [
            "decimal"    => "EntityType::DECIMAL",
            "varchar"    => "EntityType::STRING",
            "int"        => "EntityType::INT",
            "tinyint"    => "EntityType::INT",
            "datetime"   => "EntityType::DATETIME",
            "date"       => "EntityType::DATE",
        ];

        $fielddet = $this->_getFieldDetails($field);
        $type = $fielddet["field_type"];
        return $types[$type] ?? "-error-";
    }

    private function _getFieldLength(string $field): string
    {
        $fielddet = $this->_getFieldDetails($field);
        $length = $fielddet["field_length"] ?? "";
        if (!$length) {
            $length = $fielddet["ntot"] ?? "";
        }
        return $length;
    }

    private function _getFieldTpl(string $field): string
    {
        $tr = "tr_$field";
        $type = $this->_getFieldType($field);
        $length = $this->_getFieldLength($field);
        if ($length) {
            $length = "\"length\" => $length,";
        }

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

    private function _getFieldRuleTpl(string $field): string
    {
        return "->add_rule(\"$field\", \"$field\", function (\$data) {
                return \$data[\"value\"] ? false : __(\"Empty field is not allowed\");
            })";
    }

    private function _getDtColumnTpl(string $field): string
    {
        return "->add_column(\"$field\")->add_label(__(\"tr_$field\"))->add_tooltip(__(\"tr_$field\"))";
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


    private function _buildEntity(): void
    {
        //tags %FIELDS%
        $arfields = ["["];
        foreach ($this->fields as $field) {
            $fieldname = $field["field_name"];
            if (in_array($fieldname, $this->skipFields)) {
                continue;
            }
            $arfields[] = $this->_getFieldTpl($fieldname);
        }
        $arfields[] = "];";
        $strfields = implode("", $arfields);

        $contenttpl = file_get_contents($this->pathTpl);
        $contenttpl = $this->_replace($contenttpl, ["%FIELDS%" => $strfields]);

        $pathfile = $this->_replace($this->type);
        $pathfile = "{$this->pathModule}/{$pathfile}";
        $this->_createFile($pathfile, $contenttpl);
    }

    private function _buildRepository(): void
    {
        $arfields = [];
        foreach ($this->fields as $field) {
            $fieldname = $field["field_name"];
            if (in_array($fieldname, $this->skipFields)) {
                continue;
            }
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
            if (in_array($fieldname, $skip)) {
                continue;
            }
            $arfields[] = "\"m.$fieldname\"";
        }
        $infofields = implode(",\n", $arfields);

        $contenttpl = file_get_contents($this->pathTpl);
        $contenttpl = $this->_replace(
            $contenttpl,
            [
                "%TABLE%" => $this->aliases["raw"],
                "%SEARCH_FIELDS%" => $searchfields,
                "%INFO_FIELDS%" => $infofields,
            ]
        );

        $pathfile = $this->_replace($this->type);
        $pathfile = "{$this->pathModule}/{$pathfile}";
        $this->_createFile($pathfile, $contenttpl);
    }

    private function _buildController(): void
    {
        $contenttpl = file_get_contents($this->pathTpl);
        $contenttpl = $this->_replace($contenttpl);
        $pathfile = $this->_replace($this->type);
        $pathfile = "{$this->pathModule}/{$pathfile}";
        $this->_createFile($pathfile, $contenttpl);
    }

    private function _buildSearchInsertUpdateService(): void
    {
        //tags: %FIELD_RULES%
        $arfields = [];
        $columns = [];
        foreach ($this->fields as $field) {
            $fieldname = $field["field_name"];
            if (in_array($fieldname, $this->skipFields)) {
                continue;
            }
            $arfields[] = $this->_getFieldRuleTpl($fieldname);
            $columns[] = $this->_getDtColumnTpl($fieldname);
        }
        $rules = implode("", $arfields);
        $dtcolumns = implode("\n", $columns);

        $contenttpl = file_get_contents($this->pathTpl);
        $contenttpl = $this->_replace(
            $contenttpl,
            [
                "%FIELD_RULES%" => $rules,
                "%DT_COLUMNS%"  => $dtcolumns
            ]
        );
        $pathfile = $this->_replace($this->type);
        $pathfile = "{$this->pathModule}/{$pathfile}";
        $this->_createFile($pathfile, $contenttpl);
    }

    private function _buildService(): void
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
            case self::TYPE_ENTITY:
                $this->_buildEntity();
                break;
            case self::TYPE_REPOSITORY:
                $this->_buildRepository();
                break;

            case self::TYPE_DELETE_CONTROLLER:
            case self::TYPE_INFO_CONTROLLER:
            case self::TYPE_INSERT_CONTROLLER:
            case self::TYPE_UPDATE_CONTROLLER:
            case self::TYPE_SEARCH_CONTROLLER:
                $this->_buildController();
                break;
            case self::TYPE_SEARCH_SERVICE:
            case self::TYPE_INSERT_SERVICE:
            case self::TYPE_UPDATE_SERVICE:
                $this->_buildSearchInsertUpdateService();
                break;
            case self::TYPE_DELETE_SERVICE:
            case self::TYPE_INFO_SERVICE:
                $this->_buildService();
                break;
        }//swithc (type)

    }//build
}
