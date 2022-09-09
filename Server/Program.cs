using System.Net;
using System.Net.WebSockets;
using System.Text;

var builder = WebApplication.CreateBuilder(args);
var app = builder.Build();

var buffer = new byte[256]; 
var connections = new Dictionary<string, WebSocket>();

app.UseWebSockets();
app.Map("/", async context =>
{
    if (!context.WebSockets.IsWebSocketRequest)
        context.Response.StatusCode = (int)HttpStatusCode.BadRequest;
    else
    {
        using var webSocket = await context.WebSockets.AcceptWebSocketAsync();
        connections.Add(Guid.NewGuid().ToString(), webSocket);

        while (true)
        {
            if (webSocket.State == WebSocketState.Closed)
                continue;
            
            var res = await webSocket.ReceiveAsync(buffer, CancellationToken.None);
            
            if (res.MessageType == WebSocketMessageType.Close)
                await webSocket.CloseAsync(WebSocketCloseStatus.NormalClosure, null, CancellationToken.None);
            else
            {
                var recievedMessage = Encoding.ASCII.GetString(buffer, 0, res.Count);
                var recievedMessageInBuffer =  Encoding.ASCII.GetBytes(recievedMessage);

                foreach (var connection in connections) // broadcasting
                {
                    if (connection.Value.State == WebSocketState.Open)
                        await connection.Value.SendAsync(recievedMessageInBuffer, WebSocketMessageType.Text, true, default);
                    
                    else
                        connections.Remove(connection.Key);
                }
            }
        }
    }
});

await app.RunAsync();