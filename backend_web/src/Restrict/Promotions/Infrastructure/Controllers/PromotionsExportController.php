<?php
namespace App\Restrict\Promotions\Infrastructure\Controllers;

use App\Shared\Infrastructure\Controllers\Restrict\RestrictController;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Restrict\Promotions\Application\PromotionsExportService;
use App\Shared\Domain\Enums\ResponseType;
use \Exception;

final class PromotionsExportController extends RestrictController
{
    //@get
    public function export(): void
    {
        if (!$this->auth->get_user())
            $this->_get_json()
                ->set_code(ResponseType::UNAUTHORIZED)
                ->set_error([__("Your session has finished please re-login")])
                ->show();

        try {
            SF::get_callable(PromotionsExportService::class,
                ["req_uuid" =>$this->request->get_get("req_uuid")]
            )();
        }
        catch (Exception $e) {
            $this->_get_json()->set_code($e->getCode())
                ->set_error([$e->getMessage()])
                ->show();
        }
    }
}
