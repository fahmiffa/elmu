<?php
namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; 

class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Campaign::all();
        return view('home.campaign.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $action = "Tambah Pengumuman";
        return view('home.campaign.form', compact('action'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'image'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'content' => 'required|string',
        ]);

        if ($request->hasFile('image')) {
            $validated['img'] = $request->file('image')->store('campaign', 'public');
        }

        $res       = new Campaign;
        $res->name = $validated['name'];
        $res->file = $validated['img'] ?? null;
        $res->des  = $validated['content'];
        $res->save();

        return redirect()->route('dashboard.campaign.index')
            ->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Campaign $campaign)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Campaign $campaign)
    {
        $items  = $campaign;
        $action = "Tambah Pengumuman";
        return view('home.campaign.form', compact('action', 'items'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Campaign $campaign)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'image'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'content' => 'required|string',
        ]);

        if ($request->hasFile('image')) {
            $validated['img'] = $request->file('image')->store('campaign', 'public');

            if (isset($campaign->file)) {
                Storage::disk('public')->delete($campaign->file);
            }
        }

        $res       = $campaign;
        $res->name = $validated['name'];
        $res->file = $validated['img'] ?? null;
        $res->des  = $validated['content'];
        $res->save();

        return redirect()->route('dashboard.campaign.index')
            ->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Campaign $campaign)
    {
        if (isset($campaign->file)) {
            Storage::disk('public')->delete($campaign->file);
        }
        $campaign->delete();
        return back();
    }
}
