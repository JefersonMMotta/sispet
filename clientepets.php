<?php
require_once "includes/functions.php";
require_once "includes/header.php";

if (!isset($_GET['cliente_id']) ||
    !filter_var($_GET['cliente_id'],
        FILTER_VALIDATE_INT) ||
    $_GET['cliente_id'] < 1

) {
    header("location:" . base_url('clientes.php'));
}
$data['id_cliente'] = (int) $_GET['cliente_id'];

function isCLiente()
{
    global $data;
    $data['cliente'] = [];

    $conn = conectar();
    $sql = "SELECT * FROM clientes WHERE id_cliente = '{$data['id_cliente']}' AND ativo = 1";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) != 1) {
        header("location:" . base_url('clientes.php'));
    }
    while ($row = mysqli_fetch_assoc($result)) {
        array_push($data['cliente'], $row);
    }
    mysqli_close($conn);
}

function listarPetsCliente()
{
    global $data;
    $data['pets'] = [];
    $conn = conectar();
    $sql = "SELECT * FROM tb_pets WHERE cod_cliente ={$data['id_cliente']} AND ativo = 1";
    $result = mysqli_query($conn, $sql);
    $total_pets = mysqli_num_rows($result);
    $data['total_pets'] = $total_pets;
    while ($row = mysqli_fetch_assoc($result)) {
        array_push($data['pets'], $row);
    }

}

isCLiente();
listarPetsCliente();

?>

<h1>Pets</h1>
<p>Cliente: <?=$data['cliente'][0]['nome'];?></p>
<a class="btn btn-secondary btn-sm float-right mb-1" href="<?= base_url('cadastrarpet.php?cliente_id='.$data['id_cliente'])?>">Novo Pet</a>
<table class="table table-sm table-bordered">
    <thead>
        <th></th>
        <th>Nome</th>
        <th>Raça</th>
        <th>Espécie</th>
        <th>Cor</th>
        <th>Data Nascimento</th>
        <th></th>
    </thead>
    <tbody>
        <?php for ($i = 0; $i < count($data['pets']); $i++): ?>
            <tr>
                <td></td>
                <td><?=$data['pets'][$i]['nome'];?></td>
                <td><?=$data['pets'][$i]['raca'];?></td>
                <td><?=$data['pets'][$i]['especie'];?></td>
                <td><?=$data['pets'][$i]['cor'];?></td>
                <td><?= converterData($data['pets'][$i]['data_nascimento']);?></td>
                <td></td>
            </tr>
        <?php endfor;
        ?>
    </tbody>
</table>

<?php require_once "includes/footer.php";?>
