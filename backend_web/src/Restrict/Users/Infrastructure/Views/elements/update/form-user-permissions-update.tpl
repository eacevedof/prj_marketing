<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
 */
if (is_null($result["permissions"])) return;

$idUser = $result["user"]["id"];
$permissions = $result["permissions"];

$texts = [
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

$permissions = [
    "id_user" => $idUser,

    "id" => $permissions["id"] ?? "",
    "uuid" => $permissions["uuid"] ?? "",
    "json_rw" => $permissions["json_rw"] ?? "[]",
];
?>
<div id="permissions" class="tab-pane">
  <form-user-permissions-update
      csrf=<?php $this->_echoJs($csrf);?>

      useruuid="<?=$uuid?>"
      texts="<?php $this->_echoJsLit($texts);?>"

      fields="<?php $this->_echoJsLit($permissions);?>"
  />
</div>
<script type="module" src="/assets/js/restrict/users/permissions/form-user-permissions-update.js"></script>