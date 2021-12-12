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
  - no se filtra por jerarquia vertical
- sys admin
  - Tiene todas las acciones CRUD pero se filtra el acceso por permisos
  - no se filtra por jerarquia vertical
- business owner
  - Ve ciertos módulos (acceso por permisos) tiene acceso CRUD en estos si el es propietario o en caso de usuarios a 
    aquellos que esten debajo de él
- business manager
  - Ve ciertos módulos y CRUD dependiendo de permisos.
  
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
### To-Do
- loading en ajax
- responsive modal
- pestaña permisos en usuarios
- accion restore
- accion clone
