<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index()
    {
        $logs = ActivityLog::with('user')->latest()->paginate(20);
        return view('master.log.index', compact('logs'));
    }

    public function destroy($id)
    {
        ActivityLog::findOrFail($id)->delete();
        return back()->with('status', 'Log aktivitas berhasil dihapus');
    }

    public function clear()
    {
        ActivityLog::truncate();
        return back()->with('status', 'Semua log aktivitas berhasil dikosongkan');
    }
}
