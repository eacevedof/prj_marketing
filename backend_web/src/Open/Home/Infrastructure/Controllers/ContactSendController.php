<?php
namespace App\Open\Home\Infrastructure\Controllers;

use App\Shared\Infrastructure\Controllers\Open\OpenController;
use App\Shared\Domain\Enums\PageType;

final class ContactSendController extends OpenController
{
    public function send(): void
    {
        $this->set_layout("open/mypromos/home")
            ->add_var(PageType::TITLE, $title = __("My Promotions"))
            ->add_var(PageType::H1, $title)
            //->cache()
            ->render();
    }
}



