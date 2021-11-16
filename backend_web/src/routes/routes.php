<?php
return [
    ["url"=>"/","controller"=>"App\Controllers\Open\OpenController","method"=>"index"],

    ["url"=>"/forbidden","controller"=>"App\Controllers\Open\OpenController","method"=>"forbidden"],

    ["url"=>"/login","controller"=>"App\Controllers\Restrict\LoginController", "method"=>"index"],
    ["url"=>"/login/access","controller"=>"App\Controllers\Restrict\LoginController", "method"=>"access", "allowed"=>["post"]],

    ["url"=>"/restrict/logout","controller"=>"App\Controllers\Restrict\LoginController", "method"=>"logout"],
    ["url"=>"/restrict","controller"=>"App\Controllers\Restrict\DashboardController", "method"=>"index"],

    ["url"=>"/restrict/promotions","controller"=>"App\Controllers\Restrict\PromotionsController", "method"=>"index"],
    ["url"=>"/restrict/promotions/:uuid","controller"=>"App\Controllers\Restrict\PromotionsController", "method"=>"detail"],

    //@users
    ["url"=>"/restrict/users/info/:uuid","controller"=>"App\Controllers\Restrict\UsersController", "method"=>"info"],
    ["url"=>"/restrict/users/create","controller"=>"App\Controllers\Restrict\UsersController", "method"=>"create"],
    ["url"=>"/restrict/users/edit","controller"=>"App\Controllers\Restrict\UsersController", "method"=>"edit"],
    ["url"=>"/restrict/users/insert","controller"=>"App\Controllers\Restrict\UsersController", "method"=>"insert", "allowed"=>["post"]],
    ["url"=>"/restrict/users/update/:uuid","controller"=>"App\Controllers\Restrict\UsersController", "method"=>"update", "allowed"=>["post"]],
    ["url"=>"/restrict/users/delete/:uuid","controller"=>"App\Controllers\Restrict\UsersController", "method"=>"remove", "allowed"=>["post"]],
    ["url"=>"/restrict/users/?:page","controller"=>"App\Controllers\Restrict\UsersController", "method"=>"index"],
    ["url"=>"/restrict/users/search","controller"=>"App\Controllers\Restrict\UsersController", "method"=>"search"],

    ["url"=>"/logs","controller"=>"App\Controllers\LogsController","method"=>"index"],

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

//resto de rutas    
    ["url"=>"https://github.com/eacevedof/prj_phpapify/tree/master/backend/src/Controllers/Apify","controller"=>"App\Controllers\NotFoundController","method"=>"error_404"],
    ["url"=>"/404","controller"=>"App\Controllers\NotFoundController","method"=>"error_404"]
];
