<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Console\Application\Restrict\UsersAccessService
 * @file UsersAccessService.php 1.0.0
 * @date 31-10-2020 17:46 SPAIN
 * @observations
 */
namespace App\Console\Application\Restrict;

use App\Shared\Infrastructure\Services\AppService;
use App\Console\Application\IConsole;
use App\Shared\Infrastructure\Traits\ConsoleTrait;
use App\Shared\Infrastructure\Factories\DbFactory as DbF;
use TheFramework\Components\Session\ComponentEncdecrypt;
use TheFramework\Components\Db\ComponentQB;
use TheFramework\Components\ComponentFaker;

final class UsersService extends AppService implements IConsole
{
    use ConsoleTrait;

    private ComponentEncdecrypt $encdec;
    private string $word;

    public function __construct(array $input)
    {
        $this->word = $input[0] ?? ":)";
        $this->encdec = $this->_get_encdec();
    }

    private function _get_password(): string
    {
        return $this->encdec->get_hashpassword($this->word);
    }

    private function _faker(): void
    {
        $db = DbF::get_by_default();
        $faker = new ComponentFaker();

        for ($i=0; $i<100; $i++) {
            $qb = new ComponentQB();

            $qb->set_table("base_user")
                ->add_insert_fv("address", $faker->get_paragraph(25))
                ->add_insert_fv("birthdate", $faker->get_date())
                ->add_insert_fv("date_validated", $faker->get_date("2020-01-01"))
                ->add_insert_fv("delete_date", rand(0,1)?$faker->get_datetime("2020-01-01","2021-11-05"):null)
                ->add_insert_fv("delete_platform", $faker->get_rndint(1,4))
                ->add_insert_fv("delete_user", $faker->get_rndint(1,5))
                ->add_insert_fv("description", $fullname = $faker->get_paragraph(2,5))
                ->add_insert_fv("email", $faker->get_email())
                ->add_insert_fv("fullname", $fullname)
                ->add_insert_fv("id_country", $faker->get_rndint(1,10))
                ->add_insert_fv("id_gender", $faker->get_rndint(1,3))
                ->add_insert_fv("id_language", $faker->get_rndint(1,4))
                ->add_insert_fv("id_nationality", $faker->get_rndint(1,10))
                ->add_insert_fv("id_parent", $i>10 ? $faker->get_rndint(1,5): null)
                ->add_insert_fv("id_profile", $faker->get_rndint(1,4))
                ->add_insert_fv("insert_date", $faker->get_datetime("2017-01-01","2021-11-05"))
                ->add_insert_fv("insert_platform", $faker->get_rndint(1,4))
                ->add_insert_fv("insert_user", $faker->get_rndint(1,5))
                ->add_insert_fv("is_notifiable", $faker->get_rndint(0,1))
                ->add_insert_fv("secret", $faker->get_hash(8))
                ->add_insert_fv("phone",$faker->get_int(9,9))
                ->add_insert_fv("uuid",uniqid())
                ->insert()
            ;
            $sql = $qb->get_sql();
            $r = $db->exec($sql);
            if($db->is_error()) {
                $this->_pr($this->get_error());
                return;
            }
        }
    }

    //php run.php users 1234
    public function run(): void
    {
        $this->_faker();
        $this->_pr($this->word,"word");
        $password = $this->_get_password();
        $message = "password: {$password}";
        $this->logpr($message);
    }
}