<?php
namespace App\Restrict\Users\Infrastructure\Controllers;

use App\Shared\Infrastructure\Controllers\Restrict\RestrictController;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Restrict\Queries\Application\QueryExportService;
use App\Shared\Domain\Enums\ResponseType;
use \Exception;

final class UsersSearchExportController extends RestrictController
{
    //@post
    public function export(string $uuid): void
    {
        if (!$this->auth->get_user())
            $this->_get_json()
                ->set_code(ResponseType::UNAUTHORIZED)
                ->set_error([__("Your session has finished please re-login")])
                ->show();

        try {
            SF::get_callable(
                QueryExportService::class,
                [
                    "req_uuid" => $uuid,
                    "columns" => $this->request->get_post("columns", []),
                    "filename" => "users",
                ]
            )();
        }
        catch (Exception $e) {
            $this->_get_json()->set_code($e->getCode())
                ->set_error([$e->getMessage()])
                ->show();
        }
    }
}
