<?php

namespace App\Shared\Infrastructure\Components\DiskCache;

use BOOT;

final class DiskCacheComponent
{
    private string $pathcache = BOOT::PATH_DISK_CACHE ?? "./";
    private string $pathSubFolder = "";
    private int $secondsTtl = 3600;
    private string $keyToBeHashed = "";
    private string $hashName = "";
    private string $pathFinalDir = "";

    private function _loadHashName(): void
    {
        $this->hashName = md5($this->keyToBeHashed);
    }

    private function _loadPathFinalDir(): void
    {
        $path = $this->pathcache;
        if ($this->pathSubFolder) {
            $path .= "/$this->pathSubFolder";
        }
        $this->pathFinalDir = $path;
    }

    private function _getAllCachedFilesByHashName(): array
    {
        if (!is_dir($this->pathFinalDir)) {
            //705 es lo minimo para que funcione desde web
            mkdir($this->pathFinalDir, 0705, true);
            chmod($this->pathcache, 0705);
        }

        $files = scandir($this->pathFinalDir);
        if (count($files) === 2) {
            return [];
        }

        $files = array_filter($files, function ($file) {
            return strstr($file, $this->hashName);
        });
        return array_values($files);
    }

    private function _getFirstCachedFileByHashName(): string
    {
        $files = $this->_getAllCachedFilesByHashName();
        return $files[0] ?? "";
    }

    private function _getExpirationTimeAsInt(string $date): int
    {
        //$now = date("Y-m-d H:i:s");
        return (int) date("YmdHis", (strtotime($date) + $this->secondsTtl));
    }

    private function _removeAllCachedByHashName(): void
    {
        $files = $this->_getAllCachedFilesByHashName();
        foreach ($files as $file) {
            $path = "{$this->pathFinalDir}/$file";
            if (is_file($path)) {
                unlink($path);
            }
        }
    }

    public function isCachedFileAlive(): bool
    {
        $this->_loadHashName();
        $this->_loadPathFinalDir();
        $fileName = $this->_getFirstCachedFileByHashName();
        if (!$fileName) {
            return false;
        }
        $expirationDate = explode("-", $fileName);
        $expirationDate = end($expirationDate);
        $expirationDate = substr_replace($expirationDate, "", -4);
        if (!($expirationDate && is_numeric($expirationDate))) {
            return false;
        }
        return (
            ((int) $expirationDate) > ((int) date("YmdHis"))
        );
    }

    public function write(string $content): string
    {
        $this->_removeAllCachedByHashName();
        $dieTimeAsInt = $this->_getExpirationTimeAsInt(date("YmdHis"));
        $path = "{$this->pathFinalDir}/$this->hashName-{$dieTimeAsInt}.dat";
        $r = file_put_contents($path, $content);
        return "{$this->hashName} $dieTimeAsInt cache until: ".date("Y-m-d H:i:s", $dieTimeAsInt);
    }

    public function getCachedFileContent(): ?string
    {
        $filename = $this->_getFirstCachedFileByHashName();
        $filename = "$this->pathFinalDir/$filename";
        return file_get_contents($filename);
    }

    public function setSubFolder(string $pathSubFolder): self
    {
        $this->pathSubFolder = $pathSubFolder;
        return $this;
    }

    public function setKeyToBeHashed(string $keyToBeHashed): self
    {
        $this->keyToBeHashed = $keyToBeHashed;
        return $this;
    }

    public function setSecondsTtl(int $secondsTtl): self
    {
        $this->secondsTtl = $secondsTtl;
        return $this;
    }
}
