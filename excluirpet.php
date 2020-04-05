<?php
require_once "includes/functions.php";

if (!isset($_GET['action']) ||
    $_GET['action'] != 'excluir' ||
    !filter_var($_GET['id_pet'],
        FILTER_VALIDATE_INT) ||
    $_GET['id_pet'] < 1

) {
    header("location:" . base_url('clientes.php'));
}

buscarPet();

function buscarPet()
{
    global $data;
     $data['id_pet'] = (int) $_GET['id_pet'];
    $conn = conectar();
    $sql = "SELECT * FROM tb_pets where id_pet = '{$data['id_pet']}'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) != 1) {
        header("location:" . base_url('pets.php'));
    }
    excluir();
    
}
function excluir(){
    global $data;
    $conn = conectar();
    $sql = "UPDATE tb_pets set
        ativo = 0
        WHERE id_pet =  '{$data['id_pet']}' ";
    if (mysqli_query($conn, $sql) === true) {
        header("Location:".base_url('pets.php'));
    }
}

