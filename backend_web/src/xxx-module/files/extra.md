### routes.php
```php
//@xxxs
["url"=>"/restrict/xxxs/info/:uuid","controller"=>"App\Restrict\Xxxs\Infrastructure\Controllers\XxxsInfoController", "method"=>"info"],
["url"=>"/restrict/xxxs/create","controller"=>"App\Restrict\Xxxs\Infrastructure\Controllers\XxxsInsertController", "method"=>"create"],
["url"=>"/restrict/xxxs/insert","controller"=>"App\Restrict\Xxxs\Infrastructure\Controllers\XxxsInsertController", "method"=>"insert", "allowed"=>["post"]],
["url"=>"/restrict/xxxs/edit/:uuid","controller"=>"App\Restrict\Xxxs\Infrastructure\Controllers\XxxsUpdateController", "method"=>"edit"],
["url"=>"/restrict/xxxs/update/:uuid","controller"=>"App\Restrict\Xxxs\Infrastructure\Controllers\XxxsUpdateController", "method"=>"update", "allowed"=>["put"]],
["url"=>"/restrict/xxxs/delete/:uuid","controller"=>"App\Restrict\Xxxs\Infrastructure\Controllers\XxxsDeleteController", "method"=>"remove", "allowed"=>["delete"]],
["url"=>"/restrict/xxxs/undelete/:uuid","controller"=>"App\Restrict\Xxxs\Infrastructure\Controllers\XxxsDeleteController", "method"=>"undelete", "allowed"=>["patch"]],
["url"=>"/restrict/xxxs/?int:page","controller"=>"App\Restrict\Xxxs\Infrastructure\Controllers\XxxsSearchController", "method"=>"index"],
["url"=>"/restrict/xxxs/search","controller"=>"App\Restrict\Xxxs\Infrastructure\Controllers\XxxsSearchController", "method"=>"search"],
```
### ModuleService.php
```php
"xxxs" => [
    "title" => __("Xxxs"),
    "icon" => "la-gift",
    "actions" => [
        "search" => [
            "url" => "/restrict/xxxs",
        ],
        "create" => [
            "url" => "/restrict/xxxs/create",
        ],
    ]
],
```

### translations
```po
msgid "New xxx"
msgstr "New xxx"

msgid "Xxx info"
msgstr "Xxx info"

msgid "Xxxs"
msgstr "Xxxs"

msgid "Xxx"
msgstr "Xxx"

msgid "xxxs"
msgstr "xxxs"

msgid "xxx"
msgstr "xxx"

%PO_KEYS%
```

### policies
```php
//UserPolicyType.php
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
