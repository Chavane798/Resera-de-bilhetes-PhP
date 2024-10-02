<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

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

// Obtém o ID do usuário
$id_usuario = $_SESSION['id_usuario'];

// Verifica se o carrinho não está vazio
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Prepara a consulta para verificar o carrinho
    $stmt = $conn->prepare("SELECT id_viagem, quantidade FROM Carrinho WHERE id_usuario = ?");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $id_viagem = $row['id_viagem'];
            $quantidade = $row['quantidade'];

            // Atualiza a quantidade de bilhetes disponíveis
            $stmt_update = $conn->prepare("UPDATE Viagens SET bilhetes_disponiveis = bilhetes_disponiveis - ? WHERE id_viagem = ?");
            $stmt_update->bind_param("ii", $quantidade, $id_viagem);
            if (!$stmt_update->execute()) {
                echo "Erro ao atualizar bilhetes disponíveis: " . $stmt_update->error;
                exit();
            }

            // Adiciona a reserva ao banco de dados
            $stmt_reserva = $conn->prepare("INSERT INTO Reservas (id_usuario, id_viagem, quantidade) VALUES (?, ?, ?)");
            $stmt_reserva->bind_param("iii", $id_usuario, $id_viagem, $quantidade);
            if (!$stmt_reserva->execute()) {
                echo "Erro ao registrar reserva: " . $stmt_reserva->error;
                exit();
            }
        }

        // Limpa o carrinho
        $stmt_delete = $conn->prepare("DELETE FROM Carrinho WHERE id_usuario = ?");
        $stmt_delete->bind_param("i", $id_usuario);
        if (!$stmt_delete->execute()) {
            echo "Erro ao limpar o carrinho: " . $stmt_delete->error;
            exit();
        }

        echo "<div class='alert alert-success'>Compra realizada com sucesso!</div>";
    } else {
        echo "<div class='alert alert-warning'>Seu carrinho está vazio.</div>";
    }

    // Fecha as declarações
    $stmt->close();
    $conn->close();
    exit();
}

// Recupera o conteúdo do carrinho para exibição
$sql = "SELECT c.id_viagem, v.destino, v.data_hora, v.preco, c.quantidade
        FROM Carrinho c
        JOIN Viagens v ON c.id_viagem = v.id_viagem
        WHERE c.id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link rel="stylesheet" href="bootstrap.css">
</head>
<body>
    <div class="container">
        <h2 class="text-center mt-5">Resumo da Compra</h2>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Destino</th>
                    <th>Data e Hora</th>
                    <th>Preço</th>
                    <th>Quantidade</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $subtotal = $row['preco'] * $row['quantidade'];
                        $total += $subtotal;
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['destino']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['data_hora']) . "</td>";
                        echo "<td>R$ " . number_format($row['preco'], 2, ',', '.') . "</td>";
                        echo "<td>" . htmlspecialchars($row['quantidade']) . "</td>";
                        echo "<td>R$ " . number_format($subtotal, 2, ',', '.') . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center'>Seu carrinho está vazio.</td></tr>";
                }
                ?>
                <tr>
                    <td colspan="4" class="text-right"><strong>Total:</strong></td>
                    <td><strong>R$ <?php echo number_format($total, 2, ',', '.'); ?></strong></td>
                </tr>
            </tbody>
        </table>
        <form method="post" action="checkout.php">
            <button type="submit" class="btn btn-success">Finalizar Compra</button>
        </form>
    </div>
</body>
</html>

<?php
$stmt->close(); // Fecha a declaração
$conn->close(); // Fecha a conexão
?>
