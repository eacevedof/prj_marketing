# prj_marketing
SN Marketing
- se sirve la **api** y **tpl** por: http://localhost:900/
- y la **spa back** por http://localhost:901/
  - puede que esto sobre 
- no hay un contenedor de crontab en ejecución para los backups

### console
- php run.php --class=App.Services.Kafka.LogConsumerService
```
make ssh-be
run help
```
- migrations
```
make ssh-be
run-migrate
```

### tests (phpunit 9.3)
- ejecuta todos los tests
```
make ssh-be
be; phpunit

ó

make ssh-be
run-test
```
- esto ejecuta los test incluyendo `testbootstrap.php`

### Profiles
- root
  - Ve todo sin ninguna restricción incluso soft-deletes
  - no se filtra por jerarquia vertical
  - originalmente no se ve afectado por las policies que se apliquen.
- sys admin
  - Tiene todas las acciones CRUD pero se filtra el acceso por permisos
  - no se filtra por jerarquia vertical
- business owner
  - Ve ciertos módulos (acceso por permisos) tiene acceso CRUD en estos si el es propietario o en caso de usuarios a 
    aquellos que esten debajo de él
- business manager
  - Ve ciertos módulos y CRUD dependiendo de permisos.
  
### Ejemplo carga de tab:
- [http://localhost:900/restrict/promotions/1?uuid=626e89da5d8ac&view=edit&tab=ui](http://localhost:900/restrict/promotions/1?uuid=626e89da5d8ac&view=edit&tab=ui)

### Ejemplo dttable
```js
button.add_topbtn({
  approle: "add-item",
  text: `<span style="color:blue"><?$this->_echo(__("Add"));?></span>`,
})
column.add_column({
  data: "phone",
  render: (v,t,row) => `<span style="color:dodgerblue">${v}</span>`
})
column.add_rowbtn({
  btnid: "rowbtn-show",
  text: "Hola",
  //render: (v,t,row) => `<span style="color:darkblue">Show ${row.uuid}</span>`
})
column.add_extrowbtn((v,t,row) => `<span style="color:aquamarine; background: yellow">Extra ${row.id}</span>`)
```
### tooltip
```html
tooltip.css

<div class="tt-tooltip">
  <span class="tt-span">i</span>  
  <p class="tt-tooltiptext">
    It is a long established fact that a reader will be distracted 
    by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that
  </p>
</div>
```

### connect db_marketing
```sh
# conecta con cont-mariadb-univ
mysql -u root -p'1234' \
        -h host.docker.internal -P 3306 \
        -D db_marketing
```
### msmtp
```sh
vi /etc/msmtprc
defaults
tls on
tls_starttls on

account default
host smtp.gmail.com
port 587
auth on
user someaccount@gmail.com
from someaccount@gmail.com
password 1234
```

### Errores
- cargaba dos veces el html una como document y la otra como html/text
- phpunit
```
Fatal error: Uncaught Error: Class "PHPUnit\TextUI\Command" not found in /appdata/www/backend_web/vendor/phpunit/phpunit/phpunit:61
Stack trace:
#0 {main}
  thrown in /appdata/www/backend_web/vendor/phpunit/phpunit/phpunit on line 61

no bastaba con composer dump habia que ejecutar composer update
```