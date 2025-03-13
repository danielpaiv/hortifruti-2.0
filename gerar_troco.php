<?php
include_once('db_connection.php');

// Verificar se o valor de troco foi enviado
if (isset($_POST['troco'])) {
    $troco = $_POST['troco'];

    // Conectar ao banco de dados
    $conn = OpenCon();

    // Inserir o valor do troco no banco de dados (ajustar conforme necessidade)
    $sql = "INSERT INTO troco (valor_troco) VALUES ('$troco')";

    if ($conn->query($sql) === TRUE) {
        echo "Troco registrado com sucesso!";
    } else {
        echo "Erro ao registrar troco: " . $conn->error;
    }

    // Fechar conexão
    CloseCon($conn);
}
?>
