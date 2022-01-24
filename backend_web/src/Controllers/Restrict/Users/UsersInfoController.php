<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Controllers\Restrict\Users\
 * @file UsersController.php v1.0.0
 * @date 30-10-2021 14:33 SPAIN
 * @observations
 */
namespace App\Controllers\Restrict\Users;

use App\Factories\ServiceFactory as SF;
use App\Services\Common\PicklistService;
use App\Enums\PolicyType;
use App\Enums\PageType;
use App\Enums\ProfileType;
use App\Enums\ResponseType;
use App\Enums\UrlType;
use App\Enums\ExceptionType;
use App\Exceptions\NotFoundException;
use App\Exceptions\ForbiddenException;
use App\Exceptions\FieldsException;
use \Exception;

final class UsersInfoController extends RestrictController
{
    private PicklistService $picklist;
    
    public function __construct()
    {
        parent::__construct();
        $this->picklist = SF::get("Common\Picklist");
    }

    //@modal
    public function info(string $uuid): void
    {
         $this->add_var(PageType::TITLE, __("User info"))
             ->add_var(PageType::H1, __("User info"))
             ->add_var("ismodal",1);

        try {
            $info = SF::get_callable("Restrict\Users\UsersInfo", [$uuid]);
            $result = $info();
            $this->add_var("uuid", $uuid)
                ->add_var("result", $result)
                ->render_nl();
        }
        catch (NotFoundException $e) {
            $this->set_template("/error/404")
                ->add_header(ResponseType::NOT_FOUND)
                ->add_var(PageType::H1, $e->getMessage())
                ->render_nl();
        }
        catch (ForbiddenException $e) {
            $this->set_template("/error/403")
                ->add_header(ResponseType::FORBIDDEN)
                ->add_var(PageType::H1, $e->getMessage())
                ->render_nl();
        }
        catch (Exception $e) {
            $this->set_template("/error/500")
                ->add_header(ResponseType::INTERNAL_SERVER_ERROR)
                ->add_var(PageType::H1, $e->getMessage())
                ->render_nl();
        }
    }//info

}//UsersInfoController
