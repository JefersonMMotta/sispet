<?php
require_once "includes/functions.php";
require_once "includes/header.php";

function listarPedidos()
{
    global $data;
    $data['pedidos'] = [];
    $sql = "SELECT * FROM tb_orders WHERE ativo = 1 ORDER BY id_order DESC";
    $conn = conectar();
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
         array_push($data['pedidos'], $row);
        }
    } 
}
listarPedidos();
?>

<h1>Pedidos</h1>
<a class="btn btn-secondary float-right mb-1" href="<?=base_url('cadastrarPedido.php')?>">Novo</a>
<table class="table table-sm">
    <thead>
        <th></th>
        <th>Data</th>
        <th>Forma de pagamento</th>
        <th>Total Bruto</th>
        <th>Total Liquido</th>
        <th>Desconto</th>
        <th>Qtd itens</th>
    </thead>
    <tbody>
        <?php for ($i=0; $i < count($data['pedidos']) ; $i++):?>
        <tr>
            <td><a href="<?= base_url("visualizarPedido.php?action=visualizar&id_pedido=").$data['pedidos'][$i]['id_order'] ?>">
                Ver
            </a></td>
            <td><?= converterData($data['pedidos'][$i]['data'])?></td>
            <td><?= $data['pedidos'][$i]['forma_pagamento']?></td>
            <td><?= formatarMoeda($data['pedidos'][$i]['total_bruto'])?></td>
            <td><?= formatarMoeda($data['pedidos'][$i]['total_liquido'])?></td>
            <td><?= formatarMoeda($data['pedidos'][$i]['valor_desconto'])?></td>
            <td><?= $data['pedidos'][$i]['qtd_itens']?></td>
        </tr>
        <?php endfor;?>
    </tbody>

</table>

<?php require_once "includes/footer.php"?>