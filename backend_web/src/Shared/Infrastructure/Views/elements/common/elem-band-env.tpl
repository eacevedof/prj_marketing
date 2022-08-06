<!-- elem-env -->
<?php
if (ENV::is_prod()) return;

if (ENV::is_test())
  $color = [
      "color" => "#FFF",
      "bg" => "#E65C00",
  ];

if (ENV::is_dev())
  $color = [
    "color" => "#FFF",
    "bg" => "#FDFC47",
  ];

if (ENV::is_local())
  $color = [
    "color" => "#FFF",
    "bg" => "#0074ED",
  ];
?>
<div style="background-color:<?=$color["bg"]?>; color:<?=$color["color"]?>; position: sticky; height: 7px; width: 100%; border: 1px solid black;">
</div>
<!-- /elem-env -->