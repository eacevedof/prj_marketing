<?php

namespace App\Shared\Infrastructure\Helpers;

use BOOT;

include_once(BOOT::PATH_VENDOR."/phpqrcode/qrlib.php");

final class QrHelper implements IHelper
{
    private string $pathImages;
    private string $value;
    private string $filename;

    public function __construct(array $input)
    {
        $this->pathImages = BOOT::PATH_PUBLIC."/images";
        $this->value = $input["value"] ?? "";
        $this->filename = $input["filename"] ?? "";
    }

    public function saveImage(): self
    {
        $matrixPointSize = 10;
        $errorCorrectionLevel = "L";
        $pathimg = "$this->pathImages/qr/$this->filename.png";
        \QRcode::png($this->value, $pathimg, $errorCorrectionLevel, $matrixPointSize, 2);
        return $this;
    }

    public function getPublicUrl(): string
    {
        return UrlDomainHelper::getInstance()->getDomainUrlWithAppend("/images/qr/$this->filename.png");
    }
}
