<?php

namespace App\Components\DiskCache;

final class DiskCacheComponent
{
    private string $pathcache = PATH_DISK_CACHE ?? "./";
    private string $pathsub = "";
    protected int $time = 3600;
    private string $keyname = "";
    private string $hashname = "";
    private string $pathfinal = "";

    private function _load_hashname(): self
    {
        $this->hashname = md5($this->keyname);
        return $this;
    }

    private function _load_pathfinal(): void
    {
        $path = $this->pathcache;
        if ($this->pathsub) $path .= "/$this->pathsub";
        $this->pathfinal = $path;
    }

    private function _get_cached_files(): array
    {
        $files = scandir($this->pathfinal);
        if (count($files) == 2) return [];
        
        $files = array_filter($files, function ($file) {
           return strstr($file, $this->hashname); 
        });
        return $files[0] ?? [];
    }

    private function _get_cached_file(): string
    {
        $files = $this->_get_cached_files();
        return $files[0] ?? "";
    }

    private function _get_dietime(string $date): int
    {
        //$now = date("Y-m-d H:i:s");
        return (int) date("YmdHis", (strtotime($date) + $this->time));
    }

    private function _remove_olds(): void
    {
        $files = $this->_get_cached_files();
        foreach ($files as $file) {
            $path = "{$this->pathfinal}/$file";
            if (is_file($path)) unlink($path);
        }
    }

    public function is_alive(): bool
    {
        $this->_load_hashname()->_load_pathfinal();        
        $filename = $this->_get_cached_file();
        if (!$filename) return false;
        $end = explode("-",$filename);
        $end = end($end);
        $end = substr($end, -4);//quita .dat
        if (!$end || !is_numeric($end)) return false;
        $dietime = $this->_get_dietime($end);
        return ($dietime > (int) date("YmdHis"));
    }

    public function write(string $content): string
    {
        $this->_remove_olds();
        $dietime = $this->_get_dietime(date("YmdHis"));
        $path = "{$this->pathfinal}/$this->hashname-{$dietime}.dat";
        file_put_contents($path, $content);
        return $path;
    }

    public function get_content(): ?string
    {
        $filename = $this->_get_cached_file();
        return file_get_contents($filename);
    }

    public function set_folder(string $pathsub): self
    {
        $this->pathsub = $pathsub;
        return $this;
    }
    
    public function set_keyname(string $keyname): self
    {
        $this->keyname = $keyname;
        return $this;
    }

    public function set_time(int $time): self
    {
        $this->time = $time;
        return $this;
    }
}