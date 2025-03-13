<?php
include 'db_connection.php';

if (isset($_GET['id'])) {
    $productId = intval($_GET['id']);
    
    $conn = OpenCon();

    // Consulta para buscar o produto pelo ID
    $sql = "SELECT produto AS product, preco_unitario AS unit_price FROM estoque WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        echo json_encode($product);
    } else {
        echo json_encode(["error" => "Produto não encontrado."]);
    }

    $stmt->close();
    CloseCon($conn);
} else {
    echo json_encode(["error" => "ID do produto não fornecido."]);
}
?>
