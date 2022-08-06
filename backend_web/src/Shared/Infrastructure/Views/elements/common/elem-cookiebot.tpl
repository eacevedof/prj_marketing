<!-- elem-cookiebot -->
<script
    id="Cookiebot"
    src="https://consent.cookiebot.com/uc.js"
    data-cbid="d3d2901b-f06c-428a-8377-8ffd7708c1a6"
    data-blockingmode="auto"
    type="text/javascript"></script>

<script type="module">
document.addEventListener("DOMContentLoaded", () => {
  const COOKIEBOTID = "CookiebotWidget";
  let times = 0;
  let thread = setInterval(()=>{
    times++
    if (times===10) clearInterval(thread)
    const $cookiebot = document.getElementById(COOKIEBOTID)
    if ($cookiebot) {
      $cookiebot.style.display = "none";
    }
  }, 500)
})
</script>
<!-- /elem-cookiebot -->