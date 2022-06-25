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
            "body_bgimage" => "background-image",
            "body_color" => "color",
            "body_bgcolor" => "background-color",
        ],

        "footer" => [
            "url_social_fb", "url_social_ig", "url_social_twitter", "url_social_tiktok", "url_business",
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
            if (strstr($field,"bgimage")) $value = "url(\"$value\")";
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
            if (strstr($field,"bgimage"))
                $value = "linear-gradient(rgba(255,255,255,.9), rgba(255,255,255,.9)), url(\"$value\")";
            $style[] = "{$css}: $value !important";
        }
        return $style ? implode("; ",$style): "";
    }

    public function get_footer_links(): string
    {
        $links = [];
        $part = $this->mapping[self::FOOTER];
        foreach ($part as $field) {
            if (!$value = trim($this->businessdata[$field])) continue;
            $links[] = "<li><a href=\"{$value}\" target=\"_blank\" rel=\"nofollow\">{$value}</a></li>";
        }
        return $links
            ? "<ul class=\"menu simple\">".implode(" ",$links)."</ul>"
            : "";
    }

    public static function echo_style(string $property, ?string $value): void
    {
        if (!$value) return;
        if (strstr($property,"background-image")) $value = "url(\"$value\")!important";
        echo "$property: $value;";
    }
}
