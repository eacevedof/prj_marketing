<?php
return [
    ["url"=>"/promotion/:businessslug/:promotionslug","controller"=>"App\Open\PromotionCaps\Infrastructure\Controllers\PromotionCapSubscriptionsController","method"=>"index"],
    ["url"=>"/open/promotionscap/:promouuid/insert","controller"=>"App\Open\PromotionCaps\Infrastructure\Controllers\PromotionCapsInsertController", "method"=>"insert", "allowed"=>["post"]],

    ["url"=>"/login","controller"=>"App\Restrict\Login\Infrastructure\Controllers\LoginController", "method"=>"index"],
    ["url"=>"/login/access","controller"=>"App\Restrict\Login\Infrastructure\Controllers\LoginController", "method"=>"access", "allowed"=>["post"]],

    ["url"=>"/","controller"=>"App\Open\Home\Infrastructure\Controllers\HomeController","method"=>"index"],
    ["url"=>"/account/:slug","controller"=>"App\Open\Business\Infrastructure\Controllers\BusinessController","method"=>"index"],

//RESTRICT:
    ["url"=>"/restrict/logout","controller"=>"App\Restrict\Login\Infrastructure\Controllers\LoginController", "method"=>"logout"],
    ["url"=>"/restrict","controller"=>"App\Restrict\Dashboard\Infrastructure\Controllers\DashboardController", "method"=>"index"],

    //@users
    ["url"=>"/restrict/users/info/:uuid","controller"=>"App\Restrict\Users\Infrastructure\Controllers\UsersInfoController", "method"=>"info"],
    ["url"=>"/restrict/users/create","controller"=>"App\Restrict\Users\Infrastructure\Controllers\UsersInsertController", "method"=>"create"],
    ["url"=>"/restrict/users/insert","controller"=>"App\Restrict\Users\Infrastructure\Controllers\UsersInsertController", "method"=>"insert", "allowed"=>["post"]],
    ["url"=>"/restrict/users/edit/:uuid","controller"=>"App\Restrict\Users\Infrastructure\Controllers\UsersUpdateController", "method"=>"edit"],
    ["url"=>"/restrict/users/update/:uuid","controller"=>"App\Restrict\Users\Infrastructure\Controllers\UsersUpdateController", "method"=>"update", "allowed"=>["put"]],
    ["url"=>"/restrict/users/delete/:uuid","controller"=>"App\Restrict\Users\Infrastructure\Controllers\UsersDeleteController", "method"=>"remove", "allowed"=>["delete"]],
    ["url"=>"/restrict/users/undelete/:uuid","controller"=>"App\Restrict\Users\Infrastructure\Controllers\UsersDeleteController", "method"=>"undelete", "allowed"=>["patch"]],
    ["url"=>"/restrict/users/?int:page","controller"=>"App\Restrict\Users\Infrastructure\Controllers\UsersSearchController", "method"=>"index"],
    ["url"=>"/restrict/users/search","controller"=>"App\Restrict\Users\Infrastructure\Controllers\UsersSearchController", "method"=>"search"],
    //@users-tabs
    ["url"=>"/restrict/users/:uuid/permissions/update","controller"=>"App\Restrict\Users\Infrastructure\Controllers\UsersPermissionsUpdateController", "method"=>"update", "allowed"=>["put"]],
    ["url"=>"/restrict/users/:uuid/business-data/update","controller"=>"App\Restrict\Users\Infrastructure\Controllers\UsersBusinessDataUpdateController", "method"=>"update", "allowed"=>["put"]],
    ["url"=>"/restrict/users/:uuid/preferences/update","controller"=>"App\Restrict\Users\Infrastructure\Controllers\UsersPreferencesUpdateController", "method"=>"update", "allowed"=>["put"]],
    ["url"=>"/restrict/users/:uuid/preferences/delete","controller"=>"App\Restrict\Users\Infrastructure\Controllers\UsersPreferencesDeleteController", "method"=>"delete", "allowed"=>["delete"]],
    ["url"=>"/restrict/users/:uuid/preferences","controller"=>"App\Restrict\Users\Infrastructure\Controllers\UsersPreferencesListController", "method"=>"index", "allowed"=>["get"]],

    //@promotions
    ["url"=>"/restrict/promotions/info/:uuid","controller"=>"App\Restrict\Promotions\Infrastructure\Controllers\PromotionsInfoController", "method"=>"info"],
    ["url"=>"/restrict/promotions/create","controller"=>"App\Restrict\Promotions\Infrastructure\Controllers\PromotionsInsertController", "method"=>"create"],
    ["url"=>"/restrict/promotions/insert","controller"=>"App\Restrict\Promotions\Infrastructure\Controllers\PromotionsInsertController", "method"=>"insert", "allowed"=>["post"]],
    ["url"=>"/restrict/promotions/edit/:uuid","controller"=>"App\Restrict\Promotions\Infrastructure\Controllers\PromotionsUpdateController", "method"=>"edit"],
    ["url"=>"/restrict/promotions/update/:uuid","controller"=>"App\Restrict\Promotions\Infrastructure\Controllers\PromotionsUpdateController", "method"=>"update", "allowed"=>["put"]],
    ["url"=>"/restrict/promotions/delete/:uuid","controller"=>"App\Restrict\Promotions\Infrastructure\Controllers\PromotionsDeleteController", "method"=>"remove", "allowed"=>["delete"]],
    ["url"=>"/restrict/promotions/undelete/:uuid","controller"=>"App\Restrict\Promotions\Infrastructure\Controllers\PromotionsDeleteController", "method"=>"undelete", "allowed"=>["patch"]],
    ["url"=>"/restrict/promotions/?int:page","controller"=>"App\Restrict\Promotions\Infrastructure\Controllers\PromotionsSearchController", "method"=>"index"],
    ["url"=>"/restrict/promotions/search","controller"=>"App\Restrict\Promotions\Infrastructure\Controllers\PromotionsSearchController", "method"=>"search"],

    //@promotions-tabs
    ["url"=>"/restrict/promotions/:uuid/ui/update","controller"=>"App\Restrict\Promotions\Infrastructure\Controllers\PromotionUisUpdateController", "method"=>"update", "allowed"=>["put"]],

    //@business data
    ["url"=>"/restrict/promotions/info/:uuid","controller"=>"App\Restrict\BusinessData\Infrastructure\Controllers\BusinessDataInfoController", "method"=>"info"],
    ["url"=>"/restrict/promotions/create","controller"=>"App\Restrict\BusinessData\Infrastructure\Controllers\BusinessDataInsertController", "method"=>"create"],
    ["url"=>"/restrict/promotions/insert","controller"=>"App\Restrict\BusinessData\Infrastructure\Controllers\BusinessDataInsertController", "method"=>"insert", "allowed"=>["post"]],
    ["url"=>"/restrict/promotions/edit/:uuid","controller"=>"App\Restrict\BusinessData\Infrastructure\Controllers\BusinessDataUpdateController", "method"=>"edit"],
    ["url"=>"/restrict/promotions/update/:uuid","controller"=>"App\Restrict\BusinessData\Infrastructure\Controllers\BusinessDataUpdateController", "method"=>"update", "allowed"=>["put"]],
    ["url"=>"/restrict/promotions/delete/:uuid","controller"=>"App\Restrict\BusinessData\Infrastructure\Controllers\BusinessDataDeleteController", "method"=>"remove", "allowed"=>["delete"]],
    ["url"=>"/restrict/promotions/undelete/:uuid","controller"=>"App\Restrict\BusinessData\Infrastructure\Controllers\BusinessDataDeleteController", "method"=>"undelete", "allowed"=>["patch"]],
    ["url"=>"/restrict/promotions/?int:page","controller"=>"App\Restrict\BusinessData\Infrastructure\Controllers\BusinessDataSearchController", "method"=>"index"],
    ["url"=>"/restrict/promotions/search","controller"=>"App\Restrict\BusinessData\Infrastructure\Controllers\BusinessDataSearchController", "method"=>"search"],

    ["url"=>"/logs","controller"=>"App\Controllers\LogsController","method"=>"index"],

// APIFY
    ["url"=>"/apify/contexts","controller"=>"App\Controllers\Apify\ContextsController","method"=>"index"],
    ["url"=>"/apify/contexts/{id}","controller"=>"App\Controllers\Apify\ContextsController","method"=>"index"],
    
    ["url"=>"/apify/dbs/{id_context}","controller"=>"App\Controllers\Apify\DbsController","method"=>"index"],//schemas

    ["url"=>"/apify/tables/{id_context}/{schemainfo}","controller"=>"App\Controllers\Apify\TablesController","method"=>"index"],
    //["url"=>"/apify/tables/{id_context}","controller"=>"App\Controllers\Apify\TablesController","method"=>"index"], standby pq me obliga a recorre todas las bds
    ["url"=>"/apify/fields/{id_context}/{schemainfo}/{tablename}/{fieldname}","controller"=>"App\Controllers\Apify\FieldsController","method"=>"index"],
    ["url"=>"/apify/fields/{id_context}/{schemainfo}/{tablename}","controller"=>"App\Controllers\Apify\FieldsController","method"=>"index"],
    
    ["url"=>"/apify/read/raw","controller"=>"App\Controllers\Apify\Rw\ReaderController","method"=>"raw"],
    ["url"=>"/apify/read","controller"=>"App\Controllers\Apify\Rw\ReaderController","method"=>"index"],

    ["url"=>"/apify/write/raw","controller"=>"App\Controllers\Apify\Rw\WriterController","method"=>"raw"],
    ["url"=>"/apify/write","controller"=>"App\Controllers\Apify\Rw\WriterController","method"=>"index"],

    ["url"=>"/apify/security/get-password","controller"=>"App\Controllers\Apify\Security\PasswordController","method"=>"index"],
    ["url"=>"/apify/security/get-signature","controller"=>"App\Controllers\Apify\Security\SignatureController","method"=>"index"],
    ["url"=>"/apify/security/is-valid-signature","controller"=>"App\Controllers\Apify\Security\SignatureController","method"=>"is_valid_signature"],
    ["url"=>"/apify/security/encrypt","controller"=>"App\Controllers\Apify\Security\EncryptsController","method"=>"index"],

//tokens
    ["url"=>"/apify/security/login","controller"=>"App\Controllers\Apify\Security\LoginController","method"=>"index"],
    ["url"=>"/apify/security/login-middle","controller"=>"App\Controllers\Apify\Security\LoginController","method"=>"middle"],
    ["url"=>"/apify/security/is-valid-token","controller"=>"App\Controllers\Apify\Security\LoginController","method"=>"is_valid_token"],

    ["url"=>"/error/forbidden-403","controller"=>"App\Open\Errors\Infrastructure\Controllers\ErrorsController","method"=>"forbidden_403"],
    ["url"=>"/error/unexpected-500","controller"=>"App\Open\Errors\Infrastructure\Controllers\ErrorsController","method"=>"internal_500"],
    //la 404 debe ser la última ruta siempre
    ["url"=>"/error/not-found-404","controller"=>"App\Open\Errors\Infrastructure\Controllers\ErrorsController","method"=>"notfound_404"],
];
