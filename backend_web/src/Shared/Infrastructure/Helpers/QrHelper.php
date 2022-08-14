<?php
namespace App\Shared\Infrastructure\Helpers;

use \BOOT;
include_once(BOOT::PATH_VENDOR."/phpqrcode/qrlib.php");

final class QrHelper implements IHelper
{
    private string $pathimages;
    private string $value;
    private string $filename;

    public function __construct(array $input)
    {
        $this->pathimages = BOOT::PATH_PUBLIC."/images";
        $this->value = $input["value"] ?? "";
        $this->filename = $input["filename"] ?? "";
    }

    public function save_image(): self
    {
        $matrixPointSize = 10;
        $errorCorrectionLevel = "L";
        $pathimg = "$this->pathimages/qr/$this->filename.png";
        \QRcode::png($this->value, $pathimg, $errorCorrectionLevel, $matrixPointSize, 2);
        return $this;
    }

    public function get_public_url(): string
    {
        return UrlDomainHelper::get_instance()->get_full_url("/images/qr/$this->filename.png");
    }
}
