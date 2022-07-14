<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var string $pagetitle
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link rel="icon" href="./images/provider-xxx-logo-orange.svg"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./themes/mypromo/css/global.css" type="text/css" media="all" />
  <link rel="stylesheet" href="./themes/mypromo/css/sp-main.css" type="text/css" media="all" />
  <link rel="stylesheet" href="./themes/mypromo/css/sp-wave-top.css" type="text/css" media="all" />
  <link rel="stylesheet" href="./themes/mypromo/css/sp-nav.css" type="text/css" media="all" />
  <link rel="stylesheet" href="./themes/mypromo/css/sp-scrums.css" type="text/css" media="all" />
  <link rel="stylesheet" href="./themes/mypromo/css/sp-section.css" type="text/css" media="all" />
  <link rel="stylesheet" href="./themes/mypromo/css/footer.css" type="text/css" media="all" />
  <link rel="stylesheet" href="./themes/mypromo/css/sp-animation.css" type="text/css" media="all" />
  <link rel="stylesheet" href="./themes/mypromo/css/error.css" type="text/css" media="all" />
  <title>Error</title>
</head>
<body>
<main class="main-grid">
  <div class="div-wave-top">
    <svg viewBox="25 0 550 150" preserveAspectRatio="none" style="height: 300%; width: 110%;">
      <path d="M495 0H6.10352e-05V59C6.10352e-05 59 11.6918 42.2885 54.5 41.5C100.525 40.6522 171.967 19.5 218 19.5C257.5 19.5 329.036 29.6378 372.5 19.5C435.899 4.71257 495 0 495 0Z" fill="#3F3D56"/>
      <path d="M495 0H0V58C0 58 31.6918 32.7885 74.5 32C120.525 31.1522 140.967 45.5 187 45.5C226.5 45.5 303.536 28.1378 347 18C410.399 3.21257 495 0 495 0Z" fill="white" fill-opacity="0.5"/>
      <path d="M495 0H0V58C0 58 37.1918 25.2885 80 24.5C126.025 23.6522 166.967 36.5 213 36.5C252.5 36.5 295.536 25.1378 339 15C402.399 0.212575 495 0 495 0Z" fill="white"/>
    </svg>
  </div>

  <nav class="nav-flex center-x">
    <figure>
      <img id="top-mark" src="./images/logo-account-yyy.png" class="nav-icon">
    </figure>
  </nav>

  <section class="section-scrumbs center-x">
    <ul>
      <li><a href="./">&#8962; Home</a></li>
      <li><a href="#">Promotion AAA BBB</a></li>
    </ul>
  </section>

  <section class="section-grid center-x">
    <div class="div-texts">
      <img src="./images/icon-error.svg" class="icon">
      <h1>No te has inscrito bien</h1>
      <h2>Haz caso a V. Llosa</h2>
      <p>
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce tempus porta tortor non luctus. Ut ligula nisi, pulvinar et dolor id, blandit laoreet purus. Duis eu viverra nisi. Nulla risus felis, laoreet nec ligula ut, euismod tempus tortor. Nunc tellus metus, tincidunt ut blandit a, finibus a purus. Aenean auctor nibh enim, et pretium mauris consequat ac. Etiam nec ligula eget neque varius hendrerit. Cras lacus elit, pharetra id interdum nec, euismod nec est. Nunc vel tempor urna, nec pellentesque nisi. Ut placerat magna quis facilisis malesuada. Nam egestas leo vitae rhoncus dignissim. Etiam non lacinia enim. Etiam dictum enim quis magna condimentum interdum.
      </p>
      <h2>1. Titular del Portal</h2>
      <p>
        Pellentesque sapien dolor, pellentesque id sem ut, tincidunt sodales leo. Sed accumsan tellus vel eros luctus, eget aliquam felis ultrices. Fusce finibus ultrices magna, nec feugiat augue elementum et. In consectetur odio sed vehicula porttitor. Aenean justo magna, vestibulum vitae suscipit at, accumsan at nisi. Fusce et posuere dolor. Donec elementum elit venenatis arcu aliquet, sit amet commodo odio rutrum. In quis eros leo. Curabitur consectetur convallis tortor. Proin sed turpis et risus luctus facilisis. Aliquam tristique libero ut laoreet sagittis. Etiam volutpat interdum tortor nec aliquam. Integer rhoncus arcu quam.
      </p>
      <h2>1. Titular del Portal</h2>
      <p>
        Suspendisse interdum egestas dui auctor congue. Nunc maximus imperdiet nisl quis tincidunt. Nullam eget orci ullamcorper quam porttitor vulputate at a dolor. Sed efficitur, mauris et consequat suscipit, dui elit sagittis felis, eget iaculis tortor libero in dui. Duis vitae tortor ac dui aliquet hendrerit. Aliquam suscipit nec risus quis vestibulum. Integer eu enim convallis eros pellentesque hendrerit nec vitae erat. Integer pulvinar ipsum massa, in tempor purus facilisis eget. Phasellus finibus ex in felis bibendum pellentesque. Donec ut ultricies dui.
      </p>
    </div>
    <?php
    $this->_template();
    ?>
  </section>
  <?php
  $this->_element("open/elem-footer");
  ?>
</main>
<?php
$this->_element("open/elem-animation");
?>
<div id="div-totop" class="div-totop">
  <a href="#top-mark">
    <svg width="60" height="60" viewBox="0 0 29 30" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M0 8.5H20V21H8V8.5Z" fill="black"/>
      <path d="M14.1417 4.99484C8.62572 4.99544 4.13776 9.4834 4.13644 15.0001C4.13725 20.516 8.62609 25.0049 14.1421 25.0057C19.658 25.0051 24.146 20.5171 24.1473 15.0005C24.1465 9.48307 19.6584 4.99494 14.1417 4.99484ZM19.1442 15.0004L15.1422 14.9996L15.143 20.0019L13.1423 20.0019L13.1415 14.9995L9.13951 14.9988L14.1411 9.9972L19.1442 15.0004Z" fill="#FFFF00FF"/>
    </svg>
  </a>
</div>
<script type="module">
  const ishow = 50
  const $divtop = document.getElementById("div-totop")
  window.addEventListener("scroll", function() {
    if (!$divtop) return
    $divtop.style.display = "none"
    if(document.documentElement.scrollTop > ishow){
      $divtop.style.display = "inherit"
    }
  })
</script>
<script>
  const animation = document.querySelector(".ul-circles")
  animation.style.height = document.body.offsetHeight.toString().concat("px")
</script>
</body>
</html>
<!-- /error.tpl -->