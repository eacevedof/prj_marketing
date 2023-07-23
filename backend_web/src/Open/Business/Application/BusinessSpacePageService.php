<?php
namespace App\Open\Business\Application;

use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\ComponentFactory as CF;
use App\Shared\Infrastructure\Components\Date\UtcComponent;
use App\Shared\Infrastructure\Helpers\RoutesHelper as Routes;
use App\Restrict\BusinessData\Domain\BusinessDataRepository;
use App\Restrict\BusinessAttributes\Domain\BusinessAttributeRepository;

final class BusinessSpacePageService extends AppService
{
    private function _get_sanitized_html(string $html): string
    {
        return strip_tags($html, "<a>");
    }

    private function _get_all_links(string $html): array
    {
        $patten = "/<a[^<^>^\\]*>[^<^>]*<\/a>/imsx";
        preg_match_all($patten, $html, $result);
        return $result[0];
    }

    private function _get_link_replaces(array $links): array
    {
        $result = [];
        $pattern = "/rel=\".*\"/i";
        foreach ($links as $link)
        {
            $linkr = preg_replace($pattern, "rel=\"nofollow noopener noreferer\"", $link);
            if (!strstr($linkr, " rel=\"")) {
                $linkr = str_replace("<a ","<a rel=\"nofollow noopener noreferer\" ", $linkr);
            }
            $result[$link] = $linkr;
        }
        return $result;
    }

    private function _get_cleaned(string $html): string
    {
        $html = $this->_get_sanitized_html($html);
        $links = $this->_get_all_links($html);
        $linksr = $this->_get_link_replaces($links);
        $html = str_replace(array_keys($linksr),array_values($linksr),$html);
        return $html;
    }

    private function _get_promotions(string $businessslug): array
    {
        $tz = CF::get(UtcComponent::class)->get_timezone_by_ip($_SERVER["REMOTE_ADDR"]);
        $promotions = RF::get(BusinessDataRepository::class)->get_top5_last_running_promotions_by_slug($businessslug, $tz);
        $promotions = array_map(function (array $row) use ($businessslug, $tz) {
            $description = htmlentities($row["description"]);
            $url = Routes::url("subscription.create", ["businessslug"=>$businessslug, "promotionslug"=>$row["slug"]]);
            return "<a href=\"$url\">{$description}</a> <small>Desde: {$row["date_from"]} / Hasta: {$row["date_to"]} $tz</small>";
        }, $promotions);
        return $promotions;
    }

    private function _get_ps(string $html): array
    {
        $lines = explode("\n", $html);
        $ps = [];
        foreach ($lines as $line)
        {
            $ps[] = ["p"=>$line];
        }
        return $ps;
    }

    public function get_page_by_businessslug(string $businessslug): array
    {
        $promotions = $this->_get_promotions($businessslug);
        $spacepage = RF::get(BusinessAttributeRepository::class)->get_spacepage_by_businessslug($businessslug);

        $printkeys = [
            "space_about", "space_plan", "space_location", "space_contact"
        ];
        $map = [];
        array_map(function ($attr) use (&$map, $printkeys){
            $key = $attr["attr_key"];
            if (in_array($key, $printkeys))
                $map[$key] = $attr["attr_value"];
        }, $spacepage);
        $spacepage = $map;
        $businessname = RF::get(BusinessDataRepository::class)->get_by_slug($businessslug, ["business_name"])["business_name"] ?? "";


        return [
            ["h2" => __("About {0}", $businessname)],
            ...$this->_get_ps($this->_get_cleaned($spacepage["space_about"] ?? "")),

            ["h2" => __("Our points programs")],
            ...explode("\n", $this->_get_cleaned($spacepage["space_plan"] ?? "")),

            ["h2" => __("Current promotions")],
            ["ul" => $promotions],

            ["h2" => __("Where are we located?")],
            ...$this->_get_ps($this->_get_cleaned($spacepage["space_location"] ?? "")),

            ["h2" => __("Contact us")],
            ...$this->_get_ps($this->_get_cleaned($spacepage["space_contact"] ?? "")),

            ["p" => "<br/><br/><br/>"],
        ];
    }
}
