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
    $sql = "SELECT id_viagem, quantidade FROM Carrinho WHERE id_usuario = $id_usuario";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $id_viagem = $row['id_viagem'];
            $quantidade = $row['quantidade'];

            // Atualiza a quantidade de bilhetes disponíveis
            $sql_update = "UPDATE Viagens SET bilhetes_disponiveis = bilhetes_disponiveis - $quantidade WHERE id_viagem = $id_viagem";
            $conn->query($sql_update);

            // Adiciona a reserva ao banco de dados
            $sql_reserva = "INSERT INTO Reservas (id_usuario, id_viagem, quantidade) VALUES ($id_usuario, $id_viagem, $quantidade)";
            $conn->query($sql_reserva);
        }

        // Limpa o carrinho
        $sql_delete = "DELETE FROM Carrinho WHERE id_usuario = $id_usuario";
        $conn->query($sql_delete);

        echo "Compra realizada com sucesso!";
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
        WHERE c.id_usuario = $id_usuario";
$result = $conn->query($sql);
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
                        echo "<td>" . $row['destino'] . "</td>";
                        echo "<td>" . $row['data_hora'] . "</td>";
                        echo "<td>R$ " . number_format($row['preco'], 2, ',', '.') . "</td>";
                        echo "<td>" . $row['quantidade'] . "</td>";
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
