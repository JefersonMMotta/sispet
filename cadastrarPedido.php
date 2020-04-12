<?php
require_once "includes/functions.php";
require_once "includes/header.php";
$data['show-modal'] = false;
$data['show-modal-cliente'] = false;

function buscarClientePorNome()
{
    global $data;
    $data['clientes'] = [];
    $data['show-modal'] = false;
    $conn = conectar();
    $sql = "SELECT * FROM clientes WHERE nome like '%{$data['cliente']}%' AND ativo = 1";
    echo $sql;
    $result = mysqli_query($conn, $sql);
    $data['num_produtos'] = mysqli_num_rows($result);
    if ($data['num_produtos'] > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($data['clientes'], $row);
        }
        $data['show-modal-cliente'] = true;
    }
    listarCarrinho();
}

function buscarProdutoPorNome()
{
    global $data;
    $data['produtos'] = [];
    $data['show-modal'] = false;
    $conn = conectar();
    $sql = "SELECT * FROM tb_produtos WHERE nome like '%{$data['produto']}%' AND ativo = 1";
    echo $sql;
    $result = mysqli_query($conn, $sql);
    $data['num_produtos'] = mysqli_num_rows($result);
    if ($data['num_produtos'] > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($data['produtos'], $row);
        }
        $data['show-modal'] = true;
    }
    listarCarrinho();
}

function listarCarrinho()
{
    global $data;
    $data['produtos_carrinho'] = [];
    $total = 0;
    $conn = conectar();
    if (isset($_SESSION['cart']['item'])) {
        foreach ($_SESSION['cart']['item'] as $key => $qtd) {
            $sql = "SELECT *,
            (SELECT quantidade FROM tb_estoque WHERE cod_produto = id_produto) as quantidade
             FROM tb_produtos WHERE id_produto = '{$key}' AND ativo = 1";
            $result = mysqli_query($conn, $sql);
            if(mysqli_num_rows($result) != 1){
                unset($_SESSION['cart']['item'][$key]);
            }else
            {
                while ($row = mysqli_fetch_assoc($result)) {
                    if($row['quantidade'] < $qtd) {
                        $qtd = $row['quantidade'];
                        $data['fail'] = "Valor maior que a quantidade em estoque. Estoque atual {$row['quantidade']}";
                    }                  
                    $dados = array(
                        "id_produto" => $row['id_produto'],
                        "nome" => $row['nome'],
                        "preco" => $row['preco_venda'],
                        "qtd" => $qtd,
                        "subtotal" => $row['preco_venda'] * $qtd,
                    );
                    array_push($data['produtos_carrinho'], $dados);
                    $total += $dados['subtotal'];
                }
            }   
        }
        $_SESSION['cart']['total_bruto'] = $total;
        if (!isset($_SESSION['cart']['desconto'])) {
            $_SESSION['cart']['desconto'] = 0;
        }

        $_SESSION['cart']['total_liquido'] = $_SESSION['cart']['total_bruto'] - $_SESSION['cart']['desconto'];

    }

}

if (isset($_GET['action']) && $_GET['action'] == "excluir") {
    $id_produto = $_GET['id_produto'];
    if (isset($_SESSION['cart']['item'][$id_produto])) {
        unset($_SESSION['cart']['item'][$id_produto]);
    }
    listarCarrinho();
}

if (isset($_GET['action']) && $_GET['action'] == "atualizar") {
    $id_produto = $_GET['id_produto'];
    $quantidade = $_GET['quantidade'];
    if(filter_var($quantidade, FILTER_VALIDATE_INT) && $quantidade > 0 && isset($_SESSION['cart']['item'][$id_produto] )){
        $_SESSION['cart']['item'][$id_produto] = $quantidade;
    }else{
        unset($_SESSION['cart']['item'][$id_produto]);
    }
 
    listarCarrinho();
}

if (isset($_GET['action']) && $_GET['action'] == "add") {
    $id_produto = $_GET['id_produto'];
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
        $_SESSION['cart']['item'] = [];
        $_SESSION['cart']['total_bruto'] = 0;
        $_SESSION['cart']['total_liquido'] = 0;
        $_SESSION['cart']['num_itens'] = 0;
        $_SESSION['cart']['desconto'] = 0;
        $_SESSION['cart']['forma_pagamento'] = "";
    } 

    (!isset($_SESSION['cart']['item'][$id_produto])) ? $_SESSION['cart']['item'][$id_produto] = 1 : $_SESSION['cart']['item'][$id_produto] += 1;
    $data['show-modal'] = false;
    listarCarrinho();
}

if (isset($_GET['action']) && $_GET['action'] == "desconto") {
    $valor_desconto = formatarDecimal($_GET['valor_desconto']);
    if (isset($_SESSION['cart'])) {
        $_SESSION['cart']['total_liquido'] = $_SESSION['cart']['total_bruto'] - $valor_desconto;
        $_SESSION['cart']['desconto'] = $valor_desconto;
    }
    listarCarrinho();

}

if (isset($_GET['action']) && $_GET['action'] == "limparCarrinho") {
    unset($_SESSION['cart']);
}

if (isset($_GET['cliente'])) {
    global $data;
    $cliente = $_GET['cliente'];
    $data['cliente'] = $cliente;
    buscarClientePorNome();
}

if (isset($_GET['action']) && $_GET['action'] == 'addCliente') {
    $id_cliente = $_GET['id_cliente'];
    $nome_cliente = $_GET['cliente_nome'];
    $_SESSION['cart']['id_cliente'] = $id_cliente;
    $_SESSION['cart']['cliente'] = $nome_cliente;
    listarCarrinho();
}
if (isset($_GET['forma_pagamento'])) {
    $forma_pagamento = $_GET['forma_pagamento'];
    $_SESSION['cart']['forma_pagamento'] = $forma_pagamento;
}

if (isset($_GET['produto'])) {
    $data['produto'] = $_GET['produto'];
    buscarProdutoPorNome();
}
listarCarrinho();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (!isset($_SESSION['cart']) ||
        count($_SESSION['cart']['item']) < 1 ||
        empty($_SESSION['cart']['id_cliente']) ||
        empty($_SESSION['cart']['forma_pagamento'])
    ) {
        echo "<script>alert('Carrinho vazio! Selecione a Forma de pagamento!  Selecione um cliente!');history.go(-1) </script>";
        exit();
    }
    $id_cliente = $_SESSION['cart']['id_cliente'];
    $total_bruto = $_SESSION['cart']['total_bruto'];
    $total_liquido = $_SESSION['cart']['total_liquido'];
    $forma_pagamento = $_SESSION['cart']['forma_pagamento'];
    $valor_desconto = (isset($_SESSION['cart']['desconto'])) ? $_SESSION['cart']['desconto'] : 0;
    $qtd_itens = count($_SESSION['cart']['item']);
    $now = date('Y-m-d');
    $conn = conectar();
    $cod_order = 0;
    $sql = "INSERT INTO tb_orders(data, forma_pagamento, cod_cliente, total_bruto, valor_desconto, total_liquido, qtd_itens, ativo)
     VALUES ('{$now}', '{$forma_pagamento}', '{$id_cliente}', '{$total_bruto}', '{$valor_desconto}', '{$total_liquido}', '{$qtd_itens}', 1)";
    if (mysqli_query($conn, $sql)) {
        $cod_order = mysqli_insert_id($conn);    
    } else {
        $data['fail'] = "Ocorreu um erro ao cadastrar o produto";
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
    $sql_itens = "";
    foreach($_SESSION['cart']['item'] as $key=>$value){       
        $result = mysqli_query($conn, "SELECT * FROM tb_produtos WHERE id_produto = '{$key}'");
        if(mysqli_num_rows($result) == 1){
           while($row = mysqli_fetch_assoc($result)){
               $subtotal = $row['preco_venda'] * $value;
               $sql_itens .= "INSERT INTO tb_itens_order(cod_order, cod_produto, quantidade, valor_unitario, subtotal, ativo)
                VALUES ('{$cod_order}', '{$key}', '{$value}', '{$row['preco_venda']}', '{$subtotal}', 1);";
                $sql_itens .= "UPDATE tb_estoque set quantidade = quantidade - '{$value}' WHERE cod_produto ='{$key}';";   
        }
        }
    }
    if (mysqli_multi_query($conn, $sql_itens)) {
       $data['success'] = "Pedido cadastrado com sucesso";       
       unset($_SESSION['cart']);
       header("Location:visualizarPedido.php?action=visualizar&id_pedido=".$cod_order);
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }


}

?>
<a class="btn btn-danger float-right mb-1" href="<?=base_url('cadastrarPedido.php?action=limparCarrinho')?>">Limpar</a>
<div class="row">
<form class="form-inline" action="<?=base_url('cadastrarPedido.php')?>">
  <label for="produto">Produtos:</label>
  <input autocomplete="off" type="text" class="form-control" placeholder="produtos" id="produto" name="produto">
   <button type="submit" class="btn btn-primary" id="myBtn"><i class="fa fa-search" aria-hidden="true"></i></button>
</form>
<form class="form-inline" action="<?=base_url('cadastrarPedido.php')?>">
  <label for="cliente">Cliente:</label>
  <input autocomplete="off" type="text" class="form-control" placeholder="cliente" id="cliente" name="cliente" value="<?=(isset($_SESSION['cart']['cliente'])) ? $_SESSION['cart']['cliente'] : '';?>">
   <button type="submit" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i></button>
</form>
<form class="form-inline" action="<?=base_url('cadastrarPedido.php')?>">
  <label for="forma_pagamento">Forma de Pagamento:</label>
  <input list="pagamento_lista" type="text" class="form-control" placeholder="Forma de pagamento" id="forma_pagamento" name="forma_pagamento" value="<?=(isset($_SESSION['cart']['forma_pagamento'])) ? $_SESSION['cart']['forma_pagamento'] : '';?>">
   <datalist id="pagamento_lista">
        <option value="Dinheiro">
        <option value="Crédito">
        <option value="Débito">
        <option value="Cheque">
        <option value="Parcelado 1x">
        <option value="Parcelado 2x">
        <option value="Parcelado 3x">
        <option value="Parcelado 4x">
    </datalist>
  <button type="submit" class="btn btn-primary"><i class="fa fa-check" aria-hidden="true"></i></button>
</form>

</div>
<hr>
<?php if (isset($data['success'])): ?>
        <div class="alert alert-success">
             <strong>Sucesso!</strong> <?=$data['success']?>.
        </div>
    <?php endif;?>
     <?php if (isset($data['fail'])): ?>
        <div class="alert alert-danger">
            <strong>Falha!</strong> <?=$data['fail']?>.
        </div>
<?php endif;?>

<?php require_once "modalCliente.php"?>

<!-- The Modal -->
<div class="modal fade" id="myModal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Produtos</h4>
          <button type="button" class="close" data-dismiss="modal">×</button>
        </div>

        <!-- Modal body -->
        <div class="modal-body">
            <table class="table table-sm">
            <thead>
                <th>Adicionar</th>
                <th>Descrição</th>
                <th>Preço</th>
            </thead>
            <tbody>
            <?php if (isset($data['produtos'])): ?>
            <?php for ($i = 0; $i < count($data['produtos']); $i++): ?>
                <tr>
                <td>
                    <a href="<?=base_url("cadastrarPedido.php?action=add&id_produto=" . $data['produtos'][$i]['id_produto'])?>">
                    <i class="fa fa-cart-plus" aria-hidden="true"></i>
                    </a>
                </td>
                    <td><?=$data['produtos'][$i]['nome']?></td>
                    <td>R$ <?=$data['produtos'][$i]['preco_venda']?></td>
                </tr>
                            <?php endfor;?>
            <?php else:?>
                <tr>
                    <td colspan="4">Nenhum item</td>
                </tr>
            <?php endif;?>
            </tbody>
            </table>
            </div>

        <!-- Modal footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>

      </div>
    </div>
  </div>

<table class="table table-sm">
    <thead>
        <th></th>
        <th>Id</th>
        <th>Descrição</th>
        <th>Preço Unitario</th>
        <th>Qtd</th>
        <th>Subtotal </th>
    </thead>
    <tbody>
    <?php if (isset($_SESSION['cart']['item']) && isset($data['produtos_carrinho'])): ?>
        <?php for ($i = 0; $i < count($data['produtos_carrinho']); $i++): ?>
            <tr>
            <td>
               <a href="<?=base_url("CadastrarPedido.php?action=excluir&id_produto=") . $data['produtos_carrinho'][$i]['id_produto']?>">
                excluir
            </a>
            </td>
                <td><?=$data['produtos_carrinho'][$i]['id_produto']?></td>
                <td><?=$data['produtos_carrinho'][$i]['nome']?></td>
                <td><?=formatarMoeda($data['produtos_carrinho'][$i]['preco'])?></td>
                <td>
                    <form action="<?=base_url("cadastrarPedido.php?")?>">
                         <input type="hidden" name="action" value="atualizar">
                        <input type="hidden" name="id_produto" value="<?=$data['produtos_carrinho'][$i]['id_produto']?>">
                        <input name="quantidade" type="text" value="<?=$data['produtos_carrinho'][$i]['qtd']?>" maxlength="9">
                        <button type="submit">Mudar</button>
                    </form>
                </td>
                <td><?=formatarMoeda($data['produtos_carrinho'][$i]['subtotal'])?></td>
            </tr>
        <?php endfor;?>        
        <tr>
            <td colspan="4"></td>
            <td>Total bruto:</td>
            <td><?=formatarMoeda($_SESSION['cart']['total_bruto'])?></td>
        </tr>
        <tr>
            <td colspan="4"></td>
            <td>desconto:</td>
            <td>
            <form action="<?=base_url("cadastrarPedido.php?")?>">
                        <input type="hidden" name="action" value="desconto">
                        <input class="money" name="valor_desconto" type="text" value="<?=(isset($_SESSION['cart']['desconto'])) ? $_SESSION['cart']['desconto'] : 0?>">
                        <button type="submit">Desconto</button>
                    </form>
            </td>
        </tr>
        <tr>
            <td colspan="4"></td>
            <td>Total liquido:</td>
            <td><?=formatarMoeda($_SESSION['cart']['total_liquido'])?></td>
        </tr>
        <?php else:?>
                <tr>
                    <td colspan="4">Nenhum item</td>
                </tr>
                <?php endif;?>
    </tbody>  
</table>
<?php //echo '<pre>', print_r($_SESSION['cart']), '</pre>';?>

        <form action="<?=base_url("cadastrarPedido.php")?>" method="post">
            <button class="btn btn-secondary" type="submit">Cadastrar</button>
        </form>

<script>
    $(document).ready(function(){
    let modalClientes = +"<?=$data['show-modal-cliente']?>";
    let modal = +"<?=$data['show-modal']?>";
        if(modal){
            $("#myModal").modal()
        }
        if(modalClientes){
            $("#myModalClientes").modal()
        }
    });
</script>
<?php require_once "includes/footer.php"?>