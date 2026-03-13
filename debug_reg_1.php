<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Head;

$id = 'c4ca4238a0b923820dcc509a6f75849b';
$items = Head::where(DB::raw('md5(id)'), $id)->first();

if ($items) {
    echo "ID: " . $items->id . "\n";
    echo "Kelas: '" . $items->kelas . "'\n";
    echo "Program: '" . $items->program . "'\n";
    echo "Unit: '" . $items->unit . "'\n";
} else {
    echo "Item not found\n";
}
