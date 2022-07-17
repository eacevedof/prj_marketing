import {css} from "/assets/js/vendor/lit.dev/lit-bundle.js"

export const cssformcontact = css`
.form-flex {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.form-flex div {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
}

.form-flex label {
  font-weight: bold;
}

.form-flex textarea, input {
  font-size: 1rem;
  width: 400px;
  padding: 10px;
  border: 1px solid var(--color-clear-black);
  border-radius: 6px;
  resize: none;
}

.form-flex .form-buttons {
  justify-content: space-around !important;
  padding-top: 10px;
}

.form-buttons button {
  text-decoration: none;
  color: var(--color-white);
  padding: 10px 60px;
  border: 1px solid var(--color-clear-black);
  border-radius: 6px;
  font-weight: bold;
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