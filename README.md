# API-teste Contele

Rotas

* Método de retorno da API: JSON

* exceto na rota '/'

GET: '/' => Versão do Lumen e nome da API
GET: '/api/v1/' => Mostra as rotas de api disponíveis para acesso
GET: '/api/v1/routes' => Mostra as rotas disponíveis para consulta
GET: '/api/v1/{partida}/{destino}/{consumo}/{valorCombustivel}/' => Calcula a melhor rota à partir do ponto de partida e destino, mostrando o custo do combustível à partir do consumo do veículo. Exemplo:

Exemplo com números inteiros:

Request (GET) => /api/v1/A/B/10/4/

response (JSON) => {
  "route":"E D C","cost":"24,00"
}

Exemplo para números double ou float **:

Request (GET) => /api/v1/E/C/10:34/3:26/

response (JSON) => {
  "route":"E D C","cost":"18,92"
}


** para valores que não sejam inteiros, colocar a string ':' ao invés de '.' ou ','.

