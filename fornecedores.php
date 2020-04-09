<?php
require_once "includes/functions.php";
require_once "includes/header.php";


function contarFornecedores()
{
    $conn = conectar();
    $sql = "SELECT * FROM tb_fornecedores WHERE ativo = 1";
    $result = mysqli_query($conn, $sql);
    $total_fornecedores = mysqli_num_rows($result);
    mysqli_close($conn);
    return $total_fornecedores;
}

function listarFornecedores()
{
    global $data;
    $page = (isset($_GET['page'])) ? $_GET['page'] : 1 ;
    $total = contarFornecedores();    
    
    paginar($page, 3, $total);
    $conn = conectar();
    $sql = "SELECT * FROM tb_fornecedores WHERE ativo = 1 ";

    if(isset($_GET['search'])){
        $search = trim(addslashes(filter_var($_GET['search'], FILTER_SANITIZE_STRING)));
        $sql .= "AND nome like '%{$search}%' " ; 
      
    }
    $sql .= "LIMIT {$data['pagination']['inicio']},{$data['pagination']['per_page']}";
    echo $sql;
    $result = mysqli_query($conn, $sql);
    $data['num_fornecedores'] =   mysqli_num_rows($result);
    $data['fornecedores']= [];  
    if ($data['num_fornecedores'] > 0) {                 
        while($row = mysqli_fetch_assoc($result)) {
            array_push($data['fornecedores'], $row );
        }
    } 
}

listarFornecedores();
//echo '<pre>', print_r($data['fornecedores']), '</pre>';

?>




<h1>Fornecedores</h1>
<form action="<?= base_url("fornecedores.php")?>">
    <input type="text" name="search">
    <button type="submit">Pesquisar</button>

</form>
<a class="btn btn-secondary btn-sm float-right mb-1" href="<?= base_url("novoFornecedor.php")?>">Cadastrar</a>
<table class="table table-bordered table-sm">
    <thead>
        <th></th>
        <th>Código</th> 
        <th>Nome</th>
        <th>Telefone</th>
        <th>E-mail</th>
        <th></th>
    </thead>
    <tbody>
        <?php for($i = 0; $i < count($data['fornecedores']); $i++): ?>
        <tr>
            <td><a href="editarFornecedor.php?action=editar&id_fornecedor=<?=  $data['fornecedores'][$i]['id_fornecedor'] ?>" >
                    <i style='font-size:14px' class='far'>&#xf044;</i>
                </a>
            </td>
            <td><?= $data['fornecedores'][$i]['id_fornecedor'] ?></td>
            <td><?= $data['fornecedores'][$i]['nome'] ?></td>
            <td><?= $data['fornecedores'][$i]['telefone'] ?></td>
            <td><?= $data['fornecedores'][$i]['email'] ?></td>
            <td>
                <a onclick="if(!confirm('Deseja realmente excluir esse fornecedor?')) return false;" 
                
                href="excluirFornecedor.php?action=excluir&id_fornecedor=<?=  $data['fornecedores'][$i]['id_fornecedor'] ?>">
                 <i style='font-size:14px; color:red' class='fas'>&#xf12d;</i>
                </a>
            </td>
        </tr>
        <?php endfor; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="7">
               
                <a href="fornecedores.php?page=<?= $data['anterior']?>">Anterior</a>
                <a href="#">Atual</a>
                <a href="fornecedores.php?page=<?= $data['proximo']?>">Proximo</a>                 

            </td>
        </tr>
    </tfoot>
</table>
<?php require_once "includes/footer.php";?>