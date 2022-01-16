<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Services\Console\Dev\Builder\ModuleService
 * @file ModuleService.php 1.0.0
 * @date 31-10-2022 17:46 SPAIN
 * @observations
 */
namespace App\Services\Console\Dev\Builder;

final class PhpBuilder
{
   private string $type;
   private string $pathtpl;
   private array $aliases;
   private array $fields;

   public const TYPE_ENTITY = "entity";
    public const TYPE_REPOSITORY = "repository";

   public function __construct(array $aliases, array $fields, string $pathtpl, string $type=self::TYPE_ENTITY)
   {
       $this->pathtpl = $pathtpl;
       $this->aliases = $aliases;
       $this->fields = $fields;
       $this->type = $type;
   }

   private function _build_entity(): string
   {
        $contenttpl = file_get_contents($this->pathtpl);

        return $contenttpl;
   }

   public function get_content(): string
   {
       switch ($this->type) {
           case self::TYPE_ENTITY:
               return $this->_build_entity();
       }
       return "";
   }

}