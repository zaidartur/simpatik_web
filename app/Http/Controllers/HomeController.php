<?php

namespace App\Http\Controllers;

use App\Models\ArsipSurat;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function list_surat()
    {
        $request = Request();
        if (!isset($request->start) || !isset($request->end)) return null;
        if (empty($request->start) || empty($request->end)) return null;
        $start = Carbon::parse($request->start)->format('Y-m-d');
        $end = Carbon::parse($request->end)->format('Y-m-d');

        $lists = ArsipSurat::selectRaw('NO as id, drkpd as title, created_at as start, created_at as end, JENISSURAT as jenis, ISI as isi_surat, NAMAKOTA as kota, NOSURAT as surat, TGLSURAT as tgl_surat, PERIHAL as perihal')
                ->whereBetween('created_at', [$start, $end])->get();
        // $lists = ArsipSurat::whereDate('created_at', '>=', $start)->whereDate('created_at', '<=', $end)->get();
        
        foreach ($lists as $key => $value) {
            // $value->extendedProps = ['calendar' => ($value->jenis == 'Masuk' ? 'Work' : 'Important')];
            $value->surat = empty($value->surat) ? '-' : $value->surat;
            $value->extendedProps = ['calendar' => $value->jenis];
            $value->tgl_surat = empty($value->tgl_surat) ? '' : str_replace('/', '-', $value->tgl_surat);
        }

        return response()->json($lists);
    }
}
