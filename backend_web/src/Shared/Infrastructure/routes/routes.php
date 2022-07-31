<?php
return [
    [
        "url"=>"/socios/:businessslug/promocion/:promotionslug",
        "controller"=>"App\Open\PromotionCaps\Infrastructure\Controllers\PromotionCapCreateController",
        "method"=>"create", "allowed"=>["get"],
        "name"=>"subscription.create"
    ],

    [
        "url"=>"/partner/:businessslug/promotionscap/:promouuid/insert",
        "controller"=>"App\Open\PromotionCaps\Infrastructure\Controllers\PromotionCapInsertController",
        "method"=>"insert", "allowed"=>["post"],
        "name"=>"subscription.post"
    ],

    [
        "url"=>"/socios/:businessslug/suscripcion/:subscriptionuuid/confirmar",
        "controller"=>"App\Open\PromotionCaps\Infrastructure\Controllers\PromotionCapConfirmController",
        "method"=>"confirm", "allowed"=>["get"],
        "name"=>"subscription.confirm"
    ],

    [
        "url"=>"/socios/:businessslug/suscripcion/:subscriptionuuid/cancelar/",
        "controller"=>"App\Open\PromotionCaps\Infrastructure\Controllers\PromotionCapCancelController",
        "method"=>"cancel", "allowed"=>["get"],
        "name"=>"subscription.cancel"
    ],

    [
        "url"=>"/socios/:businessslug/puntos-usuario/:capuseruuid",
        "controller"=>"App\Open\UserCaps\Infrastructure\Controllers\UserCapPointsController",
        "method"=>"index", "allowed"=>["get"],
        "name"=>"user.points"
    ],

    [
        "url"=>"/socios/:slug",
        "controller"=>"App\Open\Business\Infrastructure\Controllers\BusinessController",
        "method"=>"index",
        "name"=>"business.space"
    ],
    [
        "url"=>"/terminos-y-condiciones/:promoslug",
        "controller"=>"App\Open\TermsConditions\Infrastructure\Controllers\TermsConditionsInfoController",
        "method"=>"promotion", "allowed"=>["get"],
        "name"=>"terms.by-promotion"
    ],
    [
        "url"=>"/terminos-y-condiciones",
        "controller"=>"App\Open\TermsConditions\Infrastructure\Controllers\TermsConditionsInfoController",
        "method"=>"index", "allowed"=>["get"],
        "name"=>"terms.general"
    ],

    [
        "url"=>"/politica-de-cookies",
        "controller"=>"App\Open\CookiesPolicy\Infrastructure\Controllers\CookiesPolicyInfoController",
        "method"=>"index", "allowed"=>["get"],
        "name"=>"cookies.policy"
    ],
    [
        "url"=>"/politica-de-privacidad",
        "controller"=>"App\Open\PrivacyPolicy\Infrastructure\Controllers\PrivacyPolicyInfoController",
        "method"=>"index", "allowed"=>["get"],
        "name"=>"privacy.policy"
    ],

    [
        "url"=>"/contact/send",
        "controller"=>"App\Open\Home\Infrastructure\Controllers\ContactSendController",
        "method"=>"send", "allowed"=>["post"],
        "name"=>"contact.send"
    ],

    [
        "url"=>"/login/access",
        "controller"=>"App\Restrict\Login\Infrastructure\Controllers\LoginController",
        "method"=>"access", "allowed"=>["post"],
        "name"=>"login.access"
    ],
    [
        "url"=>"/login",
        "controller"=>"App\Restrict\Login\Infrastructure\Controllers\LoginController",
        "method"=>"index", "allowed"=>["get"],
        "name"=>"login"
    ],

    [
        "url"=>"/",
        "controller"=>"App\Open\Home\Infrastructure\Controllers\HomeController",
        "method"=>"index", "allowed"=>["get"], "name"=>"home"
    ],

//RESTRICT:
    ["url"=>"/restrict/logout","controller"=>"App\Restrict\Login\Infrastructure\Controllers\LoginController", "method"=>"logout", "allowed"=>["get","post"], "name"=>"logout"],
    ["url"=>"/restrict","controller"=>"App\Restrict\Dashboard\Infrastructure\Controllers\DashboardController", "method"=>"index", "allowed"=>["get"], "name"=>"dashboard"],

//@users
    ["url"=>"/restrict/users/info/:uuid","controller"=>"App\Restrict\Users\Infrastructure\Controllers\UsersInfoController", "method"=>"info", "name"=>""],
    ["url"=>"/restrict/users/create","controller"=>"App\Restrict\Users\Infrastructure\Controllers\UsersInsertController", "method"=>"create", "name"=>""],
    ["url"=>"/restrict/users/insert","controller"=>"App\Restrict\Users\Infrastructure\Controllers\UsersInsertController", "method"=>"insert", "allowed"=>["post"], "name"=>""],
    ["url"=>"/restrict/users/edit/:uuid","controller"=>"App\Restrict\Users\Infrastructure\Controllers\UsersUpdateController", "method"=>"edit", "name"=>""],
    ["url"=>"/restrict/users/update/:uuid","controller"=>"App\Restrict\Users\Infrastructure\Controllers\UsersUpdateController", "method"=>"update", "allowed"=>["put"], "name"=>""],
    ["url"=>"/restrict/users/delete/:uuid","controller"=>"App\Restrict\Users\Infrastructure\Controllers\UsersDeleteController", "method"=>"remove", "allowed"=>["delete"], "name"=>""],
    ["url"=>"/restrict/users/undelete/:uuid","controller"=>"App\Restrict\Users\Infrastructure\Controllers\UsersDeleteController", "method"=>"undelete", "allowed"=>["patch"], "name"=>""],
    ["url"=>"/restrict/users/export/:uuid","controller"=>"App\Restrict\Users\Infrastructure\Controllers\UsersSearchExportController", "method"=>"export", "allowed"=>["post"], "name"=>""],
    ["url"=>"/restrict/users/?int:page","controller"=>"App\Restrict\Users\Infrastructure\Controllers\UsersSearchController", "method"=>"index", "name"=>""],
    ["url"=>"/restrict/users/search","controller"=>"App\Restrict\Users\Infrastructure\Controllers\UsersSearchController", "method"=>"search", "name"=>""],
//@users-tabs
    ["url"=>"/restrict/users/:uuid/permissions/update","controller"=>"App\Restrict\Users\Infrastructure\Controllers\UsersPermissionsUpdateController", "method"=>"update", "allowed"=>["put"], "name"=>""],
    ["url"=>"/restrict/users/:uuid/business-data/update","controller"=>"App\Restrict\Users\Infrastructure\Controllers\UsersBusinessDataUpdateController", "method"=>"update", "allowed"=>["put"], "name"=>""],
    ["url"=>"/restrict/users/:uuid/preferences/update","controller"=>"App\Restrict\Users\Infrastructure\Controllers\UsersPreferencesUpdateController", "method"=>"update", "allowed"=>["put"], "name"=>""],
    ["url"=>"/restrict/users/:uuid/preferences/delete","controller"=>"App\Restrict\Users\Infrastructure\Controllers\UsersPreferencesDeleteController", "method"=>"delete", "allowed"=>["delete"], "name"=>""],
    ["url"=>"/restrict/users/:uuid/preferences","controller"=>"App\Restrict\Users\Infrastructure\Controllers\UsersPreferencesListController", "method"=>"index", "allowed"=>["get"], "name"=>""],

//@promotions
    ["url"=>"/restrict/promotions/info/:uuid","controller"=>"App\Restrict\Promotions\Infrastructure\Controllers\PromotionsInfoController", "method"=>"info", "name"=>""],
    ["url"=>"/restrict/promotions/create","controller"=>"App\Restrict\Promotions\Infrastructure\Controllers\PromotionsInsertController", "method"=>"create", "name"=>""],
    ["url"=>"/restrict/promotions/insert","controller"=>"App\Restrict\Promotions\Infrastructure\Controllers\PromotionsInsertController", "method"=>"insert", "allowed"=>["post"], "name"=>""],
    ["url"=>"/restrict/promotions/edit/:uuid","controller"=>"App\Restrict\Promotions\Infrastructure\Controllers\PromotionsUpdateController", "method"=>"edit", "name"=>""],
    ["url"=>"/restrict/promotions/update/:uuid","controller"=>"App\Restrict\Promotions\Infrastructure\Controllers\PromotionsUpdateController", "method"=>"update", "allowed"=>["put"], "name"=>""],
    ["url"=>"/restrict/promotions/delete/:uuid","controller"=>"App\Restrict\Promotions\Infrastructure\Controllers\PromotionsDeleteController", "method"=>"remove", "allowed"=>["delete"], "name"=>""],
    ["url"=>"/restrict/promotions/undelete/:uuid","controller"=>"App\Restrict\Promotions\Infrastructure\Controllers\PromotionsDeleteController", "method"=>"undelete", "allowed"=>["patch"], "name"=>""],
    ["url"=>"/restrict/promotions/export/:uuid","controller"=>"App\Restrict\Promotions\Infrastructure\Controllers\PromotionsSearchExportController", "method"=>"export", "allowed"=>["post"], "name"=>""],
    ["url"=>"/restrict/promotions/?int:page","controller"=>"App\Restrict\Promotions\Infrastructure\Controllers\PromotionsSearchController", "method"=>"index", "name"=>""],
    ["url"=>"/restrict/promotions/search","controller"=>"App\Restrict\Promotions\Infrastructure\Controllers\PromotionsSearchController", "method"=>"search", "name"=>""],
//@promotions-tabs
    ["url"=>"/restrict/promotions/:uuid/ui/update","controller"=>"App\Restrict\Promotions\Infrastructure\Controllers\PromotionUisUpdateController", "method"=>"update", "allowed"=>["put"], "name"=>""],

//@business data
    ["url"=>"/restrict/promotions/info/:uuid","controller"=>"App\Restrict\BusinessData\Infrastructure\Controllers\BusinessDataInfoController", "method"=>"info", "name"=>""],
    ["url"=>"/restrict/promotions/create","controller"=>"App\Restrict\BusinessData\Infrastructure\Controllers\BusinessDataInsertController", "method"=>"create", "name"=>""],
    ["url"=>"/restrict/promotions/insert","controller"=>"App\Restrict\BusinessData\Infrastructure\Controllers\BusinessDataInsertController", "method"=>"insert", "allowed"=>["post"], "name"=>""],
    ["url"=>"/restrict/promotions/edit/:uuid","controller"=>"App\Restrict\BusinessData\Infrastructure\Controllers\BusinessDataUpdateController", "method"=>"edit", "name"=>""],
    ["url"=>"/restrict/promotions/update/:uuid","controller"=>"App\Restrict\BusinessData\Infrastructure\Controllers\BusinessDataUpdateController", "method"=>"update", "allowed"=>["put"], "name"=>""],
    ["url"=>"/restrict/promotions/delete/:uuid","controller"=>"App\Restrict\BusinessData\Infrastructure\Controllers\BusinessDataDeleteController", "method"=>"remove", "allowed"=>["delete"], "name"=>""],
    ["url"=>"/restrict/promotions/undelete/:uuid","controller"=>"App\Restrict\BusinessData\Infrastructure\Controllers\BusinessDataDeleteController", "method"=>"undelete", "allowed"=>["patch"], "name"=>""],
    ["url"=>"/restrict/promotions/?int:page","controller"=>"App\Restrict\BusinessData\Infrastructure\Controllers\BusinessDataSearchController", "method"=>"index", "name"=>""],
    ["url"=>"/restrict/promotions/search","controller"=>"App\Restrict\BusinessData\Infrastructure\Controllers\BusinessDataSearchController", "method"=>"search", "name"=>""],

//@subscriptions
    ["url"=>"/restrict/subscriptions/info/:uuid","controller"=>"App\Restrict\Subscriptions\Infrastructure\Controllers\SubscriptionsInfoController", "method"=>"info", "name"=>""],
    ["url"=>"/restrict/subscriptions/edit/:uuid","controller"=>"App\Restrict\Subscriptions\Infrastructure\Controllers\SubscriptionsUpdateController", "method"=>"edit", "name"=>""],
    ["url"=>"/restrict/subscriptions/update-status/:uuid","controller"=>"App\Restrict\Subscriptions\Infrastructure\Controllers\SubscriptionsUpdateController", "method"=>"update_status", "allowed"=>["put"], "name"=>""],
    ["url"=>"/restrict/subscriptions/export/:uuid","controller"=>"App\Restrict\Subscriptions\Infrastructure\Controllers\SubscriptionsSearchExportController", "method"=>"export", "allowed"=>["post"], "name"=>""],
    ["url"=>"/restrict/subscriptions/?int:page","controller"=>"App\Restrict\Subscriptions\Infrastructure\Controllers\SubscriptionsSearchController", "method"=>"index", "name"=>""],
    ["url"=>"/restrict/subscriptions/search","controller"=>"App\Restrict\Subscriptions\Infrastructure\Controllers\SubscriptionsSearchController", "method"=>"search", "name"=>""],

//@billings
    ["url"=>"/restrict/billings/export/:uuid","controller"=>"App\Restrict\Billings\Infrastructure\Controllers\BillingsSearchExportController", "method"=>"export", "allowed"=>["post"], "name"=>""],
    ["url"=>"/restrict/billings/?int:page","controller"=>"App\Restrict\Billings\Infrastructure\Controllers\BillingsSearchController", "method"=>"index", "name"=>""],
    ["url"=>"/restrict/billings/search","controller"=>"App\Restrict\Billings\Infrastructure\Controllers\BillingsSearchController", "method"=>"search", "name"=>""],


    ["url"=>"/logs","controller"=>"App\Controllers\LogsController","method"=>"index", "name"=>""],

// APIFY
    ["url"=>"/apify/contexts","controller"=>"App\Controllers\Apify\ContextsController","method"=>"index", "name"=>""],
    ["url"=>"/apify/contexts/{id}","controller"=>"App\Controllers\Apify\ContextsController","method"=>"index", "name"=>""],

    ["url"=>"/apify/dbs/{id_context}","controller"=>"App\Controllers\Apify\DbsController","method"=>"index", "name"=>""],//schemas

    ["url"=>"/apify/tables/{id_context}/{schemainfo}","controller"=>"App\Controllers\Apify\TablesController","method"=>"index", "name"=>""],
//["url"=>"/apify/tables/{id_context}","controller"=>"App\Controllers\Apify\TablesController","method"=>"index", "name"=>""], standby pq me obliga a recorre todas las bds
    ["url"=>"/apify/fields/{id_context}/{schemainfo}/{tablename}/{fieldname}","controller"=>"App\Controllers\Apify\FieldsController","method"=>"index", "name"=>""],
    ["url"=>"/apify/fields/{id_context}/{schemainfo}/{tablename}","controller"=>"App\Controllers\Apify\FieldsController","method"=>"index", "name"=>""],

    ["url"=>"/apify/read/raw","controller"=>"App\Controllers\Apify\Rw\ReaderController","method"=>"raw", "name"=>""],
    ["url"=>"/apify/read","controller"=>"App\Controllers\Apify\Rw\ReaderController","method"=>"index", "name"=>""],

    ["url"=>"/apify/write/raw","controller"=>"App\Controllers\Apify\Rw\WriterController","method"=>"raw", "name"=>""],
    ["url"=>"/apify/write","controller"=>"App\Controllers\Apify\Rw\WriterController","method"=>"index", "name"=>""],

    ["url"=>"/apify/security/get-password","controller"=>"App\Controllers\Apify\Security\PasswordController","method"=>"index", "name"=>""],
    ["url"=>"/apify/security/get-signature","controller"=>"App\Controllers\Apify\Security\SignatureController","method"=>"index", "name"=>""],
    ["url"=>"/apify/security/is-valid-signature","controller"=>"App\Controllers\Apify\Security\SignatureController","method"=>"is_valid_signature", "name"=>""],
    ["url"=>"/apify/security/encrypt","controller"=>"App\Controllers\Apify\Security\EncryptsController","method"=>"index", "name"=>""],

//tokens
    ["url"=>"/apify/security/login","controller"=>"App\Controllers\Apify\Security\LoginController","method"=>"index", "name"=>""],
    ["url"=>"/apify/security/login-middle","controller"=>"App\Controllers\Apify\Security\LoginController","method"=>"middle", "name"=>""],
    ["url"=>"/apify/security/is-valid-token","controller"=>"App\Controllers\Apify\Security\LoginController","method"=>"is_valid_token", "name"=>""],

    ["url"=>"/error/forbidden-403","controller"=>"App\Open\Errors\Infrastructure\Controllers\ErrorsController","method"=>"forbidden_403", "name"=>""],
    ["url"=>"/error/unexpected-500","controller"=>"App\Open\Errors\Infrastructure\Controllers\ErrorsController","method"=>"internal_500", "name"=>""],
//la 404 debe ser la última ruta siempre
    ["url"=>"/error/not-found-404","controller"=>"App\Open\Errors\Infrastructure\Controllers\ErrorsController","method"=>"notfound_404", "name"=>""],
];
