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

];
