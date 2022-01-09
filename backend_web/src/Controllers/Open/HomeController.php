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

use App\Enums\PageType;

final class HomeController extends OpenController
{
    public function index(): void
    {
        $this->set_template("open/index")
            ->add_var(PageType::TITLE, __("Home"))
            ->add_var(PageType::H1, __("Home"))
            ->render();
    }

    public function foo(): void
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
    }

}//OpenController



