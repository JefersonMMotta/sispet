<?php
require_once "includes/functions.php";
require_once "includes/header.php";

function contarProdutos()
{
    global $data;
    $nome = (isset($_GET['nome'])) ? $_GET['nome'] : null;
    $categoria = (isset($_GET['categoria'])) ? $_GET['categoria'] : null;
    $sql = "SELECT *,
      (SELECT nome FROM tb_categorias WHERE cod_categoria = id_categoria) AS categoria,
      (SELECT nome FROM tb_fornecedores WHERE cod_fornecedor = id_fornecedor) AS fornecedor,
      (SELECT quantidade FROM tb_estoque WHERE cod_produto = id_produto) AS qtd,
      (SELECT minimo FROM tb_estoque WHERE cod_produto = id_produto) AS qtd_minima
       FROM tb_produtos";
    $sql .= " WHERE ativo = 1 ";
    if (!empty($nome)) {
        $nome = trim(addslashes(filter_var($nome, FILTER_SANITIZE_STRING)));
        $data['value']['nome'] = $nome;
        $sql .= " AND nome like '%{$nome}%'";
    }

    if (!empty($categoria)) {
        $categoria = trim(addslashes(filter_var($categoria, FILTER_SANITIZE_STRING)));
        $data['value']['categoria'] = $categoria;
        $id_categoria = buscarCategoria($categoria);
        $sql .= " AND cod_categoria ='{$id_categoria}'";
    }
    $conn = conectar();
    $result = mysqli_query($conn, $sql);
    $total_produtos = mysqli_num_rows($result);
    mysqli_close($conn);
    return $total_produtos;
}

function listarProdutos()
{
    global $data;
    $total = contarProdutos();
    $page = (isset($_GET['page'])) ? $_GET['page'] : 1;
    $data['produtos'] = [];
    $data['total_produtos'] = $total;
    $nome = (isset($_GET['nome'])) ? $_GET['nome'] : null;
    $categoria = (isset($_GET['categoria'])) ? $_GET['categoria'] : null;
    $search = (isset($_GET['search'])) ? $_GET['search'] : null;
    $sql = "SELECT *,
      (SELECT nome FROM tb_categorias WHERE cod_categoria = id_categoria) AS categoria,
      (SELECT nome FROM tb_fornecedores WHERE cod_fornecedor = id_fornecedor) AS fornecedor,
      (SELECT quantidade FROM tb_estoque WHERE cod_produto = id_produto) AS qtd,
      (SELECT minimo FROM tb_estoque WHERE cod_produto = id_produto) AS qtd_minima
       FROM tb_produtos";
    $sql .= " WHERE ativo = 1 ";

    if (!empty($nome)) {
        $nome = trim(addslashes(filter_var($nome, FILTER_SANITIZE_STRING)));
        $data['value']['nome'] = $nome;
        $sql .= " AND nome like '%{$nome}%'";
    }

    if (!empty($categoria)) {
        $categoria = trim(addslashes(filter_var($categoria, FILTER_SANITIZE_STRING)));
        $data['value']['categoria'] = $categoria;
        $id_categoria = buscarCategoria($categoria);
        $sql .= " AND cod_categoria ='{$id_categoria}'";
    }
   
    paginar($page, 10, $total);
    $sql .= "LIMIT {$data['pagination']['inicio']},{$data['pagination']['per_page']}";
    $conn = conectar();
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        array_push($data['produtos'], $row);
    }
    mysqli_close($conn);
}
listarProdutos();
?>
<h1>Produtos</h1>
<form action="<?=base_url('produtos.php')?>" method="get" autocomplete="off">
<input type="text" name="nome" placeholder="Produto" value="<?=(isset($data['value']['nome'])) ? $data['value']['nome'] : '';?>">
<input type="text" name="categoria" placeholder="Categoria" value="<?=(isset($data['value']['categoria'])) ? $data['value']['categoria'] : '';?>">
<button type="submit">Buscar</button>
</form>
<a class="btn btn-secondary float-right mb-1" href="<?=base_url('cadastrarProduto.php')?>">Novo</a>
<table class="table table-bordered table-sm">
    <thead>
        <th></th>
        <th>Nome</th>
        <th>Preço Custo</th>
        <th>Preço Venda</th>
        <th>Estoque</th>
        <th>Qtd Minima</th>
        <th>Categoria</th>
        <th>Fornecedor</th>
    </thead>
    <tbody>
        <?php for ($i = 0; $i < count($data['produtos']); $i++): ?>
            <tr>
                <td>
                    <a href="<?=base_url('editarProduto.php?action=editar&id_produto=' . $data['produtos'][$i]['id_produto'])?>">
                    <i style='font-size:14px; color:seagreen' class='far'>&#xf044;</i>
                    </a>
            </td>
                <td><?=$data['produtos'][$i]['nome'];?></td>
                <td><?=$data['produtos'][$i]['preco_custo'];?></td>
                <td><?=$data['produtos'][$i]['preco_venda'];?></td>
                <td><?=$data['produtos'][$i]['qtd'];?></td>
                <td><?=$data['produtos'][$i]['qtd_minima'];?></td>
                <td><?=$data['produtos'][$i]['categoria'];?></td>
                <td><?=$data['produtos'][$i]['fornecedor'];?></td>
            </tr>
        <?php endfor;?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="8">
            <?php 
                $anterior = "";
                $proximo = "";
                $url = "produtos.php?page=";
                $anterior .= (isset($data['search'])) ? $url.$data['anterior'].'&search='.$data['search'] :$url.$data['anterior'];
                $proximo .= (isset($data['search'])) ? $url.$data['proximo'].'&search='.$data['search'] :$url.$data['proximo']; 
            ?>


              <span>Total de: <?= $data['total_produtos']?> Regsitros</span>  
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
<?php require_once "includes/footer.php";?>