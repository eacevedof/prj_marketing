<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
 */
if (is_null($result["permissions"])) return;
$textpermission = [
    "tr00" => __("Save"),
    "tr01" => __("Processing..."),
    "tr02" => __("Cancel"),
    "tr03" => __("Error"),
    "tr04" => __("<b>Data updated</b>"),

    "f00" => __("Nº"),
    "f01" => __("Code"),
    "f02" => __("User"),
    "f03" => __("Permissions JSON"),
];

$datapermission = [
    "id_user" => $result["id"] ?? "",

    "id" => $permissions["id"] ?? "",
    "uuid" => $permissions["uuid"] ?? "",
    "json_rw" => $permissions["json_rw"] ?? "[]",
];
?>
<div id="permissions" class="tab-pane">
  <form-user-permissions-update
      csrf=<?$this->_echo_js($csrf);?>

      useruuid="<?=$uuid?>"
      texts="<?$this->_echo_jslit($textpermission);?>"

      fields="<?$this->_echo_jslit($datapermission);?>"
  />
</div>