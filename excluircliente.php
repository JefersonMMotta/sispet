<?php
require_once "includes/functions.php";

if (!isset($_GET['action']) ||
    $_GET['action'] != 'excluir' ||
    !filter_var($_GET['cliente_id'],
        FILTER_VALIDATE_INT) ||
    $_GET['cliente_id'] < 1

) {
    header("location:" . base_url('clientes.php'));
}

buscarCliente();

function buscarCliente()
{
    global $data;
    $data['cliente'] = [];
    $data['cliente_id'] = (int) $_GET['cliente_id'];
    $conn = conectar();
    $sql = "SELECT * FROM clientes where id_cliente = '{$data['cliente_id']}'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) != 1) {
        header("location:" . base_url('clientes.php'));
    }
    excluir();
    
}
function excluir(){
    global $data;
    $conn = conectar();
    $sql = "UPDATE clientes set
        ativo = 0
        WHERE id_cliente =  '{$data['cliente_id']}' ";
    if (mysqli_query($conn, $sql) === true) {
        header("Location:".base_url('clientes.php'));
    }
}

