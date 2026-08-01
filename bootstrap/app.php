<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Http;
use Throwable;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\LogActivity::class,
        ]);

        $middleware->api(append: [
            \App\Http\Middleware\LogActivity::class,
        ]);

        $middleware->alias([
            'isRole'           => \App\Http\Middleware\isRole::class,
            'restrictOperator' => \App\Http\Middleware\RestrictOperator::class,
            'jwt'              => \App\Http\Middleware\JwtMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->reportable(function (Throwable $e) {
            $telegramBotToken = env('TELEGRAM_BOT_TOKEN');
            $telegramChatId = env('TELEGRAM_CHAT_ID');

            if ($telegramBotToken && $telegramChatId) {
                // Jangan kirim notif jika error 404 agar tidak berisik
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                    return;
                }

                $message = "🚨 *Terjadi Error Aplikasi!*\n\n";
                $message .= "*Message:* " . $e->getMessage() . "\n";
                $message .= "*File:* " . $e->getFile() . "\n";
                $message .= "*Line:* " . $e->getLine() . "\n";
                $message .= "\n*URL:* " . request()->fullUrl();

                try {
                    Http::post("https://api.telegram.org/bot{$telegramBotToken}/sendMessage", [
                        'chat_id'    => $telegramChatId,
                        'text'       => $message,
                        'parse_mode' => 'Markdown',
                    ]);
                } catch (\Exception $httpException) {
                    // Abaikan jika pengiriman ke telegram gagal
                }
            }
        });
    })->create();
