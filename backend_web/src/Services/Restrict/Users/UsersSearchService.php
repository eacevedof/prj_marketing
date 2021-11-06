<?php
namespace App\Services\Restrict\Users;
use App\Repositories\Base\UserPermissionsRepository;
use App\Services\AppService;
use TheFramework\Components\Session\ComponentEncdecrypt;
use App\Repositories\Base\UserRepository;
use App\Traits\SessionTrait;
use App\Traits\CookieTrait;
use App\Factories\RepositoryFactory as RF;
use App\Enums\Key;
use \Exception;

final class UsersSearchService extends AppService
{
    use SessionTrait;
    use CookieTrait;

    private string $domain;
    private array $input;
    private ComponentEncdecrypt $encdec;
    private UserRepository $repository;
    private UserPermissionsRepository $permissionrepo;

    public function __construct(array $input)
    {
        $this->input = $input;
        $this->_sessioninit();
        $this->_cookieinit()
            ->set_name("nombre")
            ->set_domain("localhost")
            ->set_valid_path("/")
        ;

        $this->encdec = $this->_get_encdec();
        $this->repository = RF::get("Base/User");
        $this->permissionrepo = RF::get("Base/UserPermissions");
    }

    public function __invoke(): array
    {
        return  [
            [
                "name"=>       "Tiger Alfa",
                "position"=>   "System Architect",
                "office"=>     "Edinburgh",
                "extn"=>       "5421",
                "start_date"=> "2011/04/25",
                "salary"=>     "$3,120",
            ],
            [
                "name"=>       "Garrett Winters",
                "position"=>   "Director",
                "office"=>     "Edinburgh",
                "extn"=>       "8422",
                "salary"=>     "$5,300",
                "start_date"=> "2011/07/25",
            ],
            [
                "name"=>       "Tiger Beta",
                "position"=>   "System Architect",
                "office"=>     "Edinburgh",
                "extn"=>       "288",
                "start_date"=> "2011/04/25",
                "salary"=>     "$4,120",
            ],
            [
                "name"=>       "MMM Winters",
                "position"=>   "sirector",
                "office"=>     "Edinburgh",
                "extn"=>       "8422",
                "salary"=>     "$5,300",
                "start_date"=> "2011/07/25",
            ],
            [
                "name"=>       "xxxx Alfa",
                "position"=>   "System Architect",
                "office"=>     "Edinburgh",
                "extn"=>       "5555",
                "start_date"=> "2011/04/25",
                "salary"=>     "$3,120",
            ],
            [
                "name"=>       "Garrett Winters 1",
                "position"=>   "Director",
                "office"=>     "Edinburgh",
                "extn"=>       "8422",
                "salary"=>     "$5,300",
                "start_date"=> "2011/07/25",
            ],
            [
                "name"=>       "Tiger Alfa 2",
                "position"=>   "System Architect",
                "office"=>     "Edinburgh",
                "extn"=>       "5421",
                "start_date"=> "2011/04/25",
                "salary"=>     "$3,120",
            ],
            [
                "name"=>       "Garrett Winters 3",
                "position"=>   "Director",
                "office"=>     "Edinburgh",
                "extn"=>       "8422",
                "salary"=>     "$5,300",
                "start_date"=> "2011/07/25",
            ],
            [
                "name"=>       "Tiger Beta 4",
                "position"=>   "System Architect",
                "office"=>     "Edinburgh",
                "extn"=>       "288",
                "start_date"=> "2011/04/25",
                "salary"=>     "$4,120",
            ],
            [
                "name"=>       "MMM Winters 5",
                "position"=>   "sirector",
                "office"=>     "Edinburgh",
                "extn"=>       "8422",
                "salary"=>     "$5,300",
                "start_date"=> "2011/07/25",
            ],
            [
                "name"=>       "xxxx Alfa 6",
                "position"=>   "System Architect",
                "office"=>     "Edinburgh",
                "extn"=>       "5555",
                "start_date"=> "2011/04/25",
                "salary"=>     "$3,120",
            ],
            [
                "name"=>       "Garrett Winters 7",
                "position"=>   "Director",
                "office"=>     "Edinburgh",
                "extn"=>       "8422",
                "salary"=>     "$5,300",
                "start_date"=> "2011/07/25",
            ],
            [
                "name"=>       "Tiger Alfa 8",
                "position"=>   "System Architect",
                "office"=>     "Edinburgh",
                "extn"=>       "5421",
                "start_date"=> "2011/04/25",
                "salary"=>     "$3,120",
            ],
            [
                "name"=>       "Garrett Winters 9",
                "position"=>   "Director",
                "office"=>     "Edinburgh",
                "extn"=>       "8422",
                "salary"=>     "$5,300",
                "start_date"=> "2011/07/25",
            ],
            [
                "name"=>       "Tiger Beta 10",
                "position"=>   "System Architect",
                "office"=>     "Edinburgh",
                "extn"=>       "288",
                "start_date"=> "2011/04/25",
                "salary"=>     "$4,120",
            ],
            [
                "name"=>       "MMM Winters 11",
                "position"=>   "sirector",
                "office"=>     "Edinburgh",
                "extn"=>       "8422",
                "salary"=>     "$5,300",
                "start_date"=> "2011/07/25",
            ],
            [
                "name"=>       "xxxx Alfa 12",
                "position"=>   "System Architect",
                "office"=>     "Edinburgh",
                "extn"=>       "5555",
                "start_date"=> "2011/04/25",
                "salary"=>     "$3,120",
            ],
            [
                "name"=>       "Garrett Winters 13",
                "position"=>   "Director",
                "office"=>     "Edinburgh",
                "extn"=>       "8422",
                "salary"=>     "$5,300",
                "start_date"=> "2011/07/25",
            ]

        ];
    }
}