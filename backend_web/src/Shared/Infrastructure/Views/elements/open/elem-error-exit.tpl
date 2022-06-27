<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var string $title
 * @var array | string $messages
 */
if (is_string($messages)) $messages = [$messages];
if (!isset($title)) $title = __("Error");
?>
<!--elem-error-die-->
<style>
section.error {
  background-color: tomato;
  color: white;
  margin: 2em;
  padding: 0.1em;
}
section.error h3 {
  margin-left: 2em;
  margin-right: 2em;
  border-bottom: 1px solid white;
  padding: 0.08rem;
}
section.error ul {

}
</style>
<section class="error">
  <h3><?php $this->_echo_nohtml($title); ?></h3>
  <?php
  $ul = [];
  foreach ($messages as $message)
    $ul[] = "<li>".$this->_nohtml($message)."</li>";
  if ($ul)
    echo "<ul>".implode("",$ul)."</ul>";
  ?>
</section>
<?php
exit;
?>
