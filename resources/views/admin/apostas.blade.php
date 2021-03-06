@extends('componentes.pagina')

@section('content')
<div class="container">
	<!--Inicio Form-->
	<form>
		<div class="row">
			<div class="col-6 form-group">
				<label>Data de início</label>
				<input class="form-control" type="date" name="dataInicio" id="dataInicio" value="{{date('Y-m-d', time())}}">
			</div>
			<div class="col-6 form-group">
				<label>Data Final</label>
				<input class="form-control" type="date" name="dataFinal" id="dataFinal" value="{{date('Y-m-d', time())}}">
			</div>	
		</div>

		<div class="row">
			<div class="col-6 form-group">
				@php
				$gerentes = App\Gerente::orderBy('name')->get();

				@endphp
				
				<label>Gerente</label>
				<select class="form-control" name="gerente" id="gerente" onchange="atualizarAgentes()">
					<option value="">Todos</option>

					@foreach($gerentes as $gerente)
						<option value="{{$gerente->id}}"> {{$gerente->name}} </option>
					@endforeach
				</select>
			</div>
			<div class="col-6 form-group">
				<label>Agente</label>
				<select class="form-control" name="agente" id="agente">
					<option value="">Todos</option>
				</select>
			</div>
		</div>

		<div class="row">
			<div class="col-6 form-group">
				<label>Status da aposta</label>
				<select class="form-control">
					<option value="">Todos</option>
				</select>
			</div>
			<div class="col-6 form-group">
				<label>Prêmios a partir de</label>
				<input class="form-control" type="number" name="premios_apartir" value="0" id="premios_apartir">
			</div>
		</div>

		<div class="row">
			<div class="col form-group">
				<button class="btn btn-block">Pesquisar</button>
			</div>
		</div>

	</form>

	<div class="row">
		<div class="col">
			<table id='tabelaApostas' class="table table-hover">
				<thead>
					<tr>
						<th>Aposta</th>
						<th>Valores</th>
						<th>Comissões</th>
					</tr>
				</thead>
				<tbody>
					@foreach($apostas as $aposta)
						@php
						$comissaoAgente = $aposta->valor_apostado * $aposta->comissao_agente;
						$valorLiquido = $aposta->valor_apostado - $comissaoAgente - $aposta->ganhou;

						$classeAposta = "";
						if($apostasComStatus[$aposta->id]['status']==2){
							$classeAposta = "table-danger";
							continue;
						}elseif($apostasComStatus[$aposta->id]['status']==3){
							$classeAposta = "table-warning";
						}elseif($apostasComStatus[$aposta->id]['status']==1){
							$classeAposta = "table-success";
						}
						@endphp

						<tr class="{{ $classeAposta }}" onclick="window.location.href='/aposta/{{$aposta->id}}'">

							<td>
								#{{$aposta->id}}<br>
								Nome: {{$aposta->nome}}<br>
								Agente: {{$aposta->agente->nickname}}<br>
								Data: {{$aposta->data_aposta}}
							</td>
							<td>
								Valor Apostado R$ {{$aposta->valor_apostado}}<br>
								Possível ganho R$ {{$aposta->premiacao}} 
							</td>
							<td>								
								Comissão {{$aposta->comissao_agente*100}}% <br>
								Comissão R$ {{number_format($comissaoAgente, 2)}}<br>
								Líquido R$ {{number_format($valorLiquido, 2)}}
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>

</div>
@endsection


@section('javascript')
<script>
	function atualizarTabelaApostas(){
		var dataInicio = $('#dataInicio').val();
		var dataFinal = $('#dataFinal').val();
		var premiosApartir = $('#premios_apartir').val();
		var agente = $("#agente").val();
		if(agente == ''){
			agente=0;
		}

		$.getJSON('/admin/apostasJSON',{
			data_inicio : dataInicio,
			data_final : dataFinal,
			agente : agente,
			premios_apartir : premiosApartir

		}).done(function(data){
			$("#tabelaApostas>tbody").html(construirTabeleApostas(data));
		});
	}

	function construirTabeleApostas(data){
		var apostas = data.apostas;
		var apostasComStatus = data.apostasComStatus;

		var tabela="";
		var somaValorApostado=0;
		var somaLiquido=0;
		var somaComissao=0;

		for(index in apostas){
			var comissaoAgente = apostas[index].valor_apostado * apostas[index].comissao_agente;
			var valorLiquido = apostas[index].valor_apostado - comissaoAgente - apostas[index].ganhou;

			somaValorApostado += parseInt(apostas[index].valor_apostado);
			somaLiquido += valorLiquido;
			somaComissao += comissaoAgente;


			var classeAposta = "";

			if(apostasComStatus[ apostas[index].id ].status == 2 ){
				classeAposta = "table-danger";
			}else if( apostasComStatus[ apostas[index].id ].status == 3 ){
				classeAposta = "table-warning";
			}else if( apostasComStatus[ apostas[index].id ].status == 1 ){
				classeAposta = "table-success";
			}

			tabela+="<tr class='"+ classeAposta +"' onclick=\"window.location.href='/aposta/"+apostas[index].id+"' \">";

			tabela+="<td>#"+
				apostas[index].id+"<br>"+
				"Nome: "+apostas[index].nome+"<br>"+
				"Agente: "+apostas[index].agente.nickname+"<br>"+
				"Data: "+apostas[index].data_aposta+
				"</td>";

			tabela+="<td>"+
				"Valor Apostado R$ "+apostas[index].valor_apostado+"<br>"+
				"Possível ganho R$ "+apostas[index].premiacao+
				"</td>";

			tabela+="<td>"+
				"Comissão "+(apostas[index].comissao_agente)*100+"%<br>"+
				"Comissão R$ "+comissaoAgente.toFixed(2)+"<br>"+
				"Líquido R$ "+valorLiquido.toFixed(2)+"<br>"+
				"</td>";			

			tabela+='</tr>';
		}
		tabela+='<tr>';
		tabela+="<th>Subtotal</th>";
		tabela+="<th>Apostas R$ "+somaValorApostado.toFixed(2)+"</th>";

		tabela+="<th>"+
				"Comissão R$ "+somaComissao.toFixed(2)+"<br>"+
				"Líquido R$ "+somaLiquido.toFixed(2)+
				"</th>";

		tabela+='</tr>';

		return tabela;

	}

	function atualizarAgentes(){
		$.get('{{ route('agentes_by_gerente') }}', {
			gerente: $("#gerente").val()			
		}).done(function (agente){
			var select = "<option values=''>Todos</option>";
			
			for(index in agente){
				select += "<option value='"+ agente[index].id +"'>"+  agente[index].name +"</option>" ;
			}
			$("#agente").html(select);
		});
	}
</script>
@endsection



@section('css')
<style>
	.table>tbody{
		font-size: 10px;
	}
</style>
@endsection