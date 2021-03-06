<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\Api\ConverterApi;
use App\Http\Controllers\Api\ConverterApi2;
use App\Http\Controllers\Api\MinhaClasse;

class Odd extends Model
{


	public function cat_palpite(){
		return $this->belongsTo('App\CatPalpite');
	}
	public function tipo_palpite(){
		return $this->belongsTo('App\TipoPalpite');
	}

    public static function inserir_odds($odds, $event_id){
		$oddsConvertidas = ConverterApi::converterOdds($odds);
		if( !isset( $oddsConvertidas->cat_palpites ) ){
			return $oddsConvertidas;
		}
		foreach ($oddsConvertidas->cat_palpites as $cat_palpite) {
			foreach ($cat_palpite->odds as $odd) {
								
				$odd['taxa'] = Odd::reduzirOdds($odd['taxa'], $cat_palpite->categoria_id);

				$oddBanco = Odd::where([
					['evento_id', $event_id],
					['tipo_palpite_id', $odd['tipo_palpite_id']],
				])->first();
				//Se ja existe as Odds, apenas atualize!
				if (isset($oddBanco)) {
					$oddBanco->valor = $odd['taxa'];
					$oddBanco->updated_at = MinhaClasse::timestamp_to_data_mysql(time());
					$oddBanco->save();					
				}else{//Se não insira-as
					$o = new Odd();
					$o->evento_id = $event_id;
					$o->cat_palpite_id = $cat_palpite->categoria_id;
					$o->tipo_palpite_id = $odd['tipo_palpite_id'];
					$o->valor = $odd['taxa'];
					$o->save();
				}
					
			}
		}
		return $oddsConvertidas;
	}

	public static function inserir_odds2($odds, $event_id){
		$oddsConvertidas = ConverterApi2::converterOdds2($odds);
		if(!isset($oddsConvertidas->cat_palpites)){
			return $oddsConvertidas;
		}

		foreach ($oddsConvertidas->cat_palpites as $cat_palpite) {
			foreach ($cat_palpite->odds as $odd) {
				if($cat_palpite->categoria_id == 1){
					$odd['taxa'] = Odd::reduzirOdds($odd['taxa']);
				}else{
					$odd['taxa'] = Odd::reduzirOdds($odd['taxa'], 1);
				}
				

				$oddBanco = Odd::where([
					['evento_id', $event_id],
					['tipo_palpite_id', $odd['tipo_palpite_id']],
				])->first();
				//Se ja existe as Odds, apenas atualize!
				if (isset($oddBanco)) {
					$oddBanco->valor = $odd['taxa'];
					$oddBanco->updated_at = MinhaClasse::timestamp_to_data_mysql(time());
					$oddBanco->save();					
				}else{//Se não insira-as
					$o = new Odd();
					$o->evento_id = $event_id;
					$o->cat_palpite_id = $cat_palpite->categoria_id;
					$o->tipo_palpite_id = $odd['tipo_palpite_id'];
					$o->valor = $odd['taxa'];
					$o->save();
				}
					
			}
		}
		return $oddsConvertidas;
	}

	private  static function reduzirOdds($odd, $cat_palpite = NULL){		
		if($cat_palpite == 1){
			if($odd <= 1.12){
				$odd =  1 + (($odd-1) * 0.6);
			}elseif($odd <= 1.35){
				$odd =  1 + (($odd-1) * 0.7);
			}elseif($odd <= 1.44){
				$odd =  1 + (($odd-1) * 0.9);
			}elseif( $odd >= 2.5 ){
				$odd =  $odd * 1.02;
			}
		}			

		else{
			$odd =  1 + (($odd-1) * 0.8);
		}

		return $odd;
	}
}
