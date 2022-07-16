<?php
namespace App\Open\PrivacyPolicy\Application;

use App\Shared\Infrastructure\Services\AppService;

final class PrivacyPolicyInfoService extends AppService
{
    public function __invoke(): array
    {
        $r = [
            ["h3" => __("For what purpose do we treat your data?")],
            ["p" => __("The data collected during the contracting flow of the tool through the Marketplace will be processed for the configuration and maintenance of the profile of the contracting Client (hereinafter “the Organization“) on the AppProviderXXX platform.")],

            ["h3" => __("What personal data do we process?")],
            ["p" => __("We will treat the Office 365 corporate email addresses of the members of the Organization that you provide us through the ”AppProviderXXX enrollment form”.")],

            ["h3" => __("What is the legal basis of the treatment?")],
            ["p" => __("The legal basis of the treatments with the purposes described is the legitimate interest in the treatment of said data, being necessary for the formalization and maintenance of the contractual relationship between ProviderXXX Spain and the contracting Organization")],

            ["h3" => __("Who are the recipients of your data?")],
            ["p" => __(
                "Your data may be communicated to the companies of the ProviderXXX Spain Group, some of which are located in countries located outside the European Economic Area that have not been declared by the European Commission to have an adequate level of data protection. The name and registered office of the entities of the ProviderXXX Spain Group appear on the website {0}.",
                "<a href=\"https://es.nttdata.com/group-companies\" target=\"_blank\">https://es.nttdata.com/group-companies</a>"
            )],
            ["p" => __("Additionally, we inform you that your data may be communicated to Treatment Managers, such as the following technology providers, some of which have processing and/or support centers located outside the European Economic Area.")],
            ["p" => __("Said communications could entail the international transfer of data to countries located outside the European Economic Area, some of which have not been declared as countries with an adequate level of protection by the European Commission. We guarantee that when your data could leave the European Economic Area, they will maintain the same level of protection based on compliance with the provisions set forth in the European data protection regulations. In this sense, international data transfers will be made (i) to countries with an adequate level of protection declared by the European Commission (ii) based on the provision of adequate guarantees such as standard contractual clauses or binding corporate regulations or (iii ) by virtue of the authorization of the competent control authority or other cases provided for in the regulations.")],

            ["h3" => __("How long will we keep your data?")],
            ["p" => __("Your data will be kept as long as the contractual relationship between the Organization and ProviderXXX Spain for the use of the AppProviderXXX platform is maintained, or as long as the Organization does not request ProviderXXX Spain to modify or delete the data provided in the form. However, the data may be kept blocked during the legally applicable prescription periods.")],

            ["h3" => __("What are your rights?")],
            ["p" => __("You can ask ProviderXXX Spain for confirmation as to whether your personal data is being processed and, if so, access them. Likewise, you can request the rectification of inaccurate data or, where appropriate, request its deletion when, among other reasons, the data is no longer necessary for the purposes for which it was collected.")],
            ["p" => __("Likewise, in certain circumstances, you may request the limitation of the processing of your data, in which case we will only keep them for the exercise or defense of claims. You may also oppose the processing of your data in certain circumstances. We will stop processing the data, except for compelling legitimate reasons or for the exercise or defense of possible claims. Finally, when appropriate, the right of portability may be exercised to obtain the data in electronic format or to transmit the same to another entity.")],
            ["p" => __("To exercise the aforementioned rights, you must write to the company's Data Protection Office, accompanied by an equivalent means of identification, to any of the following addresses:")],
        ];
        return $r;
    }
}