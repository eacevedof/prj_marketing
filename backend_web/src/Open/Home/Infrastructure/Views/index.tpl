<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 */
?>
<link rel="stylesheet" href="/themes/mypromos/css/index.css" type="text/css" media="all" />
<main class="main-grid">
  <div class="div-wave-top">
    <svg viewBox="25 0 550 150" preserveAspectRatio="none" style="height: 300%; width: 110%;">
      <path d="M495 0H6.10352e-05V59C6.10352e-05 59 11.6918 42.2885 54.5 41.5C100.525 40.6522 171.967 19.5 218 19.5C257.5 19.5 329.036 29.6378 372.5 19.5C435.899 4.71257 495 0 495 0Z" fill="#3F3D56"/>
      <path d="M495 0H0V58C0 58 31.6918 32.7885 74.5 32C120.525 31.1522 140.967 45.5 187 45.5C226.5 45.5 303.536 28.1378 347 18C410.399 3.21257 495 0 495 0Z" fill="white" fill-opacity="0.5"/>
      <path d="M495 0H0V58C0 58 37.1918 25.2885 80 24.5C126.025 23.6522 166.967 36.5 213 36.5C252.5 36.5 295.536 25.1378 339 15C402.399 0.212575 495 0 495 0Z" fill="white"/>
    </svg>
  </div>

  <nav class="nav-grid center-x">
    <figure>
      <img src="/themes/mypromos/images/provider-xxx-logo-orange.svg" class="nav-icon">
    </figure>
    <input type="checkbox" id="chk-hamburguer" autocomplete="off" />
    <ul class="nav-list-ul">
      <li class="nav-li-item"><a href="javascript: void(0)" class="nav-li-item-a" lang="EN">EN</a></li>
      <li class="nav-li-item"><a href="javascript: void(0)" class="nav-li-item-a" lang="ES">ES</a></li>
    </ul>
    <label for="chk-hamburguer" class="label-hamburger">
      <figure class="nav-menu-figure">
        <img src="/themes/mypromos/images/icon-hamburger.svg" class="nav-icon">
      </figure>
    </label>
  </nav>

  <section class="section-grid center-x">
    <div class="div-texts">
      <h1><?= __("My promotions");?></h1>
      <h2><?= __("Let us grow your marketing campaigns!.");?></h2>
      <p>
        <?= __("&ldquo;My promotions&ldquo; allows you to configure promotional landing pages with different acquisition data input.");?>
      </p>
      <p>
        <?= __("Once your client got subscribed it will receive a promotion code to be showed at your business.");?>
      </p>
      <p>
        <?= __("All your confirmed subscribers will accumulate points and you can handle these in order to increase customer loyalty.  For example by remarketing strategy.");?>
      </p>
      <button id="btn-cta" class="button-cta"><?= __("Join us!");?></button>
    </div>

    <figure class="figure-home-hero">
      <img src="/themes/mypromos/images/home-hero.svg" class="figure-home-hero">
    </figure>
  </section>

  <div class="div-wave-bottom">
    <svg viewBox="0 -72 500 148" preserveAspectRatio="none" style="height: 100%; width: 100%;">
      <path d="M0 0.503906C49.5 57.7967 91 12.9233 165.5 29.8567C240 46.79 197.5 18.4267 325 29.8567C452.5 41.2867 441.5 10.8067 500 10.8067V67.5333H0V0.503906Z" fill="#CCCCCC" fill-opacity="0.49"/>
      <path d="M0 8.97058C139 101.865 126 -6.62098 260 29.8567C438.5 59.0667 455 17.1302 500 0.646667V76H0V8.97058Z" fill="#CCCCCC"/>
      <path d="M0 13.3467C139 80.6559 116 11.0538 250 37.4846C433.5 63.5143 458 25.422 500 13.3467V76.8467H0V13.3467Z" fill="white"/>
    </svg>
  </div>
  <?php
  $this->_element("open/mypromos/elem-footer");
  ?>
</main>
<?php
$this->_element_view("form-home-contact-send");
?>
<script type="module">
const get_cookie = name => {
  const parts = document.cookie.split("; ")
  const obj = parts
      .map(str => str.split("="))
      .filter(ar => ar[0] === name)
      .map(ar => ar[1])
  return obj.length ? obj[0] : null
}
const set_cookie = (name, value, days=1) => {
  const pieces = [
    `${name}=${value?.toString() || ""}`
  ]

  if (days) {
    const date = new Date()
    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000))
    pieces.push(`expires=${date.toUTCString()}`)
  }

  pieces.push("path=/")
  document.cookie = pieces.join("; ")
}
const body = document.querySelector("body")
const dialog = document.querySelector("dialog")
const cancel = document.querySelector("#button-exit")
const show = document.querySelector("#btn-cta")

show.addEventListener("click", () => {
  body.style.overflow = "hidden"
  dialog.showModal()
})

if (cancel)
  cancel.addEventListener("click", () => {
    body.style.overflow = "auto"
    dialog.close()
  })

const langs = Array.from(document.querySelectorAll("a[lang]"))
langs.forEach(anchor => anchor.addEventListener("click", ()=>{
  set_cookie("lang", anchor.lang)
  location.reload()
}))

//show.click()
window.addEventListener("DOMContentLoaded", ()=>{
  let lang = get_cookie("lang")
  if (!lang) lang = "es"
  set_cookie("lang", lang)

  const active = "nav-li-item-active"
  langs.forEach(anchor => {
    const li = anchor.parentNode
    li.classList.remove("nav-li-item-active")
    if (anchor?.lang?.toLowerCase() === lang.toString().toLowerCase())
      li.classList.add(active)
  })
})
</script>