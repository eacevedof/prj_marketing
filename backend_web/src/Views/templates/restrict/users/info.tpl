<?php
/**
 * @var \App\Views\AppView $this
 * @var string $h1
 * @var ?string $uuid
 * @var array $result
 */
?>
<div>
    <div class="card-header">
      <h4 class="card-title mb-1"><?=$h1?></h4>
    </div>
    <div class="card-body pt-0">
        <div class="tabs-menu ">
            <ul class="nav nav-tabs profile navtab-custom panel-tabs">
                <li>
                    <a href="#profile" data-bs-toggle="tab" class="active" aria-expanded="true">
                        <span class="visible-xs">
                            <i class="las la-user-circle tx-16 me-1"></i>
                        </span>
                        <span class="hidden-xs">Profile</span>
                    </a>
                </li>
                <li>
                    <a href="#permissions" data-bs-toggle="tab" aria-expanded="false">
                        <span class="visible-xs"><i class="las la-images tx-15 me-1"></i></span>
                        <span class="hidden-xs"><?=__("Permissions")?></span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="tab-content border-start border-bottom border-right border-top-0 p-4 br-dark">
            <div class="tab-pane active" id="profile">
                <?php
                $profile = $result["user"] ?? [];
                ?>
                <ol>
                    <li><b><?=__("Nº")?>:</b>&ensp;<span><?=$profile["id"] ?? ""?></span></li>
                    <li><b><?=__("Code")?>:</b>&ensp;<span><?=$profile["uuid"] ?? ""?></span></li>
                    <li><b><?=__("Full name")?>:</b>&ensp;<span><?=$profile["fullname"] ?? ""?></span></li>
                    <li><b><?=__("Email")?>:</b>&ensp;<span><?=$profile["email"] ?? ""?></span></li>
                    <li><b><?=__("Phone")?>:</b>&ensp;<span><?=$profile["phone"] ?? ""?></span></li>
                    <li><b><?=__("Country")?>:</b>&ensp;<span><?=$profile["e_country"] ?? ""?></span></li>
                    <li><b><?=__("Language")?>:</b>&ensp;<span><?=$profile["e_language"] ?? ""?></span></li>
                    <li><b><?=__("Address")?>:</b>&ensp;<span><?=$profile["address"] ?? ""?></span></li>
                    <li><b><?=__("Birthdate")?>:</b>&ensp;<span><?=str_replace(" 00:00:00","",$profile["birthdate"] ?? "")?></span></li>
                    <li><b><?=__("Profile")?>:</b>&ensp;<span><?=$profile["e_profile"] ?? ""?></span></li>
                    <li><b><?=__("Superior")?>:</b>&ensp;<span><?=$profile["e_parent"] ?? ""?></span></li>
                </ol>
            </div>
            <div class="tab-pane" id="permissions">
                <ol>
            <?php
            $permissions = $result["permissions"] ?? [];
            foreach ($permissions as $field => $value):
            ?>
                <li><span><?$this->_echo($value);?></span></li>
            <?php
            endforeach;
            ?>
                </ol>
            </div>
        </div>

    </div>
</div>
