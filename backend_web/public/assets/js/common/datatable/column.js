
let _ths = [],
  _$table = null,
  _rowbtns = [],
  _defrowbtns = [
    {
      approle: "rowbtn-show",
      text: "Show",
      visible: true,
      html: `<button type="button" %attr%>%text%</button>`,
      attr: {
        approle: "rowbtn-show",
        uuid: "%uuid%"
      }
    },
    {
      approle: "rowbtn-edit",
      text: "Edit",
      visible: true,
      html: `<button type="button" %attr%>%text%</button>`,
      attr: {
        approle: "rowbtn-edit",
        uuid: "%uuid%"
      }
    },
    {
      approle: "rowbtn-del",
      text: "Remove",
      visible: true,
      html: `<button type="button" %attr%>%text%</button>`,
      attr: {
        approle: "rowbtn-del",
        uuid: "%uuid%"
      }
    },
  ]


const _get_columns = () => _ths
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

export const set_rowbtns = ar => _rowbtns = ar

export const add_rowbtn = obj => _rowbtns.push(obj)

const _get_mapped_rowbtns = row => {
  let objbtns = _defrowbtns.map(defbtn => {
    const confbtn = _rowbtns.filter(rowbtn => rowbtn.approle === defbtn.approle)[0] ?? null
    if (confbtn)
      return {
        ...defbtn,
        ...confbtn
      }
    return defbtn
  })

  objbtns = objbtns.filter(objbtn => objbtn.visible)
  console.log(objbtns)

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
                    return kv.replace(tag,rowvalue)
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

const get_columns = () => {
  const cols = _get_columns()
  //col con numero
  const allcols = [{
    searchable: false,
    orderable: false,
    targets: 0,
    data: null,
  }]

  cols.forEach((colname, i )=> {
    const obj = {
      targets: i+1,
      data: colname,
      //searchable: false, no afecta en nada
      visible: _is_visible(colname),
      render: function (row) {
        //to-do: tratar el tipo para aplicar un customHtml
        return row
      }
    }

    allcols.push(obj)
  })

  allcols.push({
    targets: -1,
    data: null,
    render: row => _get_mapped_rowbtns(row),
  })

  return allcols
}



export default $table => {
  _$table = $table
  _ths = Array.from(_$table.querySelectorAll(`[column]`))

  return {
    get_columns
  }
}