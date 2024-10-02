<?php
session_start();

// Verifica se o usuário está logado e se é um gestor
if (!isset($_SESSION['id_usuario']) || !$_SESSION['is_gestor']) {
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

// Consulta as reservas
$sql = "SELECT r.id_reserva, u.nome AS usuario, v.destino, r.quantidade, r.data_reserva, r.status
        FROM Reservas r
        JOIN Usuarios u ON r.id_usuario = u.id_usuario
        JOIN Viagens v ON r.id_viagem = v.id_viagem";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de Reservas</title>
    <link rel="stylesheet" href="bootstrap.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Lista de Reservas</h2>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>ID Reserva</th>
                    <th>Usuário</th>
                    <th>Destino</th>
                    <th>Quantidade</th>
                    <th>Data da Reserva</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['id_reserva'] . "</td>";
                        echo "<td>" . htmlspecialchars($row['usuario']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['destino']) . "</td>";
                        echo "<td>" . $row['quantidade'] . "</td>";
                        echo "<td>" . date('d/m/Y H:i', strtotime($row['data_reserva'])) . "</td>";
                        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center'>Nenhuma reserva encontrada.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>
