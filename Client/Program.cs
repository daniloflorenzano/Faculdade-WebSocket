using System.Net.WebSockets;
using System.Text;

using var ws = new ClientWebSocket();
await ws.ConnectAsync(new Uri("ws://localhost:5187/"), CancellationToken.None);

while (ws.State == WebSocketState.Open) 
{
    var message = Encoding.ASCII.GetBytes(Console.ReadLine());
    await ws.SendAsync(message, WebSocketMessageType.Text, true, CancellationToken.None);
}