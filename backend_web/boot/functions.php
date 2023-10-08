<?php

//functions.php 20200721
function dd($var, $title = "")
{
    $sContent = var_export($var, 1);
    if($title) {
        echo "<b style=\"font-size: small; font-family: 'Roboto', 'sans-serif'\">$title</b>";
    }
    echo "<pre style=\"background:greenyellow;border:1px solid;\">"
        .$sContent
        ."</pre>";
    exit;
}

function lgerr($var, $title = null)
{
    $dlog = date("Ymd");
    $sNow = date("Y-m-d_H:i:s");
    if($title) {
        $title = "<<  $title >>";
    }
    $title = PHP_EOL."$sNow: (lgerr) $title";
    if(!is_string($var)) {
        $var = var_export($var, 1);
    }
    if($var) {
        $var = PHP_EOL.$var.PHP_EOL;
    }
    $var = $title.$var;
    $sPathFile = BOOT::PATH_LOGS."/error/";
    if (!is_dir($sPathFile)) {
        mkdir($sPathFile);
    }
    $sPathFile .= "app_$dlog.log";
    $oCursor = fopen($sPathFile, "ab");
    fwrite($oCursor, $var);
    fclose($oCursor);
}

function appboot_loadenv(): void
{
    $arpaths = [
        "%PATH_PUBLIC%" => BOOT::PATH_PUBLIC, "%PATH_ROOT%" => BOOT::PATH_ROOT,
        "%PATH_SRC%" => BOOT::PATH_SRC, "%PATH_SRC_CONFIG%" => BOOT::PATH_SRC_CONFIG
    ];

    $arEnvs = [
        ENV::LOCAL => ".env.".ENV::LOCAL,
        ENV::DEV => ".env.".ENV::DEV,
        ENV::TEST => ".env.".ENV::TEST,
        ENV::PROD => ".env",
    ];

    foreach ($arEnvs as $envtype => $envfile) {
        $pathenv = BOOT::PATH_ROOT."/$envfile";
        if (is_file($pathenv)) {
            $content = file_get_contents($pathenv);
            $lines = explode(PHP_EOL, $content);

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
    foreach ($envcontent as $env) {
        if(substr($env, 0, 1) === "#" || trim($env) === "") {
            continue;
        }
        $parts = explode("=", $env);
        $key = trim($parts[0]);
        array_shift($parts);
        $value = implode("=", $parts);
        $value = trim($value);

        putenv(sprintf("%s=%s", $key, $value));
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

function get_console_args($argv): array
{
    $_ARG = [];
    foreach ($argv as $arg_i) {
        if (preg_match("/--([^=]+)=(.*)/", $arg_i, $arKeyVal)) {
            $_ARG[$arKeyVal[1]] = $arKeyVal[2];
        } elseif(preg_match("/-([a-zA-Z0-9])/", $arg_i, $arKeyVal)) {
            $_ARG[$arKeyVal[1]] = "true";
        }
    }
    return $_ARG;
}

function __(string $msgId): string
{
    if (!$msgId = trim($msgId)) {
        return "";
    }
    $args = func_get_args();
    array_shift($args);
    $msgChanged = $msgId;
    foreach ($args as $i => $str) {
        $rep = "{".$i."}";
        $msgChanged = str_replace($rep, $str, $msgChanged);
    }

    $lang = strtolower(trim($_REQUEST["lang"] ?? "en"));
    if ($lang === "en") {
        return $msgChanged;
    }

    $pathPoFile = PATH_ROOT."/locale/$lang/default.po";
    if(!is_file($pathPoFile)) {
        return $msgChanged;
    }

    if(!($_REQUEST["APP_TRANSLATIONS"][$lang] ?? [])) {
        $content = file_get_contents($pathPoFile);
        $content = trim($content);
        if (!$content) {
            return $msgChanged;
        }

        $lines = explode(PHP_EOL, $content);

        $trs = [];
        foreach ($lines as $i => $line) {

            if (!$line = trim($line)) {
                continue;
            }
            if (str_starts_with($line, "#")) {
                continue;
            }
            if (strstr($line, "msgstr \"")) {
                continue;
            }

            $id = str_replace("msgid \"", "", $line);
            $id = substr($id, 0, -1);
            $id = str_replace("\\\"", "\"", $id);

            $tr = trim($lines[$i + 1] ?? "");
            $tr = str_replace("msgstr \"", "", $tr);
            $tr = substr($tr, 0, -1);
            $tr = str_replace("\\\"", "\"", $tr);

            $trs[$id] = $tr;
        }
        $_REQUEST["APP_TRANSLATIONS"][$lang] = $trs;
        unset($lines, $trs);
    }

    $msgChanged = ($_REQUEST["APP_TRANSLATIONS"][$lang][$msgId] ?? "") ?: $msgId;

    foreach ($args as $i => $str) {
        $rep = "{".$i."}";
        $msgChanged = str_replace($rep, $str, $msgChanged);
    }

    return $msgChanged;
}
