<script>

function exibirModalOdds(evento){
    $.get('/evento/'+evento.id+'/odds').done(function (cat_palpites){
        var tituloModal = "<span class='text-primary'>" +evento.time1.nome + "</span> x <span class='text-danger'>" 
                + evento.time2.nome + "</span>";
        var string = "";
        $("#titulo-modal").html(tituloModal);            
        for(id_categoria in cat_palpites){
            if(id_categoria==1) string += montarResultadoFinal(cat_palpites[id_categoria]);
            else if(id_categoria==2) string += montarDuplaChance(cat_palpites[id_categoria]);
            else if(id_categoria==5) string += montarTotalDeGols(cat_palpites[id_categoria]);
            else if(id_categoria==6) string += montarAmbosMarcam(cat_palpites[id_categoria]);
            else if(id_categoria==14) string += montarResultFinalEAmbas(cat_palpites[id_categoria]);                
        }
        
        $("#modal-body").html(string);
        $("#modal-odds").modal();
    });
}

function exibirModalPalpites(){
    $.get('sessao/meus_palpites').done(function(palpites){
        var string = "";
        var cotaTotal=1;
        var quantPalpites = 0;
        for(index in palpites){
            evento_id = palpites[index].evento_id;
            tipo_palpite_id = palpites[index].tipo_palpite.id;

            string+="<tr>";
            string+="<td>";
            string+= "<span class='text-primary'>"+ palpites[index].evento.time1.nome + "</span> x ";
            string+= "<span class='text-danger'>"+ palpites[index].evento.time2.nome + '</span> <br>';
            string+=palpites[index].tipo_palpite.cat_palpite.nome + '<br>';
            string+= "<b>"+palpites[index].tipo_palpite.nome + '</b><br>';
            string+="</td>";
            string+="<td>";
            string+= "<span class='odd-palpite'>"+palpites[index].valor+"</span>";
            string+="<button class='btn btn-danger btn-sm btn-remove' "+
                    "onclick='removePalpite("+evento_id+", "+tipo_palpite_id+", this)'>X</button>";
            string+="</td>";
            string+="</tr>";

            cotaTotal *= palpites[index].valor;
            quantPalpites++;
        }
        if(cotaTotal>800){
            cotaTotal=800;
        }
        string+="<tr>";
        string+="<td>Quant Palpites: <span id='quantPalpites'>"+quantPalpites+"</span></td>"; 
        string+="<td>Cota total: <span class='text-success' id='cotaTotal'>"+cotaTotal.toFixed(2)+"</span></td>"; 
        string+="</tr>";

        $("#modal-palpites-body").html(string);
        $('#modal-palpites').modal();        
    });    
}

function montarResultadoFinal(odds){
    var string="";
    string+="<tr class='cat_palpite'><td colspan='12'>"+odds[0].cat_palpite.nome+"</td></tr>";
    string+="<tr>";
    for(id_odds in odds){
        string += "<td colspan='4'>";
        string += odds[id_odds].tipo_palpite.nome + '<br>';
        string += botaopalpite(odds[id_odds]);
        string += "</td>";
    }
    string+="</tr>";
    return string;
}
function montarDuplaChance(categoria){
    var string="";
    string+="<tr class='cat_palpite'><td colspan='12'>"+categoria[0].cat_palpite.nome+"</td></tr>";
    string+="<tr>";
    for(id_odds in categoria){
        string += "<td colspan='4'>";
        string += categoria[id_odds].tipo_palpite.nome + '<br>';
        string += botaopalpite(categoria[id_odds]);
        string += "</td>";
    }
    string+="</tr>";
    return string;
}
function montarAmbosMarcam(categoria){
    var string="";
    string+="<tr class='cat_palpite'><td colspan='12'>"+categoria[0].cat_palpite.nome+"</td></tr>";
    string+="<tr>";
    for(id_odds in categoria){
        string += "<td colspan='6'>";
        string += categoria[id_odds].tipo_palpite.nome + '<br>';
        string += botaopalpite(categoria[id_odds]);
        string += "</td>";
    }
    string+="</tr>";
    return string;
}

function montarTotalDeGols(odds){
    var string="";
    string+="<tr class='cat_palpite'><td colspan='12'>"+odds[0].cat_palpite.nome+"</td></tr>";
    string+="<tr>";
    string+=getLinha2(odds[0], odds[5]);
    string+=getLinha2(odds[1], odds[6]);
    string+=getLinha2(odds[2], odds[7]);
    string+=getLinha2(odds[3], odds[8]);
    string+=getLinha2(odds[4], odds[9]);
    return string;
}

function montarResultFinalEAmbas(categoria){
    var string="";
    string+="<tr class='cat_palpite'><td colspan='12'>"+categoria[0].cat_palpite.nome+"</td></tr>";
    string+="<tr>";
    for(id_odds in categoria){
        if(id_odds>=3)break;
        string += "<td colspan='4'>";
        string += categoria[id_odds].tipo_palpite.nome + '<br>';
        string += botaopalpite(categoria[id_odds]);
        string += "</td>";            
    }
    string+="</tr>";

    string+="<tr>";
    for(id_odds in categoria){
        if(id_odds<3)continue;
        string += "<td colspan='4'>";
        string += categoria[id_odds].tipo_palpite.nome + '<br>';
        string += botaopalpite(categoria[id_odds]);
        string += "</td>";            
    }
    string+="</tr>";

    return string;
}

function botaopalpite(palpite) {
    var btn="";
    if(palpite.selecionado != null){
        btn+="<button class='btn btn-info btn-sm btn-danger' data-evento='"+ palpite.evento_id +"' data-palpite='"+ palpite.tipo_palpite_id +"' onclick='enviarPalpite(this)'>";
    }else{
        btn+="<button class='btn btn-info btn-sm' data-evento='"+ palpite.evento_id +"' data-palpite='"+ palpite.tipo_palpite_id +"' onclick='enviarPalpite(this)'>";
    }
    
    btn+=palpite.valor;
    btn+="</button>";
    return btn;
}

function enviarPalpite(btn){
    var evento_id = $(btn).attr('data-evento');
    var tipo_palpite_id = $(btn).attr('data-palpite');
    var tipoAcao="";

    if($(btn).hasClass('btn-danger')){
        removePalpite(evento_id, tipo_palpite_id);
    }else{
        addPalpite(evento_id, tipo_palpite_id);        
    }
    
}

function addPalpite(evento_id, tipo_palpite_id) {
    $.get("/sessao/palpite/"+evento_id+"/"+tipo_palpite_id, {
        acao : 'add'
    }).done(function (data){
        if(data.sucesso==false){
            alert(data.erro);
            return;
        }
        $("button[data-evento='"+evento_id+"']").removeClass('btn-danger');
        $("button[data-evento='"+evento_id+"'][data-palpite='"+tipo_palpite_id+"']").addClass('btn-danger');
    });
}

function removePalpite(evento_id, tipo_palpite_id, btnRemove) {
    $.get("/sessao/palpite/"+evento_id+"/"+tipo_palpite_id, {
        acao : 'remove'
    }).done(function (data){
        $("button[data-evento='"+evento_id+"']").removeClass('btn-danger');     
        if(typeof(btnRemove) !="undefined"){
            cotaPalpiteExcluido = $(btnRemove).parent().find(".odd-palpite").html();
            quantPalpites = $("#quantPalpites").html();

            $(btnRemove).parent().parent().remove();
            cotaTotal = $("#cotaTotal").html();
            cotaTotal /= cotaPalpiteExcluido;
            $("#cotaTotal").html(cotaTotal.toFixed(2));
            $("#quantPalpites").html((quantPalpites-1));
        }
    });
}


function getLinha2(odd1, odd2){
    var linha="";
    linha+="<tr>";

    linha+="<td colspan='6'>";
    linha+= odd1.tipo_palpite.nome + "<br>";
    linha+= botaopalpite(odd1);
    linha+="</td>";

    linha+="<td colspan='6'>";
    linha+= odd2.tipo_palpite.nome + "<br>";
    linha+= botaopalpite(odd2);
    linha+="</td>";
    linha+="</tr>";

    return linha;
}    

function fazerAposta(){
    $.get('/aposta/fazerAposta', {
        valorAposta: $('#valorAposta').val(),
        nomeAposta: $('#nomeAposta').val()
    }).done(function(data){
        
    });
}


</script>