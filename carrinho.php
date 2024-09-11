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

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$id_usuario = $_SESSION['id_usuario'];
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
                        echo "<td>" . $row['preco'] . "</td>";
                        echo "<td>" . $row['quantidade'] . "</td>";
                        echo "<td>" . $subtotal . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center'>Seu carrinho está vazio.</td></tr>";
                }
                ?>
                <tr>
                    <td colspan="4" class="text-right"><strong>Total:</strong></td>
                    <td><strong><?php echo $total; ?></strong></td>
                </tr>
            </tbody>
        </table>
        <a href="checkout.php" class="btn btn-success">Finalizar Compra</a>
    </div>
</body>
</html>
<?php $conn->close(); ?>
