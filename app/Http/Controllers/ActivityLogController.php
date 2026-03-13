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
        try {
            ActivityLog::findOrFail($id)->delete();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['status' => 'success', 'message' => 'Log aktivitas berhasil dihapus']);
            }

            return back()->with('status', 'Log aktivitas berhasil dihapus');
        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Gagal menghapus log: ' . $e->getMessage()], 500);
            }
            return back()->with('err', 'Gagal menghapus log: ' . $e->getMessage());
        }
    }

    public function clear()
    {
        try {
            ActivityLog::truncate();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['status' => 'success', 'message' => 'Semua log aktivitas berhasil dikosongkan']);
            }

            return back()->with('status', 'Semua log aktivitas berhasil dikosongkan');
        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Gagal mengosongkan log: ' . $e->getMessage()], 500);
            }
            return back()->with('err', 'Gagal mengosongkan log: ' . $e->getMessage());
        }
    }
}
