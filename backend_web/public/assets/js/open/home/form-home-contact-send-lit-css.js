import {css} from "/assets/js/vendor/lit.dev/lit-bundle.js"

export const cssformcontact = css`
.animation-shaking-x {
  animation:frames-shaking-x;
  animation-duration: 0.25s;
  animation-iteration-count: 2;
}

@keyframes frames-shaking-x {
  0% { transform: translateX(0) }
  25% { transform: translateX(5px) }
  50% { transform: translateX(-5px) }
  75% { transform: translateX(5px) }
  100% { transform: translateX(0) }
}

.form-grid {
  display: grid;
  row-gap: 1rem;
  padding: 1rem;
  max-width: 525px;
}

.form-grid .success-top {
  border-radius: 4px;
  border: 1px solid green;
  background-color: var(--color-white);
  color: green;
  padding: .5rem;
  justify-self: center;
  text-align: center;
  width: 100%;
}

.form-grid .cell-flex {
  display: flex;
  flex-wrap: wrap;
}

.form-grid .cell-flex label {
  display: flex;
  min-width: 5rem;
  font-weight: bold;
  align-items: center;
}

.form-grid .cell-flex textarea {
  height: 5rem;
}

.form-grid .cell-flex textarea, input {
  font-size: 1rem;
  width: 400px;
  padding: 10px;
  border: 1px solid var(--color-clear-black);
  border-radius: 6px;
  resize: none;
}

.form-grid .error-top {
  margin-top: 1rem;
  background: white;
  align-self: center;
  border: 1px solid red;
  padding: 3px;
  padding-bottom: 6px;
  border-radius: 5px;
}

.form-grid .cell-flex div[approle=field-error] ul {
  display: flex;
  list-style-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
  flex-direction: column;
  margin: 0;
  padding: 0;
  width: auto;
  margin-left: 6rem;
  padding-top: .15rem;
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
  padding: 10px 40px 14px;
  border: 1px solid var(--color-clear-black);
  border-radius: 6px;
  font-weight: bold;
  font-size: 1.2rem;
  background-color: var(--color-orange);
  transition: background-color .3s;
  margin-top: 1rem;
}

.form-grid .cell-btn .button span {
  padding-bottom:5px;
}

.form-grid .cell-btn .button img {
  width: 25px;
  height: 25px;
  position: relative;
  top: 4px;
  right: -33px;
  animation: rotation 1s infinite linear;
}

@keyframes rotation {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(359deg);
  }
}

.form-grid .button-exit {
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

.form-grid .button:hover, .button-exit:hover {
  background-color: var(--color-dark-blue);
  color: var(--color-white);
}

@media screen and (max-width: 645px) {
  .form-grid {
    width: 100%;
    margin:0;
    padding:0;
    margin-top: 1.5rem;
  }  

  .form-grid .cell-flex label {
    width: 100%;
    padding-bottom: 5px;
  }

  .form-grid .cell-flex div[approle=field-error] ul {
    width: auto;
    margin-left: 1rem;
  }

  .form-grid .cell-flex textarea, input {
    width: calc(100% - 20px);
  }
  
  .form-grid .cell-flex textarea {
    height: 5rem;
  }
  
  .form-grid .cell-btn .button {
    margin-top: 0;
    margin-bottom: 10px;
  }
}
`;