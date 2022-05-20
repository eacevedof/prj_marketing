<?php
/**
 * @var App\Shared\Infrastructure\Views\AppView $this
 * @var string $title
 * @var array | string $messages
 */
?>
<!--elem-error-die-->
<style>
section.error {
  background-color: tomato;
  color: white;
}
section.error h3 {

}
section.error ul {

}
</style>
<section class="error">
  <h3><?$this->_echo_nohtml($title);?></h3>
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
