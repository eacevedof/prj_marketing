<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Factories\DbFactory
 * @file DbFactory.php v1.0.0
 * @date 29-11-2018 19:00 SPAIN
 * @observations
 */

namespace App\Shared\Infrastructure\Factories;

use PDO;
use Exception;
use TheFramework\Components\Db\ComponentMysql;
use TheFramework\Components\Db\Context\ComponentContext;

final class DbFactory
{
    private static function getIdxInContextByDbName(array $arContext, string $dbname): ?int
    {
        if (!$dbname) {
            return 0;
        }

        $schemas = $arContext["schemas"] ?? [];
        if (!$schemas) {
            return null;
        }

        foreach($schemas as $i => $schema) {
            if ($schema["database"] === $dbname) {
                return $i;
            }
        }

        return null;
    }

    private static function _getDbConfig(array $arConfig, string $dbname): array
    {
        $arContext = $arConfig["ctx"] ?? [];
        if (!$arContext) {
            return [];
        }

        $idx = self::getIdxInContextByDbName($arContext, $dbname);
        $arDbconf = [
            //"type"=>$arContext["type"] ?? "",
            "server" => $arContext["server"] ?? "",
            "port" => $arContext["port"] ?? "3306",
            "database" => $arContext["schemas"][$idx]["database"] ?? "",
            "user" => $arContext["schemas"][$idx]["user"] ?? "",
            "password" => $arContext["schemas"][$idx]["password"] ?? ""
        ];
        //pr($arDbconf);
        return $arDbconf;
    }

    public static function getMysqlInstanceByConfiguredContextAndDbName(
        ComponentContext $oCtx,
        string $dbname = ""
    ): ComponentMysql {
        //pr($oCtx,"octx");
        $arConfig = $oCtx->getSelected();
        $arConfig = self::_getDbConfig($arConfig, $dbname);
        //bug($arConfig,"arconfig");die;
        if (!$arConfig) {
            return new ComponentMysql;
        }
        $oDb = new ComponentMysql($arConfig);
        return $oDb;
    }

    public static function getPDOInstanceByConfiguredContextAndDbName(
        ComponentContext $ctx,
        string $dbname = ""
    ): ?PDO {
        $arConfig = $ctx->getSelected();
        $arConfig = self::_getDbConfig($arConfig, $dbname);
        if (!$arConfig) {
            return null;
        }

        $arconstr["mysql:host"] = $arConfig["server"] ?? "";
        $arconstr["dbname"] = $arConfig["database"] ?? "";
        $arconstr["port"] = $arConfig["port"] ?? "";

        $sString = "";
        foreach($arconstr as $sK => $sV) {
            $sString .= "$sK=$sV;";
        }

        try {
            $oPdo = new PDO(
                $sString,
                $arConfig["user"],
                $arConfig["password"],
                [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"]
            );
            $oPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $oPdo;
        } catch (Exception $e) {
            return null;
        }
    }

    public static function getMysqlInstanceByEnvConfiguration(): ComponentMysql
    {
        $context = new ComponentContext(getenv("APP_CONTEXTS"), getenv("APP_ID_CONTEXT"));
        $dbname = $context->getDbNameByAlias(getenv("APP_DB_ALIAS_1"));
        $db = self::getMysqlInstanceByConfiguredContextAndDbName($context, $dbname);
        return $db;
    }

}//DbFactory
