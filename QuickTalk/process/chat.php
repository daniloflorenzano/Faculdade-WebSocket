<?php
    include("check.php");

    if (true){
        $user_id = $_GET["id"];

        // Get user
        $getUser = $con->prepare("SELECT Username FROM User WHERE (Id LIKE ?) LIMIT 1");
        $getUser->bind_param("i", $user_id);
        $getUser->execute();
        $user = $getUser->get_result()->fetch_assoc();

        ?>
        <div class="topMenu">
            <p class="title"><?php echo $user["Username"]; ?></p>
        </div>

        <div class="innerContainer"></div>

        <form method="POST" enctype="multipart/form-data" id="sendMessage">
            <input type="number" value="<?php echo $user_id; ?>" name="id" hidden />
            <input type="text" autocomplete="off" maxlength="500" name="message" id="messageInput" placeholder="Escreva aqui a sua mensagem" />
            <input type='file' name="image" accept="image/x-png,image/jpeg" id="sendImage" hidden />
            <label for="sendMessage">
                <input type="submit" value="Enviar" onclick="sendMessage()">
            </label>
        </form>

        <script>
            const message = document.querySelector('#messageInput');
            const userName = document.querySelector('.name').innerHTML;
            const chatbox = document.querySelector('.innerContainer');


                const webSocket = new WebSocket('ws://localhost:5187');

                webSocket.onmessage = (event) => {
                    let res = {
						user: event.data.split(':')[0],
						message: event.data.split(':')[1]
					}					

                    let textClass = "msg-user";
                    if (res.user === userName) {
                        textClass = "msg-user self-user"
                    }

                    chatbox.insertAdjacentHTML('beforeend', `<p class="${textClass}">${res.user}: ${res.message}</p>`);
                }

                function sendMessage() {
                    webSocket.send(`${userName}: ${message.value}`);
				    message.value = '';
                }

                const form = document.querySelector('#sendMessage');
                form.addEventListener('submit', (e) => e.preventDefault());
        </script>
        <?php
    } else {
        ?>
        <div class="empty">
            <p>Selecione uma conversa para socializar com esse utilizador</p>
        </div>
        <?php
    }
?>