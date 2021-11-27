function Snackbar(id) {
  const _id = id
  let _time = 3

  const _$div = document.getElementById(_id)
  if(!_$div) return console.log("no snackbar found with id",_id)
  _$div.classList.add("snackbar")

  this.set_inner = html => {
    _$div.innerHTML = html
    return this
  }

  this.set_color = (back, front) => {
    if (back) _$div.style.backgroundColor = back
    if (front) _$div.style.color = front
    return this
  }

  this.set_animation = (time, out) => {
    if (!time) time = "0.5"
    if (!out) out = "2.5"
    //-webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
    //animation: fadein 0.5s, fadeout 0.5s 2.5s;
    console.log("time:",time,"out:",out)
    const animation = `fadein ${time}s, fadeout ${time}s ${out}s`
    _$div.style.webkitAnimationName = animation
    _$div.style.animation = animation
    return this
  }

  this.set_time = time => {
    _time = time
    console.log("_time set_time", _time)
    return this
  }

  this.show = () => {
    _$div.classList.add("snackbar-show")
    const time = _time
    console.log("time show", time,"_time",_time)
    setTimeout(() => {
        _$div.classList.remove("snackbar-show")
        _$div.innerHTML = ""
      }, time * 1000)
  }
}

export default Snackbar