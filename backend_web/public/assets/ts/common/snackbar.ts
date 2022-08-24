export enum SNACK  {
  ERROR = "#ee335e",
  SUCCESS = "#22c03c",
}

function Snackbar(id: string):any {
  const _id:string = id
  let _time:number = 3

  const _$div: HTMLElement | null = document.getElementById(_id)
  if(!_$div) return console.log("no snackbar found with id",_id)
  //css: common/snackbar.css
  _$div.classList.add("snackbar")

  // @ts-ignore
  this.set_inner = (html: string): any => {
    _$div.innerHTML = html
    // @ts-ignore
    return this
  }

  // @ts-ignore
  this.set_color = (back:string, front:string): any => {
    if (back) _$div.style.backgroundColor = back
    if (front) _$div.style.color = front
    // @ts-ignore
    return this
  }

  const _set_animation = (hold:number, time:number): any => {
    if (!time) time = 0.5
    const _hold:number = hold ? hold - (time * 2): 2.5
    //-webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
    //animation: fadein 0.5s, fadeout 0.5s 2.5s;
    //console.log("time:",time,"_hold:",_hold)
    const animation: string = `snackframe-fadein ${time}s, snackframe-fadeout ${time}s ${_hold}s`
    _$div.style.animation = animation
    // @ts-ignore
    return this
  }

  // @ts-ignore
  this.set_time = (time: number):any => {
    _time = time
    //console.log("_time set_time", _time)
    // @ts-ignore
    return this
  }

  this.show = () => {
    _$div.classList.add("snackbar-show")
    const tremove = _time * 900
    _set_animation(_time)
    setTimeout(() => {
      _$div.classList.remove("snackbar-show")
      _$div.style.webkitAnimationName = ""
      _$div.style.animation = ""
      //console.log("remove")
    }, tremove)
  }

}

export default Snackbar