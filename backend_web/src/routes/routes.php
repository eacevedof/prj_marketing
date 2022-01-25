<?php
return [
    ["url"=>"/","controller"=>"App\Controllers\Open\HomeController","method"=>"index"],
    
    ["url"=>"/login","controller"=>"App\Controllers\Restrict\LoginController", "method"=>"index"],
    ["url"=>"/login/access","controller"=>"App\Controllers\Restrict\LoginController", "method"=>"access", "allowed"=>["post"]],

    ["url"=>"/restrict/logout","controller"=>"App\Controllers\Restrict\LoginController", "method"=>"logout"],
    ["url"=>"/restrict","controller"=>"App\Controllers\Restrict\DashboardController", "method"=>"index"],

    //@users
    ["url"=>"/restrict/users/info/:uuid","controller"=>"App\Controllers\Restrict\UsersController", "method"=>"info"],
    ["url"=>"/restrict/users/create","controller"=>"App\Controllers\Restrict\UsersController", "method"=>"create"],
    ["url"=>"/restrict/users/edit/:uuid","controller"=>"App\Controllers\Restrict\UsersController", "method"=>"edit"],
    ["url"=>"/restrict/users/insert","controller"=>"App\Controllers\Restrict\UsersController", "method"=>"insert", "allowed"=>["post"]],
    ["url"=>"/restrict/users/update/:uuid","controller"=>"App\Controllers\Restrict\UsersController", "method"=>"update", "allowed"=>["put"]],
    ["url"=>"/restrict/users/delete/:uuid","controller"=>"App\Controllers\Restrict\UsersController", "method"=>"remove", "allowed"=>["delete"]],
    ["url"=>"/restrict/users/undelete/:uuid","controller"=>"App\Controllers\Restrict\UsersController", "method"=>"undelete", "allowed"=>["patch"]],

    ["url"=>"/restrict/users/?int:page","controller"=>"App\Controllers\Restrict\Users\UsersSearchController", "method"=>"index"],
    ["url"=>"/restrict/users/search","controller"=>"App\Controllers\Restrict\Users\UsersSearchController", "method"=>"search"],

    //@promotions
    ["url"=>"/restrict/promotions/info/:uuid","controller"=>"App\Controllers\Restrict\PromotionsController", "method"=>"info"],
    ["url"=>"/restrict/promotions/create","controller"=>"App\Controllers\Restrict\PromotionsController", "method"=>"create"],
    ["url"=>"/restrict/promotions/edit/:uuid","controller"=>"App\Controllers\Restrict\PromotionsController", "method"=>"edit"],
    ["url"=>"/restrict/promotions/insert","controller"=>"App\Controllers\Restrict\PromotionsController", "method"=>"insert", "allowed"=>["post"]],
    ["url"=>"/restrict/promotions/update/:uuid","controller"=>"App\Controllers\Restrict\PromotionsController", "method"=>"update", "allowed"=>["put"]],
    ["url"=>"/restrict/promotions/delete/:uuid","controller"=>"App\Controllers\Restrict\PromotionsController", "method"=>"remove", "allowed"=>["delete"]],
    ["url"=>"/restrict/promotions/undelete/:uuid","controller"=>"App\Controllers\Restrict\PromotionsController", "method"=>"undelete", "allowed"=>["patch"]],
    ["url"=>"/restrict/promotions/?int:page","controller"=>"App\Controllers\Restrict\PromotionsController", "method"=>"index"],
    ["url"=>"/restrict/promotions/search","controller"=>"App\Controllers\Restrict\PromotionsController", "method"=>"search"],

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

    ["url"=>"/error/forbidden-403","controller"=>"App\Controllers\Open\ErrorsController","method"=>"forbidden_403"],
    ["url"=>"/error/unexpected-500","controller"=>"App\Controllers\Open\ErrorsController","method"=>"internal_500"],
    //la 404 debe ser la última ruta siempre
    ["url"=>"/error/not-found-404","controller"=>"App\Controllers\Open\ErrorsController","method"=>"notfound_404"],
];
