<style>
/*elem-promotioncaps-style.tpl*/
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-size: 16px;
  font-family: "Roboto", sans-serif;
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
.animation-h-shaking {
  animation:frames-h-shaking;
  animation-duration: 0.25s;
  animation-iteration-count: 2;
}

@keyframes frames-h-shaking {
  0% { transform: translateX(0) }
  25% { transform: translateX(5px) }
  50% { transform: translateX(-5px) }
  75% { transform: translateX(5px) }
  100% { transform: translateX(0) }
}

.main-flex {
  display: flex;
  flex-direction: column;
  align-items: center;

  min-height: 100vh;
  max-width: 100vw;
  background-size:     cover;
  background-position: top center;
  background-repeat:   no-repeat;
}

.nav-flex {
  display: flex;
  flex-direction: row;
  justify-content: space-around;
  align-items: center;
  min-height: 2rem;
  overflow: hidden;
  background-color: #333333;
  position: sticky;
  width: 100%;
  flex-wrap: wrap;
  -webkit-box-shadow: 0px 14px 5px -10px rgba(0,0,0,0.36);
  box-shadow: 0px 14px 5px -10px rgba(0,0,0,0.36);

  position: -webkit-sticky; /* for Safari */
  position: sticky;
  top: 0;
  z-index: 1;
}

.nav-flex img {
  max-width: 175px;
  max-height: 70px;
  object-fit: cover;
}
.nav-flex h1 {
  font-size: 1rem;
  color: white;
  padding: 0.5rem;
  text-align: center;
}

.section {
  margin:0;
  padding: 0;
}
.section .subscription-message {
  background-color:rgba(255, 255, 255, 0.95);
  padding: 2rem;
  border-radius: 0.5rem;
  border: 2px solid black;
  -webkit-box-shadow: 5px 3px 6px 0px rgba(0,0,0,0.38);
  box-shadow: 5px 3px 6px 0px rgba(0,0,0,0.38);
  margin: 1rem;
  max-width: 500px;
  color: #55e755;
  font-size: 2rem;
  text-align: center;
}
</style>