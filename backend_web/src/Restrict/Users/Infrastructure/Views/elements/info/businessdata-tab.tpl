<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $result
*/
use App\Restrict\Users\Domain\Enums\UserProfileType;
if (is_null($result["businessdata"]) ||
    $result["user"]["id_profile"] != UserProfileType::BUSINESS_OWNER
)
  return;
?>
<li>
  <a href="#businessdata" data-bs-toggle="tab" aria-expanded="false">
    <span class="visible-xs"><i class="las la-images tx-15 me-1"></i></span>
    <span class="hidden-xs"><?=__("Business data")?></span>
  </a>
</li>