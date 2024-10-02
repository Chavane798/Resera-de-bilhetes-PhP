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

$id_usuario = $_SESSION['id_usuario'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Inicia a transação
    $conn->begin_transaction();

    // Busca as informações do carrinho
    $sql = "SELECT id_viagem, quantidade FROM Carrinho WHERE id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $success = true;

        while ($row = $result->fetch_assoc()) {
            $id_viagem = $row['id_viagem'];
            $quantidade = $row['quantidade'];

            // Verifica se há bilhetes suficientes
            $sql_verifica = "SELECT bilhetes_disponiveis FROM Viagens WHERE id_viagem = ? FOR UPDATE";
            $stmt_verifica = $conn->prepare($sql_verifica);
            $stmt_verifica->bind_param("i", $id_viagem);
            $stmt_verifica->execute();
            $result_verifica = $stmt_verifica->get_result();
            $dados_viagem = $result_verifica->fetch_assoc();

            if ($dados_viagem['bilhetes_disponiveis'] >= $quantidade) {
                // Atualiza a quantidade de bilhetes disponíveis
                $sql_update = "UPDATE Viagens SET bilhetes_disponiveis = bilhetes_disponiveis - ? WHERE id_viagem = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param("ii", $quantidade, $id_viagem);
                $stmt_update->execute();

                // Adiciona a reserva ao banco de dados
                $sql_reserva = "INSERT INTO Reservas (id_usuario, id_viagem, quantidade) VALUES (?, ?, ?)";
                $stmt_reserva = $conn->prepare($sql_reserva);
                $stmt_reserva->bind_param("iii", $id_usuario, $id_viagem, $quantidade);
                $stmt_reserva->execute();

            } else {
                // Falha na quantidade de bilhetes, cancela a transação
                echo "Quantidade de bilhetes indisponível para a viagem de ID $id_viagem.";
                $success = false;
                break;
            }
        }

        if ($success) {
            // Limpa o carrinho
            $sql_delete = "DELETE FROM Carrinho WHERE id_usuario = ?";
            $stmt_delete = $conn->prepare($sql_delete);
            $stmt_delete->bind_param("i", $id_usuario);
            $stmt_delete->execute();

            // Confirma a transação
            $conn->commit();
            echo "Compra realizada com sucesso!";
        } else {
            // Desfaz a transação
            $conn->rollback();
            echo "Compra falhou. Tente novamente.";
        }

    } else {
        echo "Seu carrinho está vazio.";
    }

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
$conn->close();
?>
