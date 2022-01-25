<?php
/**
 * @var App\Views\AppView $this
 * @var array $result
 * @var string $h1
 * @var string $csrf
 */

$texts = [
  "tr00" => __("send"),
  "tr01" => __("Sending..."),
  "tr02" => __("Cancel"),
  "tr03" => __("Error"),
  "tr04" => __("<b>Data updated</b>"),

  "f00" => __("Nº"),
  "f01" => __("Cod. Promo"),
  "f02" => __("Owner"),
  "f03" => __("External code"),
  "f04" => __("Description"),
  "f05" => __("Slug"),
  "f06" => __("Content"),
  "f07" => __("Type"),
  "f08" => __("Date from"),
  "f09" => __("Date to"),
  "f10" => __("Url social"),
  "f11" => __("Url design"),
  "f12" => __("Enabled"),
  "f13" => __("Invested"),
  "f14" => __("Inv returned"),
  "f15" => __("Notes"),
];

$result = [
  "id" => $result["id"],
  "uuid" => $result["uuid"],
  "id_owner" => $result["id_owner"],
  "code_erp" => $result["code_erp"],
  "description" => $result["description"],
  "slug" => $result["slug"],
  "content" => $result["content"],
  "id_type" => $result["id_type"],
  "date_from" => $result["date_from"],
  "date_to" => $result["date_to"],
  "url_social" => $result["url_social"],
  "url_design" => $result["url_design"],
  "is_active" => $result["is_active"],
  "invested" => $result["invested"],
  "returned" => $result["returned"],
  "notes" => $result["notes"],
];
?>
<div class="modal-form">
  <div class="card-header">
    <h4 class="card-title mb-1"><?=$h1?></h4>
  </div>
  <div class="card-body p-2 pt-0">
    <div class="tabs-menu ">
      <ul class="nav nav-tabs profile navtab-custom panel-tabs">
        <li>
          <a href="#tab-1" data-bs-toggle="tab" class="active" aria-expanded="true">
            <span class="visible-xs">
              <i class="las la-promotion-circle tx-16 me-1"></i>
            </span>
            <span class="hidden-xs">
              <?=__("tr_tab_1")?>
            </span>
          </a>
        </li>
          <li>
            <a href="#tab-2" data-bs-toggle="tab" aria-expanded="false">
              <span class="visible-xs"><i class="las la-images tx-15 me-1"></i></span>
              <span class="hidden-xs">
                <?=__("tr_tab_2")?>
              </span>
            </a>
          </li>
      </ul>
    </div><!--nav-->

    <div class="tab-content border-start border-bottom border-right border-top-0 p-2 br-dark">
      <div class="tab-pane active" id="profile">
        <form-promotion-edit
          csrf=<?$this->_echo_js($csrf);?>

          texts="<?$this->_echo_jslit($texts);?>"

          fields="<?$this->_echo_jslit($result);?>"
        />
      </div>

      <div class="tab-pane" id="permissions">

      </div>
    </div><!--tab-content-->

  </div><!--card-body-->
</div>
<script type="module" src="/assets/js/restrict/promotions/update.js"></script>