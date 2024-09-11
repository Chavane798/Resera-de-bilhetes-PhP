<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

// Inicializa a variável nome_usuario se estiver definida na sessão
$nome_usuario = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : '';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grid de Viagens</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="bootstrap.css">
</head>

<body>
    <!-- Cabeçalho com formulário de pesquisa, opções de login/cadastro, visualização de reservas e carrinho -->
    <header class="bg-primary p-3 text-white">
        <div class="container">
            <h1 class="text-center">Sistema de Reservas de Viagens</h1>
            <form method="GET" action="index.php" class="d-flex justify-content-center mt-3">
                <input type="text" name="destino" class="form-control me-2" placeholder="Pesquisar Destino" value="<?php echo isset($_GET['destino']) ? htmlspecialchars($_GET['destino']) : ''; ?>">
                <button type="submit" class="btn btn-light">Pesquisar</button>
            </form>
            <div class="d-flex justify-content-center mt-3">
                <?php if (!isset($_SESSION['id_usuario'])): ?>
                    <a href="login.php" class="btn btn-light me-2">Login</a>
                    <a href="cadastro.php" class="btn btn-light">Cadastro</a>
                <?php else: ?>
                    <span class="me-3">Bem-vindo, <?php echo htmlspecialchars($nome_usuario); ?>!</span>
                    <a href="visualizar_carrinho.php" class="btn btn-light me-2">Visualizar Carrinho</a>
                    <a href="logout.php" class="btn btn-danger">Sair</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Conteúdo principal -->
    <div class="grid-container mt-4">
        <?php
        // Conexão com o banco de dados
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "reserva_bilhetes";

        // Cria a conexão
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Verifica a conexão
        if ($conn->connect_error) {
            die("Conexão falhou: " . $conn->connect_error);
        }

        // Consulta os dados da viagem no banco de dados com filtro de pesquisa
        $destino = isset($_GET['destino']) ? $_GET['destino'] : '';
        $sql = "SELECT * FROM Viagens WHERE destino LIKE '%$destino%'";
        $result = $conn->query($sql);

        // Exibe os dados da viagem em cada card
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="card">';
                echo '<h2>Viagem ID: ' . $row['id_viagem'] . '</h2>';
                echo '<p>Destino: ' . $row['destino'] . '</p>';
                echo '<p>Data e Hora: ' . $row['data_hora'] . '</p>';
                echo '<p>Preço: R$ ' . number_format($row['preco'], 2, ',', '.') . '</p>';
                echo '<p>Bilhetes Disponíveis: ' . $row['bilhetes_disponiveis'] . '</p>';

                if (isset($_SESSION['id_usuario'])) {
                    // Formulário para adicionar ao carrinho
                    echo '<form method="post" action="adicionar_carrinho.php">';
                    echo '<input type="hidden" name="id_viagem" value="' . $row['id_viagem'] . '">';
                    echo '<input type="number" name="quantidade" min="1" max="' . $row['bilhetes_disponiveis'] . '" value="1" class="form-control mb-2">';
                    echo '<input type="submit" name="adicionar" class="btn btn-primary" value="Adicionar ao Carrinho">';
                    echo '</form>';
                } else {
                    echo '<p><a href="login.php" class="btn btn-primary">Faça login para reservar</a></p>';
                }

                echo '</div>';
            }
        } else {
            echo "Nenhum resultado encontrado.";
        }

        $conn->close();
        ?>
    </div>
</body>
</html>
