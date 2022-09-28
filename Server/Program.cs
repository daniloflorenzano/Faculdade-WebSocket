using System.Net;
using System.Net.WebSockets;
using System.Text;

var builder = WebApplication.CreateBuilder(args);
var app = builder.Build();
Console.WriteLine(">>> SERVIDOR INICIANDO <<<");

// Inicia buffer para as mensagens e lista de conexões
var buffer = new byte[256];
var connections = new Dictionary<string, WebSocket>();

app.UseWebSockets();
app.Map("/", async context =>
{
    if (!context.WebSockets.IsWebSocketRequest)
        context.Response.StatusCode = (int)HttpStatusCode.BadRequest;
    else
    {
        // Aceita a conexão com o cliente
        // O método "AcceptWebSocketAsync" é do tipo "System.Threading.Tasks.Task<System.Net.WebSockets.HttpListenerWebSocketContext>"
        // Então ara cada conexão é instanciada uma thread, permitindo mais de uma conexão simultânea   
        using var webSocket = await context.WebSockets.AcceptWebSocketAsync();
        connections.Add(Guid.NewGuid().ToString(), webSocket);
        
        Console.WriteLine($"{DateTime.Now} • Um cliente se conectou ao servidor");
        Console.WriteLine($"{DateTime.Now} • Total de clientes conectados: {connections.Count}");
        
        try
        {
            while (true)
            {
                // Verifica se algum cliente se desconectou
                // Caso sim, retira da lista de conexões
                var closedConnection = connections.Where(c => c.Value.State == WebSocketState.Closed).FirstOrDefault();
                if (closedConnection.Key != null)
                {
                    connections.Remove(closedConnection.Key);
                    Console.WriteLine($"{DateTime.Now} • Um cliente se desconectou do servidor");
                    Console.WriteLine($"{DateTime.Now} • Total de clientes conectados: {connections.Count}");
                }
                
                // Garantindo que uma desconexão não pare o servidor
                if (webSocket.State == WebSocketState.Closed)
                    continue;
                
                // Verifica se a mensagem vinda do cliente é uma "close message"
                var res = await webSocket.ReceiveAsync(buffer, CancellationToken.None);
                if (res.MessageType == WebSocketMessageType.Close)
                    await webSocket.CloseAsync(WebSocketCloseStatus.NormalClosure, null, CancellationToken.None);
                
                else
                {
                    var recievedMessage = Encoding.ASCII.GetString(buffer, 0, res.Count);
                    var recievedMessageInBuffer = Encoding.ASCII.GetBytes(recievedMessage);

                    // Broadcasting das mensagens
                    // Toda mensagem que o servidor receber será repassada para todos os clientes conectados
                    foreach (var connection in connections) 
                    {
                        if (connection.Value.State == WebSocketState.Open)
                            await connection.Value.SendAsync(recievedMessageInBuffer, WebSocketMessageType.Text, true, default);
                    }
                }
            }
        }
        // Tratamento de exceções
        catch (WebSocketException e)
        {
            Console.WriteLine($"{DateTime.Now} • ERRO: {e.Message}");
        }
        catch (Exception e)
        {
            Console.WriteLine($"{DateTime.Now} • ERRO: {e.Message}");
        }
    }
});

await app.RunAsync();