<?php
namespace App\Open\Business\Application;

use App\Open\PromotionCaps\Domain\PromotionCapUsersRepository;
use App\Shared\Infrastructure\Exceptions\NotFoundException;
use App\Shared\Infrastructure\Helpers\UrlDomainHelper;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
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
            ["business_name", "url_business", "url_favicon", "user_logo_1", "url_social_fb", "url_social_ig", "url_social_twitter", "url_social_tiktok","body_bgimage"]
        );
        if (!$bd) {
            throw new NotFoundException(__("Partner “{0}“ not found!", $businessslug));
        }

        $r = [
            "business" => $bd["business_name"],
            //"businessurl" => Routes::url("business.space", ["businessslug"=>$bd["slug"]]),
            //quiza conviene usar la url de su sitio original en el logo y en los restantes usar el del espacio
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
        return [
            ["h2" => "Sobre EL CHALÁN"],
            ["p" => "Los precursores de la gastronomía peruana en Aruba, Betty e Isaac iniciaron este proyecto en 1997 desde su hogar donde cada peruano residente se sentía como en casa recordando el Perú."],
            ["p" => "Hoy en dia estamos en el corazón de Aruba (Oranjestad) firmes en transmitir y deleitar con esa hospitalidad y sazón de hogar a toda la Isla y visitantes."],
            ["p" => "Nuestra Filosofía se resume en ser “Una Familia que sirve a Familias ” es por ello que convertimos una de las mejores gastronomías del mundo a precios alcanzables para todos. Visitenos y permitanos tener el honor de servirles, el único riesgo que corre es el querer repetir todos los días."],
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
            ["p" => "Horario: 11:30 – 21:00"],
            ["h3" => "Planes por puntos"],
            ["ul" => [
                "Acumula 5 puntos en 5 días seguidos y llévate un Arroz Chaufa gratis"
            ]],
            ["h3" => "Promociones en curso"],
            ["ul" => []],
            ["p" => "<br/><br/><br/>"],
        ];
    }
}
