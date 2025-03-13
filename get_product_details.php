<?php
include 'db_connection.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Abrir conexão com o banco de dados
    $conn = OpenCon();

    // Consultar o produto pelo ID
    $sql = "SELECT produto, preco_unitario FROM estoque WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Retornar os dados como JSON
        $row = $result->fetch_assoc();
        echo json_encode($row);
    } else {
        echo json_encode(null);
    }

    // Fechar a conexão
    CloseCon($conn);
}
?>
