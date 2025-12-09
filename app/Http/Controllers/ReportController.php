<?php
namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\User;
use App\Services\Firebase\FirebaseMessage;
use DB;
use Illuminate\Http\Request;

class ReportController extends Controller
{

    public function index()
    {
        $items = Report::with('users.data.reg.class','users.data.reg.programs','users.data.reg.units')->get();

        // return response()->json($items);
        return view('home.report.index', compact('items'));
    }

    public function update(Request $request, Report $report)
    {
        $report->reply = $request->re;
        $report->save();
        $fcm = User::where('id', $report->user)->first()->fcm;

        $message = [
            "message" => [
                "token"        => $fcm,
                "notification" => [
                    "title" => "Laporan",
                    "body"  => "Laporan Sudah di proses",
                ],
            ],
        ];

        FirebaseMessage::sendFCMMessage($message);

        return back();
    }
}
