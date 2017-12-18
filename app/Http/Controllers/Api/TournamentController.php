<?php

namespace App\Http\Controllers\Api;

use App\Models\GdTournament;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TournamentController extends Controller
{
    public function index()
    {
        $result = [];
        $lang = session('lang');
        GdTournament::where('status',1)->each(function ($item) use (&$result,$lang){
            $result[] = $item->responseTournament($lang);
        });
        return responseJSON($result);
    }

    public function show($id)
    {
        $lang = session('lang');
        $result = GdTournament::find($id)->responseTournament($lang);
        return responseJSON($result);
    }
}
