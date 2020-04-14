<?php
require_once "includes/functions.php";
require_once "includes/header.php";

function contarPedidos()
{   
    $sql = "SELECT * FROM tb_orders WHERE ativo = 1 ORDER BY id_order DESC";
    $conn = conectar();
    $result = mysqli_query($conn, $sql);
    $total = mysqli_num_rows($result);
    mysqli_close($conn);
    return $total;
}

function listarPedidos()
{
    global $data;
    $data['pedidos'] = [];
    $page = (isset($_GET['page'])) ? $_GET['page'] : 1 ;
    $total = contarPedidos();
    $data['total_pedidos'] = $total;
    paginar($page, 10, $total);
    $sql = "SELECT * FROM tb_orders WHERE ativo = 1 ORDER BY id_order DESC";
    $sql .= " LIMIT {$data['pagination']['inicio']},{$data['pagination']['per_page']}";
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
            <i style="color:seagreen;" class="fa fa-search-plus" aria-hidden="true"></i>
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
    <tfoot>
        <tr>
            <td colspan="8">
            <?php 
                $anterior = "";
                $proximo = "";
                $url = "pedidos.php?page=";
                $anterior .= (isset($data['search'])) ? $url.$data['anterior'].'&search='.$data['search'] :$url.$data['anterior'];
                $proximo .= (isset($data['search'])) ? $url.$data['proximo'].'&search='.$data['search'] :$url.$data['proximo']; 
            ?>


              <span>Total de: <?= $data['total_pedidos']?> Regsitros</span>  
            <ul  class="pagination justify-content-end" style="margin:20px 0">
            <?php if ($data['atual'] != $data['anterior']): ?>
                <li class="page-item"><a  class="page-link" href="<?= $anterior?>"><?= $data['anterior']?></a></li>
             <?php endif;?>
                <li class="page-item active">  <a  class="page-link" href="#"><?=$data['atual']?></a></li>
                <?php if ($data['atual'] != $data['proximo']): ?>
                <li class="page-item">  <a class="page-link" href="<?= $proximo?>"><?=$data['proximo']?></a></li>
                <?php endif;?>
            </ul>
            </td>
        </tr>
    </tfoot>

</table>

<?php require_once "includes/footer.php"?>