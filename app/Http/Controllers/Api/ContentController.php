<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Report;
use App\Models\Teach;
use App\Models\TataTertib;
use App\Models\Vidoes;
use App\Services\Firebase\FirebaseMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class ContentController extends Controller
{
    public function fcm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'   => 'required',
            'content' => 'required',
        ], [
            'required' => 'Field :attribute wajib diisi.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        return FirebaseMessage::sendTopicBroadcast(
            'all_users',
            $request->title,
            $request->content
        );
    }

    public function campaign()
    {
        $items = Campaign::latest()->get();
        return response()->json($items);
    }

    public function report()
    {
        $id   = JWTAuth::user()->id;
        $item = Report::select('reason', 'reply')->where('user', $id)->get();
        return response()->json($item);
    }

    public function ureport(Request $request)
    {
        $id = JWTAuth::user()->id;

        $validator = Validator::make($request->all(), [
            'reason' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        $check = Report::where('user', $id)->whereNull('reply');

        if ($check->exists()) {
            return response()->json(['errors' => ['message' => 'Laporan masih dalam proses']], 400);
        }
        $re         = new Report;
        $re->user   = $id;
        $re->reason = $request->reason;
        $re->save();

        return response()->json(['status' => true], 200);
    }

    public function miska()
    {
        $role = JWTAuth::user()->role;
        $id   = JWTAuth::user()->id;
        if ($role == 3) {
            $items = Teach::where('user', $id)->first();
            $teach = Teach::where("unit_id", $items->unit_id)->get();
        } else {
            $items = JWTAuth::user()->data->reg->where('do', 0)->first();
            $teach = Teach::where("unit_id", $items->unit)->get();
        }

        $da = $teach->map(function ($q) {
            return ['name' => $q->name, 'url' => $q->img ? asset('storage/' . $q->img) : asset("women.png"), 'hp' => $q->hp];
        });
        return response()->json($da);
    }

    public function video(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'yt'   => 'required|url',
            'to'   => 'required',
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        $id    = JWTAuth::user()->data->id;
        $embed = $this->youtubeToEmbed($request->yt);
        if ($embed) {
            $vid             = new Vidoes;
            $vid->teach_id   = $id;
            $vid->name       = $request->name;
            $vid->student_id = $request->to;
            $vid->pile       = $embed;
            $vid->save();
            return response()->json(['status' => true, 'data' => $vid], 200);
        } else {
            return response()->json(['error' => 'url tidak valid'], 404);
        }
    }

    public function videos()
    {
        $id   = JWTAuth::user()->data->id;
        $user = Vidoes::where('teach_id', $id);
        if ($user->exists()) {
            $items = $user->get();
        }

        $to = Vidoes::where('student_id', $id);
        if ($to->exists()) {
            $items = $to->get();
        }
        return response()->json($items);
    }

    public function tataTertib()
    {
        $item = TataTertib::first();
        return response()->json([
            'status' => true,
            'data' => $item ? $item->content : 'Tata Tertib belum diatur.'
        ]);
    }

    private function youtubeToEmbed($url)
    {
        preg_match(
            '/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([A-Za-z0-9_-]{11})/',
            $url,
            $matches
        );

        if (!isset($matches[1])) {
            return null;
        }

        return "https://www.youtube.com/embed/" . $matches[1];
    }
}
