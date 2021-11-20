<?php
namespace App\Services\Restrict\Users;
use App\Factories\RepositoryFactory;
use App\Factories\ValidatorFactory as VF;
use App\Models\Base\UserModel;
use App\Services\AppService;
use App\Repositories\Base\UserRepository;
use App\Traits\SessionTrait;
use App\Enums\KeyType;
use TheFramework\Components\Session\ComponentEncdecrypt;
use App\Factories\ModelFactory;
use App\Traits\RequestTrait;
use App\Enums\ExceptionType;
use App\Models\FieldsValidator;

final class UsersInsertService extends AppService
{
    use SessionTrait;
    use RequestTrait;

    private array $user;
    private ComponentEncdecrypt $encdec;
    private UserRepository $repository;
    private FieldsValidator $validator;
    private UserModel $model;

    public function __construct(array $input)
    {
        $this->model = ModelFactory::get("Base/User");
        $this->validator = VF::get($input, $this->model);
        $this->repository = RepositoryFactory::get("Base/UserRepository");
        $this->_load_request($input);
        $this->user = $this->_sessioninit()->get(KeyType::AUTH_USER);
        $this->encdec = $this->_get_encdec();
    }

    public function __invoke(): array
    {
        $insert = $this->_get_without_operations();
        if (!$insert)
            $this->_exeption(__("Empty data"),ExceptionType::CODE_BAD_REQUEST);

        if ($errors = $this->validator->get_errors()) {
            $this->_set_errors($errors);
            $this->_exeption(__("Fields validation errors"), ExceptionType::CODE_BAD_REQUEST);
        }

        $insert = $this->model->map_request($insert);
        $insert["secret"] = $this->encdec->get_hashpassword($insert["secret"]);
        $insert["uuid"] = uniqid();
        $this->model->add_sysinsert($insert,$this->user["id"]);

        $id = $this->repository->insert($insert);
        return [
            "id" => $id,
            "uuid" => $insert["uuid"]
        ];
    }
}