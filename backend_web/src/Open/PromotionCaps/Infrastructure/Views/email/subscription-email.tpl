<?php
/**
 * @var array $data
 */
$hello = $data["username"] ?: $data["email"];
?>
<!DOCTYPE html>
<html lang="en" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:v="urn:schemas-microsoft-com:vml">
<head>
  <title></title>
  <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />
  <!--[if mso]><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch><o:AllowPNG/></o:OfficeDocumentSettings></xml><![endif]-->
  <!--[if !mso]><!-->
  <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet" type="text/css" />
  <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" type="text/css" />
  <!--<![endif]-->
<style>
* {
  box-sizing: border-box;
}

body {
  margin: 0;
  padding: 0;
}

a[x-apple-data-detectors] {
  color: inherit !important;
  text-decoration: inherit !important;
}

#MessageViewBody a {
  color: inherit;
  text-decoration: none;
}

p {
  line-height: inherit
}

.desktop_hide,
.desktop_hide table {
  mso-hide: all;
  display: none;
  max-height: 0px;
  overflow: hidden;
}

@media (max-width:820px) {
  .desktop_hide table.icons-inner {
    display: inline-block !important;
  }

  .icons-inner {
    text-align: center;
  }

  .icons-inner td {
    margin: 0 auto;
  }

  .image_block img.big,
  .row-content {
    width: 100% !important;
  }

  .mobile_hide {
    display: none;
  }

  .stack .column {
    width: 100%;
    display: block;
  }

  .mobile_hide {
    min-height: 0;
    max-height: 0;
    max-width: 0;
    overflow: hidden;
    font-size: 0px;
  }

  .desktop_hide,
  .desktop_hide table {
    display: table !important;
    max-height: none !important;
  }
}
</style>
</head>

<body style="background-color: #fff; margin: 0; padding: 0; -webkit-text-size-adjust: none; text-size-adjust: none;">
<table appx="t1" border="0" cellpadding="0" cellspacing="0" class="nl-container" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #fff;" width="100%">
  <tbody>
  <tr>
    <td>
      <table appx="t2" align="center" border="0" cellpadding="0" cellspacing="0" class="row row-1" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
        <tbody>
        <tr>
          <td>
            <table appx="t3" align="center" border="0" cellpadding="0" cellspacing="0" class="row-content stack"
                   role="presentation"
                   style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-position: top center; color: #000000; background-color: #000000; width: 800px;"
                   width="800">
              <tbody>
              <tr>
                <td class="column column-1" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; padding-top: 5px; padding-bottom: 5px; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;" width="100%">
                  <table appx="t4" border="0" cellpadding="0" cellspacing="0" class="image_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
                    <tr>
                      <td style="width:100%;padding-right:0px;padding-left:0px;">
                        <div align="center" style="line-height:10px">
                          <img alt="Alternate text" src="<?=$data["businesslogo"]?>" style="display: block; height: auto; border: 0; width: 160px; max-width: 100%;" title="<?=$data["business"]?>" width="160" />
                        </div>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              </tbody>
            </table>
          </td>
        </tr>
        </tbody>
      </table>
      <table appx="t5" align="center" border="0" cellpadding="0" cellspacing="0" class="row row-2" role="presentation"
             style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
        <tbody>
        <tr>
          <td>
            <table appx="t6" align="center" border="0" cellpadding="0" cellspacing="0" class="row-content stack"
                   role="presentation"
                   style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-position: top center; color: #000000; background-color: #0c2f5d; width: 800px;"
                   width="800">
              <tbody>
              <tr>
                <td class="column column-1"
                    style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; padding-top: 5px; padding-bottom: 0px; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;"
                    width="100%">
                  <table appx="t7" border="0" cellpadding="0" cellspacing="0" class="text_block" role="presentation"
                         style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;" width="100%">
                    <tr>
                      <td style="padding-bottom:5px;padding-left:10px;padding-right:10px;padding-top:5px;">
                        <div style="font-family: Tahoma, Verdana, sans-serif">
                          <div class="txtTinyMce-wrapper"
                               style="font-size: 12px; font-family: 'Lato', Tahoma, Verdana, Segoe, sans-serif; mso-line-height-alt: 14.399999999999999px; color: #ffffff; line-height: 1.2;">
                            <p style="margin: 0; font-size: 38px; text-align: left;"><span
                                      style="font-size:30px;"><strong><?=__("Hello {0}!!", $hello) ?></strong></span></p>
                          </div>
                        </div>
                      </td>
                    </tr>
                  </table>
                  <table appx="t8" border="0" cellpadding="10" cellspacing="0" class="text_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;" width="100%">
                    <tr>
                      <td>
                        <div style="font-family: Tahoma, Verdana, sans-serif">
                          <div class="txtTinyMce-wrapper"
                               style="font-size: 12px; font-family: 'Lato', Tahoma, Verdana, Segoe, sans-serif; mso-line-height-alt: 14.399999999999999px; color: #ffffff; line-height: 1.2;">
                            <p style="margin: 0; font-size: 17px; text-align: left;">
                              <span style="font-size:17px;">
                                <?=__("You have subscribed to promotion: <strong>&ldquo;{0}&rdquo;</strong> published by", $data["promotion"])?>
                                <span style="color:#ffffff;">
                                  <strong><a href="<?=$data["businessurl"]?>" rel="noopener" style="text-decoration:none;color:#ffffff;" target="_blank" title="<?=$data["business"]?>"><?=$data["business"]?></a></strong>
                                </span>.
                              </span>
                            </p>
                            <p style="margin: 0; font-size: 17px; text-align: left;"><span style="font-size:17px;"> </span></p>
                            <p style="margin: 0; font-size: 17px;"><span style="font-size:17px;"><?=__("Thank you for your active participating") ?>. </span></p>
                            <p style="margin: 0; font-size: 17px; mso-line-height-alt: 14.399999999999999px;"> </p>
                            <p style="margin: 0; font-size: 17px;"><span style="font-size:17px;"><?=__("Please, click on confirm button to finish your subscription")?>.</span></p>
                          </div>
                        </div>
                      </td>
                    </tr>
                  </table>
                  <table appx="t9" border="0" cellpadding="10" cellspacing="0" class="button_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
                    <tr>
                      <td>
                        <div align="center">
                          <!--[if mso]>
                          <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="<?=$data["confirm_link"]?>" style="height:42px;width:179px;v-text-anchor:middle;" arcsize="10%" stroke="false" fillcolor="#00bfff"><w:anchorlock/><v:textbox inset="5px,0px,0px,0px"><center style="color:#ffffff; font-family:Tahoma, Verdana, sans-serif; font-size:16px">
                          <![endif]-->
                          <a href="<?=$data["confirm_link"]?>" style="text-decoration:none;display:inline-block;color:#ffffff;background-color:#00bfff;border-radius:4px;width:auto;border-top:1px solid #00bfff;font-weight:400;border-right:1px solid #00bfff;border-bottom:1px solid #00bfff;border-left:1px solid #00bfff;padding-top:5px;padding-bottom:5px;font-family:'Lato', Tahoma, Verdana, Segoe, sans-serif;text-align:center;mso-border-alt:none;word-break:keep-all;" target="_blank">
                            <span style="padding-left:55px;padding-right:50px;font-size:16px;display:inline-block;letter-spacing:normal;">
                              <span style="font-size: 16px; margin: 0; line-height: 2; word-break: break-word; mso-line-height-alt: 32px;"><?=__("CONFIRM")?></span>
                            </span>
                          </a>
                          <!--[if mso]>
                          </center></v:textbox></v:roundrect>
                          <![endif]-->
                        </div>
                      </td>
                    </tr>
                  </table>
                  <table appx="t10" border="0" cellpadding="10" cellspacing="0" class="text_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;" width="100%">
                    <tr>
                      <td>
                        <div style="font-family: Tahoma, Verdana, sans-serif">
                          <div class="txtTinyMce-wrapper" style="font-size: 12px; font-family: 'Lato', Tahoma, Verdana, Segoe, sans-serif; mso-line-height-alt: 14.399999999999999px; color: #ffffff; line-height: 1.2;">
                            <p style="margin: 0; font-size: 17px;">
                              <span style="font-size:14px;"><?=__("Remember to use this email in all of your future subscriptions in order to accumulate points") ?>. </span>
                            </p>
                          </div>
                        </div>
                      </td>
                    </tr>
                  </table>
                  <table appx="t11" border="0" cellpadding="10" cellspacing="0" class="text_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;" width="100%">
                    <tr>
                      <td>
                        <div style="font-family: Arial, sans-serif">
                          <div class="txtTinyMce-wrapper" style="font-size: 12px; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; mso-line-height-alt: 14.399999999999999px; color: #ffffff; line-height: 1.2;">
                            <p style="margin: 0;">
                              <span style="color:#00bfff;"><?=__("Promotion code: <strong>{0}</strong>", $data["promocode"])?></span>
                            </p>
                            <p style="margin: 0;">
                              <span style="color:#00bfff;"><?=__("Subscription code: <strong>{0}</strong>", $data["subscode"])?></span>
                            </p>
                          </div>
                        </div>
                      </td>
                    </tr>
                  </table>
                  <table appx="t12" border="0" cellpadding="0" cellspacing="0" class="image_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
                    <tr>
                      <td style="width:100%;padding-right:0px;padding-left:0px;">
                        <div align="center" style="line-height:10px">
                          <a href="vr" style="outline:none" tabindex="-1" target="_blank">
                            <img alt="Alternate text" class="big" src="https://css.eduardoaf.com/forms/with-bgimage/sabado.png" style="display: block; height: auto; border: 0; width: 800px; max-width: 100%;" title="Alternate text" width="800" />
                          </a>
                        </div>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              </tbody>
            </table>
          </td>
        </tr>
        </tbody>
      </table>
      <table appx="t13" align="center" border="0" cellpadding="0" cellspacing="0" class="row row-3" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
        <tbody>
        <tr>
          <td>
            <table appx="t14" align="center" border="0" cellpadding="0" cellspacing="0" class="row-content stack" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; color: #000000; width: 800px;" width="800">
              <tbody>
              <tr>
                <td appx="t14b" class="column column-1" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; padding-top: 0px; padding-bottom: 0px; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;" width="100%">
                  <table appx="t15" border="0" cellpadding="0" cellspacing="0" class="text_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;" width="100%">
                    <tr>
                      <td style="padding-bottom:15px;padding-left:15px;padding-right:15px;padding-top:25px;">
                        <div style="font-family: sans-serif">
                          <div class="txtTinyMce-wrapper" style="color: #C0C0C0; font-size: 12px; mso-line-height-alt: 14.399999999999999px; line-height: 1.2; font-family: 'Lato', Tahoma, Verdana, Segoe, sans-serif;">
                            <p style="margin: 0; font-size: 12px; text-align: center;">
                              <span style="color:#C0C0C0;">
                                *<?=__("Promotion ends at {0} UTC", $data["promodateto"])?><br/>
                              </span>
                            </p>
                          </div>
                        </div>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              </tbody>
            </table>
          </td>
        </tr>
        </tbody>
      </table>
      <table appx="t16" align="center" border="0" cellpadding="0" cellspacing="0" class="row row-4" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
        <tbody>
        <tr>
          <td>
            <table appx="t17" align="center" border="0" cellpadding="0" cellspacing="0" class="row-content stack" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; color: #000000; width: 800px;" width="800">
              <tbody>
              <tr>
                <td class="column column-1" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; padding-top: 5px; padding-bottom: 5px; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;" width="100%">
                  <table appx="t18" border="0" cellpadding="0" cellspacing="0" class="social_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
                    <tr>
                      <td style="text-align:center;padding-right:0px;padding-left:0px;">
                        <table appx="t19" align="center" border="0" cellpadding="0" cellspacing="0" class="social-table"
                               role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;"
                               width="188px">
                          <tr>
                            <td style="padding:0 15px 0 0px;">
                              <a href="https://www.facebook.com/" target="_blank">
                                <img alt="Facebook" height="32" src="https://css.eduardoaf.com/mailing/subscription/images/facebook2x.png" style="display: block; height: auto; border: 0;" title="Facebook" width="32" /></a>
                            </td>
                            <td style="padding:0 15px 0 0px;">
                              <a href="https://twitter.com/" target="_blank">
                                <img alt="Twitter" height="32" src="https://css.eduardoaf.com/mailing/subscription/images/twitter2x.png" style="display: block; height: auto; border: 0;" title="Twitter" width="32" /></a>
                            </td>
                            <td style="padding:0 15px 0 0px;">
                              <a href="https://plus.google.com/" target="_blank">
                                <img alt="Google+" height="32" src="https://css.eduardoaf.com/mailing/subscription/images/googleplus2x.png" style="display: block; height: auto; border: 0;" title="Google+" width="32" /></a>
                            </td>
                            <td style="padding:0 15px 0 0px;">
                              <a href="https://instagram.com/" target="_blank">
                                <img alt="Instagram" height="32" src="https://css.eduardoaf.com/mailing/subscription/images/instagram2x.png" style="display: block; height: auto; border: 0;" title="Instagram" width="32" /></a>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
                  <table appx="t20" border="0" cellpadding="10" cellspacing="0" class="divider_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
                    <tr>
                      <td style="padding: 0">
                        <div align="center">
                          <table appx="t21" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
                            <tr>
                              <td class="divider_inner" style="padding:0; font-size: 1px; line-height: 1px; border-top: 2px dotted #D4D3D3;">
                                <span> </span>
                              </td>
                            </tr>
                          </table>
                        </div>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              </tbody>
            </table>
          </td>
        </tr>
        </tbody>
      </table>
      <table appx="t22" align="center" border="0" cellpadding="0" cellspacing="0" class="row row-5" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
        <tbody>
        <tr>
          <td>
            <table appx="t23" align="center" border="0" cellpadding="0" cellspacing="0" class="row-content stack" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; color: #000000; width: 800px;" width="800">
              <tbody>
              <tr>
                <td appx="t24" class="column column-1" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; padding-top: 0px; padding-bottom: 0px; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;" width="100%">
                  <table appx="t25" border="0" cellpadding="3" cellspacing="0" class="text_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;" width="100%">
                    <tr>
                      <td>
                        <div style="font-family: sans-serif">
                          <div class="txtTinyMce-wrapper" style="color: #C0C0C0; font-size: 10px; mso-line-height-alt: 14.399999999999999px; line-height: 1.2; font-family: 'Lato', Tahoma, Verdana, Segoe, sans-serif;">
                            <p style="margin: 0; font-size: 10px; text-align: center;">
                              <span style="color:#C0C0C0;">
                                <?php
                                $terms = trim($data["promoterms"]);
                                $terms = explode("-", $terms);
                                $string = [];
                                foreach ($terms as $term){
                                  $term = trim($term);
                                  if ($term) $string[] = $term;
                                }
                                echo implode("<br />", $string);
                                ?>
                              </span>
                              <span style="color:#C0C0C0;">
                                <a href="<?=$data["terms_link"]?>" rel="noopener" style="text-decoration: underline; color: #C0C0C0;" target="_blank">
                                  <?= __("Check terms and conditions")?>
                                </a>
                              </span>
                            </p>
                          </div>
                        </div>
                      </td>
                    </tr>
                  </table>
                  <table appx="t26" border="0" cellpadding="10" cellspacing="0" class="text_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;" width="100%">
                    <tr>
                      <td>
                        <div style="font-family: sans-serif">
                          <div class="txtTinyMce-wrapper" style="color: #C0C0C0; font-size: 12px; mso-line-height-alt: 14.399999999999999px; line-height: 1.2; font-family: 'Lato', Tahoma, Verdana, Segoe, sans-serif;">
                            <p style="margin: 0; font-size: 12px; text-align: center;">
                              <span style="color:#C0C0C0;">
                                <?= __("Changed your mind? You can")?>
                                <a href="<?=$data["unsubscribe_link"]?>" rel="noopener" style="text-decoration: underline; color: #C0C0C0;" target="_blank">
                                  <?= __("unsubscribe")?>
                                </a>
                                <?= __("at any time")?>.
                              </span>
                            </p>
                          </div>
                        </div>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              </tbody>
            </table>
          </td>
        </tr>
        </tbody>
      </table>
      <table appx="t27" align="center" border="0" cellpadding="0" cellspacing="0" class="row row-6" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
        <tbody>
        <tr>
          <td>
            <table appx="t28" align="center" border="0" cellpadding="0" cellspacing="0" class="row-content stack" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; color: #000000; width: 800px;" width="800">
              <tbody>
              <tr>
                <td class="column column-1" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; padding-top: 5px; padding-bottom: 5px; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;" width="100%">
                  <table appx="t29" border="0" cellpadding="0" cellspacing="0" class="icons_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
                    <tr>
                      <td style="vertical-align: middle; color: #9d9d9d; font-family: inherit; font-size: 15px; padding-bottom: 0px; padding-top: 0px; text-align: center;">
                        <table appx="t30" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
                          <tr>
                            <td style="vertical-align: middle; text-align: center;">
                              <!--[if vml]>
                              <table appx="t31" align="left" cellpadding="0" cellspacing="0" role="presentation" style="display:inline-block;padding-left:0px;padding-right:0px;mso-table-lspace: 0pt;mso-table-rspace: 0pt;">
                              <![endif]-->
                              <!--[if !vml]><!-->
                              <table appx="t32" cellpadding="0" cellspacing="0" class="icons-inner" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; display: inline-block; margin-right: -4px; padding-left: 0px; padding-right: 0px;">
                                <!--<![endif]-->
                                <tr>
                                  <td style="vertical-align: middle; text-align: center; padding-top: 0px; padding-bottom: 5px; padding-left: 5px; padding-right: 6px;">
                                    <span style="font-family: 'Lato', Tahoma, Verdana, Segoe, sans-serif; font-size: 10px; color: #9d9d9d; vertical-align: middle; letter-spacing: normal; text-align: center;">
                                      <?=__("Designed by")?>
                                    </span>
                                    <a href="https://eduardoaf.com" style="text-decoration: none;" target="_blank">
                                      <img align="center"
                                           alt="Empowered by Eduardo A. F." class="icon" height="32" src="https://resources.theframework.es/eduardoaf.com/20200906/095050-logo-eduardoafcom_500.png"
                                           style="display: block; height: auto; margin: 0 auto; border: 0;"
                                           width="80" />
                                    </a>
                                  </td>
                                </tr>
                              </table>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              </tbody>
            </table>
          </td>
        </tr>
        </tbody>
      </table>
    </td>
  </tr>
  </tbody>
</table><!-- End -->
</body>
</html>