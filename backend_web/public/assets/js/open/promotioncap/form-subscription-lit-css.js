import {css} from "/assets/js/vendor/lit.dev/lit-bundle.js"

export const cssformsubscription = css`
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

a {
  text-decoration: underline;
  color: #3a4f71;
  font-weight: bold;
}
a:link, a:visited {
  color: black;
}
a:hover {
  color: deepskyblue;
}

.form-grid {
  display: grid;
  row-gap: .7rem;
  background-color:rgba(255, 255, 255, 0.95);
  padding: 1rem;
  border-radius: 0.5rem;
  border: 1px solid black;
  -webkit-box-shadow: 5px 3px 6px 0px rgba(0,0,0,0.38);
  box-shadow: 5px 3px 6px 0px rgba(0,0,0,0.38);
  margin: 1rem;
  max-width: 500px;
}

.form-grid .error-top {
  border-radius: 4px;
  border: 1px solid tomato;
  background-color: #fdf0f0;
  color: red;
  padding: .5rem;
  justify-self: center;
  text-align: center;
  width:100%;
}

.form-grid .cell-flex {
  display: flex;
  flex-wrap: wrap;
}

.cell-flex label {
  display: flex;
  min-width: 5.5rem;
  font-weight: bold;
  align-items: center;
}

.cell-flex div[approle=field-error] ul {
  display: flex;
  list-style-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
  flex-direction: column;
  margin: 0;
  margin-top: 2px;
  padding-left: 6.75rem;
}

.cell-flex div[approle=field-error] ul li {
  color: rgb(238, 51, 94);
}

.cell-btn {
  justify-content: center;
}

.cell-btn .button {
  background-color: deepskyblue;
  color: white;
  border: 2px solid #3a4f71;
  font-size: 1.2rem;

  display: flex;
  justify-content: center;
  align-items: center;
  position: relative;

  margin-top: 0.5rem;
  min-width: 6rem;
  padding: 0.6rem;
  border-radius: 0.5rem;
  cursor: pointer;
}

.cell-btn .button::after {
  content: "";
  position: absolute;
  display: block;
  width: 100%;
  height: 100%;
}

.button-glow {
  overflow: hidden;
}

.button-glow:after {
  left: -100%;
  clip-path: polygon(10% 0, 70% 0, 90% 100%, 30% 100%);
  background-color: rgba(255, 255, 255, 0.6);
  transition: all 300ms ease;
}

.button-glow:hover::after {
  left: 100%;
}

.cell-flex input:not([type=checkbox]), textarea, select{
  width: 20rem;
  padding: 0.4rem;
  border-radius: 0.5rem;
  border: 1.5px solid deepskyblue;
  font-size: 1rem;
  background-color: white;
}

.cell-flex input[type=date] {
  width: 10rem;
}

.cell-flex select {
  width: 20rem;
}

.cell-flex textarea {
  height: 2.50rem;
  min-height: 2.50rem;
  max-height: 2.50rem;
  max-width: 100%;
}

.cell-chk label {
  font-weight: normal;
}

.cell-chk input[type=checkbox] {
  font-size: 1rem;
  display: grid;
  grid-template-columns: 1em auto;
  gap: 0.5rem;
  /* Add if not using autoprefixer */
  -webkit-appearance: none;
  /* Remove most all native input styles */
  appearance: none;
  /* For iOS < 15 */
  background-color: white;
  width: 1.25rem;
  height: 1.25rem;
  border: 0.15rem solid deepskyblue;
  border-radius: 0.15rem;
  transform: translateY(-0.075rem);
  place-content: center;
  margin-right: 0.5rem;
}

.cell-chk input[type=checkbox]::before {
  content: "";
  position: absolute;
  top:8px;
  left: -2px;
  width: 0.65rem;
  height: 0.65rem;
  clip-path: polygon(14% 44%, 0 65%, 50% 100%, 100% 16%, 80% 0%, 43% 62%);
  transform: scale(0);
  transform-origin: bottom left;
  transition: 120ms transform ease-in-out;
  box-shadow: inset 1rem 1rem #3a4f71;
}

.cell-chk input[type=checkbox]:checked::before {
  transform: scale(2);
}

.cell-chk div[approle=field-error] ul {
  padding-left: 50px;
}

@media screen and (max-width: 481px) {
  .form-grid {
    background-color:rgba(255, 255, 255, 0.970);
    margin-top: 1rem;
    margin-bottom: 0;
  }

  .cell-flex label {
    width: 100%;
    padding-bottom: 5px;
  }

  .cell-flex input:not([type=checkbox]), textarea, select{
    width: 100% !important;
    border-radius: 0.4rem;
  }

  .cell-flex div[approle=field-error] ul {
    width: 100%;
    padding-left: 1.0rem;
  }
  .cell-chk input[type=checkbox] {
    width: 1.50rem;
    height: 1.50rem;
  }
}
`;
