import {css} from "/assets/js/vendor/lit.dev/lit-bundle.js"

export const cssformsubscription = css`
.form-subscription {
  margin:0;
  padding:0;
  border: 2px solid black; 
  font-size: 1.2em;
}

.form-row .form-group {
    width: 50%;
    padding: 0 15px;
}

.form-group {
    margin-bottom: 23px
    position: relative;
}
label {
    font-size: 14px;
    font-weight: bold;
    font-family: 'Montserrat';
    margin-bottom: 2px;
    display: block;
}
input, select {
    display: block;
    width: 100%;
    border: 1px solid #ebebeb;
    padding: 11px 20px;
    box-sizing: border-box;
    font-family: 'Montserrat';
    font-weight: 500;
    font-size: 13px;
}
input, select, textarea {
    outline: none;
    appearance: unset !important;
    -moz-appearance: unset !important;
    -webkit-appearance: unset !important;
    -o-appearance: unset !important;
    -ms-appearance: unset !important;
}
`;