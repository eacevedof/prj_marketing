<?php

namespace App\Shared\Infrastructure\Components\Hierarchy;

final class HierarchyComponent
{
    private function _getIdParentByIdChild(string $idChild, array $childrenAndParents): array|string
    {
        $parentsByChild = array_filter($childrenAndParents, function (array $item) use ($idChild) {
            return (string) $item["id"] === $idChild;
        });
        if (!$parentsByChild) {
            return [];
        }

        $parentsByChild = array_values($parentsByChild);
        //print_r($newar);
        $idParent = $parentsByChild[0]["id_parent"];
        if (!$idParent) {
            return $parentsByChild[0];
        }

        //obtener el item padre directo
        $parentsByChild = array_filter($childrenAndParents, function ($item) use ($idParent) {
            return $item["id"] === $idParent;
        });
        $parentsByChild = array_values($parentsByChild);
        //print_r($newar);
        $idParent = $parentsByChild[0]["id_parent"];
        if (!$idParent) {
            return $parentsByChild[0];
        }

        return $this->_getIdParentByIdChild($idParent, $childrenAndParents);
    }

    private function _addChildrenToAccumulator(
        string $idParent,
        array $childrenAndParents,
        array &$accumulator = []
    ): void {
        $children = array_filter($childrenAndParents, function ($item) use ($idParent) {
            return (string) $item["id_parent"] === $idParent;
        });

        if (!$children) {
            return;
        }

        $ids = array_map(function ($item) {
            return $item["id"];
        }, $children);

        $accumulator = array_merge($accumulator, $ids);
        foreach ($ids as $idParent) {
            $this->_addChildrenToAccumulator($idParent, $childrenAndParents, $accumulator);
        }
    }

    public function getChildrenIds(string $idParent, array $childrenAndParents): array
    {
        $childrenIds = [];
        $this->_addChildrenToAccumulator($idParent, $childrenAndParents, $childrenIds);

        $childrenIds = array_map(function (string $idChild) use ($childrenAndParents) {
            $found = array_filter($childrenAndParents, function ($arItem) use ($idChild) {
                return (string) $arItem["id"] === $idChild;
            });
            $found = array_values($found);
            return $found[0];
        }, $childrenIds);

        $childrenIds = array_values($childrenIds);
        return $childrenIds;
    }

    public function getTopParent(string $idChild, array $childrenAndParents): array
    {
        return $this->_getIdParentByIdChild($idChild, $childrenAndParents);
    }
}
