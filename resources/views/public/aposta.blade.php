@extends('componentes.pagina')

@section('titulo')
#{{ $aposta->id }} - {{ $aposta->nome }}
@endsection

@section('content')
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-2 col-md-3"></div>
			<div class="col">
				@if(isset($aposta->controle))
					<a style="display: none;" id="botaoCompartilhar" class="btn btn-info btn-block" href="whatsapp://send?text={{ route('viewcomprovante', $aposta->controle) }}">
					Compartilhar 
					</a>
					<button onclick="compartilhar()" style="display: none;" id="botaoCompartilharWV" class="btn btn-info btn-block" >
					Compartilhar 
					</button>
				@endif
				
			</div>
			<div class="col-sm-2 col-md-3"></div>
		</div>

		<div class="row">
			<div class="col-sm-2 col-md-3"></div>
			<div class="col-sm-8 col-md-6">
				<div class="card">
					<div class="card-header text-center">
					    Nº da Aposta: <b>{{$aposta->id}}</b><br>
					    Criada em: {{$aposta->data_aposta}}<br>
					    @if( isset($aposta->data_validacao) )
					    Validada em: {{$aposta->data_validacao}}<br>
					    @endif					    
					    Nome: {{$aposta->nome}}<br>
					    @if(isset($aposta->agente_id))
					    	Agente: {{$aposta->agente->nickname}}<br>
					    @endif
					    @auth

					    	@if( isset($aposta->cliente_id) )
					    		Cliente: <b>{{ $aposta->cliente->nome }}</b><br>
					    	@endif

						    
						@endif
					</div>

					<div class="card-body text-center">
						@foreach($aposta->palpites as $palpite)

						@php
							$classe = "";
							if($palpite->situacao_palpite_id==4){
								$classe="palpite-anulado";
							}elseif($palpite->situacao_palpite_id==2){
								$classe = "palpite-errou";
							}elseif($palpite->situacao_palpite_id==1){
								$classe = "palpite-acertou";
							}
						@endphp

						<div class="palpite {{$classe}}">
							<div class="evento">
								<span class="evento-id">Evento: {{$palpite->evento->id}}</span><br>
								<span class="nome-liga">{{$palpite->evento->liga->nome}}</span><br>
								<span class="text-primary"> <b>{{$palpite->evento->time1->nome}}</span></b> vs 
								<span class="text-danger"><b>{{$palpite->evento->time2->nome}}</span></b><br>
								<span class="data-evento">{{$palpite->evento->data}}</span>
							</div>	
							<div class="desc-palpite">
								<span>{{$palpite->tipo_palpite->cat_palpite->nome}}</span><br>
								<span><b>{{$palpite->tipo_palpite->nome}}</b> : {{$palpite->cotacao}}</span> <br>
								@if(isset($palpite->evento->scores))
									@if($palpite->tempoJogo=="completo")
										<span>Resultado: <b>{{$palpite->evento->scores->score_t1}} x {{$palpite->evento->scores->score_t2}}</b></span>
									@elseif($palpite->tempoJogo=="tempo1")
										<span>Resultado: <b>{{$palpite->evento->scores->score_t1}} x {{$palpite->evento->scores->score_t2}}</b> 1ºT</span>
									@elseif($palpite->tempoJogo=="tempo2")
										<span>Resultado: <b>{{$palpite->evento->scores->score_t1}} x {{$palpite->evento->scores->score_t2}}</b> 2ºT</span>
									@endif
								@endif
								@if($palpite->situacao_palpite_id==4)
									<br><b>{{ __('Palpite Anulado') }}</b>
								@endif
							</div>						
						</div>
						@endforeach
					</div>

					<div class="card-footer text-center">
						Cota Total: {{$aposta->cotacao_total}}<br>
						Valor Apostado: R$ {{$aposta->valor_apostado}}<br>
						Possíveis ganhos: R$ {{$aposta->premiacao}}<br>
						@auth
							@if( (auth()->user()->id == $aposta->agente_id) && isset($aposta->aposta_paga))
						    <div class="row justify-content-center">
						    	<select style="font-size: 13px" class="form-control col-5" id="aposta_paga" disabled>
					    			@if($aposta->aposta_paga == 0)
							    		<option value="1">Aposta paga</option>
							    		<option value="0" selected>Aposta não paga</option>
						    		@elseif($aposta->aposta_paga == 1)
							    		<option value="1" selected>Aposta paga</option>
							    		<option value="0" >Aposta não paga</option>
						    		@endif
						    	</select>
						    	<button class="col-3 btn btn-sm btn-warning" onclick="liberarEdicao(this)" salvar="false" >Atualizar</button>
						    </div>					    								    	
						    @endif
					    @endif <!-- Fim auth -->
					</div>
				</div>
				<div class="col-sm-2 col-md-3"></div>
			</div>
				
		</div>


		<!-- Aposta -->
		@include('componentes.imprimiraposta', $aposta)

		<div class="row justify-content-center">
			<div class="col-12 col-sm-4">
				<button id="btnCopiarAposta" onclick="copiarAposta()" class="btn btn-success btn-block">Copiar Aposta</button><br>
			</div>
		</div>

		<div class="row justify-content-center">
			<div class="col-12 col-sm-4">
				<button style="display: none" id="btnImprimirAposta" onclick="imprimir()" class="btn btn-primry btn-block">Imprimir</button>
			</div>
		</div>

		<br>
		<br>

	</div>

	
@endsection

@section('css')
	<style>		
		.palpite{
			border-radius: 20px;
			border: 1px solid #ddd;
		}
		.card{
			font-size: 13px;
		}
		.data-evento, .evento-id, .status-evento{
			font-size: 10px;
		}
		.palpite-errou{
			background: #fbb;
		}
		.palpite-acertou{
			background: #bfb;
		}
		.palpite-anulado{
			background: #ffa;
		}
	</style>
@endsection

@section('javascript')
	<script>
		$(document).ready(function(){

			if( navigator.userAgent.match(/Android/i)
			 		|| navigator.userAgent.match(/webOS/i)
			 		|| navigator.userAgent.match(/iPhone/i)
					|| navigator.userAgent.match(/iPad/i)
					|| navigator.userAgent.match(/iPod/i)
					|| navigator.userAgent.match(/BlackBerry/i)
					|| navigator.userAgent.match(/Windows Phone/i)
			 ){
			 	if( navigator.userAgent.match('; wv') ){
			 		$("#botaoCompartilharWV").show();
			 	}else{
			 		$("#botaoCompartilhar").show();
			 	}
			    
			  }

			@auth('web-admin')
				if( navigator.userAgent.match('; wv') ){
					$("#btnImprimirAposta").show();
				}				
			@endif

			@auth()
				if( navigator.userAgent.match('; wv') ){
					$("#btnImprimirAposta").show();
				}
			@endif
		  	
		});

		function compartilhar(){
			var string = '{{route('viewcomprovante', $aposta->controle) }}';
			App.compartilhaAposta(string);
		}

		function copiarAposta(){
	      var aux = document.getElementById("textAposta");
	      aux.select();
	      document.execCommand("copy");
	   }

	   function imprimir(){
	      var string = $("#textAposta").val();
	      App.imprimeAposta(string);
	   }


	   	function liberarEdicao(botao){

	   		var acao = $(botao).attr('Salvar');

	   		if(acao == 'false'){
	   			$(botao).removeClass('btn-warning');
		   		$(botao).addClass('btn-success');
		   		$(botao).html('Salvar');
		   		$(botao).attr('Salvar', 'true');
		   		$("#aposta_paga").attr('disabled', false);
	   		}

	   		if(acao == 'true'){
	   			

		   		$.post('{{ route('ajax.agente.atualizarAposta') }}', {
		   			aposta_id: {{ $aposta->id }},
		   			aposta_paga: $("#aposta_paga").val()
		   		}).done(function(resposta){
		   			alert("Aposta atualizada");
		   			$(botao).removeClass('btn-success');
			   		$(botao).addClass('btn-warning');
			   		$(botao).html('Atualizar');
			   		$(botao).attr('Salvar', 'false');
		   			$("#aposta_paga").attr('disabled', true);
		   		});

		   		
	   		}

	   	
	   	}

	</script>

@endsection