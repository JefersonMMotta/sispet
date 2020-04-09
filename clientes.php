<?php
require_once "includes/functions.php";
require_once "includes/header.php";


function contarClientes()
{
    $conn = conectar();
    $sql = "SELECT * FROM clientes WHERE ativo = 1";
    $result = mysqli_query($conn, $sql);
    $total_clientes = mysqli_num_rows($result);
    mysqli_close($conn);
    return $total_clientes;
}

function listarCLientes()
{
    global $data;
    $page = (isset($_GET['page'])) ? $_GET['page'] : 1 ;
    $total = contarClientes();
    
    
    paginar($page, 3, $total);
    $conn = conectar();
    $sql = "SELECT * FROM clientes WHERE ativo = 1 ";
    if(isset($_GET['search']) && strlen($_GET['search']) > 2){
        $search = trim(addslashes(filter_var($_GET['search'], FILTER_SANITIZE_STRING)));
        $sql .= "AND nome like '%{$search}%' " ; 
      
    }
    $sql .= "LIMIT {$data['pagination']['inicio']},{$data['pagination']['per_page']}";
    $result = mysqli_query($conn, $sql);
    $data['num_clientes'] =   mysqli_num_rows($result);
    $data['clientes']= [];  
    if ($data['num_clientes'] > 0) {                 
        while($row = mysqli_fetch_assoc($result)) {
            array_push($data['clientes'], $row );
        }
    } 
}

listarCLientes();
//echo '<pre>', print_r($data['clientes']), '</pre>';

?>




<h1>Clientes</h1>
<form action="<?= base_url("clientes.php")?>">
    <input type="text" name="search">
    <button type="submit">Pesquisar</button>

</form>
<a class="btn btn-secondary btn-sm float-right mb-1" href="<?= base_url("novocliente.php")?>">Cadastrar</a>
<table class="table table-bordered table-sm">
    <thead>
        <th></th>
        <th>Código</th> 
        <th>Pets</th>
            
        <th>Nome</th>
        <th>Telefone</th>
        <th>E-mail</th>
        <th></th>
    </thead>
    <tbody>
        <?php for($i = 0; $i < count($data['clientes']); $i++): ?>
        <tr>
            <td><a href="editarcliente.php?action=editar&cliente_id=<?=  $data['clientes'][$i]['id_cliente'] ?>" >
                    <i style='font-size:14px' class='far'>&#xf044;</i>
                </a>
            </td>
            <td><?= $data['clientes'][$i]['id_cliente'] ?></td>
            <td><a href="<?= base_url('clientepets.php?cliente_id='). $data['clientes'][$i]['id_cliente'] ?>">Pets</a></td>
            <td><?= $data['clientes'][$i]['nome'] ?></td>
            <td><?= $data['clientes'][$i]['telefone'] ?></td>
            <td><?= $data['clientes'][$i]['email'] ?></td>
            <td>
                <a onclick="if(!confirm('Deseja realmente excluir esse cliente?')) return false;" 
                
                href="excluircliente.php?action=excluir&cliente_id=<?=  $data['clientes'][$i]['id_cliente'] ?>">
                 <i style='font-size:14px; color:red' class='fas'>&#xf12d;</i>
                </a>
            </td>
        </tr>
        <?php endfor; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="7">
               
                <a href="clientes.php?page=<?= $data['anterior']?>">Anterior</a>
                <a href="#">Atual</a>
                <a href="clientes.php?page=<?= $data['proximo']?>">Proximo</a>                 

            </td>
        </tr>
    </tfoot>
</table>
<?php require_once "includes/footer.php";?>