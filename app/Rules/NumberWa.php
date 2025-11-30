<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NumberWa implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Normalisasi nomor
        $normalized = $this->normalizeNumber($value);

        if (!$normalized) {
            $fail('Nomor tidak valid');
            return;
        }

        try {
            $response = Http::timeout(30)->post(env('URL_WA').'/number', [
                'number' => config('services.numberwa.sender'),
                'to'     => $normalized,
            ]);

            if ($response->failed()) {
                Log::error($response->json());
                $fail('Nomor WhatsApp tidak valid');
            }

        } catch (\Exception $e) {
            Log::error('WA check failed: ' . $e->getMessage());
            $fail('Terjadi kesalahan dalam validasi nomor WA');
        }
    }

    private function normalizeNumber($value)
    {
        // Hilangkan spasi / karakter non-digit
        $value = preg_replace('/\D/', '', $value);

        // Awalan 0 → ubah menjadi 62
        if (preg_match('/^0[0-9]+$/', $value)) {
            return '62' . substr($value, 1);
        }

        // Sudah format 62xxxx
        if (preg_match('/^62[0-9]+$/', $value)) {
            return $value;
        }

        return false;
    }
}
