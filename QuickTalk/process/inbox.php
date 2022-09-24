<?php
    include("check.php");

    /* ?>
    <div class="chat selected" onclick="chat('<?php echo $user['Id']; ?>')">
        <img src="img/globe.png" />
        <p>Toda a comunidade</p>
    </div>
    <?php */
    
    // Query
    $stmt = $con->prepare("SELECT * FROM Conversations WHERE (MainUser = ?) ORDER BY Modification DESC");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->num_rows;

    if ($count < 1) {
        echo '<div class="empty"><p>Pesquise um utilizador e começe um chat!</p></div>';
    }

    while ($inbox = $result->fetch_assoc()) {
        $stmt = $con->prepare("SELECT Id, Username, Picture FROM User WHERE (Id LIKE ?) LIMIT 1");
        $stmt->bind_param("i", $inbox["OtherUser"]);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user) {
            ?>
            <div class="chat <?php if($inbox["Unread"] == "y") { echo "new"; } ?>" onclick="chat('<?php echo $user['Id']; ?>')">
                <img src="profilePics/<?php echo $user["Picture"]; ?>" />
                <p><?php echo $user["Username"]; ?></p>
            </div>
            <?php
        }
    }
?>