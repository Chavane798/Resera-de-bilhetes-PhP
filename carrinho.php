<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit();
}

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

$id_usuario = $_SESSION['id_usuario'];

// Prepara a consulta para evitar injeção de SQL
$stmt = $conn->prepare("SELECT c.id_viagem, v.destino, v.data_hora, v.preco, c.quantidade
                         FROM Carrinho c
                         JOIN Viagens v ON c.id_viagem = v.id_viagem
                         WHERE c.id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Carrinho de Compras</title>
    <link rel="stylesheet" href="bootstrap.css">
</head>
<body>
    <div class="container">
        <h2 class="text-center mt-5">Carrinho de Compras</h2>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Destino</th>
                    <th>Data e Hora</th>
                    <th>Preço</th>
                    <th>Quantidade</th>
                    <th>Total</th>
                    <th>Ação</th>
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
                        echo "<td><a href='remover_carrinho.php?id_viagem=" . $row['id_viagem'] . "' class='btn btn-danger'>Remover</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center'>Seu carrinho está vazio.</td></tr>";
                }
                ?>
                <tr>
                    <td colspan="4" class="text-right"><strong>Total:</strong></td>
                    <td><strong>R$ <?php echo number_format($total, 2, ',', '.'); ?></strong></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <a href="checkout.php" class="btn btn-success">Finalizar Compra</a>
    </div>
</body>
</html>
<?php 
$stmt->close(); // Fecha a declaração
$conn->close(); // Fecha a conexão
?>
