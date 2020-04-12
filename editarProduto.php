<?php
require_once "includes/functions.php";
require_once "includes/header.php";

if (!isset($_GET['action']) ||
    $_GET['action'] != 'editar' ||
    !filter_var($_GET['id_produto'],
        FILTER_VALIDATE_INT) ||
    $_GET['id_produto'] < 1

) {   
    echo "<script>alert('Parametros inválidos.');history.go(-1) </script>";
    exit();
}

$data['id_produto'] = trim(addslashes(filter_var($_GET['id_produto'], FILTER_SANITIZE_STRING)));

function buscarProduto()
{
    global $data;
    $data['produto'] = [];
    $sql = "SELECT *,
    (SELECT nome FROM tb_categorias WHERE cod_categoria = id_categoria) AS categoria,
    (SELECT nome FROM tb_fornecedores WHERE cod_fornecedor = id_fornecedor) AS fornecedor,
    (SELECT quantidade FROM tb_estoque WHERE cod_produto = id_produto) AS qtd,
    (SELECT minimo FROM tb_estoque WHERE cod_produto = id_produto) AS qtd_minima
    FROM tb_produtos WHERE id_produto = '{$data['id_produto']}' AND ativo = 1 ";

    $conn = conectar();
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) != 1){
       // echo "<script>alert('Parametros inválidos.');history.go(-1) </script>";
        exit();
    }
    while($row = mysqli_fetch_assoc($result)){
        array_push($data['produto'], $row);
    }
}
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
buscarProduto();
listarCategorias();
listarFornecedores();



if($_SERVER['REQUEST_METHOD'] == "POST"){
    global $data;
    $id_produto = (int)trim(addslashes(filter_var($_POST['id_produto'], FILTER_SANITIZE_STRING)));
    $nome = validar($_POST['nome'], 'Nome', true);
    $preco_custo = validar($_POST['preco_custo'], 'Preço Custo', true);
    $preco_venda = validar($_POST['preco_venda'], 'Preço Venda', true);
    $unidade = validar($_POST['unidade'], 'Unidade', true);
    $embalagem = validar($_POST['embalagem'], 'Embalagem', true);
    $categoria = validar($_POST['categoria'], 'Categoria', true);
    $fornecedor = validar($_POST['fornecedor'], 'Fornecedor', true);
    $qtd = validar($_POST['qtd'], 'Qtd.', true);
    $qtd_minima = validar($_POST['qtd_minima'], 'Qtd. Minima', true);
    $data['nome_fornecedor'] = $fornecedor;
    $data['nome_categoria'] = $categoria;   
    if (!$data['validation']['has_error'] && $id_produto == $_GET['id_produto']) {
        $id_categoria = buscarCategoria();
        $id_fornecedor = buscarFornecedor();
        if($id_categoria == 0){
            $id_categoria = cadastrarCategoria();
        }
        if($id_fornecedor == 0){
            $id_fornecedor = cadastrarFornecedor();
        }
        $sql = "UPDATE tb_produtos set  nome = '{$nome}',
                                        preco_custo = '{$preco_custo}',
                                        preco_venda ='{$preco_venda}',
                                        unidade     = '{$unidade}',
                                        embalagem   = '{$embalagem}',
                                        cod_categoria =  '{$id_categoria}',
                                        cod_fornecedor = '{$id_fornecedor}'
                                        WHERE id_produto = '{$id_produto}'";
       
        $conn = conectar();      
        if (!mysqli_query($conn, $sql)) {
            $data['fail'] = "Ocorreu um erro ao cadastrar o produto";
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    $sql_estoque = "UPDATE tb_estoque set  quantidade = '{$qtd}', minimo = '{$qtd_minima}' WHERE cod_produto = '{$id_produto}'";   
        if (!mysqli_query($conn, $sql_estoque)) {
            $data['fail'] = "Ocorreu um erro ao cadastrar o estoque";
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
        $data['success'] = "Produto editado com sucesso";
        buscarProduto();
        unset($data['value']);       
    }

}
?>
<h1>Editar Produto</h1>
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
<form action="<?= base_url('editarProduto.php?action=editar&id_produto='.$data['id_produto'])?>" method="post">

<input type="hidden" name="id_produto" value="<?= $data['id_produto'] ?>">
<div class="row">
<div class="form-group col-md-6">
        <label for="nome">Nome</label>
        <input type="text" class="form-control" name="nome" id="nome" value="<?= (isset($data['value']['Nome'])) ? $data['value']['Nome']:  $data['produto'][0]['nome'] ;?>">
        <span style="color:red"><?=(isset($data['validation']['Nome'])) ? $data['validation']['Nome'] : ''?></span>
    </div>

    <div class="form-group col-md-3" >
        <label for="preco_custo">Preço Custo</label>
        <input type="text" class="form-control money" name="preco_custo" id="preco_custo" value="<?= (isset($data['value']['Preço Custo'])) ? $data['value']['Preço Custo']: $data['produto'][0]['preco_custo']  ;?>" >
        <span style="color:red"><?=(isset($data['validation']['Preço Custo'])) ? $data['validation']['Preço Custo'] : ''?></span>
    </div>

    <div class="form-group col-md-3">
        <label for="preco_venda">Preço Venda</label>
        <input type="text" class="form-control money" name="preco_venda" id="preco_venda" value="<?= (isset($data['value']['Preço Venda'])) ? $data['value']['Preço Venda']: $data['produto'][0]['preco_venda']  ;?>">
        <span style="color:red"><?=(isset($data['validation']['Preço Venda'])) ? $data['validation']['Preço Venda'] : ''?></span>
    </div>
</div>
<div class="row">    
    <div class="form-group col-md-6">
        <label for="categoria">Categoria</label>
        <input list="lista-categorias" type="text" class="form-control" name="categoria" id="categoria" value="<?= (isset($data['value']['Categoria'])) ? $data['value']['Categoria']: $data['produto'][0]['categoria']  ;?>">
        <datalist id="lista-categorias">
        <?php for ($i=0; $i < count($data['categorias']) ; $i++):?>
            <option value="<?= $data['categorias'][$i]['nome']?>">
        <?php endfor; ?>
        </datalist>
        

        <span style="color:red"><?=(isset($data['validation']['Categoria'])) ? $data['validation']['Categoria'] : ''?></span>
    </div>

    <div class="form-group col-md-6">
        <label for="fornecedor">Fornecedor</label>
        <input autocomplete="off" list="lista-fornecedores" type="text" class="form-control" name="fornecedor" id="fornecedor" value="<?= (isset($data['value']['Fornecedor'])) ? $data['value']['Fornecedor']: $data['produto'][0]['fornecedor']  ;?>" >
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
        <input autocomplete="off"  list="lista-unidades" type="text" class="form-control" name="unidade" id="unidade" value="<?= (isset($data['value']['Unidade'])) ? $data['value']['Unidade']: $data['produto'][0]['unidade']  ;?>">
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
        <input  type="text" class="form-control" name="embalagem" id="embalagem" value="<?= (isset($data['value']['embalagem'])) ? $data['value']['Embalagem']: $data['produto'][0]['embalagem'] ;?>">
        <span style="color:red"><?=(isset($data['validation']['Embalagem'])) ? $data['validation']['Embalagem'] : ''?></span>
    </div>

    <div class="form-group col-md-2">
        <label for="qtd">Qtd.</label>
        <input type="text" class="form-control" name="qtd" id="qtd" value="<?= (isset($data['value']['Qtd.'])) ? $data['value']['Qtd.']: $data['produto'][0]['qtd']  ;?>">
        <span style="color:red"><?=(isset($data['validation']['Qtd.'])) ? $data['validation']['Qtd.'] : ''?></span>
    </div>

    <div class="form-group col-md-2">
        <label for="qtd_minima">Qtd. Minima</label>
        <input type="text" class="form-control" name="qtd_minima" id="qtd_minima" value="<?= (isset($data['value']['Qtd. Minima'])) ? $data['value']['Qtd. Minima']: $data['produto'][0]['qtd_minima']  ;?>">
        <span style="color:red"><?=(isset($data['validation']['Qtd. Minima'])) ? $data['validation']['Qtd. Minima'] : ''?></span>
    </div>
</div>

    

    

    <button class="btn btn-secondary" type="submit">Editar</button>


</form>



<?php require_once "includes/footer.php";?>