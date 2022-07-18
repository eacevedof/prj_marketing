import {css} from "/assets/js/vendor/lit.dev/lit-bundle.js"

export const cssformcontact = css`
.form-grid {
  display: grid;
  row-gap: 1rem;
  padding: 1rem;
  max-width: 600px;
}

.form-grid .cell-flex {
  display: flex;
  flex-wrap: wrap;
}

.form-grid .cell-flex label {
  display: flex;
  min-width: 5.5rem;
  font-weight: bold;
  align-items: center;
}

.form-grid .cell-flex textarea, input {
  font-size: 1rem;
  width: 400px;
  padding: 10px;
  border: 1px solid var(--color-clear-black);
  border-radius: 6px;
  resize: none;
}

.form-grid .cell-flex div[approle=field-error] ul {
  display: flex;
  list-style-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
  flex-direction: column;
  margin: 0;
  margin-top: 2px;
  margin-left: 5.5rem;
}

.form-grid .cell-flex div[approle=field-error] ul li {
  color: rgb(238, 51, 94);
}

.form-grid .cell-btn {
  display: flex;
  justify-content: center;
}

.form-grid .cell-btn .button {
  text-decoration: none;
  color: var(--color-white);
  padding: 10px 60px;
  border: 1px solid var(--color-clear-black);
  border-radius: 6px;
  font-weight: bold;
  font-size: 1.2rem;
  background-color: var(--color-orange);
  transition: background-color .3s;
}

.button-exit {
  position: absolute;
  top: 1px;
  right: 1px;
  background-color: var(--color-clear-black);
  color: var(--color-clear-black);
  font-weight: bold;
  padding-right: 1px;
  padding-left: 2px;
  border-radius: 30px;
}
`;