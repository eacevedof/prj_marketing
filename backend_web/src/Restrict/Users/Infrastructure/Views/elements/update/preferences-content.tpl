<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
 */
if (is_null($result["preferences"])) return;
$preferences = $result["preferences"];
$iduser = $result["user"]["id"];

$texts = [
  "tr00" => __("Save"),
  "tr01" => __("Processing..."),
  "tr02" => __("Cancel"),
  "tr03" => __("Error"),
  "tr04" => __("<b>Data updated</b>"),

  "f00" => __("tr_id"),
  "f01" => __("tr_id_user"),
  "f02" => __("tr_pref_key"),
  "f03" => __("tr_pref_value"),
];

$preferences = array_map(function(array $row) {
  return [
    "id" => $row["id"],
    "pref_key" => $row["pref_key"],
    "pref_value" => $row["pref_value"],
  ];
}, $preferences);
?>
<div id="preferences" class="tab-pane">
  <form-user-preferences-update
      csrf=<?$this->_echo_js($csrf);?>

      useruuid="<?=$uuid?>"
      texts="<?$this->_echo_jslit($texts);?>"

      fields="<?$this->_echo_jslit($preferences);?>"
  />
</div>
