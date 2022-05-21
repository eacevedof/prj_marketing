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
            $export = SF::get_callable(PromotionsExportService::class, $this->request->get_get());
            $result = $export();
            header("Content-Description: File Transfer");
            header("Content-Type: csv");
            header("Content-Disposition: attachment; filename=promotions.csv");
            header("Content-Transfer-Encoding: binary");
            header("Connection: Keep-Alive");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Pragma: public");
            header("Content-Length: ".strlen($result));
            exit;
        }
        catch (Exception $e) {
            $this->_get_json()->set_code($e->getCode())
                ->set_error([$e->getMessage()])
                ->show();
        }
    }
}
