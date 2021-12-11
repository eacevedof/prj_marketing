<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Helpers\Views\DatatableHelper
 * @file AppHelper.php 1.0.0
 * @date 29-11-2018 19:00 SPAIN
 * @observations
 * @tags: #apify
 */
namespace App\Helpers\Views;

use App\Helpers\IHelper;
use App\Helpers\AppHelper;

final class DatatableHelper extends AppHelper implements IHelper
{
    private array $columns = [];
    private array $language;
    private int $perpage = 25;
    private array $actions = [];
    private string $colname = "";
    private array $searchopts = [];

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

    private function _get_json($data): string
    {
        return json_encode($data);
    }

    public function add_column(string $name): self
    {
        $this->colname = $name;
        $this->columns[$name] = [
            "css" => "",
            "is_virtual" => false,
            "is_visible" => true,
            "name" => $name,
            "path_schema" => $name,
            "is_orderable" => true,
            "is_searchable" => true,
            "label" => $name,
            "tooltip" => "",
            "input" => "",
        ];

        return $this;
    }
    
    public function is_visible(bool $isvisible=true): self
    {
        $this->columns[$this->colname]["is_visible"] = $isvisible;
        return $this;
    }

    public function is_orderable(bool $isordenable=false): self
    {
        $this->columns[$this->colname]["is_orderable"] = $isordenable;
        return $this;
    }

    public function is_virtual(bool $isvirtual=true): self
    {
        $this->columns[$this->colname]["is_virtual"] = $isvirtual;
        return $this;
    }

    public function is_searchable(bool $issearchable=false): self
    {
        $this->columns[$this->colname]["is_searchable"] = $issearchable;
        return $this;
    }

    public function add_label(string $label): self
    {
        $this->columns[$this->colname]["label"] = $label;
        return $this;
    }

    public function add_tooltip(string $tooltip): self
    {
        $this->columns[$this->colname]["tooltip"] = $tooltip;
        return $this;
    }

    public function add_type(string $type): self
    {
        $this->columns[$this->colname]["type"] = $type;
        return $this;
    }

    public function path_schema(string $path): self
    {
        $this->columns[$this->colname]["path_schema"] = $path;
        return $this;
    }

    public function set_colname(string $name): self
    {
        $this->colname = $name;
        return $this;
    }

    private function _get_attribs(array $coldata): string
    {
        $orderable = ($coldata["is_orderable"] ?? "");
        $orderable = ($isvirtual = $coldata["is_virtual"] ?? "") ? false: $orderable;

        $searchable = ($coldata["is_searchable"] ?? "");
        $searchable = $isvirtual ? false: $searchable;
        $type = ($coldata["type"] ?? "string");

        $attribs = [
            ($coldata["css"] ?? "") ? "class=\"{$coldata["css"]}\"": "",
            ($coldata["is_visible"] ?? "") ? "visible=\"{$coldata["is_visible"]}\"": "",
            ($coldata["name"] ?? "") ? "column=\"{$coldata["name"]}\"": "",
            ($coldata["path_schema"] ?? "") ? "path=\"{$coldata["path_schema"]}\"" : "",
            $orderable ? "orderable=\"$orderable\"" : "",
            $searchable ? "searchable=\"$searchable\"" : "",
            "type=\"$type\"",
        ];
        $attribs = trim(implode(" ", $attribs));
        return $attribs ? " $attribs": "";
    }

    private function _get_actions_attrs(): string
    {
        $attribs = ["approle=\"actions\""];
        foreach ($this->actions as $action) {
            $action = trim($action);
            $attribs[] = "$action=\"1\"";
        }
        return implode(" ", $attribs);
    }

    public function get_ths(): string
    {
        if(!$this->columns) return "";
        $ths = ["<th></th>"];
        foreach ($this->columns as $coldata) {
            $attribs = $this->_get_attribs($coldata);
            $label = htmlentities($coldata["label"]);
            $tooltip = "";
            if($coldata["tooltip"]) {
                $tooltip = htmlentities($coldata["tooltip"]);
                $tooltip = "<i data-tooltip=\"$tooltip\"></i>";
            }
            $th = "<th$attribs><span title=\"$label\">$label</span>$tooltip</th>";
            $ths[] = $th;
        }
        if($this->actions) {
            $actions = __("Actions");
            $actions = htmlentities($actions);
            $attrs = $this->_get_actions_attrs();
            $ths[] = "<th $attrs>$actions</th>";
        }
        return implode("\n", $ths);
    }

    public function get_tf(): string
    {
        if(!$this->columns) return "";
        $ths = ["<td></td>"];
        foreach ($this->columns as $coldata) {
            $data["css"] = $coldata["css"];
            $data["label"] = $coldata["label"];
            $data["tooltip"] = $coldata["tooltip"];

            $attribs = $this->_get_attribs($data);
            $label = htmlentities($data["label"]);
            $tooltip = "";
            if($data["tooltip"]) {
                $tooltip = htmlentities($data["tooltip"]);
                $tooltip = "<i data-tooltip=\"$tooltip\"></i>";
            }
            $th = "<th$attribs><span title=\"$label\">$label</span>$tooltip</th>";
            $ths[] = $th;
        }
        if($this->actions) {
            $actions = __("Actions");
            $actions = htmlentities($actions);
            $attrs = $this->_get_actions_attrs();
            $ths[] = "<th $attrs>$actions</th>";
        }
        return implode("\n", $ths);
    }

    public function get_search_tds(): string
    {
        $tds = ["<td></td>"];
        $i = 0;
        foreach ($this->columns as $colname => $coldata) {
            $i++;
            $title = __("search")." ".$coldata["label"];
            $input = "<input type=\"text\" placeholder=\"{$title}\" approle=\"column-search\" appcolidx=\"{$i}\" />";
            $options = $this->searchopts[$colname] ?? null;
            $select = $this->_get_select($options, $coldata, $i);
            if($select) $input = $select;
            $issearch = ($coldata["is_searchable"] && !$coldata["is_virtual"]);
            if(!$issearch) $input = "";
            $tds[] = "<td search=\"$colname\">$input</td>";
        }
        if($this->actions) {
            $tds[] = "<th></th>";
        }
        return implode("\n", $tds);
    }

    private function _get_select(?array $options, array $coldata, int $i): string
    {
        if($options === null) return "";

        $title = __("search")." ".$coldata["label"];
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

    public function show_perpage(): void
    {
        echo $this->_get_json($this->perpage);
    }

    public function add_search_opts(array $options): self
    {
        $this->searchopts[$this->colname] = $options;
        return $this;
    }

    public function add_action(string $action): self
    {
        $this->actions[] = $action;
        return $this;
    }

    public function show_actions(): void
    {
        echo $this->_get_json(array_unique($this->actions));
    }

    public function show_lanaguage(): void
    {
        echo $this->_get_json($this->language);
    }
}//DatatableHelper
