<?php
namespace App\Open\TermsConditions\Application;

use App\Shared\Infrastructure\Exceptions\NotFoundException;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Domain\Enums\LanguageType;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Restrict\Promotions\Domain\PromotionRepository;

final class TermsConditionsInfoService extends AppService
{
    private string $lang;

    public function __construct(array $input = [])
    {
        $this->input = $input["promoslug"] ?? "";
        $this->lang = $input["lang"] ?? LanguageType::ES;
    }

    private function _general_terms(): array
    {
        $domain = getenv("APP_DOMAIN");
        return [
            ["h2" => __("1. Portal Owner")],
            ["p" => __("This Legal Notice regulates the conditions of use of the website <a href=\"{0}\" target=\"_blank\">{1}</a> (hereinafter, the “Website“ or the “Portal“) responsibility of:", $domain, $domain)],
            ["ul" => [
                "providerxxx.es - Madrid 28002 - ".__("Spain"),
                __("Contact").": <a href=\"mailto:info@providerxxx.es\">info@providerxxx.es</a>",
            ]],
            ["p" => __("The fact of accessing the Website implies that you have read and accept, without reservation, these conditions. The Service Provider reserves the right to deny, suspend, interrupt or cancel access or use, totally or partially, of this website to those users or visitors who fail to comply with any of the conditions set forth in this Legal Notice.")],

            ["h2" => __("2. Intellectual and industrial property")],
            ["p" => __("All the contents of the Portal, understanding by these, by way of example, the texts, videos, photographs, graphics, images, icons, technologies, software, links and other audiovisual or sound content, as well as its graphic design and source codes (hereinafter , the “Contents“), are the intellectual property of the Service Provider or third parties, and none of the exploitation rights recognized by current regulations regarding intellectual property over them can be understood to be transferred to the user, except for those that are strictly necessary for the use of the Portal.")],
            ["p" => __("In particular, the reproduction, transformation, distribution, public communication, making available to the public and in general any other form of exploitation, by any procedure, of all or part of the contents of this website, as well as its design and the selection and form of presentation of the materials included in it.")],
            ["p" => __("These acts of exploitation may only be carried out with the express authorization of the Service Provider and provided that reference is made to the Service Provider's ownership of the indicated intellectual and industrial property rights.")],
            ["p" => __("It is also prohibited to decompile, disassemble, reverse engineer, sub-license or transmit in any way, translate or make derivative works of the computer programs necessary for the operation, access and use of this website and the services on it contents, as well as carry out, with respect to all or part of such programs, any of the exploitation acts described in the previous paragraph.The user of the website must refrain in any case from deleting, altering, evading or manipulating any protection device or security systems that may be installed in it.")],
            ["p" => __("The trademarks, trade names or distinctive signs are the property of the Service Provider or third parties, without it being understood that access to the Portal attributes any right over the aforementioned trademarks, trade names and/or distinctive signs.")],

            ["h2" => __("3. Terms of use of the Portal")],
            ["h3" => __("3.1  General")],
            ["p" => __(
                "The use of the Portal attributes the condition of user and implies the acceptance of all the conditions included in this Legal Notice and the rest that are present in {0} and {1}.",
                "<a href=\"/cookies-policy\" target=\"_blank\">".__("Cookies Policy")."</a>",
                "<a href=\"/privacy-policy\" target=\"_blank\">".__("Privacy Policy")."</a>"
            )],
            ["p" => __("Notwithstanding the foregoing, access to certain services and/or content is restricted to certain users, and regulations, instructions, general conditions or particular conditions may be established that, where appropriate, replace, complete and/or modify this Notice Legal.")],
            ["p" => __("The user undertakes to make correct use of the Portal in accordance with the Law and this Legal Notice. The user will be liable to the Service Provider or to third parties for any damages that may be caused as a result of breach of said obligation.")],
            ["p" => __("The use of the content made available to users for marketing is strictly prohibited. In this way, the user will not be able to provide services to third parties benefiting from the contents included in the Portal.")],
            ["p" => __("Likewise, the use of the Portal for harmful purposes of goods or interests of the Service Provider or third parties or that in any other way overload, damage or disable the networks, servers and other computer equipment (hardware) or products and computer applications (software) of the Service Provider or third parties.")],
            ["p" => __("In any case, users undertake to use the Portal in full compliance with the Terms and Conditions that regulate the corresponding public cloud services:")],

            ["h3" => __("3.2 Contents")],
            ["p" => __("The user agrees to use the Content in accordance with the Law and this Legal Notice, as well as with the other conditions, regulations and instructions that may be applicable in accordance with the provisions of the Legal Notice.")],
            ["p" => __("With a merely enunciative character, the user, in accordance with current legislation, must refrain from:")],
            ["ul" => [
                __("Reproducing, copying, distributing, making available, publicly communicating, transforming or modifying the Content except in cases authorized by law or expressly consented to by the Service Provider or by whoever owns the exploitation rights in your case. "),
                __("Reproducing or copying for private use the Contents that may be considered as Software or Database in accordance with current legislation on intellectual property, as well as its public communication or making it available to third parties when these acts necessarily imply the reproduction by part of the user or a third party."),
                __("Extract and/or reuse all or a substantial part of the Contents of the Portal as well as the databases that the Service Provider makes available to users.")
            ]],
            ["h3" => __("3.3 Introduction of links to the Portal")],
            ["p" => __("Internet users who want to introduce links from their own web pages to the Portal must comply with the conditions detailed below, without ignoring them avoiding the responsibilities derived from the Law:")],
            ["p" => __("The link will only link to the home page or main page of the Portal but may not reproduce it in any way (inline links, copy of the texts, graphics, etc).")],
            ["p" => __("It will be prohibited in any case, in accordance with the applicable legislation and in force at any time, to establish frames or frameworks of any kind that surround the Portal or allow the display of the Contents through Internet addresses other than those of the Portal and, in any case, when they are displayed together with contents outside the Portal in such a way that: (I) produces, or may produce, error, confusion or deception in users about the true origin of the service or Content; (II) supposes an act of unfair comparison or imitation; (III) serves to take advantage of the reputation of the brand and prestige of the Service Provider; or (IV) in any other way is prohibited by current legislation.")],
            ["p" => __("No type of false, inaccurate or incorrect statement will be made from the page that introduces the link about the Service Provider, its partners, employees, clients or about the quality of the services it provides.")],
            ["p" => __("In no case, it will be expressed on the page where the link is located that the Service Provider has given its consent for the insertion of the link or that in another way it sponsors, collaborates, verifies or supervises the services of the sender.")],
            ["p" => __("The use of any denominative, graphic or mixed brand or any other distinctive sign of the Service Provider within the sender's page is prohibited, except in the cases permitted by law or expressly authorized by the Service Provider and whenever it is permitted, in these cases, a direct link to the Portal in the manner established in this clause.")],
            ["p" => __("The page that establishes the link must faithfully comply with the law and may not in any case have or link to its own content or that of third parties that: (I) are illegal, harmful or contrary to morality and good customs (pornographic, violent , racist, etc.); (II) induce or may induce in the user the false conception that the Service Provider subscribes, endorses, adheres or in any way supports the ideas, statements or expressions, legal or illegal, of the sender ; (III) are inappropriate or not pertinent to the activity of the Service Provider in attention to the place, content and theme of the sender's website.")],

            ["h2" => __("4. Exclusion of liability")],
            ["h3" => __("4.1 Of the quality of the Service")],
            ["p" => __("Access to the Portal does not imply an obligation on the part of the Service Provider to control the absence of viruses, worms or any other harmful computer element. The user is responsible, in any case, for the availability of adequate tools for the detection and disinfection of harmful computer programs.")],
            ["p" => __("Service Provider is not responsible for any damage caused to the computer equipment of users or third parties during the provision of the Portal service.")],

            ["h3" => __("4.2 Availability of the Service")],
            ["p" => __("Access to the Portal requires services and supplies from third parties, including transport through telecommunications networks whose reliability, quality, continuity and operation does not correspond to the Service Provider. Consequently, the services provided through the Portal may be suspended, canceled or inaccessible, prior to or simultaneously with the provision of the Portal service.")],
            ["p" => __("Service Provider is not responsible for damages or losses of any kind produced in the user that cause failures or disconnections in the telecommunications networks that produce the suspension, cancellation or interruption of the Portal service during the provision of the same or with character previous.")],

            ["h3" => __("4.3 Links")],
            ["p" => __("The access service to the Portal may include technical link devices, directories and even search tools that allow the user to access other Internet pages and portals (hereinafter, 'Linked Sites'). In these cases, the Service Provider acts as a provider of intermediation services in accordance with article 17 of Law 34/2002, of July 12, on Services of the Information Society and Electronic Commerce (hereinafter, the 'LSSI') and will only be responsible for the contents and services provided on the Linked Sites to the extent that they have effective knowledge of the illegality and have not deactivated the link with due diligence.In the event that the user considers that there is a Linked Site with illegal or inappropriate content, they may communicate it to the Service Provider in accordance with the procedure and the effects established in clause 5, without this communication in any case entailing the obligation to and remove the corresponding link.")],
            ["p" => __("In no case, the existence of Linked Sites must presuppose the existence of agreements with those responsible or owners thereof, nor the recommendation, promotion or identification of the Service Provider with the statements, content or services provided.")],
            ["p" => __("Service Provider does not know the contents and services of the Linked Sites and therefore is not responsible for damages caused by the illegality, quality, outdated, unavailability, error and uselessness of the contents and/or services of the Linked Sites or for any other damage that is not directly attributable to the Service Provider.")],

            ["h2" => __("5. Communication of illicit and inappropriate activities")],
            ["p" => __("In the event that the user or any other Internet user becomes aware that the Linked Sites refer to pages whose content or services are illegal, harmful, degrading, violent or contrary to morality; or that any of the information included by the users themselves, through the services offered on the Portal, have a consideration equal to that described above, the user may communicate it through the means of contact informed in section 1.")],
            ["p" => __("The reception by the Service Provider of the communication provided for in this clause will not imply, according to the provisions of the LSSI, effective knowledge of the activities and/or contents indicated by the caller.")],
            ["h2" => __("6. Modifications")],
            ["p" => __("In order to improve the Website, the Service Provider reserves the right, at any time and without prior notice, to modify, expand or temporarily suspend the presentation, configuration, technical specifications and services of the Website, unilaterally.")],
            ["p" => __("Likewise, it reserves the right to modify these conditions of use at any time, as well as any other applicable general or particular conditions.")],

            ["h2" => __("7. Legislation")],
            ["p" => __("This Legal Notice is governed in each and every one of its extremes by Spanish law.")],
        ];
    }

    public function __invoke(): array
    {
        return $this->_general_terms();
    }

    private function _promotion_terms(string $promotion, string $conditions): array
    {
        //todo hay que agregar las fechas limites
        // hasta agotar existencias
        // rifable o acumulativa (tratarlo en en el contador)
        $lines = trim($conditions);
        if (!$lines) return [
            ["h2" => "- ".__("Promotion Terms: {0}", $promotion)],
            ["p" => __("None")],
        ];
        $lines = explode("\n", $lines);

        $conds[0] = ["h2" => "- ".__("Promotion Terms: {0}", $promotion)];
        $conds[1] = ["ul"=>[]];
        foreach ($lines as $line) {
            $conds[1]["ul"][] = $line;
        }
        $conds[2] = ["h2" => "- ".__("General Terms")];
        return $conds;
    }

    public function get_by_promotion(): array
    {
        $promo = RF::get(PromotionRepository::class)->get_by_slug($this->input);
        if (!$promo)
            throw new NotFoundException(__("Promotion not found"));

        return array_merge(
            $this->_promotion_terms($promo["description"], $promo["content"]),
            $this->_general_terms(),
        );
    }
}
