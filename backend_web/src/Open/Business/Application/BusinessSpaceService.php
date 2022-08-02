<?php
namespace App\Open\Business\Application;

use App\Open\PromotionCaps\Domain\PromotionCapUsersRepository;
use App\Shared\Infrastructure\Exceptions\NotFoundException;
use App\Shared\Infrastructure\Helpers\UrlDomainHelper;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\ComponentFactory as CF;
use App\Shared\Infrastructure\Components\Date\UtcComponent;
use App\Shared\Infrastructure\Helpers\RoutesHelper as Routes;
use App\Restrict\BusinessData\Domain\BusinessDataRepository;
use App\Restrict\Promotions\Domain\PromotionRepository;
use App\Open\PromotionCaps\Domain\PromotionCapSubscriptionsRepository;

final class BusinessSpaceService extends AppService
{
    private int $istest;

    //"_test_mode" => $this->request->get_get("mode", "")==="test",
    public function __construct(array $input=[])
    {
        $this->istest = (int)($input["_test_mode"] ?? "");
    }

    public function get_data_by_promotion(string $promouuid): array
    {
        $space =  RF::get(BusinessDataRepository::class)->get_space_by_promotion($promouuid);
        if (!$space) return [];

        $url = Routes::url("subscription.create", [
            "businessslug"=>$space["businessslug"],
            "promotionslug"=>$space["promoslug"]
        ]);

        if ($this->istest) $url .= "?mode=test";
        $space["promotionlink"] = UrlDomainHelper::get_instance()->get_full_url($url);
        return $space;
    }

    public function get_data_by_promotion_slug(string $promoslug): array
    {
        $promouuid = RF::get(PromotionRepository::class)->get_by_slug($promoslug, ["uuid"]);
        if (!$promouuid) return [];
        return $this->get_data_by_promotion($promouuid["uuid"]);
    }

    public function get_data_by_promocap(string $promocapuuid): array
    {
        $promoid = RF::get(PromotionCapSubscriptionsRepository::class)->get_by_uuid($promocapuuid, ["id_promotion"])["id_promotion"] ?? 0;
        $promouuid = RF::get(PromotionRepository::class)->get_by_id($promoid, ["uuid"]);
        if (!$promouuid) return [];
        return $this->get_data_by_promotion($promouuid["uuid"]);
    }

    public function get_data_by_promocapuser(string $capuseruuid): array
    {
        $promoid = RF::get(PromotionCapUsersRepository::class)->get_by_uuid($capuseruuid, ["id_promotion"])["id_promotion"] ?? 0;
        $promouuid = RF::get(PromotionRepository::class)->get_by_id($promoid, ["uuid"]);
        if (!$promouuid) return [];
        return $this->get_data_by_promotion($promouuid["uuid"]);
    }

    public function get_data_by_uuid(string $businessuuid): array
    {
        return RF::get(BusinessDataRepository::class)->get_space_by_uuid($businessuuid);
    }

    public function get_data_by_slug(string $businessslug): array
    {
        $bd = RF::get(BusinessDataRepository::class)->get_by_slug(
            $businessslug,
            [
                "slug", "business_name", "url_business", "url_favicon", "user_logo_1", "url_social_fb", "url_social_ig",
                "url_social_twitter", "url_social_tiktok","body_bgimage"
            ]
        );
        if (!$bd) {
            throw new NotFoundException(__("Partner “{0}“ not found!", $businessslug));
        }

        $r = [
            "business" => $bd["business_name"],
            //"businessurl" => Routes::url("business.space", ["businessslug"=>$bd["slug"]]),
            //quiza conviene usar la url de su sitio original en el logo y en los restantes usar el del espacio
            "businessslug" => $bd["slug"],
            "businessfavicon" => $bd["url_favicon"],
            "businessurl" => $bd["url_business"],
            "businesslogo" => $bd["user_logo_1"],
            "businessbgimage" => $bd["body_bgimage"],
            "urlfb" => $bd["url_social_fb"],
            "urlig" => $bd["url_social_ig"],
            "urltwitter" => $bd["url_social_twitter"],
            "urltiktok" => $bd["url_social_tiktok"],
        ];
        return $r;
    }

    public function get_promotion_url(string $promouuid): string
    {
        $space =  RF::get(BusinessDataRepository::class)->get_space_by_promotion($promouuid);
        if (!$space) return [];

        $url = Routes::url("subscription.create", [
            "businessslug"=>$space["businessslug"],
            "promotionslug"=>$space["promoslug"]
        ]);
        return $this->istest ? "$url?mode=test" : $url;
    }

    public static function chalan(): array
    {
        $tz = CF::get(UtcComponent::class)->get_timezone_by_ip($_SERVER["REMOTE_ADDR"]);
        $slug = "el-chaln-peruvian-cousine-44";
        $promotions = RF::get(BusinessDataRepository::class)->get_top5_last_running_promotions_by_slug($slug, $tz);
        $promotions = array_map(function (array $row) use ($slug, $tz) {
            $description = htmlentities($row["description"]);
            $url = Routes::url("subscription.create", ["businessslug"=>$slug, "promotionslug"=>$row["slug"]]);
            return "<a href=\"$url\">{$description}</a> <small>Desde: {$row["date_from"]} / Hasta: {$row["date_to"]} $tz</small>";
        }, $promotions);


        return [
            ["h2" => "Sobre EL CHALÁN"],
            ["p" => "Es un proyecto gastronómico emprendido por el matrimonio de Betty e Isaac allá por 1997; desde su hogar donde la comunidad peruana residente en la isla se sentía como en casa rememorando aquellos sabores tan únicos."],
            ["p" => "Hoy continua más vivo que nunca gracias al trabajo de Dayana, Christian y el resto del staff que día a día se esfuerzan para que los visitantes se lleven la mejor de las experiencias."],
            ["p" => "La filosofía que nos hace distintos es: “Una Familia que sirve a Familias”. Por ello que convertimos una de las mejores gastronomías del mundo a precios asequibles para todos. Visítenos y permítanos tener el honor de servirles. El único riesgo que corre es el querer repetir todos los días."],
            ["h3" => "Planes por puntos"],
            ["ul" => [
                "Acumula 5 puntos en 5 días seguidos y llévate un Arroz Chaufa gratis",
                "A fin de mes sortearemos algunas sorpresas "
            ]],
            ["h3" => "Promociones en curso"],
            ["ul" => $promotions],
            ["h2" => "¿Dónde estamos?"],
            ["p" => "En Caya Betico Croes 152 (975,93 km) <br/> 297 Oranjestad - <b>Aruba</b>"],
            ["p" => "<a 
href=\"https://www.google.com/maps/place/Caya+G.+F.+Betico+Croes+152,+Oranjestad,+Aruba/@12.5189601,-70.0309033,17z/data=!3m1!4b1!4m5!3m4!1s0x8e853894e2ca1f09:0x9a74e6e217c0a32c!8m2!3d12.5189601!4d-70.0287146\"
target=\ª_blank\"
rel=\"nofollow noopener noreferer\"
>Abrir en Google Maps</a>"],
            ["h2" => "Datos de contacto"],
            ["p" => "Email: <a href=\"mailto:elchalanaruba@hotmail.com\" rel=\"nofollow noopener noreferer\">elchalanaruba@hotmail.com</a>"],
            ["p" => "Teléfono: <a href=\"tel:+297 582 7591\" rel=\"nofollow noopener noreferer\">+297 582 7591</a>"],
            ["p" => "Telegram: <a href=\"https://t.me/c/1394909256/17561\" rel=\"nofollow noopener noreferer\">+297 699 4346</a>"],
            ["p" => "Horario: 11:30 – 21:00"],

            ["p" => "<br/><br/><br/>"],
        ];
    }
}
