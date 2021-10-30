<?php
//functions.php 20200721
function appboot_loadenv(): void
{
    $arpaths = [
        "%PATH_PUBLIC%" => PATH_PUBLIC, "%PATH_ROOT%" => PATH_ROOT,
        "%PATH_SRC%" => PATH_SRC, "%PATH_SRC_CONFIG%" => PATH_SRC_CONFIG
    ];
    
    $arEnvs = ["local" => ".env.local", "dev" => ".env.dev","test" => ".env.test","prod" => ".env", ];

    foreach ($arEnvs as $envtype => $envfile) {
        $pathenv = PATH_ROOT . DS . $envfile;
        if (is_file($pathenv)) {
            $content = file_get_contents($pathenv);
            $lines = explode("\n", $content);

            foreach ($lines as $strline) {
                if (strstr($strline, "=")) {
                    $keyval = explode("=", $strline);
                    $key = trim($keyval[0]);
                    if ($key) {
                        $value = trim($keyval[1] ?? "");
                        $value = str_replace(array_keys($arpaths), array_values($arpaths), $value);
                        putenv(sprintf("%s=%s", $key, $value));
                        $_ENV[$key] = $value;
                    }
                }//if line has =
            }//foreach lines
            return;
        }//if is file
    }//foreach envs

    $_SERVER += $_ENV;
}

function console_loadenv(string $pathenv): void
{
    $envcontent = file_get_contents($pathenv);
    $envcontent = explode(PHP_EOL, $envcontent);
    foreach ($envcontent as $env)
    {
        if(substr($env, 0, 1) === "#" || trim($env)==="") continue;
        $parts = explode("=",$env);
        $key = trim($parts[0]);
        array_shift($parts);
        $value = implode("=",$parts);
        $value = trim($value);

        putenv(sprintf("%s=%s", $key, $value));
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

function get_console_args($argv): array
{
    $_ARG = array();
    foreach ($argv as $arg_i)
    {
        if (preg_match("/--([^=]+)=(.*)/",$arg_i,$arKeyVal)) {
            $_ARG[$arKeyVal[1]] = $arKeyVal[2];
        }
        elseif(preg_match("/-([a-zA-Z0-9])/",$arg_i,$arKeyVal)) {
            $_ARG[$arKeyVal[1]] = "true";
        }
    }
    return $_ARG;
}

function __(): string
{
    $args = func_get_args();
    $msgid = array_shift($args);
    if (!$msgid) return "";

    $msgchanged = $msgid;
    foreach ($args as $i => $str) {
        $rep = "{$i}";
        $msgchanged = str_replace($rep, $str, $msgchanged);
    }

    $lang = strtolower(trim($_REQUEST["ACTION_LANG"] ?? "en"));
    if ($lang === "en") return $msgchanged;

    $pathpo = PATH_BOOT."/locale/$lang/default.po";
    if(!is_file($pathpo)) return $msgchanged;

    if(!$_REQUEST["TRANSLATIONS"] ?? []) {
        $content = file_get_contents($pathpo);
        $content = trim($content);
        if (!$content) return $msgchanged;

        $lines = explode(PHP_EOL, $content);

        $trs = [];
        foreach ($lines as $i => $line) {

            if (!$line = trim($line)) continue;
            if(strstr($line, "msgstr \"")) continue;

            $id = str_replace("msgid \"","", $line);
            $id = substr($id, 0, -1);

            $tr = trim($lines[$i+1] ?? "");
            $tr = str_replace("msgstr \"","", $tr);
            $tr = substr($tr, 0, -1);

            $trs[$id] = $tr;
        }
        $_REQUEST["TRANSLATIONS"] = $trs;
    }

    $msgchanged = $_REQUEST["TRANSLATIONS"][$msgid] ?? $msgid;
    foreach ($args as $i => $str) {
        $rep = "{$i}";
        $msgchanged = str_replace($rep, $str, $msgchanged);
    }

    return $msgchanged;
}

use \App\Factories\KafkaFactory;

function get_log_producer(): \App\Components\Kafka\ProducerComponent
{
    return KafkaFactory::get_producer();
}