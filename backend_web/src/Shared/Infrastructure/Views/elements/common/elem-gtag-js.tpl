<!-- elem-gtag -->
<?php
if (!ENV::is_prod()) return;

use App\Shared\Infrastructure\Factories\ComponentFactory as CF;
use App\Shared\Infrastructure\Components\Request\RequestComponent;
$requri = CF::get(RequestComponent::class)->get_request_uri();
if (strstr($requri,"/restrict")) return;
?>
<script async src="https://www.googletagmanager.com/gtag/js?id=G-WJCL30FNFS"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag("js", new Date());
gtag("config", "G-WJCL30FNFS");
</script>
<!-- /elem-gtag -->