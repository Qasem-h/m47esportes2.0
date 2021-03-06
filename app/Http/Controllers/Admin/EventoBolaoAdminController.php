<?php

namespace App\Http\Controllers\Admin;

use App\Liga;
use App\Time;
use App\EventoBolao;
use App\ScoreBolao;
use App\Http\Controllers\Api\MinhaClasse;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EventoBolaoAdminController extends Controller{
    public function __construct(){
        $this->middleware('auth:web-admin');
    }

    public function getEventosBolaoJson(Request $request){
    	$request->validate([
    		'liga_id' => 'required|integer'
    	]);

    	$eventos = EventoBolao::where('liga_id', $request->liga_id)
    		->get();

    	foreach ($eventos as $evento) {
    		$evento->liga;
    		$evento->time1;
    		$evento->time2;
    		$evento->data = MinhaClasse::data_mysql_to_datahora_formatada($evento->data_evento);
    	}

    	return $eventos;
    }

    public function showFormCadastro(Request $request){
    	$ligas = Liga::where('is_top_list', '>=', 2)
            ->orderBy('is_top_list', 'desc')
            ->orderBy('nome')
            ->get();

        $times = Time::where('cc', 'br')        
        	->where('has_squad', 1)	
        	->orderBy('nome')
        	->get();

    	return view('admin.bolaos.cadastro_evento_bolaos', compact('ligas', 'times'));
    }

    public function store(Request $request){
    	$request->validate([
    		'liga_id' => 'required|integer',
    		'data_evento' => 'required',
    		'time1_id' => 'required|integer',
    		'time2_id' => 'required|integer',
    	]);

    	$evento = new EventoBolao();
    	$evento->liga_id = $request->liga_id;
    	$evento->data_evento = $request->data_evento;
    	$evento->time1_id = $request->time1_id;
    	$evento->time2_id = $request->time2_id;
    	$evento->save();
        return back();
    }


    public function showAtualizarEventos(){
        $eventos = EventoBolao::all()->take(30)->sortBy('id');
        return view('admin.bolaos.atualizar_eventos_bolaos', compact('eventos'));
    }

    public function update(Request $request){
        $request->validate([
            'evento_id' => 'required|integer',
            'score_time1' => 'required|integer',
            'score_time2' => 'required|integer',
        ]);

        $evento = EventoBolao::find($request->evento_id);
        if(isset( $evento )){
            $score = ScoreBolao::where([
                ['evento_id', $request->evento_id]
            ])->first();
            if(isset($score)){
                $score->evento_id = $request->evento_id;
                $score->score_time1 = $request->score_time1;
                $score->score_time2 = $request->score_time2;
                $score->save();
            }else{
                $score = new ScoreBolao();
                $score->evento_id = $request->evento_id;
                $score->score_time1 = $request->score_time1;
                $score->score_time2 = $request->score_time2;
                $score->save();
            }
        }
        return back();
    }
}
