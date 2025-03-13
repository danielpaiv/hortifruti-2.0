<?php
session_start();

if (isset($_POST['submit']) && !empty($_POST['nome']) && !empty($_POST['senha'])) {
    include_once('config.php');

    // Abrir conexão com o banco de dados
    $conexao = OpenCon();

    // Obter dados do formulário
    $nome = $conexao->real_escape_string($_POST['nome']);
    $senha = $conexao->real_escape_string($_POST['senha']);

    // Exibir dados para debug (remova em produção)
    print_r('Nome: ' . $nome . '<br>');
    print_r('Senha: ' . $senha . '<br>');

    // Consulta ao banco de dados
    $sql = "SELECT * FROM adm WHERE nome = ? AND senha = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param('ss', $nome, $senha);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar resultado
    if ($result->num_rows < 1) {
        // Login falhou
        unset($_SESSION['nome']);
        unset($_SESSION['senha']);
        header('Location: index.php');
    } else {
        // Login bem-sucedido
        $_SESSION['nome'] = $nome;
        $_SESSION['senha'] = $senha;
        header('Location:painel.php');
    }

    // Fechar a conexão
    CloseCon($conexao);
} else {
    // Campos não preenchidos
    header('Location: index.php');
}
