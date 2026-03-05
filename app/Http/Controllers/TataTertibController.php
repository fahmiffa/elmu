<?php

namespace App\Http\Controllers;

use App\Models\TataTertib;
use Illuminate\Http\Request;

class TataTertibController extends Controller
{
    /**
     * Show the form (single record, no table).
     */
    public function index()
    {
        $item = TataTertib::first();
        return view('master.tata-tertib.index', compact('item'));
    }

    /**
     * Update the single tata tertib record.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string',
        ], [
            'content.required' => 'Konten tata tertib wajib diisi.',
        ]);

        $item = TataTertib::first();

        if (!$item) {
            $item = new TataTertib;
        }

        $item->content = $validated['content'];
        $item->save();

        return redirect()->route('dashboard.master.tata-tertib.index')
            ->with('status', 'Tata tertib berhasil diperbarui!');
    }
}
