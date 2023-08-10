<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Shared\Infrastructure\Helpers\Views\DatatableHelper
 * @file DatatableHelper.php 1.0.0
 * @date 29-11-2018 19:00 SPAIN
 * @observations
 * @tags: #apify
 */

namespace App\Shared\Infrastructure\Helpers\Views;

use App\Shared\Infrastructure\Helpers\{
    AppHelper,
    IHelper
};

final class DatatableHelper extends AppHelper implements IHelper
{
    private array $columns = [];
    private array $language;
    private int $perPage = 25;
    private array $actions = [];
    private string $colName = "";
    private array $searchOptions = [];

    public function __construct()
    {
        $this->language = [
            "processing" => __("Processing..."),
            "search" => __("Search&nbsp;:"),
            "lengthMenu" => __("Afficher _MENU_ &eacute;l&eacute;ments"),
            "info" => __("Affichage de l&eacute;lement _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments"),
            "infoEmpty" => __("Affichage de l&eacute;lement 0 &agrave; 0 sur 0 &eacute;l&eacute;ments"),
            "infoFiltered" => __("(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)"),
            "infoPostFix" => __(""),
            "loadingRecords" => __("Loading..."),
            "zeroRecords" => __("Aucun &eacute;l&eacute;ment &agrave; afficher"),
            "emptyTable" => __("Aucune donnée disponible dans le tableau"),
            "paginate" => [
                "first" => __("First"),
                "previous" => __("Previous"),
                "next" => __("Next"),
                "last" => __("Last"),
            ],
            "aria" => [
                "sortAscending" => __(": activer pour trier la colonne par ordre croissant"),
                "sortDescending" => __(": activer pour trier la colonne par ordre décroissant"),
            ]
        ];
    }

    private function _getJsonEncoded($data): string
    {
        return json_encode($data);
    }

    public function addColumn(string $colName): self
    {
        $this->colName = $colName;
        $this->columns[$colName] = [
            "css" => "",
            "is_virtual" => false,
            "is_visible" => true,
            "name" => $colName,
            "path_schema" => $colName,
            "is_orderable" => true,
            "is_searchable" => true,
            "label" => $colName,
            "tooltip" => "",
            "input" => "",
        ];

        return $this;
    }

    public function isVisible(bool $isVisible = true): self
    {
        $this->columns[$this->colName]["is_visible"] = $isVisible;
        return $this;
    }

    public function isOrderable(bool $isOrderable = false): self
    {
        $this->columns[$this->colName]["is_orderable"] = $isOrderable;
        return $this;
    }

    public function isVirtual(bool $isVirtual = true): self
    {
        $this->columns[$this->colName]["is_virtual"] = $isVirtual;
        return $this;
    }

    public function isSearchable(bool $isSearchable = false): self
    {
        $this->columns[$this->colName]["is_searchable"] = $isSearchable;
        return $this;
    }

    public function addLabel(string $label): self
    {
        $this->columns[$this->colName]["label"] = $label;
        return $this;
    }

    public function addTooltip(string $tooltip): self
    {
        $this->columns[$this->colName]["tooltip"] = $tooltip;
        return $this;
    }

    public function addType(string $type): self
    {
        $this->columns[$this->colName]["type"] = $type;
        return $this;
    }

    public function pathSchema(string $path): self
    {
        $this->columns[$this->colName]["path_schema"] = $path;
        return $this;
    }

    public function setColName(string $colName): self
    {
        $this->colName = $colName;
        return $this;
    }

    private function _getHtmlAttribsFromColumnData(array $colData): string
    {
        $orderable = ($colData["is_orderable"] ?? "");
        $orderable = ($isvirtual = $colData["is_virtual"] ?? "") ? false : $orderable;

        $searchable = ($colData["is_searchable"] ?? "");
        $searchable = $isvirtual ? false : $searchable;
        $type = ($colData["type"] ?? "string");

        $attribs = [
            ($colData["css"] ?? "") ? "class=\"{$colData["css"]}\"" : "",
            ($colData["is_visible"] ?? "") ? "visible=\"{$colData["is_visible"]}\"" : "",
            ($colData["name"] ?? "") ? "column=\"{$colData["name"]}\"" : "",
            ($colData["path_schema"] ?? "") ? "path=\"{$colData["path_schema"]}\"" : "",
            $orderable ? "orderable=\"$orderable\"" : "",
            $searchable ? "searchable=\"$searchable\"" : "",
            "type=\"$type\"",
        ];
        $attribs = trim(implode(" ", $attribs));
        return $attribs ? " $attribs" : "";
    }

    private function _getHtmlAttribsFromActions(): string
    {
        $attribs = ["approle=\"actions\""];
        $actions = array_unique($this->actions);
        foreach ($actions as $action) {
            $action = trim($action);
            $attribs[] = "$action=\"1\"";
        }
        return implode(" ", $attribs);
    }

    public function getHtmlThs(): string
    {
        if (!$this->columns) {
            return "";
        }
        $ths = ["<th></th>"];
        foreach ($this->columns as $columnConfig) {
            $attribs = $this->_getHtmlAttribsFromColumnData($columnConfig);
            $label = htmlentities($columnConfig["label"]);
            $tooltip = "";
            if ($columnConfig["tooltip"]) {
                $tooltip = htmlentities($columnConfig["tooltip"]);
                $tooltip = "<i data-tooltip=\"$tooltip\"></i>";
            }
            $th = "<th$attribs><span title=\"$label\">$label</span>$tooltip</th>";
            $ths[] = $th;
        }
        if ($this->actions) {
            $actions = __("Actions");
            $actions = htmlentities($actions);
            $attrs = $this->_getHtmlAttribsFromActions();
            $ths[] = "<th $attrs>$actions</th>";
        }
        return implode("\n", $ths);
    }

    public function getHtmlTdsForTableFoot(): string
    {
        if (!$this->columns) {
            return "";
        }
        $ths = ["<td></td>"];
        foreach ($this->columns as $columConfig) {
            $data["css"] = $columConfig["css"];
            $data["label"] = $columConfig["label"];
            $data["tooltip"] = $columConfig["tooltip"];

            $attribs = $this->_getHtmlAttribsFromColumnData($data);
            $label = htmlentities($data["label"]);
            $tooltip = "";
            if ($data["tooltip"]) {
                $tooltip = htmlentities($data["tooltip"]);
                $tooltip = "<i data-tooltip=\"$tooltip\"></i>";
            }
            $th = "<th$attribs><span title=\"$label\">$label</span>$tooltip</th>";
            $ths[] = $th;
        }
        if ($this->actions) {
            $actions = __("Actions");
            $actions = htmlentities($actions);
            $attrs = $this->_getHtmlAttribsFromActions();
            $ths[] = "<th $attrs>$actions</th>";
        }
        return implode("\n", $ths);
    }

    public function getSearchableTds(): string
    {
        $tds = ["<td></td>"];
        $i = 0;
        foreach ($this->columns as $column => $colConfig) {
            $i++;
            $title = __("search")." ".$colConfig["label"];
            $input = "<input type=\"text\" placeholder=\"{$title}\" approle=\"column-search\" appcolidx=\"{$i}\" />";
            $options = $this->searchOptions[$column] ?? null;
            $select = $this->_getHtmlSelect($options, $colConfig, $i);
            if ($select) {
                $input = $select;
            }
            $issearch = ($colConfig["is_searchable"] && !$colConfig["is_virtual"]);
            if (!$issearch) {
                $input = "";
            }
            $tds[] = "<td search=\"$column\">$input</td>";
        }
        if ($this->actions) {
            $tds[] = "<th></th>";
        }
        return implode("\n", $tds);
    }

    private function _getHtmlSelect(?array $options, array $colConfig, int $i): string
    {
        if ($options === null) {
            return "";
        }

        $title = __("search")." ".$colConfig["label"];
        $select = [
            "<select placeholder=\"{$title}\" approle=\"column-search\" appcolidx=\"{$i}\" />"
        ];
        foreach ($options as $key => $value) {
            $value = htmlentities($value);
            $select[] = "<option value=\"$key\">$value</option>";
        }
        $select[] = "</select>";
        return implode("\n", $select);
    }

    public function showPerPageInfo(): void
    {
        echo $this->_getJsonEncoded($this->perPage);
    }

    public function add_search_opts(array $options): self
    {
        $this->searchOptions[$this->colName] = $options;
        return $this;
    }

    public function addAction(string $action): self
    {
        $this->actions[] = $action;
        return $this;
    }

    public function showActions(): void
    {
        echo $this->_getJsonEncoded(array_unique($this->actions));
    }

    public function showLanguage(): void
    {
        echo $this->_getJsonEncoded($this->language);
    }
}//DatatableHelper
