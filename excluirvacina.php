<?php
require_once "includes/functions.php";

if (!isset($_GET['action']) ||
    $_GET['action'] != 'excluir' ||
    !filter_var($_GET['id_vacina'],
        FILTER_VALIDATE_INT) ||
    $_GET['id_vacina'] < 1

) {
    header("location:" . base_url('clientes.php'));
}
$data['id_vacina'] = trim(addslashes(filter_var($_GET['id_vacina'], FILTER_SANITIZE_STRING)));
$data['id_pet'] = trim(addslashes(filter_var($_GET['id_pet'], FILTER_SANITIZE_STRING)));

buscarVacina();

function buscarVacina()
{
    global $data;
     $data['id_vacina'] = (int) $_GET['id_vacina'];
    $conn = conectar();
    $sql = "SELECT * FROM tb_vacinas where id_vacina = '{$data['id_vacina']}'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) != 1) {
        header("location:" . base_url('vacinaspet.php?id_pet=').$data['id_pet']);
    }
    excluir();
    
}
function excluir(){
    global $data;
    $conn = conectar();
    $sql = "UPDATE tb_vacinas set
        ativo = 0
        WHERE id_vacina =  '{$data['id_vacina']}' ";
    if (mysqli_query($conn, $sql) === true) {
        header("Location:".base_url('vacinaspet.php?id_pet=').$data['id_pet']);
    }
}

