function Snackbar(id) {
  const _id = id
  let _time = 3000

  const _$div = document.getElementById(_id)
  if(!_$div) return console.log("no snackbar found with id",_id)
  _$div.classList.add("snackbar")

  this.set_inner = html => {
    _$div.innerHTML = html
    return this
  }

  this.set_time = time => {
    _time = time
    return this
  }

  this.show = () => {
    _$div.classList.add("snackbar-show")
    setTimeout(() => {
        _$div.classList.remove("snackbar-show")
        _$div.innerHTML = ""
      },
      3000)
  }
}

export default Snackbar