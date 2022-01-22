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
msgid "Xxxs"
msgstr ""

msgid "Xxx"
msgstr ""

msgid "xxxs"
msgstr ""

msgid "xxx"
msgstr ""

%PO_KEYS%
```

### policies
```php
//PolicyType.php
const XXXS_READ = "xxxs:read";
const XXXS_WRITE = "xxxs:write";
```

### permissions
```sql
SELECT id,id_user,json_rw FROM `base_user_permissions` WHERE 1 AND id_user=1
UPDATE `base_user_permissions` SET json_rw = REPLACE(json_rw,'\n]',',\n"xxxs:read",\n"xxxs:write"\n]') WHERE id_user=1  
/*
"xxxs:read",
"xxxs:write"
 */
```
