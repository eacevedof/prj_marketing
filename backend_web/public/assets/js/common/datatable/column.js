
let _ths = [],
  _$table = null,
  _rowbtns = [],
  _defrowbtns = [
    {
      btnid: "rowbtn-show",
      render: (v,t,row) => `<button type="button" btnid="rowbtn-show" uuid="${row?.uuid ?? ""}">Show</button>`
    },
    {
      btnid: "rowbtn-edit",
      render: (v,t,row) => `<button type="button" btnid="rowbtn-edit" uuid="${row?.uuid ?? ""}">Edit</button>`
    },
    {
      btnid: "rowbtn-del",
      render: (v,t,row) => `<button type="button" btnid="rowbtn-del" uuid="${row?.uuid ?? ""}">Remove</button>`
    },
    {
      btnid: "rowbtn-undel",
      render: (v,t,row) => `<button type="button" btnid="rowbtn-undel" uuid="${row?.uuid ?? ""}">Restore</button>`
    },
    {
      btnid: "rowbtn-clone",
      render: (v,t,row) => `<button type="button" btnid="rowbtn-clone" uuid="${row?.uuid ?? ""}">Clone</button>`
    },
  ],
  _extrowbtns = [],
  _columns = []

const _get_rowbtn_ids = () => ["show","edit","del","undel","clone"]
                              .filter(str => _$table.querySelector(`[approle='actions']`)?.getAttribute(str)==="1")
                              .map(str => `rowbtn-${str}`)

const _get_colnames_from_ths = () => _ths
  .map($th => $th.getAttribute("column"))
  .filter(col => col!=="")

const _is_visible = column => _ths.filter(
  $th => $th.getAttribute("column") === column
).filter($th => $th.getAttribute("visible")==="1").length > 0

const _is_ordenable = column => _ths.filter(
  $th => $th.getAttribute("column") === column
).filter($th => $th.getAttribute("orderable")==="1").length > 0

const _is_searchable = column => _ths.filter(
  $th => $th.getAttribute("column") === column
).filter($th => $th.getAttribute("searchable")==="1").length > 0

const _get_type = column => _ths.filter(
  $th => $th.getAttribute("column") === column
).map($th => $th.getAttribute("type"))

//to-do, tengo que crear metodo render por cada boton para que alcance la
// row
const _get_rowbtns_value = (v, t, row) => {
  const visibleids = _get_rowbtn_ids()
  const rowbtns = _defrowbtns.filter(objbtn => visibleids.includes(objbtn.btnid))
  let final = rowbtns.map(objbtn => objbtn.render(v, t, row))
  return final.concat(_extrowbtns.map(render => render(v, t, row))).join("&nbsp;")
}

//en dttable.js => columnDefs: dtcolumn($table).get_columns(),
const get_columns = () => {
  const colnames = _get_colnames_from_ths()

  //row numbers
  const allcols = [{
    searchable: false,
    orderable: false,
    targets: 0,
    data: null,
  }]

  //data columns
  colnames.forEach((colname, i )=> {
    //columna basica que entiende dttable
    let obj = {
      targets: i+1,
      data: colname,
      //searchable: false, no afecta en nada
      visible: _is_visible(colname),
      //https://datatables.net/manual/data/renderers
      //( data, type, row )
      render: value => value
    }
    const col = _columns.filter(obj => obj.data === colname)[0] ?? {}
    obj = {
      ...obj,
      ...col
    }
    allcols.push(obj)
  })

  //row buttons
  allcols.push({
    targets: -1,
    data: null,
    render: (v,t,row) => _get_rowbtns_value(v,t,row),
  })

  return allcols
}

export const column = {
  add_column: obj => _columns.push(obj),
  add_rowbtn: obj => _rowbtns.push(obj),
  add_extrowbtn: obj => _extrowbtns.push(obj),
}

export default $table => {
  _$table = $table
  _ths = Array.from(_$table.querySelectorAll(`[column]`))

  return {
    get_columns
  }
}