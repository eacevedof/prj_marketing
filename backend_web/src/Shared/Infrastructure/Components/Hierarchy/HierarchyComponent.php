<?php

namespace App\Shared\Infrastructure\Components\Hierarchy;

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
        $childids = [];
        $this->_load_childs($id, $data, $childids);

        $childids = array_map(function ($childid) use ($data) {
            $found = array_filter($data, function ($d) use($childid) {
                return $d["id"] === $childid;
            });
            $found = array_values($found);
            return $found[0];
        }, $childids);

        $childids = array_values($childids);
        return $childids;
    }

    public function get_topparent(string $id, array $data): array
    {
        return $this->_get_parent($id, $data);
    }
}