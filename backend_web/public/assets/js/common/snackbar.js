export var SNACK;
(function (SNACK) {
    SNACK["ERROR"] = "#ee335e";
    SNACK["SUCCESS"] = "#22c03c";
})(SNACK || (SNACK = {}));
function Snackbar(id) {
    const _id = id;
    let _time = 3;
    const _$div = document.getElementById(_id);
    if (!_$div)
        return console.log("no snackbar found with id", _id);
    //css: common/snackbar.css
    _$div.classList.add("snackbar");
    // @ts-ignore
    this.set_inner = (html) => {
        _$div.innerHTML = html;
        // @ts-ignore
        return this;
    };
    // @ts-ignore
    this.set_color = (back, front) => {
        if (back)
            _$div.style.backgroundColor = back;
        if (front)
            _$div.style.color = front;
        // @ts-ignore
        return this;
    };
    const _set_animation = (hold, time) => {
        if (!time)
            time = 0.5;
        const _hold = hold ? hold - (time * 2) : 2.5;
        //-webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
        //animation: fadein 0.5s, fadeout 0.5s 2.5s;
        //console.log("time:",time,"_hold:",_hold)
        const animation = `snackframe-fadein ${time}s, snackframe-fadeout ${time}s ${_hold}s`;
        _$div.style.animation = animation;
        // @ts-ignore
        return this;
    };
    // @ts-ignore
    this.set_time = (time) => {
        _time = time;
        //console.log("_time set_time", _time)
        // @ts-ignore
        return this;
    };
    // @ts-ignore
    this.show = () => {
        _$div.classList.add("snackbar-show");
        const tremove = _time * 900;
        _set_animation(_time, null);
        setTimeout(() => {
            _$div.classList.remove("snackbar-show");
            _$div.style.animation = "";
        }, tremove);
    };
}
export default Snackbar;
