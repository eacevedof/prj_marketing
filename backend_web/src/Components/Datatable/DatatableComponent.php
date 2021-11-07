<?php

namespace App\Components\Datatable;

final class DatatableComponent
{
    private array $request;
    private array $fields;

    public function __construct(array $request)
    {
        $this->request = $request;
        $this->_load_fields();
    }

    protected function _get_sanitized(?string $value)
    {
        if($value===null) return null;
        return str_replace("'","\\'", $value);
    }

    private function _load_fields(): void
    {
        foreach ($this->request["columns"] as $i => $column) {
            $name = $column["data"] ?? $i;
            $value = $column["search"]["value"] ?? "";
            $this->fields[$name] = ["position"=>$i, "value"=>$value];
        }
    }

    public function get_search(): array
    {
        if(!$this->request) return [];

        $search = [
            "global" => $this->_get_sanitized($this->request["search"]["value"] ?? ""),
            "fields" => [],
            "all" => [],
            "order" => $this->_get_order(),
            "limit" => $this->_get_limit()
        ];

        foreach ($this->fields as $field => $data) {
            $search["all"][] = $field;
            if ($value = $data["value"])
                $search["fields"][$field] = $this->_get_sanitized($value);
        }
        return $search;
    }

    private function _get_order(): array
    {
        $pos = (int)$this->request["order"][0]["column"] ?? 0;

        foreach ($this->fields as $fieldname => $data)
            if($data["position"] === $pos)
                break;

        return [
            "field" => $fieldname,
            "dir"  => $this->request["order"][0]["dir"] ?? "ASC"
        ];
    }

    private function _get_limit(): array
    {
        return [
            "from" => $this->request["start"] ?? 0,
            "length" => $this->request["length"] ?? 25
        ];
    }
}