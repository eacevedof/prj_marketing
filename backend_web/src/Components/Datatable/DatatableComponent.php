<?php

namespace App\Components\Datatable;

final class Datatable
{
    private array $request;
    private array $fields;

    public function __construct(array $request)
    {
        $this->request = $request;
        $this->_load_fields();
    }

    private function _load_fields(): void
    {
        foreach ($this->request["columns"] as $i => $column) {
            $name = $column["data"] ?? $i;
            $value = $column["search"] ?? "";
            $this->fields[$name] = ["position"=>$i, "value"=>$value];
        }
    }

    public function get_search(){return $this->request["search"]["value"] ?? "";}

    public function get_order(): array
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

    public function get_limit(): array
    {
        return [
            "from" => $this->request["start"] ?? 0,
            "length" => $this->request["length"] ?? 25
        ];
    }
}