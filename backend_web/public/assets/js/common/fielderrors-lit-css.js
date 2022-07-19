import {css} from "/assets/js/vendor/lit.dev/lit-bundle.js"

export const cssfielderror = css`
form .error-input {
    border-color: #ee335e;
    padding-right: calc(1.5em + 0.75rem);
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='%23fa5c7c' viewBox='-2 -2 7 7'%3e%3cpath stroke='%23fa5c7c' d='M0 0l3 3m0-3L0 3'/%3e%3ccircle r='.5'/%3e%3ccircle cx='3' r='.5'/%3e%3ccircle cy='3' r='.5'/%3e%3ccircle cx='3' cy='3' r='.5'/%3e%3c/svg%3E");
    background-repeat: no-repeat;
    background-position: center right calc(0.375em + 0.1875rem);
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}
form input[type=checkbox].error-input {
    border-color: #ee335e !important;
    padding-right: 0;
    background-position: center;
}

form div[approle="field-error"] ul {
    display: flex;
    list-style-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
    flex-direction: column;
    padding: 0;
    margin-top: 0;
    margin-bottom: 0;
    margin-left: 20px;
}

form div[approle="field-error"] ul li{
    color: #ee335e;
    margin-top: 3px;
}
`;