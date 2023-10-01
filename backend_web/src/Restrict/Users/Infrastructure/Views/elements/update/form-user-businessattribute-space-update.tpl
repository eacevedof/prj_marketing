<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
 */
use App\Shared\Infrastructure\Helpers\RoutesHelper as Routes;
if (is_null($result["businessattributespace"])) return;

$idUser = $result["user"]["id"];
$businessattributespace = $result["businessattributespace"];
$url = Routes::getUrlByRouteName("businessattributespace.update", ["uuid"=>$uuid]);

$spaceurl = ($slug = $result["businessdata"]["slug"] ?? "")
    ? Routes::getUrlByRouteName("business.space", ["businessSlug" =>$slug])
    : "";
$texts = [
    "tr00" => __("Save"),
    "tr01" => __("Processing..."),
    "tr02" => __("Cancel"),
    "tr03" => __("Error"),
    "tr04" => __("<b>Data updated</b>"),

    "f00" => __("tr_id"),
    "f01" => __("tr_id_user"),
    "f02" => __("tr_attr_key"),
    "f03" => __("tr_attr_value"),
];
?>
<div id="businessattributespace" class="tab-pane">
    <form-user-businessattribute-space-update
        csrf=<?php $this->_echoJs($csrf);?>
        url="<?php $this->_echo($url);?>"
        spaceurl="<?php $this->_echo($spaceurl);?>"
        texts="<?php $this->_echoJsLit($texts);?>"
        fields="<?php $this->_echoJsLit($businessattributespace);?>"
    />
</div>
<script type="module" src="/assets/js/restrict/users/businessdata/form-user-businessattribute-space-update.js"></script>
