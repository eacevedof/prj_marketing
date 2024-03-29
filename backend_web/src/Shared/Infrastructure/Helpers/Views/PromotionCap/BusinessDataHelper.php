<?php

namespace App\Shared\Infrastructure\Helpers\Views\PromotionCap;

use App\Shared\Infrastructure\Helpers\{
    AppHelper,
    IHelper
};

final class BusinessDataHelper extends AppHelper implements IHelper
{
    private array $businessData;

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

    public function __construct(array $businessData)
    {
        $this->businessData = $businessData;
    }

    public function getStyleHeader(): string
    {
        $styles = [];
        $part = $this->mapping[self::HEAD];
        $color = "";
        foreach ($part as $field => $css) {
            if (!$value = trim($this->businessData[$field])) {
                continue;
            }
            if (strstr($field, "bgimage")) {
                $value = "url(\"$value\")";
            }
            if ($field==="head_color")
                $color = $value;

            $styles[] = "{$css}: $value";
        }
        if (!$styles)  return "";

        $style = ".nav-flex {".implode("; ", $styles)."}";
        if (str_contains($style, "color:"))
            $style .= " .nav-flex h1 {color: $color}";
        return $style;
    }

    public function getStyleBody(): string
    {
        $style = [];
        $part = $this->mapping[self::BODY];
        foreach ($part as $field => $css) {
            if (!$value = trim($this->businessData[$field])) {
                continue;
            }
            if (strstr($field, "bgimage")) {
                $value = "linear-gradient(rgba(255,255,255,.9), rgba(255,255,255,.9)), url(\"$value\")";
            }
            $style[] = "{$css}: $value !important";
        }
        return $style ? implode("; ", $style) : "";
    }

    public function getFooterLinks(): string
    {
        $links = [];
        $part = $this->mapping[self::FOOTER];
        foreach ($part as $field) {
            if (!$value = trim($this->businessData[$field])) {
                continue;
            }
            $links[] = "<li><a href=\"{$value}\" target=\"_blank\" rel=\"nofollow\">{$value}</a></li>";
        }
        return $links
            ? "<ul class=\"menu simple\">".implode(" ", $links)."</ul>"
            : "";
    }

    public static function echoStyle(string $property, ?string $value): void
    {
        if (!$value) {
            return;
        }
        if (strstr($property, "background-image")) {
            $value = "url(\"$value\")!important";
        }
        echo "$property: $value;";
    }
}
