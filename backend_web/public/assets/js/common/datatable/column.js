let _ths = []
let _$table = null

const _get_columns = () => _ths
  .map($th => $th.getAttribute("column"))
  .filter(col => col!=="")

const is_visible = column => _ths.filter(
  $th => $th.getAttribute("column") === column
).filter($th => $th.getAttribute("visible")==="1").length > 0

const is_ordenable = column => _ths.filter(
  $th => $th.getAttribute("column") === column
).filter($th => $th.getAttribute("orderable")==="1").length > 0

const is_searchable = column => _ths.filter(
  $th => $th.getAttribute("column") === column
).filter($th => $th.getAttribute("searchable")==="1").length > 0

const get_type = column => _ths.filter(
  $th => $th.getAttribute("column") === column
).map($th => $th.getAttribute("type"))

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
      visible: is_visible(colname),
      render: function (row) {
        return row
      }
    }

    allcols.push(obj)
  })

  allcols.push({
    targets: -1,
    data: null,
    render: function(row) {
      //type: display
      const uuid = row.uuid ?? ""
      if(!uuid) return ""
      const links = [
        `<button type="button" uuid="${uuid}" approle="rowbtn-show">show</button>`,
        `<button type="button" uuid="${uuid}" approle="rowbtn-edit">edit</button>`,
        `<button type="button" uuid="${uuid}" approle="rowbtn-del">del</button>`,
      ]
      return links.join("&nbsp;");
    },
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