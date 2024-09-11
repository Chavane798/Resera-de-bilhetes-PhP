<?php
// cadastro.php

// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "reserva_bilhetes";
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $senha = password_hash($_POST["senha"], PASSWORD_BCRYPT); // Criptografa a senha

    // Prepara e executa a inserção dos dados
    $stmt = $conn->prepare("INSERT INTO Usuarios (nome, email, senha) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nome, $email, $senha);

    if ($stmt->execute()) {
        echo "Usuário registrado com sucesso!";
        header("Location: login.php"); // Redireciona para a página de login após o cadastro
        exit();
    } else {
        echo "Erro ao registrar o usuário: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bootstrap.css">
    <title>Cadastro</title>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Cadastro</h2>
        <form method="post" action="cadastro.php">
            <fieldset>
                <div class="form-group">
                    <label for="nome" class="form-label mt-4">Nome de Usuário</label>
                    <input type="text" class="form-control" id="nome" name="nome" placeholder="Digite o nome do usuário" required>
                </div>
                <div class="form-group">
                    <label for="email" class="form-label mt-4">E-mail</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Digite seu e-mail" required>
                </div>
                <div class="form-group">
                    <label for="senha" class="form-label mt-4">Senha</label>
                    <input type="password" class="form-control" id="senha" name="senha" placeholder="Digite sua senha" autocomplete="off" required>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Cadastrar</button>
            </fieldset>
        </form>
    </div>
</body>
</html>
