<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var string $title
 * @var string $error
*/
?>
<?php
echo isset($title) ? "<h1>$title</h1>" : "";
echo isset($error) ? "<p>$error</p>" : "";
?>