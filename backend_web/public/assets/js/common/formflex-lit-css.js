import {css} from "/assets/js/vendor/lit.dev/lit-bundle.js"

export const cssformflex = css`
.modal-form {
  width: 100%;
  margin: 0;
  padding: 0;
}

form label {
  font-weight: 500;
}

.flex-row {
  display: flex;
  flex-direction: row;
  flex-wrap: wrap;
  gap: 15px;
}

.flex-column-n1 {
  display: flex;
  flex-direction: column;
  flex-basis: 100%;
  flex: 1;
}

.flex-column-n2 {
  display: flex;
  flex-direction: column;
  flex-basis: 100%;
  flex: 1;
}
`;