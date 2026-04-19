<?php

namespace App\Services\Identity\Providers;

use App\Models\User;
use App\Services\Identity\Contracts\EriProviderContract;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class RealEriProvider implements EriProviderContract
{
    public function resolveUser(Request $request): User
    {
        if (! $request->isMethod('post')) {
            throw new RuntimeException('ERI autentifikatsiya POST so\'rovi bilan amalga oshiriladi.');
        }

        $tin = (string) $request->input('tin', '');
        $name = (string) $request->input('name', '');
        $email = (string) $request->input('email', '');

        if ($tin === '' || $name === '' || $email === '') {
            throw new RuntimeException('STIR, korxona nomi va email majburiy.');
        }

        $verificationUrl = (string) config('services.eri.verification_url', '');
        $signedPayload = $request->input('signed_payload');

        if ($verificationUrl !== '') {
            $this->verifyWithRemoteService($request, $tin, $name, $email, $signedPayload);
        } elseif ($signedPayload !== null && $signedPayload !== '') {
            $this->verifySignedPayloadLocally($request, (string) $signedPayload);
        } else {
            throw new RuntimeException(
                'ERI: .env da ERI_VERIFICATION_URL (tashqi tekshiruv xizmati) yoki formada signed_payload (E-IMZO imzosi) ko\'rsatilishi kerak.'
            );
        }

        $user = User::query()->where('tin', $tin)->first();
        if ($user) {
            $user->forceFill([
                'name' => $name,
                'email' => $email,
                'person_type' => 'yuridik',
                'is_verified' => true,
            ])->save();

            return $user->fresh() ?? $user;
        }

        return User::create([
            'tin' => $tin,
            'name' => $name,
            'email' => $email,
            'password' => bcrypt(Str::random(48)),
            'role' => 'employer',
            'person_type' => 'yuridik',
            'is_verified' => true,
        ]);
    }

    private function verifyWithRemoteService(Request $request, string $tin, string $name, string $email, mixed $signedPayload): void
    {
        $url = (string) config('services.eri.verification_url');
        $timeout = (int) config('services.eri.verification_timeout', 30);

        $payload = [
            'client_id' => config('services.eri.client_id'),
            'client_secret' => config('services.eri.client_secret'),
            'tin' => $tin,
            'name' => $name,
            'email' => $email,
            'signed_payload' => $signedPayload,
            'challenge' => $request->session()->get('eri_challenge'),
        ];

        try {
            $response = Http::asJson()
                ->timeout($timeout)
                ->post($url, $payload);
        } catch (RequestException $e) {
            throw new RuntimeException('ERI tekshiruv xizmatiga ulanishda xato: '.$e->getMessage(), 0, $e);
        }

        if (! $response->successful()) {
            throw new RuntimeException('ERI tekshiruv xizmati: HTTP '.$response->status());
        }

        /** @var array<string, mixed>|null $body */
        $body = $response->json();
        if (! is_array($body)) {
            throw new RuntimeException('ERI tekshiruv javobi JSON emas.');
        }

        if (! ($body['valid'] ?? false)) {
            throw new RuntimeException((string) ($body['message'] ?? 'ERI tekshiruvi rad etildi.'));
        }
    }

    private function verifySignedPayloadLocally(Request $request, string $signedPayload, string $tin): void
    {
        $challenge = (string) $request->session()->pull('eri_challenge', '');
        if ($challenge === '') {
            throw new RuntimeException('ERI sessiya challenge topilmadi. Sahifani yangilab qayta urinib ko\'ring.');
        }

        $binary = base64_decode($signedPayload, true);
        if ($binary === false) {
            throw new RuntimeException('signed_payload noto\'g\'ri Base64.');
        }

        $tmpContent = tempnam(sys_get_temp_dir(), 'eri_msg_');
        $tmpPkcs7 = tempnam(sys_get_temp_dir(), 'eri_pk7_');
        $tmpCerts = tempnam(sys_get_temp_dir(), 'eri_crt_');
        if ($tmpContent === false || $tmpPkcs7 === false || $tmpCerts === false) {
            throw new RuntimeException('Vaqtinchalik fayl yaratilmadi.');
        }

        try {
            file_put_contents($tmpContent, $challenge);
            file_put_contents($tmpPkcs7, $binary);

            $flags = (int) PKCS7_DETACHED;
            if (filter_var((string) env('ERI_PKCS7_NOVERIFY', false), FILTER_VALIDATE_BOOLEAN)) {
                $flags |= (int) PKCS7_NOVERIFY;
            }

            $result = @openssl_pkcs7_verify($tmpPkcs7, $flags, $tmpCerts, [], [], $tmpContent);
            if ($result !== 1) {
                $err = openssl_error_string() ?: 'noma\'lum';
                throw new RuntimeException('E-IMZO PKCS7 tekshiruvi muvaffaqiyatsiz: '.$err);
            }
        } finally {
            @unlink($tmpContent);
            @unlink($tmpPkcs7);
            @unlink($tmpCerts);
        }
    }
}
