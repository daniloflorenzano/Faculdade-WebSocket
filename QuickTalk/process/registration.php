<?php
    include("connection/connect.php");

    if(isset($_POST["username"]) && isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["repPassword"])) {

        // Normalization
        $username = $_POST["username"];
        $email = $_POST["email"];
        $password = $_POST["password"];
        $RepPassword = $_POST["repPassword"];

        // Check if values are okay
        if ($username == "" || $email == "" || $password == "" || $RepPassword == "") {
            die(header("HTTP/1.0 401 Preenche todos os campos do formulário"));
        }

        // Check if username already exists
        $checkUsername = $con->prepare("SELECT Id FROM User WHERE Username = ?");
        $checkUsername->bind_param("s", $username);
        $checkUsername->execute();
        $count = $checkUsername->get_result()->num_rows;
        if ($count > 0) {
            die(header("HTTP/1.0 401 Username existente"));
        }

        // Check if email already exists
        $checkEmail = $con->prepare("SELECT Id FROM User WHERE Email = ?");
        $checkEmail->bind_param("s", $email);
        $checkEmail->execute();
        $count = $checkEmail->get_result()->num_rows;
        if ($count > 0) {
            die(header("HTTP/1.0 401 Conta registada com este e-mail existente"));
        }
        
        // Verify password repeat
        if ($password != $RepPassword) {
            die(header("HTTP/1.0 401 Passwords diferentes"));
        }

        // Ecrypt password
        $password = password_hash($password, PASSWORD_DEFAULT);
        
        // Create secure code and token
        $token = bin2hex(openssl_random_pseudo_bytes(20));
        $secure = rand(1000000000, 9999999999);
        
        // Queries for creation and collection
        $stmt = $con->prepare("INSERT INTO User (`Username`, `Email`, `Password`, `Online`, `Token`, `Secure`, `Creation`) 
                                                VALUES (?, ?, ?, now(), ?, ?, now())");
        $stmt->bind_param("ssssi", $username, $email, $password, $token, $secure);
        $stmt->execute();

        $getUser = $con->prepare("SELECT Id, Token, Secure FROM User WHERE Email = ?");
        $getUser->bind_param("s", $email);
        $getUser->execute();
        $user = $getUser->get_result()->fetch_assoc();

        if ($stmt && $user) {
            setcookie("ID", $user['Id'], time() + (10 * 365 * 24 * 60 * 60));
            setcookie("TOKEN", $user['Token'], time() + (10 * 365 * 24 * 60 * 60));
            setcookie("SECURE", $user['Secure'], time() + (10 * 365 * 24 * 60 * 60));
            return true;
        } else {
            die(header("HTTP/1.0 401 Ocorreu um erro na base de dados"));
        }
    } else {
        die(header("HTTP/1.0 401 Formulário de autenticação inválido"));
    }
?>