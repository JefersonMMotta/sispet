<?php
require_once "includes/functions.php";

if (!isset($_GET['action']) ||
    $_GET['action'] != 'excluir' ||
    !filter_var($_GET['id_fornecedor'],
        FILTER_VALIDATE_INT) ||
    $_GET['id_fornecedor'] < 1

) {
    header("location:" . base_url('fornecedores.php'));
}

buscarCliente();

function buscarCliente()
{
    global $data;
    $data['cliente'] = [];
    $data['id_fornecedor'] = (int) $_GET['id_fornecedor'];
    $conn = conectar();
    $sql = "SELECT * FROM tb_fornecedores where id_fornecedor = '{$data['id_fornecedor']}'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) != 1) {
        header("location:" . base_url('fornecedores.php'));
    }
    excluir();
    
}
function excluir(){
    global $data;
    $conn = conectar();
    $sql = "UPDATE tb_fornecedores set
        ativo = 0
        WHERE id_fornecedor =  '{$data['id_fornecedor']}' ";
    if (mysqli_query($conn, $sql) === true) {
        header("Location:".base_url('fornecedores.php'));
    }
}

