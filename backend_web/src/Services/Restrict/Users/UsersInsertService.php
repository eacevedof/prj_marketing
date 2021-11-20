<?php
namespace App\Services\Restrict\Users;
use App\Factories\ComponentFactory as CF;
use App\Factories\RepositoryFactory;
use App\Factories\ValidatorFactory as VF;
use App\Models\Base\UserModel;
use App\Repositories\Base\UserPermissionsRepository;
use App\Services\AppService;
use App\Repositories\Base\UserRepository;
use App\Traits\SessionTrait;
use App\Enums\KeyType;
use TheFramework\Components\Session\ComponentEncdecrypt;
use App\Factories\ModelFactory;
use App\Traits\RequestTrait;
use App\Enums\ExceptionType;

final class UsersInsertService extends AppService
{
    use SessionTrait;
    use RequestTrait;

    private ComponentEncdecrypt $encdec;
    private UserRepository $repository;
    private FieldsValidator $validator;
    private UserModel $model;

    public function __construct(array $input)
    {
        $this->model = ModelFactory::get("Base/User");
        $this->validator = VF::get($input, $this->model);
        $this->repository = RepositoryFactory::get("Base/UserRepository");
        $this->request = $input;
        $this->_sessioninit();
        $this->encdec = $this->_get_encdec();
    }

    public function __invoke(): string
    {
        $insert = $this->_get_without_operations();
        $this->_exeption(__("Empty data"),ExceptionType::CODE_BAD_REQUEST);

        $insert["secret"] = $this->encdec->get_hashpassword($insert["password"]);
        unset($insert["password"]);
        $insert["insert_user"] = $this->session->get(KeyType::AUTH_USER)["id"] ?? "";
        $insert["insert_date"] = date("Y-m-d H:i:s");

        if(!$insert) $this->_exeption(__("No data"));
        $r = $this->repository->insert($insert, false);

        return $r;
    }
}