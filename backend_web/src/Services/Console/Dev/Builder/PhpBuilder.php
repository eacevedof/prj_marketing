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

    public const TYPE_ENTITY = "entity";
    public const TYPE_REPOSITORY = "repository";

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
            $length = $fielddet["ntot"];
        return $length;
    }

    private function _get_field_tpl(string $field): string
    {
       $tr = "tr_$field";
       $type = $this->_get_type($field);
       $length = $this->_get_length($field);

       return "
        \"$field\" => [
            \"label\" => __(\"$tr\"),
            EntityType::REQUEST_KEY => \"$field\",
            \"config\" => [
                \"type\" => $type,
                \"length\" => $length,
            ]
        ],
       ";
    }

    private function _build_entity(): void
    {
        //tags %FIELDS%
        $contenttpl = file_get_contents($this->pathtpl);
        $arfields = ["["];
        foreach ($this->fields as $field)
            $arfields[] = $this->_get_field_tpl($field["field_name"]);
        $arfields[] = "];";
        $strfields = implode("", $arfields);
        $contenttpl = str_replace("%FIELDS%", $strfields, $contenttpl);
        $pathfile = "{$this->pathmodule}/{$this->aliases["uppercased"]}Entity.php";
        file_put_contents($pathfile, $contenttpl);
    }

    public function build(): void
    {
        switch ($this->type) {
            case self::TYPE_ENTITY:
                $this->_build_entity();
            break;
        }
    }

}