<?php
    include("check.php");

    if (isset($_GET["id"])){
        $user_id = $_GET["id"];

        // Query
        $stmt = $con->prepare("SELECT `Sender`, `Message`, `Image` FROM Chat WHERE (Sender = ? AND Reciever = ?) OR (Reciever = ? AND Sender = ?) ORDER BY Id");
        $stmt->bind_param("iiii", $user_id, $uid, $user_id, $uid);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->num_rows;

        $getUser = $con->prepare("SELECT Id, Username, Picture FROM User WHERE (Id LIKE ?) LIMIT 1");
        $getUser->bind_param("i", $user_id);
        $getUser->execute();
        $user = $getUser->get_result()->fetch_assoc();

        if ($count < 1) {
            echo '<p class="info">Envie a sua primeira mensagem para '.$user["Username"].'</p>';
        } else {
            while ($message = $result->fetch_assoc()) {
                if($message["Sender"] == $uid && $message["Image"] != "") {
                    ?>
                    <div class="row sent">
                        <img src="uploads/<?php echo $message["Image"] ?>" />
                    </div>
                    <?php
                } elseif($message["Sender"] == $uid) {
                    ?>
                    <div class="row sent">
                        <p><?php echo $message["Message"] ?></p>
                    </div>
                    <?php
                } elseif($message["Image"] != "") {
                    ?>
                    <div class="row recieved">
                        <img src="uploads/<?php echo $message["Image"] ?>" />
                    </div>
                    <?php
                } else {
                    ?>
                    <div class="row recieved">
                        <p><?php echo $message["Message"] ?></p>
                    </div>
                    <?php
                }
            }
    
            // Update conversation has opened
            $stmt = $con->prepare("UPDATE Conversations SET `Unread` = 'n' WHERE (MainUser = ? AND OtherUser = ?)");
            $stmt->bind_param("ii", $uid, $user_id);
            $stmt->execute();
        }

    } else {
        die(header("HTTP/1.0 401 Faltam parametros"));
    }
?>