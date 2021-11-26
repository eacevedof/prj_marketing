let ths = []

const _get_columns = () => ths
  .map($th => $th.getAttribute("column"))
  .filter(col => col!=="")

const is_visible = column => ths.filter(
  $th => $th.getAttribute("column") === column
).filter($th => $th.getAttribute("visible")==="1").length > 0

const is_ordenable = column => ths.filter(
  $th => $th.getAttribute("column") === column
).filter($th => $th.getAttribute("orderable")==="1").length > 0

const is_searchable = column => ths.filter(
  $th => $th.getAttribute("column") === column
).filter($th => $th.getAttribute("searchable")==="1").length > 0

const get_type = column => ths.filter(
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

export default ($table) => {
  if(ths.length === 0)
    ths = Array.from($table.querySelectorAll(`[column]`))

  return {
    get_columns,
    is_ordenable,
    is_searchable,
    get_type,
  }
}