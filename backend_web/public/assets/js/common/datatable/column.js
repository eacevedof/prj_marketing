let $objtable = null
let ths = []

const get_colums = () => ths.map($th => $th.getAttribute("column"))

const is_visible = column => ths.filter(
  $th => $th.getAttribute("column") === column
).filter($th => $th.getAttribute("isvisible")==="1").length > 0

const is_ordenable = column => ths.filter(
  $th => $th.getAttribute("column") === column
).filter($th => $th.getAttribute("orderable")==="1").length > 0

const is_searchable = column => ths.filter(
  $th => $th.getAttribute("column") === column
).filter($th => $th.getAttribute("searchable")==="1").length > 0


export default ($table) => {
  $objtable = $table
  ths = Array.from($table.querySelectorAll(`[column]`))

  return {
    get_colums,
  }
}