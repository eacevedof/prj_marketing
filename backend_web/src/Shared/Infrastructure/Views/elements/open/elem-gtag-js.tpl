<!-- elem-gtag -->
<?php
if (getenv("APP_ENV")!=="prod") return;
?>
<script async src="https://www.googletagmanager.com/gtag/js?id=G-WJCL30FNFS"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag("js", new Date());
gtag("config", "G-WJCL30FNFS");
</script>
<!-- /elem-gtag -->