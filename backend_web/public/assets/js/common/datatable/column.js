
let _ths = [],
  _$table = null,
  _rowbtns = [],
  _defrowbtns = [
    {
      btnid: "rowbtn-show",
      visible: true,
      render: (v,t,row) => `<button type="button" uuid="${row?.uuid ?? ""}">Show</button>`
    },
    {
      btnid: "rowbtn-edit",
      visible: true,
      render: (v,t,row) => `<button type="button" uuid="${row?.uuid ?? ""}">Edit</button>`
    },
    {
      btnid: "rowbtn-del",
      visible: true,
      render: (v,t,row) => `<button type="button" uuid="${row?.uuid ?? ""}">Remove</button>`
    },
    {
      btnid: "rowbtn-undel",
      visible: false,
      render: (v,t,row) => `<button type="button" uuid="${row?.uuid ?? ""}">Restore</button>`
    },
    {
      btnid: "rowbtn-clone",
      visible: false,
      render: (v,t,row) => `<button type="button" uuid="${row?.uuid ?? ""}">Clone</button>`
    },
  ],
  _columns = []

const _get_actions = () => ["show","edit","del"]
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

const _get_mapped_rowbtns = () => {
  const thactions = _get_actions()
  const objbtns = _defrowbtns
                    .filter(objbtn => thactions.includes(objbtn.approle))
                    .map(defbtn => {
                      const confbtn = _rowbtns.filter(rowbtn => rowbtn.approle === defbtn.approle)[0] ?? {}
                      return {
                        ...defbtn,
                        ...confbtn
                      }
                    })

  return objbtns.filter(objbtn => objbtn.visible)
}

//to-do, tengo que crear metodo render por cada boton para que alcance la
// row
const _get_rowhtml_btns = (objbtns, row) => {
  objbtns = objbtns.map(objbtn => {
    const attr = objbtn?.attr
    let strattr = ""
    if (attr) {
      const keys = Object.keys(attr)
      strattr = keys.map(key => {
        const tag = attr[key]
        const kv = `${key}="${tag}"`

        if (tag.match(/%[\w]+%/ig)) {
          const rowkey = tag.replaceAll("%","")
          const rowvalue = row[rowkey] ?? ""
          return kv.replace(tag, rowvalue)
        }
        return kv
      }).join(" ")
    }
    const html = objbtn?.html
      .replace("%attr%", strattr)
      .replace("%text%", objbtn?.text ?? "")
    return html
  })

  return objbtns.join("&nbsp;")
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
  const rowbtns = _get_mapped_rowbtns()
  allcols.push({
    targets: -1,
    data: null,
    render: (v,t,row) => _get_rowhtml_btns(rowbtns, row),
  })

  return allcols
}

export const column = {
  add_column: obj => _columns.push(obj),
  add_rowbtn: obj => _rowbtns.push(obj)
}

export default $table => {
  _$table = $table
  _ths = Array.from(_$table.querySelectorAll(`[column]`))

  return {
    get_columns
  }
}