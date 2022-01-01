<?php

namespace App\Components\Response;

final class ResponseComponent
{


    public function show_json_ok($arRows, $inData=1): void
    {
        $arTmp = $arRows;
        if($inData) $arTmp = ["data" => $arRows];

        $sJson = json_encode($arTmp);
        $this->send_http_status(200);
        header("Content-Type: application/json");
        s($sJson);
    }

    public function show_json_nok($message,$code): void
    {
        $arTmp = [
            "data" => ["message"=>$message, "code"=>$code]
        ];

        $sJson = json_encode($arTmp);
        $this->send_http_status($code);
        header("Content-Type: application/json");
        s($sJson);
    }

    public function json($arData): string
    {
        header("Content-type: application/json");
        echo json_encode($arData);
    }
}