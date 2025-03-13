<?php
    // Conexão com o banco de dados
    $servername = "localhost"; // Alterar conforme necessário
    $username = "root";        // Alterar conforme necessário
    $password = "";            // Alterar conforme necessário
    $database = "hortfruti";   // Alterar conforme necessário

    $conn = new mysqli($servername, $username, $password, $database);

    // Verificar a conexão
    if ($conn->connect_error) {
        die("Falha na conexão: " . $conn->connect_error);
    }

    // Função para salvar o faturamento
    function salvarFaturamento($dinheiro, $cartao, $pix, $user_id) {
        global $conn;

        // Prepare a query para inserir o faturamento com o user_id
        $stmt = $conn->prepare("INSERT INTO faturamento (user_id, pagamento_dinheiro, pagamento_cartao, pagamento_pix) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("dddd", $user_id, $dinheiro, $cartao, $pix);

        if ($stmt->execute()) {
            echo "Faturamento registrado com sucesso!";
        } else {
            echo "Erro ao salvar faturamento: " . $stmt->error;
        }

        $stmt->close();
    }

    // Capturar os valores do formulário (exemplo de valores recebidos via POST)
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Verificar se o usuário está logado
        session_start();

        // Garantir que o usuário esteja logado antes de processar
        if (!isset($_SESSION['user_id'])) {
            echo "Usuário não autenticado!";
            exit();
        }

        // Capturar os dados de pagamento
        $dinheiro = $_POST["cash-payment"] ?? 0;
        $cartao = $_POST["card-payment"] ?? 0;
        $pix = $_POST["pix-payment"] ?? 0;
        $user_id = $_SESSION['user_id']; // ID do usuário logado

        // Salvar o faturamento no banco de dados, incluindo o user_id
        salvarFaturamento($dinheiro, $cartao, $pix, $user_id);
    }

    // Fechar conexão
    $conn->close();
?>

