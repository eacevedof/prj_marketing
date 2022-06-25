<?php
use App\Shared\Infrastructure\Helpers\Views\PromotionCap\BusinessDataHelper as BH;
?>
<style>
/*
promotion-cap-style-rewrite.tpl
*/
body {
  font-size: 16px;
  font-family: "Roboto", "Helvetica Neue", "Helvetica", "Arial";
  margin: 0;
  padding: 0;
  text-align: center;
}
/*div wrapper*/
.wrapper {
  width: 90vw;
  display: inline-block;
  border: 1px solid #C2CCD1;
  border-radius: 25px;
}
.wrapper header{
  background-repeat: no-repeat;
  background-position: right;
  background-size: auto 100%;
  height: 6em;
<?=$bdhelp->get_style_header()?>
}
.wrapper header h2 {
  padding: 0;
  margin: 0;
  padding-top: 1em;
  padding-left: 1em;
  float: left;
}

.wrapper main{
  margin:0;
  padding:0;
  height: 80vh;
  background-repeat: repeat-x;
  background-position: center;
  background-size: auto 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
<?=$bdhelp->get_style_body()?>
}

.wrapper main section {
  /*
  border: 1px solid red;
   */
  margin: 0;
  padding: 0;

  background-repeat: no-repeat;
  background-position: center;
  background-size: cover;
<?php
BH::echo_style("background-color", $promotion["bgcolor"]);
BH::echo_style("background-image", $promotion["bgimage_lg"]);
?>
}

.div-promotion {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%,-50%);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.div-promotion h1 {
  margin-bottom: 1em;
}

.social-footer {
  padding: 1em;
  background: #8a8a8a;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-align-items: center;
  -ms-flex-align: center;
  align-items: center;
  -webkit-justify-content: space-between;
  -ms-flex-pack: justify;
  justify-content: space-between;
}

.social-footer .social-footer-icons ul {
  list-style-type: none;
  margin: 0;
  padding: 0;
  overflow: hidden;
}
.social-footer .social-footer-icons ul li {
  float: left;
}

.social-footer .social-footer-icons li:last-of-type {
  margin-right: 0;
}

.social-footer .social-footer-icons .fa {
  font-size: 1.3rem;
  color: #fefefe;
}

.social-footer .social-footer-icons .fa:hover {
  color: #4a4a4a;
  transition: color 0.3s ease-in;
}
</style>