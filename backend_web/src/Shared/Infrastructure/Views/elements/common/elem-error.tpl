<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var string $title
 * @var string $description
*/
?>
<div>
<?php
echo isset($title) ? "<h1>$title</h1>" : "";
echo isset($description) ? "<p>$description</p>" : "";
?>
</div>