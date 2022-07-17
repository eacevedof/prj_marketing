<?php
namespace App\Open\CookiesPolicy\Application;

use App\Shared\Infrastructure\Services\AppService;

final class CookiesPolicyInfoService extends AppService
{
    public function __invoke(): array
    {
        $r = [
            ["p" => __("ProviderXXX S.A. (hereinafter, “ProviderXXX“) informs you, through this Cookies Policy, about the use of data storage and recovery devices in users' terminal equipment.")],

            ["h2" => __("1. What are cookies?")],
            ["p" => __("Cookies are files or files that are downloaded to the <i>computer / smartphone / tablet</i> of the User (hereinafter, the “User”), when accessing certain websites and applications, which allow storage and retrieval of the User's data. Cookies are used for different purposes, such as recognizing you as a User, obtaining information about your browsing habits and adapting the way content is displayed.")],

            ["h2" => __("2.- What cookies do we use?")],
            ["p" => __("The Website may use the following types of cookies. A cookie may fall into more than one category:")],

            ["ul" => [
                [
                    ["b" => __("Own cookies")],
                    ["span" => __("Own cookies are those that are sent to the User's device from a computer or domain managed by ProviderXXX and from which the service requested by the User is provided.")],
                ],
                [
                    ["b" => __("Third party cookies")],
                    ["span" => __("Third-party cookies are those sent to the User's device from a computer or domain that may or may not be managed by ProviderXXX but by another entity that processes the data collected by the cookie for its own purposes. Own cookies are identified in this policy indicating that the owner is “ProviderXXX“. The rest are owned by the third parties indicated in each case. In section 6, you can access their respective cookie policies by clicking on the name of the third party, including the transfers to third countries that, where appropriate, they carry out.")],
                ],
                [
                    ["b" => __("Session cookies and persistent cookies")],
                    ["span" => __("session cookies are designed to collect and store data while the User accesses the Website. The information is kept only during the session, and disappears when it ends. Persistent cookies, however, continue to be stored in the terminal for a certain time even after the session has ended.")],
                ],
                [
                    ["b" => __("Technical cookies")],
                    ["span" => __("These cookies allow the Website to function correctly, so they are essential for the User to be able to use all the options of the Website and to be able to navigate and use its functions normally.")],
                ],
                [
                    ["b" => __("Preference or personalization cookies")],
                    ["span" => __("are those that allow remembering information that allows the User to access the Website with some characteristics that make their experience different from that of other users, for example, the language, the type of browser through which the service is performed, the regional configuration from where the service is accessed, etc.")],
                ],
                [
                    ["b" => __("Analysis or measurement cookies")],
                    ["span" => __("are those that allow the monitoring and analysis of the behavior of the Users of the websites to which they are linked, including the quantification of the impacts of the advertisements, where appropriate. The information collected through this type of cookie is used to measure the activity of the Website and to create browsing profiles of the Users, in order to introduce improvements based on the analysis of the usage data made by the Users. from service.")],
                ],
                [
                    ["b" => __("Behavioral advertising cookies")],
                    ["span" => __("are those that store information on the behavior of Users obtained through the continuous observation of their browsing habits, which allows the development of a specific profile to display advertising based on it.")],
                ],
            ]],
            ["p" => __("In <a href=\"#section-6\">section 6</a> of this policy, the cookies used on the Website are detailed.")],
            ["h2" => __("3.- Are international data transfers made with the use of cookies?")],
            ["p" => __("The acceptance of some of these cookies may involve the international transfer of data to third countries necessary for the operation of the services of our providers. The legal basis for said transfer is your consent, expressed by consenting to the use of the cookies in each case. In any case, international data transfers will be made to countries with an adequate level of protection declared by the European Commission and/or based on the provision of adequate guarantees such as standard contractual clauses (articles 45 and 46 of the General Protection Regulation of data).")],
            ["p" => __("You can find out about the transfers to third countries that, where appropriate, are made by the third parties identified in this cookie policy in their corresponding policies, accessible in section 6.")],

            ["h2" => __("4. How to disable cookies?")],
            ["p" => __("All browsers allow you to make changes to disable cookie settings. This is why most browsers offer the ability to manage cookies, for finer control over privacy.")],
            ["p" => __("These settings are located in the “options“ or “preferences“ menu of your browser.")],
            ["p" => __("If you wish to revoke your informed consent regarding the use of cookies sent from the Website, or modify it, you can access the cookie configuration panel or make the changes through the configuration of your browser, without preventing access to the contents.")],
            ["p" => __("These settings are located in the “options“ or “preferences“ menu of your browser. Below, you can find the links for each browser to disable cookies by following the instructions:")],
            ["h3" => "Internet Explorer (<a href=\"https://goo.gl/iU2wh2\" target=\"_blank\">https://goo.gl/iU2wh2</a>)",],
            ["ul" => [
                __("In the tools menu, select ”Internet Options.”"),
                __("Click on the privacy tab."),
                __("You can configure privacy with a slider with six positions that allows you to control the number of cookies that will be installed: Block all cookies, High, Medium High, Medium (default level), Low and Accept all cookies."),
            ]],
            ["h3" => "Mozilla Firefox (<a href=\"http://goo.gl/QXWYmv\" target=\"_blank\">http://goo.gl/QXWYmv</a>)"],
            ["ul" => [
                __("At the top of the Firefox window, click on the Tools menu."),
                __("Select Options."),
                __("Select the Privacy panel."),
                __("In the Firefox option you can choose Use custom settings for history to configure the options."),
            ]],
            ["h3" => "Google Chrome (<a href=\"http://goo.gl/fQnkSB\" target=\"_blank\">http://goo.gl/fQnkSB</a>)"],
            ["ul" => [
                __("Click on the menu located on the toolbar."),
                __("Select Settings."),
                __("Click Show advanced options."),
                __("In the “Privacy” selection, click the Content Settings button."),
                __("In the Cookies selection, the options can be configured."),
            ]],
            ["h3" => "Safari (<a href=\"https://goo.gl/PcjEm3\" target=\"_blank\">https://goo.gl/PcjEm3</a>; <a href=\"https://goo.gl/dQywEo\" target=\"_blank\">https://goo.gl/dQywEo</a>)"],
            ["ul" => [
                __("In the configuration menu select the “Preferences” option."),
                __("Open the privacy tab."),
                __("Select the option you want from the “block cookies” section."),
                __("Remember that certain functions and the full functionality of the Website may not be available after disabling cookies."),
            ]],
            ["p" => __(
            "In order for visitors to the Website to have the possibility to prevent Google Analytics from using their data, Google has developed a Google opt-out browser add-on, available here: {0}",
            "<a href=\"https://tools.google.com/dlpage/gaoptout\" target=\"_blank\">https://tools.google.com/dlpage/gaoptout</a>."
            ),],
            ["h2" => __("5.- Cookies on mobile devices")],
            ["p" => __("Cookies and other storage devices are also used when you access the Website from mobile devices.")],
            ["p" => __("As with computer browsers, mobile device browsers allow you to make changes to privacy options or settings to disable or delete cookies.")],
            ["p" => __("If you wish to modify the privacy options, follow the instructions specified by the developer of your mobile device browser. Below, you can find some examples of the links that will guide you to modify the privacy options on your mobile device:")],
            ["ul" => [
               "IOS (<a href=\"http://goo.gl/61xevS\" target=\"_blank\">http://goo.gl/61xevS)</a>",
               "Windows Phone (<a href=\"https://goo.gl/tKyb0y\" target=\"_blank\">https://goo.gl/tKyb0y</a>)",
               "Chrome Mobile (<a href=\"http://goo.gl/XJp7N\" target=\"_blank\">http://goo.gl/XJp7N</a>)",
               "Opera Mobile (<a href=\"http://goo.gl/Nzr8s7\" target=\"_blank\">http://goo.gl/Nzr8s7</a>)",
            ]],
            ["h2" => __("<span id=\"section-6\">6.- Acceptance, configuration or rejection of cookies, and more information</span>")],
            ["p" => __("In the cookie banner, you can click the “ACCEPT ALL” button to accept all cookies. Likewise, you can click on “ACCEPT SELECTION“ to accept only the cookies marked in the configuration boxes available in the banner, or you can click on “REJECT“ to reject unnecessary cookies.")],
            ["p" => __("We inform you that in the case of blocking or not accepting the installation of cookies, it is possible that certain services will not be available without the use of cookies or that you will not be able to access certain services or take full advantage of everything that this Website offers you.")],
            ["p" => __("<a href=\"javascript: Cookiebot.renew()\">Change your consent</a>")],
            ["p" => __("You can obtain more information about the processing of your data by contacting {0}", "<a href=\"mailto:info@providerxxx.es\">info@providerxxx.es</a>")],
        ];
        //print_r($_REQUEST["APP_TRANSLATIONS"]);die;
        return $r;
    }
}