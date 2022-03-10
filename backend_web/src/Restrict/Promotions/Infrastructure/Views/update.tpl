<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
 * @var string $h1
 * @var string $csrf
 */

$texts = [
    "tr00" => __("Save"),
    "tr01" => __("Processing..."),
    "tr02" => __("Cancel"),
    "tr03" => __("Error"),
    "tr04" => __("<b>Data updated</b>"),

    "f00" => __("Nº"),
    "f01" => __("Code"),
    "f02" => __("Owner"),
    "f03" => __("Timezone"),
    "f04" => __("External code"),
    "f05" => __("Description"),
    "f06" => __("Slug"),
    "f07" => __("Date from"),
    "f08" => __("Date to"),
    "f09" => __("Content"),
    "f10" => __("Bg color"),
    "f11" => __("Bg image xs"),
    "f12" => __("Bg image sm"),
    "f13" => __("Bg image md"),
    "f14" => __("Bg image lg"),
    "f15" => __("Bg image xl"),
    "f16" => __("Bg image xxl"),
    "f17" => __("Invested"),
    "f18" => __("Inv returned"),
    "f19" => __("Max. confirmed"),
    "f20" => __("Raffleable"),
    "f21" => __("Cumulative"),
    "f22" => __("Tags"),
    "f23" => __("Notes"),
    "f24" => __("Viewed"),
    "f25" => __("Subscribed"),
    "f26" => __("Confirmed"),
    "f27" => __("Executed"),
];

$result = [
    "id" => $result["id"],
    "uuid" => $result["uuid"],
    "id_owner" => $result["id_owner"],
    "id_tz" => $result["id_tz"],
    "code_erp" => $result["code_erp"],
    "description" => $result["description"],
    "slug" => $result["slug"],
    "date_from" => $result["date_from"],
    "date_to" => $result["date_to"],
    "content" => $result["content"],
    "bgcolor" => $result["bgcolor"],
    "bgimage_xs" => $result["bgimage_xs"],
    "bgimage_sm" => $result["bgimage_sm"],
    "bgimage_md" => $result["bgimage_md"],
    "bgimage_lg" => $result["bgimage_lg"],
    "bgimage_xl" => $result["bgimage_xl"],
    "bgimage_xxl" => $result["bgimage_xxl"],
    "invested" => $result["invested"],
    "returned" => $result["returned"],
    "max_confirmed" => $result["max_confirmed"],
    "is_raffleable" => $result["is_raffleable"],
    "is_cumulative" => $result["is_cumulative"],
    "tags" => $result["tags"],
    "notes" => $result["notes"]
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
          <a href="#main" data-bs-toggle="tab" class="active" aria-expanded="true">
            <span class="visible-xs">
              <i class="las la-promotion-circle tx-16 me-1"></i>
            </span>
            <span class="hidden-xs">
              <?=__("Promotion")?>
            </span>
          </a>
        </li>
        <li>
          <a href="#promotionui" data-bs-toggle="tab" aria-expanded="false">
            <span class="visible-xs"><i class="las la-images tx-15 me-1"></i></span>
            <span class="hidden-xs">
              <?=__("UI")?>
            </span>
          </a>
        </li>
      </ul>
    </div><!--nav-->

    <div class="tab-content border-start border-bottom border-right border-top-0 p-2 br-dark">
      <div id="main" class="tab-pane active">
        <form-promotion-update
          csrf=<?$this->_echo_js($csrf);?>

          texts="<?$this->_echo_jslit($texts);?>"

          fields="<?$this->_echo_jslit($result);?>"
        />
      </div>

      <div id="promotionui" class="tab-pane">
      </div>
    </div><!--tab-content-->

  </div><!--card-body-->
</div>
<script type="module" src="/assets/js/restrict/promotions/update.js"></script>