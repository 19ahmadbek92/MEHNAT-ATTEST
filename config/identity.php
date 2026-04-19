<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OneID / ERI kirish marshrutlari (guest)
    |--------------------------------------------------------------------------
    |
    | Avval faqat local yoki APP_DEMO_SSO=true da ochilgan edi. Productionda
    | haqiqiy provayderlar uchun APP_SSO_ROUTES_ENABLED=true (default) qoldiring.
    | PHPUnit: phpunit.xml da false qilib SSO ni yopish mumkin.
    |
    */

    'sso_routes_enabled' => (bool) env('APP_SSO_ROUTES_ENABLED', true),

];
