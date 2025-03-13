<?php

    $data_hora = date("Y-m-d H:i:s"); // Obter a data e hora atual no formato correto
    // Conexão com o banco de dados
    include 'db_connection.php'; // Ajuste o caminho do arquivo

    

    $conn = OpenCon(); // Chama a função para abrir a conexão

    

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = intval($_POST['id']);
        $produto = $_POST['produto'];
        $quantidade = floatval($_POST['quantidade']);
        $preco = floatval($_POST['preco']);
        $preco_unitario = floatval($_POST['preco_unitario']);
        $descricao = $_POST['descricao'];
        $data_hora = $_POST['data_hora'];
    
        // Converter o valor do campo datetime-local para o formato do banco de dados
        $data_hora = date("Y-m-d H:i:s", strtotime($_POST['data_hora']));
    
        $sql = "UPDATE estoque SET produto = ?, quantidade = ?, preco = ?, preco_unitario = ?, descricao = ?, data_hora = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdddssi", $produto, $quantidade, $preco, $preco_unitario, $descricao, $data_hora, $id);
    
        if ($stmt->execute()) {
            echo "Produto atualizado com sucesso!";
            header("Location: editarEstoque.php"); // Redirecione para a página da tabela
            exit;
        } else {
            echo "Erro ao atualizar produto: " . $conn->error;
        }
    }
    CloseCon($conn);
?>
