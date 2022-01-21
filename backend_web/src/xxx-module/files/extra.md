### routes.php
```php
return [
    //@xxxs
    ["url"=>"/restrict/xxxs/info/:uuid","controller"=>"App\Controllers\Restrict\XxxsController", "method"=>"info"],
    ["url"=>"/restrict/xxxs/create","controller"=>"App\Controllers\Restrict\XxxsController", "method"=>"create"],
    ["url"=>"/restrict/xxxs/edit/:uuid","controller"=>"App\Controllers\Restrict\XxxsController", "method"=>"edit"],
    ["url"=>"/restrict/xxxs/insert","controller"=>"App\Controllers\Restrict\XxxsController", "method"=>"insert", "allowed"=>["post"]],
    ["url"=>"/restrict/xxxs/update/:uuid","controller"=>"App\Controllers\Restrict\XxxsController", "method"=>"update", "allowed"=>["put"]],
    ["url"=>"/restrict/xxxs/delete/:uuid","controller"=>"App\Controllers\Restrict\XxxsController", "method"=>"remove", "allowed"=>["delete"]],
    ["url"=>"/restrict/xxxs/undelete/:uuid","controller"=>"App\Controllers\Restrict\XxxsController", "method"=>"undelete", "allowed"=>["patch"]],
    ["url"=>"/restrict/xxxs/?int:page","controller"=>"App\Controllers\Restrict\XxxsController", "method"=>"index"],
    ["url"=>"/restrict/xxxs/search","controller"=>"App\Controllers\Restrict\XxxsController", "method"=>"search"],
];
```

### translations
```po
%PO_KEYS%
```

### policies
```
const XXXS_READ = "xxxs:read";
const XXXS_WRITE = "xxxs:write";
```

### permissions
```json
```
