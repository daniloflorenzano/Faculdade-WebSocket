using System.Net;
using System.Net.WebSockets;
using System.Text;
using Microsoft.AspNetCore.Connections;

var builder = WebApplication.CreateBuilder(args);
var app = builder.Build();

var buffer = new byte[256];
var connections = new Dictionary<string, WebSocket>();

Console.WriteLine(">>> SERVIDOR INICIANDO <<<");

app.UseWebSockets();
app.Map("/", async context =>
{
    if (!context.WebSockets.IsWebSocketRequest)
        context.Response.StatusCode = (int)HttpStatusCode.BadRequest;
    else
    {
        using var webSocket = await context.WebSockets.AcceptWebSocketAsync();
        connections.Add(Guid.NewGuid().ToString(), webSocket);
        
        Console.WriteLine($"{DateTime.Now} • Um cliente se conectou ao servidor");
        Console.WriteLine($"{DateTime.Now} • Total de clientes conectados: {connections.Count}");
        
        try
        {
            while (true)
            {
                var closedConnection = connections.Where(c => c.Value.State == WebSocketState.Closed).FirstOrDefault();
                if (closedConnection.Key != null)
                {
                    connections.Remove(closedConnection.Key);
                    Console.WriteLine($"{DateTime.Now} • Um cliente se desconectou do servidor");
                    Console.WriteLine($"{DateTime.Now} • Total de clientes conectados: {connections.Count}");
                }
                
                if (webSocket.State == WebSocketState.Closed)
                    continue;

                var res = await webSocket.ReceiveAsync(buffer, CancellationToken.None);

                if (res.MessageType == WebSocketMessageType.Close)
                    await webSocket.CloseAsync(WebSocketCloseStatus.NormalClosure, null, CancellationToken.None);
                else
                {
                    var recievedMessage = Encoding.ASCII.GetString(buffer, 0, res.Count);
                    var recievedMessageInBuffer = Encoding.ASCII.GetBytes(recievedMessage);

                    foreach (var connection in connections) // broadcasting
                    {
                        if (connection.Value.State == WebSocketState.Open)
                            await connection.Value.SendAsync(recievedMessageInBuffer, WebSocketMessageType.Text, true, default);
                    }
                }
            }
        }
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