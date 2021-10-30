<?php
/**
 * @var \App\Views\AppView $this
 * @var string $pagetitle
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$pagetitle?></title>
    <script src="https://unpkg.com/vue@next"></script>
</head>
<body>
<?
$this->_template();
?>
<div id="counter">
    Counter: {{ counter }}
</div>
<script>
const Counter = {
    data() {
        return {
            counter: 0
        }
    },
    mounted() {
        setInterval(() => {
            this.counter++
        }, 1000)
    }
}

Vue.createApp(Counter).mount('#counter')
</script>
</body>
</html>
