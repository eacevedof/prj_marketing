<?php

namespace App\Shared\Infrastructure\Components\Datatable;

final class DatatableComponent
{
    private array $request;
    private array $fields;

    public function __construct(array $request)
    {
        $this->request = $request;
        $this->_loadFieldsPositionAndValues();
    }

    private function _getSanitizedString(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }
        return str_replace("'", "\\'", $value);
    }

    private function _loadFieldsPositionAndValues(): void
    {
        foreach ($this->request["columns"] as $i => $column) {
            if (!$name = ($column["data"] ?? "")) {
                continue;
            }
            $value = $column["search"]["value"] ?? "";
            $this->fields[$name] = ["position" => $i, "value" => $value];
        }
    }

    public function getSearchPayload(): array
    {
        if (!$this->request) {
            return [];
        }

        $search = [
            "global" => $this->_getSanitizedString($this->request["search"]["value"] ?? ""),
            "fields" => [],
            "all" => [],
            "order" => $this->_getOrderByConfig(),
            "limit" => $this->_getLimitConfig()
        ];

        foreach ($this->fields as $field => $data) {
            $search["all"][] = $field;
            if ($value = $data["value"]) {
                $search["fields"][$field] = $this->_getSanitizedString($value);
            }
        }
        return $search;
    }

    private function _getOrderByConfig(): array
    {
        $pos = (int) $this->request["order"][0]["column"] ?? 0;

        foreach ($this->fields as $fieldName => $data) {
            if ($data["position"] === $pos) {
                break;
            }
        }

        return [
            "field" => $fieldName,
            "dir"  => $this->request["order"][0]["dir"] ?? "ASC"
        ];
    }

    private function _getLimitConfig(): array
    {
        return [
            "from" => $this->request["start"] ?? 0,
            "length" => $this->request["length"] ?? 25
        ];
    }
}
