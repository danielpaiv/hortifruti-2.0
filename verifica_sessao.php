<?php
session_start();

// Verificar se a sessão contém os dados esperados
if (isset($_SESSION['user_id']) && isset($_SESSION['nome'])) {
    echo 'ID do usuário na sessão: ' . $_SESSION['user_id'] . '<br>';
    echo 'Nome do usuário na sessão: ' . $_SESSION['nome'] . '<br>';
} else {
    echo 'Nenhum dado de usuário encontrado na sessão.';
}
?>
