<?php
session_start();

// Verificar se o formulário foi enviado corretamente
if (isset($_POST['submit']) && !empty($_POST['nome']) && !empty($_POST['senha'])) {
    include_once('config.php');

    // Abrir conexão com o banco de dados
    $conexao = OpenCon();

    // Obter dados do formulário e proteger contra SQL Injection
    $nome = $conexao->real_escape_string($_POST['nome']);
    $senha = $conexao->real_escape_string($_POST['senha']);

    // Exibir dados para debug (remova em produção)
    echo 'Debug - Dados recebidos: <br>';
    echo 'Nome: ' . htmlspecialchars($nome) . '<br>';
    echo 'Senha: ' . htmlspecialchars($senha) . '<br>';

    // Consulta ao banco de dados
    $sql = "SELECT id, nome, senha FROM usuarios WHERE nome = ? AND senha = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param('ss', $nome, $senha);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar resultado da consulta
    if ($result->num_rows < 1) {
        // Login falhou
        echo 'Login falhou. Usuário ou senha inválidos.<br>';
        unset($_SESSION['user_id']);
        unset($_SESSION['nome']);
        unset($_SESSION['senha']);
        header('Location: index.php');
    } else {
        // Login bem-sucedido
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id']; // Armazenar o ID do usuário na sessão
        $_SESSION['nome'] = $user['nome'];
        $_SESSION['senha'] = $user['senha'];

        // Exibir ID e nome para debug
        echo 'Login bem-sucedido!<br>';
        echo 'ID do usuário armazenado na sessão: ' . $_SESSION['user_id'] . '<br>';
        echo 'Nome do usuário armazenado na sessão: ' . $_SESSION['nome'] . '<br>';

        // Redirecionar para outra página
        header('Location: http://localhost/hortifruti/formulario_hortifruti.php');
    }

    // Fechar a conexão com o banco
    CloseCon($conexao);
} else {
    // Campos não preenchidos
    echo 'Por favor, preencha todos os campos!<br>';
    header('Location: index.php');
}
?>
