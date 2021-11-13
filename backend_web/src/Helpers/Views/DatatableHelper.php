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
use \Exception;

final class DatatableHelper extends AppHelper implements IHelper
{
    private array $columns = [];
    private array $tranlations = [];
    private int $perpage = 25;
    private array $buttons = [];
    private string $colname = "";

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
            "path-schema" => $name,
            "is_orderable" => true,
            "is_searchable" => true,
            "label" => $name,
            "tooltip" => "",
        ];

        return $this;
    }
    
    public function is_visible(bool $isvisible=false): self
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

    public function schema_path(string $path): self
    {
        $this->columns[$this->colname]["path-schema"] = $path;
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

        $attribs = [
            ($coldata["css"] ?? "") ? "class=\"{$coldata["css"]}\"": "",
            ($coldata["is_visible"] ?? "") ? "visible=\"{$coldata["is_visible"]}\"": "",
            ($coldata["name"] ?? "") ? "column=\"{$coldata["name"]}\"": "",
            ($coldata["path-schema"] ?? "") ? "path=\"{$coldata["path-schema"]}\"" : "",
            $orderable ? "orderable=\"$orderable\"" : "",
            $searchable ? "searchable=\"$searchable\"" : "",
        ];
        $attribs = trim(implode(" ", $attribs));
        return $attribs ? " $attribs": "";
    }

    public function get_ths(): string
    {
        if(!$this->columns) return "";
        $ths = [];
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
        return implode("\n", $ths);
    }

    public function get_tf(): string
    {
        if(!$this->columns) return "";
        $ths = [];
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
        return implode("\n", $ths);
    }

    public function show_perpage(): void
    {
        echo $this->_get_json($this->perpage);
    }

}//DatatableHelper
