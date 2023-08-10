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

use App\Shared\Infrastructure\Helpers\{
    AppHelper,
    IHelper
};

final class BusinessDataHelper extends AppHelper implements IHelper
{
    public function getSpanColored(array $data, string $key): string
    {
        if (!($color = $data[$key] ?? "")) {
            return "";
        }
        return "<span style=\"background-color:$color;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>";
    }

    public function getBlankLink(array $data, string $key): string
    {
        if (!($url = $data[$key] ?? "")) {
            return "";
        }
        return "<a href=\"$url\" target=\"_blank\">$url</a>";
    }

    public function getImgBlankLink(array $data, string $key): string
    {
        if (!($url = $data[$key] ?? "")) {
            return "";
        }
        return "<a href=\"$url\" target=\"_blank\"><img src=\"$url\" class=\"img-thumbnail wd-30p\" style=\"width: 30%!important;\"/></a>";
    }

    public function getBlankLinkDomain(array $data, string $key): string
    {
        if (!($url = $data[$key] ?? "")) {
            return "";
        }
        $domain = $_SERVER["HTTP_HOST"];
        $url = "//".$domain."/afiliado/".$url;
        return "<a href=\"$url\" target=\"_blank\">$url</a>";
    }

}
