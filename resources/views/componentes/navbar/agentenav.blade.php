<nav class="navbar navbar-expand-lg  navbar-dark bg-dark">
  <a class="navbar-brand" href="/">M47esportes</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">

      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Apostas
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
          <a class="dropdown-item" href="/">Fazer aposta</a>
          <a class="dropdown-item" href="{{ route('agenteapostas') }}">Suas apostas</a>
          <a class="dropdown-item" href="{{ route('relatorio_agente') }}">Relatório</a>
          <a class="dropdown-item" href="{{ route('agentevalidar') }}">Validar aposta</a>
          <a class="dropdown-item" href="{{ route('agente.clientes') }}">Meus Clientes</a>
        </div>
      </li>

      @if( Auth::user()->permissao_bolao == 1 )
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Bolões
          </a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
            <a class="dropdown-item" href="{{ route('agente.bolaodisponivel') }}">Bolões disponiveis</a>
            <a class="dropdown-item" href="{{ route('agente.bolao.apostas') }}">Ultimas Apostas</a>
          </div>
      </li>
      @endif

      <li class="nav-item">
        <a class="nav-link" href="{{ route('agenteconta') }}">Minha conta</a>
      </li>
      
      <li class="nav-item">
        <form method="post" action="{{route('logout')}}">
          @csrf
          <button class="btn btn-warning">Sair</button>
        </form>
      </li>
      
    </ul>
  </div>
</nav>