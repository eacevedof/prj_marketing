<?php
/**
 * @var array $data
 */
$hello = ucwords(strtolower($data["username"] ?? "")) ?: $data["email"];
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
                          <img alt="<?=$data["business"]?>" src="<?=$data["businesslogo"]?>" style="display: block; height: auto; border: 0; width: 160px; max-width: 100%;" title="<?=$data["business"]?>" width="160" />
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
                          <div class="txtTinyMce-wrapper" style="font-size: 12px; font-family: 'Lato', Tahoma, Verdana, Segoe, sans-serif; mso-line-height-alt: 14.399999999999999px; color: #ffffff; line-height: 1.2;">
                            <p style="margin: 0; font-size: 38px; text-align: left;">
                              <span style="font-size:30px;"><strong><?=__("Hello {0}!!", $hello) ?></strong></span>
                            </p>
                          </div>
                        </div>
                      </td>
                    </tr>
                  </table>
                  <table appx="t10" border="0" cellpadding="10" cellspacing="0" class="text_block" role="presentation"
                         style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word; padding-bottom:25px;" width="100%">
                    <tr>
                      <td>
                        <div style="font-family: Tahoma, Verdana, sans-serif">
                          <div class="txtTinyMce-wrapper" style="font-size: 12px; font-family: 'Lato', Tahoma, Verdana, Segoe, sans-serif; mso-line-height-alt: 14.399999999999999px; color: #ffffff; line-height: 1.2;">
                            <p style="margin: 0; font-size: 17px; text-align: left;">
                              <span style="font-size:17px;">
                                <?=__("Your accumulated points at <strong>{0}</strong> have been updated", $data["business"])?>.
                              </span>
                            </p>
                            <p style="margin: 0; font-size: 17px; padding-top: 10px; text-align: left;">
                              <?=__(" Check it out")?>
                              <a href="<?= $data["points_link"] ?>" rel="noopener" style="text-decoration:none;color:#00bfff;" target="_blank" title="<?=__("Accumulated points") ?>">
                                <strong><?=__("here") ?></strong>.
                              </a>
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
                            <?php
                            if ($url = $data["urlfb"]):
                              ?>
                              <td style="padding:0 15px 0 0px;">
                                <a href="<?=$url?>" target="_blank">
                                  <img alt="Facebook" height="32" src="https://app-rsrc.getbee.io/public/resources/social-networks-icon-sets/t-only-logo-dark-gray/facebook@2x.png" style="display: block; height: auto; border: 0;" title="Facebook" width="32" />
                                </a>
                              </td>
                            <?php
                            endif;
                            if ($url = $data["urltwitter"]):
                              ?>
                              <td style="padding:0 15px 0 0px;">
                                <a href="<?=$url?>" target="_blank">
                                  <img alt="Twitter" height="32" src="https://app-rsrc.getbee.io/public/resources/social-networks-icon-sets/t-only-logo-dark-gray/twitter@2x.png" style="display: block; height: auto; border: 0;" title="Twitter" width="32" />
                                </a>
                              </td>
                            <?php
                            endif;
                            if ($url = $data["urlig"]):
                              ?>
                              <td style="padding:0 15px 0 0px;">
                                <a href="<?=$url?>" target="_blank">
                                  <img alt="Instagram" height="32" src="https://app-rsrc.getbee.io/public/resources/social-networks-icon-sets/t-only-logo-dark-gray/instagram@2x.png" style="display: block; height: auto; border: 0;" title="Instagram+" width="32" />
                                </a>
                              </td>
                            <?php
                            endif;
                            if ($url = $data["urltiktok"]):
                              ?>
                              <td style="padding:0 15px 0 0px;">
                                <a href="<?=$url?>" target="_blank">
                                  <img alt="Tik Tok" height="32" src="https://app-rsrc.getbee.io/public/resources/social-networks-icon-sets/t-only-logo-dark-gray/tiktok@2x.png" style="display: block; height: auto; border: 0;" title="Tiktok" width="32" />
                                </a>
                              </td>
                            <?php
                            endif;
                            ?>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
                  <table appx="t20" border="0" cellpadding="10" cellspacing="0" class="divider_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
                    <tr>
                      <td style="padding:0; padding-top:10px;">
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
                                      Designed by                                    </span>
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
