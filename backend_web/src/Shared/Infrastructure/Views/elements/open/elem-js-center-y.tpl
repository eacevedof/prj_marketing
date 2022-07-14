<script type="module">
const ishow = 50
const $divtop = document.getElementById("div-totop")
window.addEventListener("scroll", function() {
  if (!$divtop) return
  $divtop.style.display = "none"
  if(document.documentElement.scrollTop > ishow){
    $divtop.style.display = "inherit"
  }
})
</script>
<script>
//esto no puede ser type=module pq sino la animacion se ve muy grande
const animation = document.querySelector(".ul-circles")
animation.style.height = document.body.offsetHeight.toString().concat("px")
</script>