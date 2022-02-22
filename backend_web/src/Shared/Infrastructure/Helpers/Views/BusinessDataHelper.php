<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Helpers\Views\AnchorHelper
 * @file AnchorHelper.php 1.0.0
 * @date 29-11-2018 19:00 SPAIN
 * @observations
 * @tags: #apify
 */
namespace App\Shared\Infrastructure\Helpers\Views;

use App\Shared\Infrastructure\Helpers\IHelper;
use App\Shared\Infrastructure\Helpers\AppHelper;

final class BusinessDataHelper extends AppHelper implements IHelper
{
    public function get_color(array $data, string $key): string
    {
        if (!($color = $data[$key] ?? "")) return "";
        return "<span style=\"background-color:$color;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>";
    }

    public function get_link(array $data, string $key): string
    {
        if (!($url = $data[$key] ?? "")) return "";
        return "<a href=\"$url\" target=\"_blank\">$url</a>";
    }

    public function get_img_link(array $data, string $key): string
    {
        if (!($url = $data[$key] ?? "")) return "";
        return "<a href=\"$url\" target=\"_blank\"><img src=\"$url\" class=\"img-thumbnail wd-30p\" style=\"width: 30%!important;\"/></a>";
    }

    public function get_link_domain(array $data, string $key): string
    {
        if (!($url = $data[$key] ?? "")) return "";
        $domain = $_SERVER["HTTP_HOST"];
        $url = "//".$domain."/".$url;
        return "<a href=\"$url\" target=\"_blank\">$url</a>";
    }

}
