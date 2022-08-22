"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.erase_cookie = exports.get_cookie = void 0;
const get_cookie = (name) => {
    const parts = document.cookie.split("; ");
    const obj = parts
        .map(str => str.split("="))
        .filter(ar => ar[0] === name)
        .map(ar => ar[1]);
    return obj.length ? obj[0] : null;
};
exports.get_cookie = get_cookie;
const set_cookie = (name, value, days = 1) => {
    const pieces = [
        `${name}=${(value === null || value === void 0 ? void 0 : value.toString()) || ""}`
    ];
    if (days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        pieces.push(`expires=${date.toUTCString()}`);
    }
    pieces.push("path=/");
    document.cookie = pieces.join("; ");
};
const erase_cookie = (name) => {
    const pieces = [
        `${name}=`,
        "path=/",
        "expires=Thu, 01 Jan 1970 00:00:01 GMT"
    ];
    document.cookie = pieces.join("; ");
};
exports.erase_cookie = erase_cookie;
exports.default = set_cookie;
