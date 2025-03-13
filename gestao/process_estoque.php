<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $produto = $_POST['produto'];
    $quantidade = $_POST['quantidade'];
    $preco = $_POST['preco'];
    $preco_unitario = $_POST['preco_unitario'];
    $descricao = $_POST['descricao'];

    // Abrir conexão
    $conn = OpenCon();

    // Exemplo de inserção segura usando MySQLi
    $stmt = $conn->prepare("INSERT INTO estoque (produto, quantidade, preco, preco_unitario, descricao) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sddds", $produto, $quantidade, $preco, $preco_unitario, $descricao);
    $stmt->execute();
    $stmt->close();

    // Fechar conexões
    CloseCon($conn);

    // Redirecionar ou mostrar mensagem de sucesso
    echo "<script>alert('Produto cadastrado com sucesso!'); window.location.href='formulario_estoque.php';</script>";
} else {
    echo "Método de requisição inválido.";
}
?>
