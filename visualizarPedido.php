<?php
require_once "includes/functions.php";
require_once "includes/header.php";

if (!isset($_GET['action']) ||
    $_GET['action'] != 'visualizar' ||
    !filter_var($_GET['id_pedido'],
        FILTER_VALIDATE_INT) ||
    $_GET['id_pedido'] < 1

) {
      echo "<script>alert('Parametros inválidos.');history.go(-1) </script>";
    exit();
}
$data['id_pedido'] = $_GET['id_pedido'];

function buscarPedido()
{
    global $data;
    $data['pedido'] = [];
    $data['itens'] = [];
    $conn = conectar();
    $sql = "SELECT * FROM tb_orders WHERE id_order = '{$data['id_pedido']}'";
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) != 1){
        echo "<script>alert('Pedido não cadastrado.');history.go(-1) </script>";
        exit();
    }
    while($row = mysqli_fetch_assoc($result)){
        array_push($data['pedido'], $row);
    }
    $sql_itens = "SELECT *,
    (SELECT nome FROM tb_produtos WHERE cod_produto = id_produto) as produto,
    (SELECT preco_venda FROM tb_produtos WHERE cod_produto = id_produto) as preco
     from tb_itens_order WHERE cod_order = {$data['id_pedido']}";
    $result = mysqli_query($conn, $sql_itens);
    while($row = mysqli_fetch_assoc($result)){
        array_push($data['itens'], $row);
    }
}

buscarPedido();
?>
<style>

table {
  border-collapse: collapse;
  width: 100%;
}

th {
  text-align: left;
}
</style>
<table class="">
    <tr>
        <td colspan="5">Nome Fantasia</td>        
    </tr>
    <tr>
        <td colspan="5">Rua, numero</td>        
    </tr>
    <tr>
        <td colspan="5">Bairro, cidade -UF</td>        
    </tr>
    <tr>
        <td colspan="5">Inscrição estadual</td>            
    </tr>
    <tr>
        <td colspan="5">CNPJ</td>            
    </tr>
    <tr>
        <td colspan="5"><hr></td>
    </tr>
    <tr>
        <td colspan="5">Cupon não fiscal</td>
    </tr>
    <tr>
        <td colspan="5"><hr></td>
    </tr>

    <tr>
        <td>Pedido:</td>
        <td>000<?= $data['pedido'][0]['id_order']?></td>
        <td><?= $data['pedido'][0]['data_cadastro']?></td>
    </tr>
    <tr>
        <td>Codigo</td>
        <td>Descrição</td>
        <td>Qtd</td>
        <td>Unitario</td>
        <td>Valor</td>
    </tr>
    <?php for ($i=0; $i < count($data['itens']) ; $i++) :?>
        <tr>
        <td><?= $data['itens'][$i]['cod_produto']?></td>
        <td><?= $data['itens'][$i]['produto']?></td>
        <td><?= $data['itens'][$i]['quantidade']?></td>
        <td><?= formatarMoeda($data['itens'][$i]['valor_unitario'])?></td>
        <td><?= formatarMoeda($data['itens'][$i]['subtotal'])?></td>
    </tr>    
    <?php endfor;?>
    <tr>
        <td colspan="5"><hr></td>
    </tr>
    <tr>
        <td >Total:</td>
        <td><?= formatarMoeda($data['pedido'][0]['total_bruto'])?></td>
    </tr>
    <tr>
        <td>Desconto:</td>
        <td><?= formatarMoeda($data['pedido'][0]['valor_desconto'])?></td>
    </tr>
    <tr>
        <td>Total:</td>
        <td><?=formatarMoeda($data['pedido'][0]['total_liquido'])?></td>
    </tr>
</table>