<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @version 1.5.1
 * @name ComponentEncdecrypt
 * @file component_encdecrypt.php
 * @date 15-04-2017 10:04 (SPAIN)
 * @observations:
 * https://github.com/eacevedof/prj_theframework/blob/9192e1a90010792048a6383efbebbd85f105922a/the_application/components/appcomponent_encdecrypt.php
 * @requires:
 */

namespace TheFramework\Components\Session;

final class ComponentEncDecrypt
{
    public const ENCRYPT_TYPE_NORMAL = "normal";
    public const ENCRYPT_TYPE_NUMERIC = "numeric";

    private string $encryptType;
    private int $iLenDirty = 5;
    private array $arNumberConfig;
    private array $arExcludeChars;

    private string $sSslMethod;
    private string $sSslKey;
    private string $sSslIv;

    private ?bool $useSalt;
    private string $saltString;
    private bool $useTime = false;

    public function __construct(?bool $useSalt = true)
    {
        $this->arExcludeChars = [];
        $this->arNumberConfig = [
            "0" => "e","1" => "F","2" => "g","3" => "H","4" => "i",
            "5" => "j","6" => "K","7" => "l","8" => "m","9" => "N"
        ];

        $this->encryptType = self::ENCRYPT_TYPE_NORMAL;
        $this->sSslMethod = "AES-256-CBC";
        $this->sSslKey = "@11111111@";
        $this->sSslIv = "99326425";

        $this->useSalt = $useSalt;
        $this->saltString = "@#$.salt.$#@";

        if (defined("ENV_SSLENC_METHOD")) {
            $this->sSslMethod = ENV_SSLENC_METHOD;
        }
        if (defined("ENV_SSLENC_KEY")) {
            $this->sSslKey = ENV_SSLENC_KEY;
        }
        if (defined("ENV_SSLENC_IV")) {
            $this->sSslIv = ENV_SSLENC_IV;
        }
        if (defined("ENV_MD5_SALT")) {
            $this->saltString = ENV_MD5_SALT;
        }
    }

    private function tryToApplySalt(string &$toBeSalted, ?bool $useSalt = null): void
    {
        if ($useSalt === null) {
            $useSalt = $this->useSalt;
        }
        if ($useSalt) {
            $toBeSalted = "$this->saltString.$toBeSalted";
        }
    }

    public function getSslEncrypted(string $sToEncrypt, ?bool $useSalt = null): string
    {
        $sSslKey = $this->sSslKey;
        $this->tryToApplySalt($sSslKey, $useSalt);
        $sHashKey = hash("sha256", $sSslKey);
        $sHashIv = substr(hash("sha256", $this->sSslIv), 0, 16);
        //print_r(openssl_get_cipher_methods());
        //pr("toenc:$sToEncrypt,method:$this->sSslMethod,hashkey:$sHashKey,0,hashiv:$sHashIv");
        if ($this->useTime) {
            $sToEncrypt = $sToEncrypt."-".date("YmdHis");
        }
        $sEncrypted = openssl_encrypt($sToEncrypt, $this->sSslMethod, $sHashKey, 0, $sHashIv);
        $sEncrypted = base64_encode($sEncrypted);
        return $sEncrypted;
    }//get_sslencrypted

    public function getSslDecrypted(string $encryptedString, ?bool $useSalt = null): string
    {
        $sSslKey = $this->sSslKey;
        $this->tryToApplySalt($sSslKey, $useSalt);
        $sHashKey = hash("sha256", $sSslKey);
        $sHashIv = substr(hash("sha256", $this->sSslIv), 0, 16);
        $sDecrypted = base64_decode($encryptedString);
        $sDecrypted = openssl_decrypt($sDecrypted, $this->sSslMethod, $sHashKey, 0, $sHashIv);
        if ($this->useTime) {
            $sDecrypted = explode("-", $sDecrypted);
            return $sDecrypted[0];
        }
        return $sDecrypted;
    }//get_ssldecrypted

    public function getMd5Password(string $sToEncrypt, ?bool $useSalt = null): string
    {
        $this->tryToApplySalt($sToEncrypt, $useSalt);
        $sMd5 = md5($sToEncrypt);
        return $sMd5;
    }//get_md5password

    public function isValidMd5Password(string $plainPass, string $md5Pass, ?bool $useSalt = null): bool
    {
        $sMd5 = $this->getMd5Password($plainPass, $useSalt);
        return($sMd5 === $md5Pass);
    }//check_md5password

    public function getPasswordHashed(string $plainPassword, ?bool $useSalt = null): string
    {
        $this->tryToApplySalt($plainPassword, $useSalt);
        return password_hash($plainPassword, PASSWORD_DEFAULT);
    }//get_hashpassword

    public function isValidPassword(string $plainPass, string $hashPass, ?bool $useSalt = null): bool
    {
        $this->tryToApplySalt($plainPass, $useSalt);
        return password_verify($plainPass, $hashPass);
    }

    public function getRandomWord(int $iLen = 8): string
    {
        $iLen = $iLen - 4;
        $arConsonants = ["b","c","d","f","g","h","j","k","l","m","n","p","q","r","s","t","v","w","x","y","z",
            "B","C","D","F","G","H","J","K","L","M","N","P","Q","R","S","T","V","W","X","Y","Z"
        ];
        $arVocals = ["a","e","i","o","u","A","E","I","O","U"];
        $arChars = ["@","#","&","$"];
        $arNumbers = ["0","1","2","3","4","5","6","7","8","9"];

        $arWord = [];
        $arWord = [];
        for($i = 0; $i < $iLen; $i++) {
            if ($i % 2 === 0) {
                $iPos = array_rand($arConsonants, 1);
                $arWord[] = $arConsonants[$iPos];
            } else {
                $iPos = array_rand($arVocals, 1);
                $arWord[] = $arVocals[$iPos];
            }
        }

        $iPos = array_rand($arChars, 1);
        $arWord[] = $arChars[$iPos];
        shuffle($arNumbers);
        for($i = 0;$i < 3;$i++) {
            $iPos = array_rand($arNumbers, 1);
            $arWord[] = $arNumbers[$iPos];
        }
        return implode("", $arWord);
    }

    private function replaceNumbersWithLetters(string &$sNumeric): void
    {
        $arNumbers = array_keys($this->arNumberConfig);
        $arNums = str_split($sNumeric);
        foreach($arNums as $i => $cNumber) {
            if (in_array($cNumber, $arNumbers)) {
                $arNums[$i] = $this->arNumberConfig[$cNumber];
            }
        }
        $sNumeric = implode("", $arNums);
    }

    private function replaceLettersWithNumbers(string &$sLetters): void
    {
        $arLetters = array_values($this->arNumberConfig);
        $arChars = str_split($sLetters);
        foreach($arChars as $i => $cLetter) {
            if (in_array($cLetter, $arLetters)) {
                $arChars[$i] = array_search($cLetter, $this->arNumberConfig);
            }
        }
        $sLetters = implode("", $arChars);
    }//num_replace

    private function getRandomString(?int $iLenDirty = null): string
    {
        if (!$iLenDirty) {
            $iLenDirty = $this->iLenDirty;
        }

        $arChars = str_split(
            "abcdefghijklmnopqrstuvwxyz"
            ."ABCDEFGHIJKLMNOPQRSTUVWXYZ"
            ."0123456789!@#$%^&*()"
        );
        //quito los caracteres que no me interesan, por ejemplo %,$ para las urls
        if ($this->arExcludeChars) {
            foreach($arChars as $i => $cChar) {
                if (in_array($cChar, $this->arExcludeChars)) {
                    unset($arChars[$i]);
                }
            }
        }

        shuffle($arChars);
        $sRandom = "";
        foreach(array_rand($arChars, $iLenDirty) as $iPos) {
            $sRandom .= $arChars[$iPos];
        }
        return $sRandom;
    }//getRandomString

    private function getDirtyString(string $sString): string
    {
        $sString = strrev($sString);
        $arChars = str_split($sString);
        $sDirty = array();
        foreach($arChars as $c) {
            $sDirty[] = $c;
            $sDirty[] = $this->getRandomString();
        }
        $sDirty = implode("", $sDirty);
        $sDirty = $this->getRandomString().$sDirty;
        return $sDirty;
    }//dirty

    private function getDirtyNumber(string $sNumeric): string
    {
        //cambio los numeros por letras segun arNumberConfig
        $this->replaceNumbersWithLetters($sNumeric);
        return $this->getDirtyString($sNumeric);
    }//getDirtyNumber

    private function cleanInventedChars(string $sString, ?int $iLenDirty = null): string
    {
        if (!$iLenDirty) {
            $iLenDirty = $this->iLenDirty;
        }
        $arChars = str_split($sString);
        $sCleaned = [];

        $iDirty = 0;
        $i = 0;
        foreach($arChars as $c) {
            if ($i == $iLenDirty) {
                $sCleaned[] = $c;
                $i = 0;
            } else {
                $i++;
            }
        }
        $sCleaned = implode("", $sCleaned);
        $sCleaned = strrev($sCleaned);
        return $sCleaned;
    }//clean

    private function cleanAndLeaveOnlyNumbers(string $sString, int $iLenDirty = null)
    {
        //limpio los caracteres inventados
        $sCleaned = $this->cleanInventedChars($sString, $iLenDirty);
        $this->replaceLettersWithNumbers($sCleaned);
        return $sCleaned;
    }//clean_number

    public function get_encrypted(string $text): string
    {
        switch($this->encryptType) {
            case self::ENCRYPT_TYPE_NUMERIC:
                return $this->getDirtyNumber($text);
            default://normal
                return $this->getDirtyString($text);
        }
    }//encrypt

    public function get_decrypted(string $sString): string
    {
        switch($this->encryptType) {
            case self::ENCRYPT_TYPE_NUMERIC:
                return $this->clean_number($sString);
            default://normal
                return $this->cleanInventedChars($sString);
        }
    }//get_decrypted

    public function getCsrf(?bool $useSalt = null): string
    {
        $sessionId = session_id();
        if ($sessionId) {
            $sessionId = $this->getPasswordHashed($sessionId, $useSalt);
        }
        return $sessionId;
    }//get_csrf

    public function get_uniqid($sToUnique, $inMd5 = 0)
    {
        if ($inMd5) {
            return md5(uniqid($sToUnique, true));
        }
        return uniqid($sToUnique, true);
    }//get_uniqid

    public function getSalted(string $sPass): string
    {
        return "{$this->saltString}{$sPass}";
    }

    public function setEncryptType(string $encType): void
    {
        $this->encryptType = $encType;
    }

    public function setLenOfDirty(int $iLenDirty): void
    {
        $this->iLenDirty = $iLenDirty;
    }

    public function setCharsToExclude(string | array $mxChars): void
    {
        if (is_string($mxChars)) {
            $this->arExcludeChars = str_split($mxChars);
        } elseif (is_array($mxChars)) {
            $this->arExcludeChars = $mxChars;
        }
        $this->arExcludeChars = array_unique($this->arExcludeChars);
    }

    public function addCharToBeExcluded(string $cChar): void
    {
        $this->arExcludeChars[] = $cChar;
        $this->arExcludeChars = array_unique($this->arExcludeChars);
    }

    public function setSslKey(string $sslKey): void
    {
        $this->sSslKey = $sslKey;
    }

    public function setSaltString(string $saltString): void
    {
        $this->saltString = $saltString;
    }

    public function setSslMethod(string $sslMethod): void
    {
        $this->sSslMethod = $sslMethod;
    }

    public function setSslIv(int $sslIv): void
    {
        $this->sSslIv = $sslIv;
    }

    public function setUseSaltString(bool $isOn = true): void
    {
        $this->useSalt = $isOn;
    }

    //se usa para añadir miga a los valores numericos de modo que siempre devuelva un hash distinto
    public function setUseTimeEntropyForNumericType(bool $isOn = true): void
    {
        $this->useTime = $isOn;
    }
}//ComponentEncdecrypt
