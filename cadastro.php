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

// Inicializa variáveis de mensagem
$mensagem = '';
$erro = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST["nome"]);
    $email = trim($_POST["email"]);
    $senha = $_POST["senha"];
    
    // Validação básica dos campos
    if (empty($nome) || empty($email) || empty($senha)) {
        $mensagem = "Todos os campos são obrigatórios.";
        $erro = true;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = "E-mail inválido.";
        $erro = true;
    } else {
        // Verifica se o e-mail já está cadastrado
        $stmt = $conn->prepare("SELECT * FROM Usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $mensagem = "E-mail já cadastrado. Tente outro.";
            $erro = true;
        } else {
            // Criptografa a senha
            $senha_criptografada = password_hash($senha, PASSWORD_BCRYPT); 

            // Prepara e executa a inserção dos dados
            $stmt = $conn->prepare("INSERT INTO Usuarios (nome, email, senha) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $nome, $email, $senha_criptografada);

            if ($stmt->execute()) {
                $mensagem = "Usuário registrado com sucesso!";
                header("Location: login.php"); // Redireciona para a página de login após o cadastro
                exit();
            } else {
                $mensagem = "Erro ao registrar o usuário: " . $stmt->error;
                $erro = true;
            }

            $stmt->close();
        }
    }
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
        
        <?php if ($mensagem): ?>
            <div class="alert alert-<?php echo $erro ? 'danger' : 'success'; ?>" role="alert">
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>
        
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
