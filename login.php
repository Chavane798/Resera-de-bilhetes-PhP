<?php
session_start(); // Inicia a sessão

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

// Processamento do login
$message = ""; // Mensagem de feedback

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Prepara e executa a consulta
    $stmt = $conn->prepare("SELECT id_usuario, nome, senha, is_gestor FROM Usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($senha, $user['senha'])) {
            $_SESSION['id_usuario'] = $user['id_usuario'];
            $_SESSION['nome'] = $user['nome'];
            $_SESSION['is_gestor'] = $user['is_gestor']; // Armazena se o usuário é gestor

            // Direciona para a página apropriada
            if ($email === "gervasiochavane798@gmail.com") {
                header("Location: lista_reservas.php"); // Redireciona diretamente para o painel do gestor
            } elseif ($user['is_gestor']) {
                header("Location: lista_reservas.php"); // Redireciona para o painel do gestor
            } else {
                header("Location: index.php"); // Redireciona para a página principal
            }
            exit();
        } else {
            $message = "Senha incorreta.";
        }
    } else {
        $message = "Usuário não encontrado.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="bootstrap.css">
</head>
<body>
    <div class="container">
        <h2 class="text-center mt-5">Login</h2>
        <form method="post" action="login.php" class="mt-3">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" name="email" required>
            </div>
            <div class="mb-3">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" class="form-control" name="senha" required>
            </div>
            <?php if ($message): ?>
                <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            <div class="d-flex justify-content-center mt-3">
                <button type="submit" class="btn btn-primary me-2">Entrar</button>
                Não tem uma conta? <a href="cadastro.php" class="btn btn-secondary">Cadastre-se</a>
            </div>
        </form>
    </div>
</body>
</html>
