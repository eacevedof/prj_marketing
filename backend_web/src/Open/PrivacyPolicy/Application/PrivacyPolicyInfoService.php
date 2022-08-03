<?php
namespace App\Open\PrivacyPolicy\Application;

use App\Shared\Infrastructure\Services\AppService;

final class PrivacyPolicyInfoService extends AppService
{
    public function __invoke(): array
    {
        $r = [
            ["h3" => __("For what purpose do we treat your data?")],
            ["p" => __("The data collected during the contracting flow of the tool through the sing up and/or subscription process will be handled for the configuration and maintenance of the profile of the contracting Client (hereinafter “Organization“) on the AppProviderXXX platform.")],

            ["h3" => __("What personal data do we process?")],
            ["p" => __("There is registration by subscription and Organization. In each case the required data varies but at least an email account is requested. For subscribers, it will be used, with your prior consent, for the purpose of notifications of offers, raffles and promotions that may be of interest.")],
            ["p" => __("At Organization level, It will be used to configure the space on the platform, thus allowing the publication of promotional landings.")],

            ["h3" => __("What is the legal basis of the treatment?")],
            ["p" => __("The legal basis of the treatments with the purposes described is the legitimate interest in the treatment of said data, being necessary for the formalization and maintenance of the contractual relationship between ProviderXXX and the contracting Organization")],

            ["h3" => __("Who are the recipients of your data?")],
            ["p" => __("Your data may be communicated to the ProviderXXX's Organizations, some of which are located in countries outside the European Economic Area that have not been declared by the European Commission to have an adequate level of data protection.")],
            ["p" => __("Additionally, we inform you that your data may be communicated to Treatment Managers, such as the following technology providers, some of which have processing and/or support centers located outside the European Economic Area.")],

            ["h3" => __("How long will we keep your data?")],
            ["p" => __("Your data will be kept as long as the contractual relationship between the Organization and ProviderXXX for the use of the AppProviderXXX platform is maintained, or as long as the Organization does not request ProviderXXX to modify or delete the data provided in the form. However, the data may be kept blocked during the legally applicable prescription periods.")],

            ["h3" => __("What are your rights?")],
            ["p" => __("You can ask ProviderXXX for confirmation as to whether your personal data is being processed and, if so, access them. Likewise, you can request the rectification of inaccurate data or, where appropriate, request its deletion when, among other reasons, the data is no longer necessary for the purposes for which it was collected.")],
            ["p" => __("Likewise, in certain circumstances, you may request the limitation of the processing of your data, in which case we will only keep them for the exercise or defense of claims. You may also oppose the processing of your data in certain circumstances. We will stop processing the data, except for compelling legitimate reasons or for the exercise or defense of possible claims. Finally, when appropriate, the right of portability may be exercised to obtain the data in electronic format or to transmit the same to another entity.")],
            ["p" => __("To exercise the aforementioned rights, you must write to the company's Data Protection Office, accompanied by an equivalent means of identification, to any of the following addresses:")],
            ["ul" => [
                __("Post mail") .": "."Madrid - 28002",
                __("Email") .": "."<a href=\"mailto:dataprivacy@mypromos.es\">dataprivacy@mypromos.es</a>",
            ]],
            ["p" => __("To help us process your request, please indicate in the subject “Data Protection“")],
            ["p" => __("Finally, you are informed of your right to file a claim with the Spanish Data Protection Agency if you are aware of or consider that an event may lead to a breach of the applicable data protection regulations.")],
        ];
        return $r;
    }
}