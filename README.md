# prj_marketing
SN Marketing
- se sirve la **api** y **tpl** por: http://localhost:900/
- y la **spa back** por http://localhost:901/
  - puede que esto sobre 
- no hay un contenedor de crontab en ejecución para los backups

### console
- php run.php --class=App.Services.Kafka.LogConsumerService

### Profiles
- root
  - Ve todo sin ninguna restricción incluso soft-deletes
- sys admin
  - Tiene todas las acciones CRUD pero se filtra el acceso por permisos
- business owner
  - Ve ciertos módulos (acceso por permisos) tiene acceso CRUD en estos si el es propietario o en caso de usuarios a 
    aquellos que esten debajo de él
- business manager
  - Ve ciertos módulos y CRUD dependiendo de permisos.