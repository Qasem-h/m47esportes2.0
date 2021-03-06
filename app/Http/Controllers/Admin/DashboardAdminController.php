<?php

namespace App\Http\Controllers\Admin;
date_default_timezone_set('America/Fortaleza');

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Aposta;
use App\Agente;
use Illuminate\Support\Facades\DB;

class DashboardAdminController extends Controller
{

    public function __construct(){
        $this->middleware('auth:web-admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        return view('admin.admin_dashboard');
    }

    public function getApostasPorAgente(){
        $data0 = date("Y-m-d", time() );
        $data6 = date("Y-m-d", strtotime('-6 days', strtotime($data0)) );

        $filtro0 = [
            ['agente_id', "<>", NULL],
            ['data_aposta', ">=", $data6."T00:00:00"],
            ['data_aposta', "<=", $data0."T23:59:59"],
        ];


        $apostas = DB::table('apostas')->select('agente_id', DB::raw('SUM(valor_apostado) soma_apostas'))
            ->where($filtro0)
            ->groupBy('agente_id')
            ->orderBy(DB::raw('SUM(valor_apostado)'), 'DESC')
            ->get();

        foreach ($apostas as $aposta) {
            // $aposta->premios = ;
            $aposta->agente = DB::table('agentes')->select('nickname')
                ->where('id', $aposta->agente_id)
                ->first();
        }
        return $apostas;
    }

    public function getApostasPorSemana(){
        $data0 = date("Y-m-d", time() );
        $data1 = date("Y-m-d", strtotime('-1 days', strtotime($data0)) );
        $data2 = date("Y-m-d", strtotime('-2 days', strtotime($data0)) );
        $data3 = date("Y-m-d", strtotime('-3 days', strtotime($data0)) );
        $data4 = date("Y-m-d", strtotime('-4 days', strtotime($data0)) );
        $data5 = date("Y-m-d", strtotime('-5 days', strtotime($data0)) );
        $data6 = date("Y-m-d", strtotime('-6 days', strtotime($data0)) );

        $filtro0 = [
            ['agente_id', "<>", NULL],
            ['data_aposta', ">=", $data0."T00:00:00"],
            ['data_aposta', "<=", $data0."T23:59:59"],
        ];
        $filtro1 = [
            ['agente_id', "<>", NULL],
            ['data_aposta', ">=", $data1."T00:00:00"],
            ['data_aposta', "<=", $data1."T23:59:59"],
        ];
        $filtro2 = [
            ['agente_id', "<>", NULL],
            ['data_aposta', ">=", $data2."T00:00:00"],
            ['data_aposta', "<=", $data2."T23:59:59"],
        ];
        $filtro3 = [
            ['agente_id', "<>", NULL],
            ['data_aposta', ">=", $data3."T00:00:00"],
            ['data_aposta', "<=", $data3."T23:59:59"],
        ];
        $filtro4 = [
            ['agente_id', "<>", NULL],
            ['data_aposta', ">=", $data4."T00:00:00"],
            ['data_aposta', "<=", $data4."T23:59:59"],
        ];
        $filtro5 = [
            ['agente_id', "<>", NULL],
            ['data_aposta', ">=", $data5."T00:00:00"],
            ['data_aposta', "<=", $data5."T23:59:59"],
        ];
        $filtro6 = [
            ['agente_id', "<>", NULL],
            ['data_aposta', ">=", $data6."T00:00:00"],
            ['data_aposta', "<=", $data6."T23:59:59"],
        ];

        $soma0 = Aposta::where($filtro0)->get()->sum('valor_apostado');
        $soma1 = Aposta::where($filtro1)->get()->sum('valor_apostado');
        $soma2 = Aposta::where($filtro2)->get()->sum('valor_apostado');
        $soma3 = Aposta::where($filtro3)->get()->sum('valor_apostado');
        $soma4 = Aposta::where($filtro4)->get()->sum('valor_apostado');
        $soma5 = Aposta::where($filtro5)->get()->sum('valor_apostado');
        $soma6 = Aposta::where($filtro6)->get()->sum('valor_apostado');

        $label = "l";

        $array = [
            date($label, strtotime('-6 days', strtotime($data0))) => $soma6,
            date($label, strtotime('-5 days', strtotime($data0))) => $soma5,
            date($label, strtotime('-4 days', strtotime($data0))) => $soma4,
            date($label, strtotime('-3 days', strtotime($data0))) => $soma3,
            date($label, strtotime('-2 days', strtotime($data0))) => $soma2,
            date($label, strtotime('-1 days', strtotime($data0))) => $soma1,
            date($label, strtotime('-0 days', strtotime($data0))) => $soma0,                      
            
        ];

        return $array;
    }
}
