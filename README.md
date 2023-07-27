# prj_marketing
SN Marketing
- se sirve la **api** y **tpl** por: http://localhost:900/
- y la **spa back** por http://localhost:901/
  - puede que esto sobre 
- no hay un contenedor de crontab en ejecución para los backups

### console
- php run.php --class=App.Services.Kafka.LogConsumerService
```
sonar \
  -Dsonar.projectKey=prj-marketing \
  -Dsonar.sources=. \
  -Dsonar.host.url=http://localhost:3500 \
  -Dsonar.login=sqp_635fb25b1a382cdc444204938bd0fce36c34f439

make ssh-be
run help
```
- migrations
```
cd $PATHWEB/db; phinx migrate -e testing
o
make ssh-be
run-migrate
```

### tests (phpunit 9.3)
- ejecuta todos los tests
```
make ssh-be
be; phpunit

en prod:
/usr/bin/php8.2-cli vendor/bin/phpunit tests

ó

make ssh-php
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
  text: `<span style="color:blue"><?php $this->_echo(__("Add"));?></span>`,
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

### connect db_mypromos
```sh
# conecta con cont-mariadb-univ
mysql -u root -p'1234' \
        -h host.docker.internal -P 3306 \
        -D db_mypromos
```
### msmtp
```sh
#vi /usr/local/etc/php/conf.d/php.ini o
#vi /usr/local/etc/php/conf.d/php-sendmail.ini
sendmail_path = "/usr/bin/msmtp -t -i"

#vi /etc/msmtprc
defaults
auth on
tls on
tls_starttls on
tls_trust_file /etc/ssl/certs/ca-certificates.crt
logfile ~/.msmtp.log

# GMAIL
account gmail 
host smtp.gmail.com
port 587 
from username@gmail.com 
user username
password 1234

account default:gmail

echo "hello world" | /usr/bin/msmtp -d xxx@gmail.com
php -r "mail('xxx@yahoo.com', 'PHP test', 'Test from PHP as wwwrun user');"
```
### Desplegado (deploy)
```shell
config .env a partir de .env.prod
# el error de dominio viene pq los ficheros .json no son los del env
# actualizar con datos de pro
modificado:     backend_web/.env

# eliminar
backend_web/config/contexts.local.json
backend_web/config/encdecrypt.local.json
backend_web/config/login.local.json

# crear estos
backend_web/config/contexts.json
backend_web/config/encdecrypt.json
backend_web/config/login.json

#enlaces simbolicos
backend_web/console/php.ini 
backend_web/public/php.ini

# fichero $HOME/.msmtprc
defaults
auth on
tls on
tls_starttls on
tls_trust_file /etc/ssl/certs/ca-certificates.crt
logfile ~/msmtp.log

# GMAIL
account gmail 
host smtp.gmail.com
port 587 
from xxx@gmail.com 
user xxx
password yyy


# fichero $HOME/php.ini 
# (no vale $HOME en la ruta tiene q ser path-to-htdocs)
# X configura log
sendmail_path="/usr/bin/msmtp -t -i --file=<path-to-htdocs>/.msmtprc -X mail.log -d >> msmtp.log"

# crear el enlace simbolico
ln -s <path-to-htdocs>/php.ini <path-to-htdocs>/xxx/public/php.ini
ln -s <path-to-htdocs>/php.ini <path-to-htdocs>/xxx/console/php.ini

# -c <path> indica que cargue el php.ini de esa ruta
# -r "xxxx" ejecute ese codigo sin necesidad de tags <??>
php8.1-cli -c ~ -r "mail('xxx@gmail.com', 'PHP done', 'Test from PHP as wwwrun gg');"
# fichero de pruebas
php8.1-cli -c ~ mail.php

# para ejecutar comandos
cd $PATHWEB/db; /usr/bin/php8.0-cli phinx migrate -e testing
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