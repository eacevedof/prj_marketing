import {css} from "/assets/js/vendor/lit.dev/lit-bundle.js"

export const csstooltip = css`
.tt-tooltip {
    font-family: "Consolas", "Bitstream Vera Sans Mono", "Courier New", Courier, monospace;
    position: relative;
    display: inline-block;
}

.tt-tooltip .tt-tooltiptext {
    visibility: hidden;
    width: 400px;
    background-color: #2366C9;
    color: #fff;
    padding: 1px 5px;
    border-radius: 5px;
    position: absolute;
    z-index: 1;
    left: 13px;
    top: 3px;
    font-weight: lighter;
}

.tt-tooltip:hover .tt-tooltiptext {
    visibility: visible;
}

.tt-span {
    font-size: 12px;
    height: 12px;
    width: 12px;
    padding-top: 1px;
    padding-left: 3px;
    border-radius: 50%;
    background: #2366C9;
    color: white;
    display: inline-flex;
    align-items: center;
}
`;