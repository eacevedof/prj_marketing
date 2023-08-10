<?php

namespace App\Restrict\Promotions\Infrastructure\Controllers;

use Exception;
use App\Shared\Domain\Enums\ResponseType;
use App\Restrict\Queries\Application\QueryExportService;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Controllers\Restrict\RestrictController;

final class PromotionsSearchExportController extends RestrictController
{
    //@post
    public function export(string $uuid): void
    {
        if (!$this->authService->getAuthUserArray()) {
            $this->_getJsonInstanceFromResponse()
                ->setResponseCode(ResponseType::UNAUTHORIZED)
                ->setErrors([__("Your session has finished please re-login")])
                ->show();
        }

        try {
            SF::getCallableService(
                QueryExportService::class,
                [
                    "req_uuid" => $uuid,
                    "columns" => $this->requestComponent->getPost("columns", []),
                    "filename" => "promotions",
                ]
            )();
        } catch (Exception $e) {
            $this->_getJsonInstanceFromResponse()->setResponseCode($e->getCode())
                ->setErrors([$e->getMessage()])
                ->show();
        }
    }
}
