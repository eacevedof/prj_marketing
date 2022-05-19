<?php
namespace App\Shared\Infrastructure\Helpers\Views\PromotionCap;

use App\Shared\Infrastructure\Helpers\IHelper;
use App\Shared\Infrastructure\Helpers\AppHelper;

final class BusinessDataHelper extends AppHelper implements IHelper
{
    private array $businessdata;

    private const HEAD = "head";
    private const BODY = "body";
    private const FOOTER = "footer";

    private array $mapping = [
        "head" => [
            "head_bgimage" => "background-image",
            "head_color" => "color",
            "head_bgcolor" => "background-color",
        ],
        "body" => [
            "body_bgcolor" => "background-image",
            "body_color" => "color",
            "body_bgcolor" => "background-color",
        ],

        "links" => [
            "url_business", "url_social_fb", "url_social_ig", "url_social_twitter", "url_social_tiktok"
        ]
    ];


    public function __construct(array $businessdata)
    {
        $this->businessdata = $businessdata;
    }

    public function get_style_header(): string
    {
        $style = [];
        $part = $this->mapping[self::HEAD];
        foreach ($part as $field => $css) {
            if (!$value = trim($this->businessdata[$field])) continue;
            $style[] = "{$css}: $value";
        }
        return $style ? implode("; ",$style): "";
    }

    public function get_style_body(): string
    {
        $style = [];
        $part = $this->mapping[self::BODY];
        foreach ($part as $field => $css) {
            if (!$value = trim($this->businessdata[$field])) continue;
            $style[] = "{$css}: $value";
        }
        return $style ? implode("; ",$style): "";
    }

    public function get_footer_links(): string
    {
        $links = [];
        $part = $this->mapping[self::BODY];
        foreach ($part as $field) {
            if (!$value = trim($this->businessdata[$field])) continue;
            $links[] = "<li><a href=\"{}\" target=\"_blank\" rel=\"nofollow\">{$value}</a></li>";
        }
        return $links
            ? "<li>".implode(" ",$links)."</li>"
            : "";
    }
}
