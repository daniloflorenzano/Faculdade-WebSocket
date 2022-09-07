using System.Net;
using System.Net.WebSockets;
using System.Text;

var builder = WebApplication.CreateBuilder(args);
var app = builder.Build();

var buffer = new byte[256]; 
var messages = new List<string>() {" "};

app.UseWebSockets();
app.Map("/", async context =>
{
    if (!context.WebSockets.IsWebSocketRequest)
        context.Response.StatusCode = (int)HttpStatusCode.BadRequest;
    else
    {
        using var webSocket = await context.WebSockets.AcceptWebSocketAsync();
        while (webSocket.State == WebSocketState.Open)
        {
            var res = await webSocket.ReceiveAsync(buffer, CancellationToken.None);
            if (res.MessageType == WebSocketMessageType.Close)
                await webSocket.CloseAsync(WebSocketCloseStatus.NormalClosure, null, CancellationToken.None);
            else
            {
                var recievedMessage = Encoding.ASCII.GetString(buffer, 0, res.Count);
                var recievedMessageInBuffer =  Encoding.ASCII.GetBytes(recievedMessage);

                if (messages.Last() != recievedMessage)
                {
                        messages.Add(recievedMessage);
                        await webSocket.SendAsync(recievedMessageInBuffer, WebSocketMessageType.Text, true, CancellationToken.None);
                }

                await Task.Delay(1000);
            }
        }
    }
});

await app.RunAsync();