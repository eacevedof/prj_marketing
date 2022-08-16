<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var array $authuser
 * @var array $topmenu
 */
use App\Shared\Infrastructure\Helpers\Views\EnvIconHelper;
$logourl = EnvIconHelper::icon_restrict();
$authuser = $authuser ?? [];
if ($authuser):
?>
<style>
.div-container *{
  margin: 0;
  padding: 0;
}

.div-container {
  display: flex;
  z-index: 10;
  position: sticky;
  width: 100%;
}

.nav-flex {
  display: flex;
  width: 100%;
  height: 4rem;
  z-index: 10;
  background: #fff;
  border-top: 0;
  margin-top: 0;
  border-bottom: 1px solid #d5d8e2;
  box-shadow: 0 1px 15px 1px #c0c0c7;
}

.nav-flex .item-icon {
  height: 45px;
  width: 150px;
}

.item-icon img {
  height: 60px;
  width: 150px;
}

.nav-flex .item-menu {
  flex-grow: 4;
  display: flex;
  justify-content: center;
}

.item-menu .ul-menu {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
}

.nav-flex .item-hamburger {
  flex-grow: 1;
  display: flex;
  justify-content: flex-end;
}

.nav-flex .ul-menu {
  display: flex;
}

.nav-flex .ul-menu li {
  list-style: none;
  font-weight: bold;
  text-align: center;
  display: block;
  padding: 0;
  margin: 0;
}

.nav-flex .ul-menu li a {
  text-decoration: none;
  transition: .5s all ease;
  display: block;
  margin: 0px 2px;
  border-radius: 0px;
  position: relative;
  color: #5b6e88;
  padding: 15px 12px 15px 12px;
  font-weight: 400;
}

.nav-flex .ul-menu li a:hover {
  color: #0162e8;
  background: #fff;
  cursor:pointer;
}

.ul-menu li ul {
  display:none;
  position:absolute;
  min-width:140px;
  color: #0162e8;
  background: #fff;
}

.ul-menu li:hover > ul {
  display: block;
  position: absolute;
  top: auto;
  z-index: 1000;
  margin: 0px;
  padding: 5px;
  min-width: 190px;
  background-color: #fff;
  box-shadow: 0 8px 16px 0 rgb(230 233 239 / 40%);
  border: 1px solid #ecf0fa;
}

.ul-menu li:hover > ul li {
  position: relative;
  margin: 0px;
  padding: 0px;
  padding-top: 5px;
  padding-bottom: 5px;
  display: block;
}

.ul-menu li:hover > ul li a {
  background-image: none;
  color: #6d7790;
  border-right: 0 none;
  text-align: left;
  display: block;
  line-height: 22px;
  padding: 8px 35px;
  text-transform: none;
  letter-spacing: normal;
  border-right: 0px solid;
}


#chk-hamburger {
  display: none;
}

.lbl-hamburger {
  display: none;
  font-size: 1.8rem;
  padding-right: 1rem;
}
.lbl-hamburger i {
  color: #5b6e88;
}

@media(max-width: 960px) {
  .div-container {
    position: fixed;
  }

  .lbl-hamburger {
    display: block;
  }

  .nav-flex .item-menu {
    flex-direction: column;
    height: 100vh;
    width: 40%;
    left:-100%;
    position: fixed;
    font-size: 1rem;
    transition: .5s all ease;
    box-shadow: 1px  0 15px 1px #c0c0c7;
    background-color: #fff;
    justify-content: normal;
  }

  .nav-flex .ul-menu {
    margin-top: 10px;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: flex-start;
  }

  .nav-flex .ul-menu li {
    width: 100%;
    border-top: 1px dashed #5b6e88;
    border-bottom: 1px solid #5b6e88;
    padding-top: 10px;
    padding-bottom: 10px;
    padding-left: 10px;
    text-align: left;
  }

  .ul-menu li:hover > ul li {
    border-top: 0;
    border-bottom: 1px solid #C2CCD1;
  }
  /*
  cambia el contenido del icono hamburger por una x si el check esta marcado
  */
  #chk-hamburger:checked~.lbl-hamburger i:before {
    color: #5b6e88;
    /*icono x*/
    content: "\f00d";
  }
}
</style>

<!--Horizontal-main auth-->
<div class="div-container">
  <nav class="nav-flex">
    <span class="item-icon">
      <a href="/restrict">
        <img src="<?=$logourl?>"/>
      </a>
    </span>

    <span class="item-menu">
      <ul class="ul-menu">
        <li>
          <a href="#">
            <svg xmlns="http://www.w3.org/2000/svg" height="17" viewBox="0 0 24 24">
              <path d="M0 0h24v24H0V0z" fill="none"/><path d="M5 5h4v6H5zm10 8h4v6h-4zM5 17h4v2H5zM15 5h4v2h-4z" opacity=".3"/>
              <path d="M3 13h8V3H3v10zm2-8h4v6H5V5zm8 16h8V11h-8v10zm2-8h4v6h-4v-6zM13 3v6h8V3h-8zm6 4h-4V5h4v2zM3 21h8v-6H3v6zm2-4h4v2H5v-2z"/>
            </svg>
            <?=__("Modules")?><i class="fe fe-chevron-down horizontal-icon"></i>
          </a>
          <ul>
            <?php
            foreach ($topmenu as $config):
              $title = $config["title"] ?? "";
              $url = $config["search"] ?? "";
              ?>
              <li><a href="<?=$url?>"><?=$title?></a></li>
            <?php
            endforeach;
            ?>
          </ul>
        </li>

        <li>
          <a href="/restrict/logout">
            <svg xmlns="http://www.w3.org/2000/svg" height="17" viewBox="0 0 24 24" >
              <path d="M0 0h24v24H0V0z" fill="none"/><path d="M5 5h4v6H5zm10 8h4v6h-4zM5 17h4v2H5zM15 5h4v2h-4z" opacity=".3"/>
              <path d="M3 13h8V3H3v10zm2-8h4v6H5V5zm8 16h8V11h-8v10zm2-8h4v6h-4v-6zM13 3v6h8V3h-8zm6 4h-4V5h4v2zM3 21h8v-6H3v6zm2-4h4v2H5v-2z"/>
            </svg>
            <?=__("Logout")?>
          </a>
        </li>
      </ul>
    </span>

    <span class="item-hamburger">
      <input type="checkbox" id="chk-hamburger" autocomplete="off" />
      <label for="chk-hamburger" class="lbl-hamburger">
        <i class="fa fa-bars"></i>
      </label>
    </span>
  </nav>
</div>
<!--Horizontal-main auth-->
<script type="module">
const $chkhamburger = document.getElementById("chk-hamburger")

if ($chkhamburger) {
  $chkhamburger.addEventListener("change", (e) => {
    const $itemmenu = document.querySelector(".item-menu")
    if (!$itemmenu) return
    //aqui hay animación
    $itemmenu.style.left = "-100%"
    if (e.target.checked) $itemmenu.style.left = 0
  })
}
</script>
<?php
endif;
?>