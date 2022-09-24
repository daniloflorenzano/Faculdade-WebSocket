<?php
    include("check.php");

    if ($_GET["term"]){
        $username = mysqli_real_escape_string($con, $_GET["term"]);

        // Query
        $stmt = $con->prepare("SELECT Id, Username, Picture FROM User WHERE (Username LIKE '%$username%') ORDER BY Username DESC LIMIT 20");
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->num_rows;

        if ($count < 1) {
            echo '<p class="noResults">Sem resultados</p>';
        }

        while ($user = $result->fetch_assoc()) {
            ?>
            <div class="row" onclick="$('#searchContainer').hide(); chat('<?php echo $user['Id'] ?>');">
                <img src="profilePics/<?php echo $user["Picture"] ?>" />
                <p><?php echo $user["Username"] ?></p>
            </div>
            <?php
        }
    }
?>