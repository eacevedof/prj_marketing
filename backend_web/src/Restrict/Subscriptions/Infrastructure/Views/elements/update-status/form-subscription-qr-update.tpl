<?php
use App\Shared\Infrastructure\Helpers\RoutesHelper as Routes;
$texts = [
  "tr00" => __("Save"),
  "tr01" => __("Processing..."),
  "tr02" => __("Cancel"),
  "tr03" => __("Error"),
  "tr04" => __("<b>Data updated</b>"),

  "f00" => "",
  "f01" => __("Notes"),
];

$puturl = Routes::url("qr.updatestatus");
?>
<script async="false" type="module">
import { BarcodeDetectorPolyfill } from "https://cdn.jsdelivr.net/npm/@undecaf/barcode-detector-polyfill@0.9.11/dist/main.js";
(function () {
  if ("BarcodeDetector" in window) return
  window["BarcodeDetector"] = BarcodeDetectorPolyfill
})()
//alert("module false")
//alert(window["BarcodeDetector"])
</script>

<div id="main" class="tab-pane active">
  <h4><?=__("Voucher QR code validation")?></h4>
  <form-subscription-qr-update
      texts="<?php $this->_echo_jslit($texts);?>"
      url="<?php $this->_echo($puturl)?>"
  />
  <script type="module" src="/assets/js/restrict/subscriptions/form-subscription-qr-update.js"></script>
</div>

