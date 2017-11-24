<?php

namespace frontend\helpers;


class PdfHelper
{
    public static function sliceAndWriteHtml($mpdf, $html)
    {
        $chunks = explode('<!-- split -->', $html);
        foreach ($chunks as $chunk) {
            $mpdf->WriteHTML($chunk);
        }
    }

    public static function getBlockHeight($mpdf, $html)
    {
        $startHeight = empty($mpdf->y) ? 0 : $mpdf->y;
        $startPage = ($mpdf->PageNo() > 0) ? $mpdf->PageNo() : 1;
        self::sliceAndWriteHtml($mpdf, $html);
        $endHeight = $mpdf->y;
        $endPage = $mpdf->PageNo();
        return (($endHeight - $startHeight) + $mpdf->h * ($endPage - $startPage));
    }
}