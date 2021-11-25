let ths = []

const get_colums = () => ths
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

export default ($table) => {
  if(ths.length === 0)
    ths = Array.from($table.querySelectorAll(`[column]`))

  return {
    get_colums,
    is_visible,
    is_ordenable,
    is_searchable,
    get_type,
  }
}