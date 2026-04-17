<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Demo OneID / ERI (E-IMZO) kirish
    |--------------------------------------------------------------------------
    |
    | Productionda odatda false. Staging yoki namoyish uchun .env da
    | APP_DEMO_SSO=true qilib yoqiladi. Local muhitda har doim ruxsat beriladi.
    |
    */

    'sso' => (bool) env('APP_DEMO_SSO', false),

    // Demo identifikatsiya ma'lumotlari (faqat APP_DEMO_SSO=true bo'lganda ishlatiladi)
    'oneid' => [
        'name' => env('DEMO_ONEID_NAME', 'OneID User'),
        'pinfl' => env('DEMO_ONEID_PINFL', '12345678901234'),
    ],

    'eri' => [
        'name' => env('DEMO_ERI_NAME', 'ERI Company'),
        'tin' => env('DEMO_ERI_TIN', '123456789'),
        'email' => env('DEMO_ERI_EMAIL', 'company@eri.uz'),
    ],

];
