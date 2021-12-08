<?php

namespace App\Components\Hierarchy;

final class HierarchyComponent
{
    private function _get_parent($id, $ar)
    {
        $newar = array_filter($ar, function($item) use ($id){
            return $item["id"] === $id;
        });
        $newar = array_values($newar);
        //print_r($newar);
        $idparent = $newar[0]["id_parent"];
        if(!$idparent) return $newar[0];

        //obtener el item padre directo
        $newar = array_filter($ar, function($item) use ($idparent){
            return $item["id"] === $idparent;
        });
        $newar = array_values($newar);
        //print_r($newar);
        $idparent = $newar[0]["id_parent"];
        if(!$idparent) return $newar[0];

        return $this->_get_parent($idparent, $ar);
    }

    private function _load_childs($id, $ar, &$ac=[])
    {
        $childs = array_filter($ar, function ($item) use ($id){
            return $item["id_parent"] === $id;
        });

        if(!$childs) return;

        $ids = array_map(function ($item){
            return $item["id"];
        }, $childs);

        $ac = array_merge($ac, $ids);
        foreach ($ids as $id)
            $this->_load_childs($id, $ar, $ac);
    }

    public function get_childs(string $id, array $data): array
    {
        $childs = [];
        $this->_load_childs($id, $data, $childs);
        $childs = array_map(function ($child) use ($data) {
            $found = array_filter($data, function ($d) use($child) {
                return $d["id"] === $child["id"];
            });
            $found = array_values($found);
            return $found[0];
        }, $childs);

        $childs = array_values($childs);
        return $childs;
    }

    public function get_topparent(string $id, array $data): array
    {
        return $this->_get_parent($id, $data);
    }
}