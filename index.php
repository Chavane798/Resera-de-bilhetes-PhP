<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

// Inicializa a variável nome_usuario se estiver definida na sessão
$nome_usuario = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : '';

// Função para conectar ao banco de dados
function conectarDB() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "reserva_bilhetes";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Conexão falhou: " . $conn->connect_error);
    }
    return $conn;
}

// Função para buscar viagens
function buscarViagens($conn, $destino) {
    $stmt = $conn->prepare("SELECT * FROM Viagens WHERE destino LIKE ?");
    $searchParam = "%$destino%";
    $stmt->bind_param("s", $searchParam);
    $stmt->execute();
    return $stmt->get_result();
}

// Conexão com o banco de dados
$conn = conectarDB();

// Obtém o destino da pesquisa
$destino = isset($_GET['destino']) ? htmlspecialchars($_GET['destino']) : '';

// Consulta os dados da viagem no banco de dados
$result = buscarViagens($conn, $destino);
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
                <input type="text" name="destino" class="form-control me-2" placeholder="Pesquisar Destino" value="<?php echo $destino; ?>">
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
        // Exibe os dados da viagem em cada card
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $disabled = $row['bilhetes_disponiveis'] > 0 ? '' : 'disabled';
                echo '<div class="card">';
                echo '<h2>Viagem ID: ' . htmlspecialchars($row['id_viagem']) . '</h2>';
                echo '<p>Destino: ' . htmlspecialchars($row['destino']) . '</p>';
                echo '<p>Data e Hora: ' . htmlspecialchars($row['data_hora']) . '</p>';
                echo '<p>Preço: Mt ' . number_format($row['preco'], 2, ',', '.') . '</p>';
                echo '<p>Bilhetes Disponíveis: ' . htmlspecialchars($row['bilhetes_disponiveis']) . '</p>';

                if (isset($_SESSION['id_usuario'])) {
                    // Formulário para adicionar ao carrinho
                    echo '<form method="post" action="adicionar_carrinho.php">';
                    echo '<input type="hidden" name="id_viagem" value="' . htmlspecialchars($row['id_viagem']) . '">';
                    echo '<input type="number" name="quantidade" min="1" max="' . htmlspecialchars($row['bilhetes_disponiveis']) . '" value="1" class="form-control mb-2" ' . $disabled . '>';
                    echo '<input type="submit" name="adicionar" class="btn btn-primary" value="Adicionar ao Carrinho" ' . $disabled . '>';
                    echo '</form>';
                } else {
                    echo '<p><a href="login.php" class="btn btn-primary">Faça login para reservar</a></p>';
                }

                echo '</div>';
            }
        } else {
            echo "<p>Nenhum resultado encontrado.</p>";
        }

        $conn->close();
        ?>
    </div>
</body>
</html>
