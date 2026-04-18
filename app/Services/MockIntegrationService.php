<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class MockIntegrationService
{
    /**
     * Davlat axborot tizimlariga (Masalan, my.gov.uz yoki Yagona Mehnat Tizimi) ma'lumot jo'natish simulyatsiyasi.
     *
     * @return bool
     */
    public function sendConclusionToStateServices(array $data)
    {
        Log::info('--- MOCK INTEGRATION API CALL ---', $data);

        // Tarmoq va server kechikishlarini simulyatsiya qilish (0.5 - 1 soniya)
        usleep(rand(500000, 1000000));

        $response = [
            'status' => 'success',
            'integrated_id' => 'STG-'.rand(100000, 999999),
            'message' => 'Yagona bazada muvaffaqiyatli saqlandi',
            'timestamp' => now()->toDateTimeString(),
        ];

        Log::info('--- MOCK API JAVOBI ---', $response);

        return true;
    }
}
