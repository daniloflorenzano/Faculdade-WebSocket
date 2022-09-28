# Websocket com .NET e C#
Websocket feito em grupo para a disciplina de Ferramentas de Gerenciamento de Redes - 2° Período.

A aplicação conta com projetos Cliente e Servidor que comunicam entre si usando conceitos de *Socket* e *Broadcast*.

## Proposta
Aplicação de comunicação por chat em tempo real com dois ou mais clientes.

## Intrunções para rodar os projetos

### Server

- Certifique-se que possui [.NET 6](https://dotnet.microsoft.com/en-us/download/dotnet/6.0) em sua máquina
- Navegue até o diretório */Server*
- Execute o comando no bash ou cmd: ``dotnet run``

### WebClient

- Navegue até o diretório */WebClient*
- Apenas execute o arquivo *index.html* utilizando algum navegador
- Caso esteja usando VsCode, também é possível rodar o projeto através da extensão "LiveServer". Dessa forma ficará disponível para a rede local.

### GreenChat

O projeto GreenChat foi desenvolvido para ser administrado com PhpMyAdmin. E portanto, para validação apenas dos conceitos de socket, recomendamos que use o projeto WebClient no lugar deste.

## Referências utilizadas

### Servidor
Artigo - https://balta.io/blog/aspnet-websockets

Documentação - https://docs.microsoft.com/pt-br/dotnet/api/system.net.websockets?view=net-6.0

### Cliente
Documentação - https://developer.mozilla.org/pt-BR/docs/Web/API/WebSockets_API/Writing_WebSocket_client_applications

Video Tutorial - https://www.youtube.com/watch?v=tmvL_TlLEhA

Video Tutorial - https://www.youtube.com/watch?v=zpTlJ6dtOxA

Video Tutorial - https://www.youtube.com/watch?v=Z8yCpSxnWOw

Video Tutorial - https://www.youtube.com/watch?v=hXUQpiW9Tho

Video Tutorial - https://www.youtube.com/watch?v=-xmo2wVNpNg
