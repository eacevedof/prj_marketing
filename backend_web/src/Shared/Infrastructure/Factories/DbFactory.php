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

use TheFramework\Components\Db\ComponentMysql;
use TheFramework\Components\Db\Context\ComponentContext;
use \PDO;

final class DbFactory
{

    private static function get_db_idx($arContext,$sDb)
    {
        if(!$sDb) return 0;
        $schemas = $arContext["schemas"] ?? [];
        if(!$schemas) return null;

        foreach($schemas as $i=>$arSchema)
            if($arSchema["database"] === $sDb)
                return $i;

        return null;
    }

    private static function _get_dbconfig($arConfig,$sDb)
    {
        $arContext = $arConfig["ctx"]?? [];
        if(!$arContext) return [];
        $iDb = self::get_db_idx($arContext,$sDb);

        $arDbconf = [
            //"type"=>$arContext["type"] ?? "",
            "server"=>$arContext["server"] ?? "",
            "port"=>$arContext["port"] ?? "3306",
            "database"=>$arContext["schemas"][$iDb]["database"] ?? "",
            "user"=>$arContext["schemas"][$iDb]["user"]?? "",
            "password"=>$arContext["schemas"][$iDb]["password"]?? ""
        ];
        //pr($arDbconf);
        return $arDbconf;
    }

    /**
     * @param ComponentContext $oCtx Contexto con varias bases de datos
     * @param $sDb el nombre de la bd seleccionada para abrir una conexión
     * @return ComponentMysql representa la bd dentro del contexto
     */
    public static function get_dbobject_by_ctx(ComponentContext $oCtx, $sDb="")
    {
        //pr($oCtx,"octx");
        $arConfig = $oCtx->get_selected();
        $arConfig = self::_get_dbconfig($arConfig,$sDb);
        //bug($arConfig,"arconfig");die;
        if(!$arConfig) return new ComponentMysql();
        $oDb = new ComponentMysql($arConfig);
        return $oDb;
    }

    public static function get_pdo_by_ctx(ComponentContext $ctx, string $db=""): ?PDO
    {
        $arConfig = $ctx->get_selected();
        $arConfig = self::_get_dbconfig($arConfig, $db);
        if(!$arConfig) return null;

        $arconstr["mysql:host"] = $arConfig["server"] ?? "";
        $arconstr["dbname"] = $arConfig["database"] ?? "";
        $arconstr["port"] = $arConfig["port"] ?? "";

        $sString = "";
        foreach($arconstr as $sK=>$sV) $sString .= "$sK=$sV;";

        try {
            $oPdo = new PDO(
                $sString,
                $arConfig["user"],
                $arConfig["password"],
                [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"]
            );
            $oPdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION );
            return $oPdo;
        }
        catch (\Exception $e)
        {
            return null;
        }
    }

    public static function get_by_default(): ComponentMysql
    {
        $context = new ComponentContext(getenv("APP_CONTEXTS"), getenv("APP_ID_CONTEXT"));
        $dbname = $context->get_dbname(getenv("APP_DB_ALIAS_1"));
        $db = self::get_dbobject_by_ctx($context, $dbname);
        return $db;
    }

}//DbFactory
