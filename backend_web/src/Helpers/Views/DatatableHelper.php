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

    //"<th class=\"column-%s\" data-visible=\"%s\" data-name=\"%s\" data-data=\"%s\" data-orderable=\"%s\" data-searchable=\"%s\"><span label=\"%s\">%s</span>%s</th>\n",
    /*
     *          "<th class=\"column-%s\" data-visible=\"%s\" data-name=\"%s\" data-data=\"%s\" data-orderable=\"%s\" data-searchable=\"%s\"><span label=\"%s\">%s</span>%s</th>\n",
                                $column->id, # class="column-xxx"
                                empty($columnsToShow) || in_array($column->id, $columnsToShow) ? 'true' : 'false', # visible by default
                                $column->id, # data-name="xxxx"
                                $column->dataIdentifier, # data-data="xxx"
                                $column->virtual || $column->disableOrdering ? 'false' : 'true', # data-orderable="xxx"
                                $column->searchable ? 'true' : 'false', # data-searchable="xxx"
                                $column->label, # span label
                                $column->label, # label
                                $helpIcon         # help*/
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
        $orderable = $coldata["is_orderable"];
        $orderable = ($isvirtual = $coldata["is_virtual"]) ? false: $orderable;

        $searchable = $coldata["is_searchable"];
        $searchable = $isvirtual ? false: $searchable;

        $attribs = [
            "class=\"{$coldata["css"]}\"",
            "data-visible=\"{$coldata["is_visible"]}\"",
            "data-name=\"{$coldata["name"]}\"",
            "data-data=\"{$coldata["path-schema"]}\"",
            "data-orderable=\"$orderable\"",
            "data-searchable=\"$searchable\"",
        ];
        return implode(" ", $attribs);
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
            $th = "<th $attribs><span label=\"$label\">$label</span>$tooltip</th>";
            $ths[] = $th;
        }
        return implode("\n", $ths);
    }

}//DatatableHelper
