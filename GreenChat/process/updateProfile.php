<?php
    include("check.php");

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        //Stores the filename as it was on the client computer.
        $imagename = $username."_".rand(999, 999999).$_FILES['imgInp']['name'];

        //Stores the filetype e.g image/jpeg
        //$imagetype = $_FILES['pic']['type'];

        //Stores any error codes from the upload.
        //$imageerror = $_FILES['pic']['error'];

        //Stores the tempname as it is given by the host when uploaded.
        $imagetemp = $_FILES['imgInp']['tmp_name'];

        //The path you wish to upload the image to
        $imagePath = "../profilePics/";

        if (is_uploaded_file($imagetemp)) {
            if (move_uploaded_file($imagetemp, $imagePath . $imagename)) {
                $stmt = $con->prepare("UPDATE User SET `Picture` = ? WHERE Id = ?");
                $stmt->bind_param("si", $imagename, $uid);
                $stmt->execute();
                if ($stmt) {
                    return true;
                } else {
                    die(header("HTTP/1.0 401 Erro ao guardar imagem na base de dados"));
                }
            } else {
                die(header("HTTP/1.0 401 Erro ao guardar imagem"));
            }
        } else {
            die(header("HTTP/1.0 401 Erro ao carregar imagem"));
        }
    } else {
        die(header("HTTP/1.0 401 Faltam parametros"));
    }
?>