<?php
// register.php
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $senha = password_hash($_POST["senha"], PASSWORD_BCRYPT); // Criptografa a senha

    $stmt = $conn->prepare("INSERT INTO Usuarios (nome, email, senha) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nome, $email, $senha);

    if ($stmt->execute()) {
        echo "Usuário registrado com sucesso!";
        header("Location: login.php");
    } else {
        echo "Erro ao registrar o usuário: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>
