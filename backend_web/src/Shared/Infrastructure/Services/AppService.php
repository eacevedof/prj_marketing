<?php

namespace App\Shared\Infrastructure\Services;

use Exception;
use App\Shared\Domain\Enums\ExceptionType;
use TheFramework\Components\Config\ComponentConfig;
use TheFramework\Components\Session\ComponentEncDecrypt;
use App\Shared\Infrastructure\Components\Date\UtcComponent;
use App\Shared\Domain\Repositories\Common\SysFieldRepository;
use App\Shared\Infrastructure\Traits\{EnvTrait, ErrorTrait, LogTrait};
use App\Shared\Infrastructure\Factories\{ComponentFactory as CF, RepositoryFactory as RF};
use App\Shared\Infrastructure\Exceptions\{BadRequestException, ForbiddenException, NotFoundException};

abstract class AppService
{
    use EnvTrait;
    use ErrorTrait;
    use LogTrait;

    protected mixed $input;

    protected function _throwException(string $message, int $code = ExceptionType::CODE_INTERNAL_SERVER_ERROR): void
    {
        $this->logErr($message, "app-service.exception");
        switch ($code) {
            case ExceptionType::CODE_BAD_REQUEST: throw new BadRequestException($message);
            case ExceptionType::CODE_FORBIDDEN: throw new ForbiddenException($message);
            case ExceptionType::CODE_NOT_FOUND: throw new NotFoundException($message);
        }
        throw new Exception($message, $code);
    }

    protected function _getEncDecryptInstance(): ComponentEncDecrypt
    {
        $pathFile = $this->getEnvValue("APP_ENCDECRYPT") ?? __DIR__.DIRECTORY_SEPARATOR."encdecrypt.json";
        $config = (new ComponentConfig($pathFile))->get_node("domain", $this->getEnvValue("APP_DOMAIN"));
        if (!$config) {
            $this->_throwException("Domain {$this->getEnvValue("APP_DOMAIN")} is not authorized");
        }

        $encDecrypt = new ComponentEncDecrypt;
        $encDecrypt->setSslMethod($config["sslenc_method"] ?? "");
        $encDecrypt->setSslKey($config["sslenc_key"] ?? "");
        $encDecrypt->setSaltString($config["sslsalt"] ?? "");
        return $encDecrypt;
    }

    protected function _getRowWithSysDataByTz(array $row, string $tz = UtcComponent::TZ_UTC): array
    {
        $dateFields = ["insert_date", "update_date", "delete_date"];
        $utc = CF::getInstanceOf(UtcComponent::class);
        foreach($row as $field => $dtValue) {
            if(in_array($field, $dateFields) && $dtValue) {
                $row[$field] = $utc->getUtcDtInTargetTz($dtValue, $tz);
            }
        }

        $sysData = RF::getInstanceOf(SysFieldRepository::class)->getSysDataByRowData($row);
        return array_merge($row, $sysData);
    }

}//AppService
