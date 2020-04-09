<?php
require_once "includes/functions.php";
require_once "includes/header.php";

if (!isset($_GET['id_pet']) ||
    !filter_var($_GET['id_pet'],
        FILTER_VALIDATE_INT) ||
    $_GET['id_pet'] < 1

) {
    echo "<script>alert('Parametros inválidos.');history.go(-1) </script>";
    exit();
}
$data['id_pet'] = (int) $_GET['id_pet'];

function buscarPet()
{
    global $data;
    $data['pet'] = [];
    $sql = "SELECT *,(SELECT nome FROM clientes WHERE cod_cliente = id_cliente) as cliente FROM tb_pets WHERE id_pet = '{$data['id_pet']}'";
    $conn = conectar();
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) != 1) {
        echo "<script>alert('Parametros inválidos.');history.go(-1) </script>";
        exit();
    }
    while ($row = mysqli_fetch_assoc($result)) {
        array_push($data['pet'], $row);
    }
}

function listarVacinas()
{
    global $data;
    $data['vacinas'] = [];
    $sql = "SELECT * FROM tb_vacinas WHERE cod_pet = '{$data['id_pet']}' AND ativo=1";
    $conn = conectar();
    $result = mysqli_query($conn, $sql);   
    while ($row = mysqli_fetch_assoc($result)) {
        array_push($data['vacinas'], $row);
    }
}

buscarPet();
listarVacinas();
?>
<h1>Vacinas</h1>
<a class="btn btn-secondary btn-sm float-right mb-1" href="<?= base_url('cadastrarvacina.php?id_pet='.$data['pet'][0]['id_pet'] )?>"> Nova</a>
<h3>Pet: <?= $data['pet'][0]['nome'];?></h3>
<p>Cliente: <?= $data['pet'][0]['cliente'];?></p>

<table class="table table-bordered table-sm">
    <thead>
        <th></th>
        <th>Nome</th>
        <th>Dose</th>
        <th>Data</th>
        <th>Próxima</th>
        <th></th>
    </thead>
    <tbody>
        <?php for($i=0; $i < count($data['vacinas']) ; $i++) :?>
        <tr>
            <td>
                <a href="<?=base_url('editarvacina.php?action=editar&id_vacina=').$data['vacinas'][$i]['id_vacina'] ?>">
                     <i style='font-size:14px' class='far'>&#xf044;</i>
                 </a>
            </td>
            <td><?= $data['vacinas'][$i]['nome']?></td>
            <td><?= $data['vacinas'][$i]['dose']?></td>
            <td><?= converterData($data['vacinas'][$i]['data_aplicacao'])?></td>
            <td><?= converterData($data['vacinas'][$i]['data_prox_aplicacao'])?></td>
            <td>
                <a href="<?=base_url('excluirvacina.php?action=excluir&id_vacina=').$data['vacinas'][$i]['id_vacina'].'&id_pet='.$data['id_pet'] ?>">
                    <i style='font-size:14px; color:red' class='fas'>&#xf12d;</i>
                </a>
            </td>
        </tr>
        <?php endfor; ?>
    </tbody>
</table>
<?php require_once "includes/footer.php";?>