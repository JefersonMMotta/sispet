<?php
require_once "includes/functions.php";
require_once "includes/header.php";



function listarCategorias()
{
    global $data;
    $data['categorias'] = [];
    $sql = "SELECT * FROM tb_categorias WHERE ativo = 1";
    $conn = conectar();
    $result = mysqli_query($conn, $sql);
    $data['num_categorias'] = mysqli_num_rows($result);
    while($row = mysqli_fetch_assoc($result)){
        array_push($data['categorias'], $row);
    }
}

function listarFornecedores()
{
    global $data;
    $data['fornecedores'] = [];
    $sql = "SELECT * FROM tb_fornecedores WHERE  ativo = 1";
    $conn = conectar();
    $result = mysqli_query($conn, $sql);
    $data['num_fornecedores'] = mysqli_num_rows($result);
    while($row = mysqli_fetch_assoc($result)){
        array_push($data['fornecedores'], $row);
    }
}

function cadastrarCategoria()
{
    global $data;
    $sql = "INSERT INTO tb_categorias (nome, ativo) VALUES('{$data['nome_categoria']}', 1)";
    $conn = conectar();
    $last_id = 0;
    if (mysqli_query($conn, $sql)) {
        $last_id = mysqli_insert_id($conn);    
    } else {
        $data['fail']= "Ocorreu um erro ao cadastrar a categoria";
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }   
    return $last_id;
}

function buscarCategoria()
{
    global $data;
    $data['categorias'] = [];
    $sql = "SELECT * FROM tb_categorias WHERE nome = '{$data['nome_categoria']}' AND  ativo = 1";
    $conn = conectar();
    $result = mysqli_query($conn, $sql);
    $data['num_categorias'] = mysqli_num_rows($result);
    $id_categoria = 0;
    while($row = mysqli_fetch_assoc($result)){
       $id_categoria = $row['id_categoria'];
    }
    return $id_categoria;
}

function cadastrarFornecedor()
{
    global $data;
    $sql = "INSERT INTO tb_fornecedores (nome, ativo) VALUES('{$data['nome_fornecedor']}', 1)";
    $conn = conectar();
    $last_id = 0;
    if (mysqli_query($conn, $sql)) {
        $last_id = mysqli_insert_id($conn);    
    } else {
        $data['fail'] = "Ocorreu um erro ao cadastrar o fornecedor";
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }   
    return $last_id;
}

function buscarFornecedor()
{
    global $data;
    $data['fornecedores'] = [];
    $sql = "SELECT * FROM tb_fornecedores WHERE nome = '{$data['nome_fornecedor']}' AND ativo = 1";
    $conn = conectar();
    $result = mysqli_query($conn, $sql);
    $data['num_fornecedores'] = mysqli_num_rows($result);
    $id_fornecedor = 0;
    while($row = mysqli_fetch_assoc($result)){
        $id_fornecedor = $row['id_fornecedor'];
    }
    return $id_fornecedor;
}

listarCategorias();
listarFornecedores();



if($_SERVER['REQUEST_METHOD'] == "POST"){
    global $data;
    $nome = validar($_POST['nome'], 'Nome', true);
    $preco_custo = validar($_POST['preco_custo'], 'Preço Custo', true);
    $preco_venda = validar($_POST['preco_venda'], 'Preço Venda', true);
    $categoria = validar($_POST['categoria'], 'Categoria', true);
    $fornecedor = validar($_POST['fornecedor'], 'Fornecedor', true);
    $unidade = validar($_POST['unidade'], 'Unidade', true);
    $embalagem = validar($_POST['embalagem'], 'Embalagem', true);
    $qtd = validar($_POST['qtd'], 'Qtd.', true);
    $qtd_minima = validar($_POST['qtd_minima'], 'Qtd. Minima', true);
    $data['nome_fornecedor'] = $fornecedor;
    $data['nome_categoria'] = $categoria;   
    if (!$data['validation']['has_error']) {
        $id_categoria = buscarCategoria();
        $id_fornecedor = buscarFornecedor();
        if($id_categoria == 0){
            $id_categoria = cadastrarCategoria();
        }
        if($id_fornecedor == 0){
            $id_fornecedor = cadastrarFornecedor();
        }
        $sql = "INSERT INTO tb_produtos (nome, preco_custo, preco_venda, unidade, embalagem,  cod_categoria, cod_fornecedor, ativo)";
        $sql .= " VALUES ('{$nome}', '{$preco_custo}', '{$preco_venda}', '{$unidade}', '{$embalagem}', $id_categoria, $id_fornecedor, 1)";
        $conn = conectar();
        $cod_produto = 0;
        if (mysqli_query($conn, $sql)) {
            $cod_produto = mysqli_insert_id($conn);    
        } else {
            $data['fail'] = "Ocorreu um erro ao cadastrar o produto";
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
        $sql_estoque = "INSERT INTO tb_estoque (cod_produto, quantidade, minimo, ativo) VALUES ('{$cod_produto}','{$qtd}', '{$qtd_minima}', 1)";   
        if (!mysqli_query($conn, $sql_estoque)) {
            $data['fail'] = "Ocorreu um erro ao cadastrar o estoque";
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
        $data['success'] = "Produto cadastrado com sucesso";
        unset($data['value']);       
    }

}
?>
<h1>Cadastrar Produto</h1>
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
<form action="<?= base_url('cadastrarProduto.php')?>" method="post">
<div class="row">
    <div class="form-group col-md-6">
        <label for="nome">Nome</label>
        <input type="text" class="form-control" name="nome" id="nome" value="<?= (isset($data['value']['Nome'])) ? $data['value']['Nome']: '' ;?>">
        <span style="color:red"><?=(isset($data['validation']['Nome'])) ? $data['validation']['Nome'] : ''?></span>
    </div>

    <div class="form-group col-md-3" >
        <label for="preco_custo">Preço Custo</label>
        <input type="text" class="form-control" name="preco_custo" id="preco_custo" value="<?= (isset($data['value']['Preço Custo'])) ? $data['value']['Preço Custo']: '' ;?>" >
        <span style="color:red"><?=(isset($data['validation']['Preço Custo'])) ? $data['validation']['Preço Custo'] : ''?></span>
    </div>

    <div class="form-group col-md-3">
        <label for="preco_venda">Preço Venda</label>
        <input type="text" class="form-control" name="preco_venda" id="preco_venda" value="<?= (isset($data['value']['Preço Venda'])) ? $data['value']['Preço Venda']: '' ;?>">
        <span style="color:red"><?=(isset($data['validation']['Preço Venda'])) ? $data['validation']['Preço Venda'] : ''?></span>
    </div>
</div>
<div class="row">    
    <div class="form-group col-md-6">
        <label for="categoria">Categoria</label>
        <input list="lista-categorias" type="text" class="form-control" name="categoria" id="categoria" value="<?= (isset($data['value']['Categoria'])) ? $data['value']['Categoria']: '' ;?>">
        <datalist id="lista-categorias">
        <?php for ($i=0; $i < count($data['categorias']) ; $i++):?>
            <option value="<?= $data['categorias'][$i]['nome']?>">
        <?php endfor; ?>
        </datalist>
        <span style="color:red"><?=(isset($data['validation']['Categoria'])) ? $data['validation']['Categoria'] : ''?></span>
    </div>

    <div class="form-group col-md-6">
        <label for="fornecedor">Fornecedor</label>
        <input autocomplete="off" list="lista-fornecedores" type="text" class="form-control" name="fornecedor" id="fornecedor" value="<?= (isset($data['value']['Fornecedor'])) ? $data['value']['Fornecedor']: '' ;?>" >
        <datalist id="lista-fornecedores">
        <?php for ($i=0; $i < count($data['fornecedores']) ; $i++):?>
            <option value="<?= $data['fornecedores'][$i]['nome']?>">
        <?php endfor; ?>
        </datalist>
        <span style="color:red"><?=(isset($data['validation']['Fornecedor'])) ? $data['validation']['Fornecedor'] : ''?></span>
    </div>
</div>    
<div class="row">
     <div class="form-group col-md-4">
        <label for="unidade">Unidade</label>
        <input autocomplete="off" list="lista-unidades" type="text" class="form-control" name="unidade" id="unidade" value="<?= (isset($data['value']['Unidade'])) ? $data['value']['Unidade']: '' ;?>">
        <datalist id="lista-unidades">        
         <option value="Unidade">
         <option value="Peça">
         <option value="Kilo">
         <option value="GRAMA">
         <option value="Folha">  
         <option value="Litro">
         <option value="Mililitro">        
         <option value="Unidade">
         <option value="Galão">
         <option value="Lata">
         <option value="Tubo"> 
         <option value="Mão de Obra">             
        </datalist>
        <span style="color:red"><?=(isset($data['validation']['Unidade'])) ? $data['validation']['Unidade'] : ''?></span>
    </div>
    
     <div class="form-group col-md-4">
        <label for="embalagem">Qtd. Embalagem</label>
        <input  type="text" class="form-control" name="embalagem" id="embalagem" value="<?= (isset($data['value']['Embalagem'])) ? $data['value']['Embalagem']: '' ;?>">
        <span style="color:red"><?=(isset($data['validation']['Embalagem'])) ? $data['validation']['Embalagem'] : ''?></span>
    </div>
    
    <div class="form-group col-md-2">  
         <label for="qtd">Qtd.</label>
        <input type="text" class="form-control" name="qtd" id="qtd" value="<?= (isset($data['value']['Qtd.'])) ? $data['value']['Qtd.']: '' ;?>">
         <span style="color:red"><?=(isset($data['validation']['Qtd.'])) ? $data['validation']['Qtd.'] : ''?></span>
    </div>
    <div class="form-group col-md-2">
        <label for="qtd_minima">Qtd. Minima</label>
        <input type="text" class="form-control" name="qtd_minima" id="qtd_minima" value="<?= (isset($data['value']['Qtd. Minima'])) ? $data['value']['Qtd. Minima']: '' ;?>">
        <span style="color:red"><?=(isset($data['validation']['Qtd. Minima'])) ? $data['validation']['Qtd. Minima'] : ''?></span>
    </div>
</div>    
<button class="btn btn-secondary" type="submit">Cadastrar</button>
</form>



<?php require_once "includes/footer.php";?>