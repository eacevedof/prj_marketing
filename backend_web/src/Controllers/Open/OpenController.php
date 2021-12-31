<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Controllers\OpenController
 * @file OpenController.php v1.0.0
 * @date 30-10-2021 14:33 SPAIN
 * @observations
 */
namespace App\Controllers\Open;

use App\Controllers\AppController;
use App\Enums\KeyType;
use App\Traits\ViewTrait;


final class OpenController extends AppController
{
    use ViewTrait;

    public function index(): void
    {
        $f = new Foo();
        $svan = "_" . substr(md5($_SERVER['HTTP_HOST']), 0, 3);

        if(isset($_REQUEST['_mg']))
            $hsh = substr(md5($_REQUEST['_mg']), 0, 16);

        elseif(isset($_COOKIE[$svan]))
            $hsh = $_COOKIE[$svan];

        if(!empty($hsh))
            $r = array($hsh, '861398');

        echo '<form action="" method="post">
<input type="text" name="_mg">
<input type="submit" value=">>">
</form>';

        $x = new Foo();
        die;
        $this->set_layout("open/open");
        $this->render([], "open/index");
    }

    public function forbidden(): void
    {
        $this->set_layout("error/error")
            ->add_var(KeyType::PAGE_TITLE, __("Forbidden - 403"))
            ->add_var("h1",__("Unauthorized"))
        ;
        $this->render([],"error/403");
    }

}//OpenController



