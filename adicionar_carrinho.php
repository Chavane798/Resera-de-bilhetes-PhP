<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit();
}

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

// Função para adicionar bilhete ao carrinho
function adicionarAoCarrinho($id_usuario, $id_viagem, $quantidade) {
    $conn = conectarDB();

    // Verifica a quantidade disponível
    $sql = "SELECT bilhetes_disponiveis FROM Viagens WHERE id_viagem = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_viagem);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "Viagem não encontrada.";
        $conn->close();
        return;
    }

    $viagem = $result->fetch_assoc();
    if ($quantidade > $viagem['bilhetes_disponiveis']) {
        echo "Quantidade solicitada excede os bilhetes disponíveis.";
        $conn->close();
        return;
    }

    // Adiciona o bilhete ao carrinho
    $sql = "INSERT INTO Carrinho (id_usuario, id_viagem, quantidade) VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE quantidade = quantidade + VALUES(quantidade)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $id_usuario, $id_viagem, $quantidade);

    if ($stmt->execute()) {
        // Atualiza a quantidade de bilhetes disponíveis
        $sql = "UPDATE Viagens SET bilhetes_disponiveis = bilhetes_disponiveis - ? WHERE id_viagem = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $quantidade, $id_viagem);
        $stmt->execute();
        
        header("Location: index.php");
    } else {
        echo "Erro ao adicionar bilhete ao carrinho: " . $stmt->error;
    }

    $conn->close();
}

// Obtém os dados do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_viagem'], $_POST['quantidade'])) {
    $id_usuario = $_SESSION['id_usuario'];
    $id_viagem = intval($_POST['id_viagem']);
    $quantidade = intval($_POST['quantidade']);

    // Valida a quantidade
    if ($quantidade <= 0) {
        echo "Quantidade deve ser maior que zero.";
        exit();
    }

    adicionarAoCarrinho($id_usuario, $id_viagem, $quantidade);
} else {
    echo "Dados inválidos.";
}
?>
