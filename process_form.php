<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar se o ID do usuário foi enviado
    if (!isset($_POST['user-id']) || empty($_POST['user-id'])) {
        echo "Erro: ID do usuário não fornecido.";
        exit;
    }

    $user_id = $_POST['user-id']; // Recebe o ID do usuário

    // Obter os dados dos produtos enviados
    $produtos = $_POST['product'];
    $pagamento_dinheiro = $_POST['cash-payment'];
    $pagamento_cartao = $_POST['card-payment'];
    $pagamento_pix = $_POST['pix-payment'];

    // Abrir conexão com o banco de dados
    $conn = OpenCon();

    // Processar cada produto e inserir na tabela de vendas
    foreach ($produtos as $produto) {
        $produto_nome = $produto['name'];
        $quantidade = $produto['quantity'];
        $preco_unitario = $produto['unitPrice'];
        $valor_total = $produto['totalPrice'];

        // Exemplo de inserção segura usando MySQLi
        $stmt = $conn->prepare("INSERT INTO vendas (produto, quantidade, preco_unitario, valor_total, pagamento_dinheiro, pagamento_cartao, pagamento_pix, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sddddddi", $produto_nome, $quantidade, $preco_unitario, $valor_total, $pagamento_dinheiro, $pagamento_cartao, $pagamento_pix, $user_id);
        $stmt->execute();
        $stmt->close();
    }

    // Fechar a conexão
    CloseCon($conn);

    // Responder ao cliente (confirmação de sucesso)
    echo "Venda finalizada com sucesso!";
} else {
    echo "Método de requisição inválido.";
}
?>
