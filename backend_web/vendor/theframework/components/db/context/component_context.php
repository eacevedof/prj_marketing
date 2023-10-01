<?php

namespace TheFramework\Components\Db\Context;

use Exception;

final class ComponentContext
{
    private $isError;
    private $arErrors;

    private $arContexts;
    private $arContextPublic;

    private $idSelected;
    private $arSelected;

    public function __construct(string $sPathfile = "", string $idSelected = "")
    {
        $this->idSelected = $idSelected;
        $this->arContexts = [];

        if (!$sPathfile) {
            $sPathfile = $_ENV["APP_CONTEXTS"] ?? __DIR__.DIRECTORY_SEPARATOR."contexts.json";
        }

        if (!is_file($sPathfile)) {
            $this->addError("No context file found: $sPathfile");
            return;
        }
        $this->_loadArrayFromJson($sPathfile);
        $this->_loadPublicContexts();
        $this->_loadSelectedContext();
    }

    private function _loadArrayFromJson(string $sPathfile): void
    {
        if ($sPathfile) {
            if (is_file($sPathfile)) {
                $sJson = file_get_contents($sPathfile);
                $this->arContexts = json_decode($sJson, 1, JSON_UNESCAPED_UNICODE);
                if (is_null($this->arContexts)) {
                    throw new Exception("Contexts not loaded");
                }
            } else {
                $this->addError("_load_array_fromjson: file $sPathfile not found");
            }
        } else {
            $this->addError("_load_array_fromjson: no pathfile passed");
        }
    }

    /**
     * carga la información que no es sensible, por eso se elimina schemas
     */
    private function _loadPublicContexts(): void
    {
        foreach($this->arContexts as $arContext) {
            unset($arContext["schemas"],$arContext["server"],$arContext["port"]);
            $this->arContextPublic[] = $arContext;
        }
    }

    private function _loadSelectedContext(): void
    {
        //pr($this->idSelected);
        //si no se pasa id se asume que no se ha seleccionado un contexto
        $this->arSelected["ctx"] = $this->getContextById($this->idSelected);
        //pr($this->arSelected,"arselected");die;
        if ($this->arSelected["ctx"]) {
            $this->arSelected["ctx"] = $this->arSelected["ctx"][array_keys($this->arSelected["ctx"])[0]];
        }

        $this->arSelected["pubconfig"] = $this->getPublicConfigByKV("id", $this->idSelected);
        //pr($this->arSelected,"arSelected");
    }

    private function _getContextInLevel1By(
        string $sKey,
        string $sValue,
        array $arArray = []
    ): array {
        if (!$sKey && !$sValue) {
            return [];
        }
        if (!$arArray) {
            $arArray = $this->arContexts;
        }

        $arFiltered = array_filter($arArray, function ($arConfig) use ($sKey, $sValue) {
            $confval = $arConfig[$sKey] ?? "";
            return $confval === $sValue;
        });
        return $arFiltered;
    }

    public function getContexts(): array
    {
        return $this->arContexts;
    }

    public function getContextById(string $id): array
    {
        return $this->_getContextInLevel1By("id", $id);
    }

    public function getContextByKV(string $key, string $val): array
    {
        return $this->_getContextInLevel1By($key, $val);
    }

    public function getSchemasByKV(string $key, string $val): array
    {
        $arConfig = $this->_getContextInLevel1By($key, $val);
        if ($arConfig) {
            $arConfig = $arConfig[array_keys($arConfig)[0]];
            return $arConfig["schemas"];
        }
        return [];
    }

    public function getSelected(): array
    {
        return $this->arSelected;
    }
    public function getSelectedId(): string
    {
        return $this->arSelected["ctx"]["id"] ?? "";
    }

    public function getSelectedSchemas(): array
    {
        return $this->arSelected["ctx"]["schemas"] ?? [];
    }

    public function getPublicConfigByKV(string $key, string $val): array
    {
        $arConfig = $this->_getContextInLevel1By($key, $val, $this->arContextPublic);
        if ($arConfig) {
            return $arConfig[array_keys($arConfig)[0]];
        }
        return [];
    }

    public function getPublicConfig(): array
    {
        return $this->arContextPublic;
    }
    public function getErrors(): array
    {
        return isset($this->arErrors) ? $this->arErrors : [];
    }
    public function isError(): bool
    {
        return $this->isError;
    }
    public function getDbNameByAlias(string $alias): string
    {
        $schemas = $this->getSelectedSchemas();
        foreach ($schemas as $schema) {
            $schalias = $schema["alias"] ?? "";
            if ($schalias === $alias) {
                return $schema["database"] ?? "";
            }
        }
        return "";
    }

    private function _addError(string $message): void
    {
        $this->isError = true;
        $this->arErrors[] = $message;
    }

}//ComponentContext

/*
Array
(
    [0] => Array
        (
            [id] => c1
            [alias] => Docker mysql
            [description] => Docker mysql
            [type] => mysql
            [server] => 127.0.0.1
            [port] => 3350
            [schemas] => Array
                (
                    [0] => Array
                        (
                            [database] => db_one
                            [user] => root
                            [password] => root
                        )

                    [1] => Array
                        (
                            [database] => db_two
                            [user] => root
                            [password] => root
                        )

                )

        )

    [1] => Array
        (
        )

)
*/
