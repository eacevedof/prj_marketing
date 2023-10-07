<?php

namespace App\Open\Business\Application;

use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Components\Date\UtcComponent;
use App\Restrict\BusinessData\Domain\BusinessDataRepository;
use App\Shared\Infrastructure\Helpers\RoutesHelper as Routes;
use App\Restrict\BusinessAttributes\Domain\BusinessAttributeRepository;
use App\Shared\Infrastructure\Factories\{ComponentFactory as CF, RepositoryFactory as RF};

final class BusinessSpacePageService extends AppService
{
    public function getPageByBusinessSlug(string $businessSlug): array
    {
        $promotions = $this->_getPromotions($businessSlug);
        $spacePage = RF::getInstanceOf(BusinessAttributeRepository::class)->getSpacePageByBusinessSlug($businessSlug);

        $printKeys = [
            "space_about", "space_plan", "space_location", "space_contact"
        ];
        $map = [];
        array_map(function ($attr) use (&$map, $printKeys) {
            $key = $attr["attr_key"];
            if (in_array($key, $printKeys)) {
                $map[$key] = $attr["attr_value"];
            }
        }, $spacePage);
        $spacePage = $map;
        $businessData = RF::getInstanceOf(BusinessDataRepository::class)->getBusinessDataByBusinessDataSlug($businessSlug, ["business_name", "url_business"]);

        $businessName = htmlentities(trim($businessData["business_name"] ?? ""));
        $urlBusiness = htmlentities(trim($businessData["url_business"] ?? ""));

        return [
            ["h2" => __("About {0}", ...$this->_getUrlBusinessLink($businessName, $urlBusiness))],
            ...$this->_getHtmlParagraphs($this->_getCleanedHtml($spacePage["space_about"] ?? "")),

            ["h2" => __("Our points programs")],
            ...explode("\n", $this->_getCleanedHtml($spacePage["space_plan"] ?? "")),

            ["h2" => __("Current promotions")],
            ["ul" => $promotions ?: [__("Currently there are no promotions.")]],

            ["h2" => __("Where are we located?")],
            ...$this->_getHtmlParagraphs($this->_getCleanedHtml($spacePage["space_location"] ?? "")),

            ["h2" => __("Contact us")],
            ...$this->_getHtmlParagraphs($this->_getCleanedHtml($spacePage["space_contact"] ?? "")),

            ["p" => "<br/><br/><br/>"],
        ];
    }

    private function _getSanitizedHtml(string $html): string
    {
        return strip_tags($html, "<a>");
    }

    private function _getAllLinks(string $html): array
    {
        $patten = "/<a[^<^>^\\]*>[^<^>]*<\/a>/imsx";
        preg_match_all($patten, $html, $result);
        return $result[0];
    }

    private function _getLinkReplaces(array $links): array
    {
        $result = [];
        $pattern = "/rel=\".*\"/i";
        foreach ($links as $link) {
            $linkr = preg_replace($pattern, "rel=\"nofollow noopener noreferer\"", $link);
            if (!strstr($linkr, " rel=\"")) {
                $linkr = str_replace("<a ", "<a rel=\"nofollow noopener noreferer\" ", $linkr);
            }
            $result[$link] = $linkr;
        }
        return $result;
    }

    private function _getCleanedHtml(string $html): string
    {
        $html = $this->_getSanitizedHtml($html);
        $links = $this->_getAllLinks($html);
        $linksReplaced = $this->_getLinkReplaces($links);
        $html = str_replace(array_keys($linksReplaced), array_values($linksReplaced), $html);
        return $html;
    }

    private function _getPromotions(string $businessSlug): array
    {
        $tz = CF::getInstanceOf(UtcComponent::class)->getTimezoneByIp($_SERVER["REMOTE_ADDR"]);
        $promotions = RF::getInstanceOf(BusinessDataRepository::class)->getTop5LastRunningPromotionsByBusinessSlug($businessSlug, $tz);
        $promotions = array_map(function (array $row) use ($businessSlug, $tz) {
            $description = htmlentities($row["description"]);
            $url = Routes::getUrlByRouteName("subscription.create", ["businessSlug" => $businessSlug, "promotionSlug" => $row["slug"]]);
            return "<a href=\"$url\">{$description}</a> <br/><small>Desde: {$row["date_from"]} / Hasta: {$row["date_to"]} $tz</small>";
        }, $promotions);
        return $promotions;
    }

    private function _getUrlBusinessLink(string $businessName, string $businessUrl): array
    {
        if (!$businessUrl) return [$businessName];
        return ["<a href=\"$businessUrl\" rel=\"nofollow noopener noreferer\" target=\"__blank\">{$businessName}</a>"];
    }

    private function _getHtmlParagraphs(string $html): array
    {
        $lines = explode("\n", $html);
        $ps = [];
        foreach ($lines as $line) {
            $ps[] = ["p" => $line];
        }
        return $ps;
    }

}
