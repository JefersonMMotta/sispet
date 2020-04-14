<?php
require_once "includes/functions.php";
require_once "includes/header.php";

function contarClientes()
{
    $conn = conectar();
    $sql = "SELECT * FROM clientes WHERE ativo = 1 ";
    if(isset($_GET['search'])){
        $search = trim(addslashes(filter_var($_GET['search'], FILTER_SANITIZE_STRING)));
        $sql .= "AND nome like '%{$search}%' ";
    }
    $result = mysqli_query($conn, $sql);
    $total_clientes = mysqli_num_rows($result);
    mysqli_close($conn);
    return $total_clientes;
}

function listarCLientes()
{
    global $data;
    $page = (isset($_GET['page'])) ? $_GET['page'] : 1;
    $total = contarClientes();
    $data['total_clientes'] = $total;

    paginar($page, 10, $total);
    $conn = conectar();
    $sql = "SELECT * FROM clientes WHERE ativo = 1 ";
    if (isset($_GET['search']) && strlen($_GET['search']) > 2) {
        $search = trim(addslashes(filter_var($_GET['search'], FILTER_SANITIZE_STRING)));
        $sql .= "AND nome like '%{$search}%' ";
        $data['search'] = $search;

    }
    $sql .= "LIMIT {$data['pagination']['inicio']},{$data['pagination']['per_page']}";
    $result = mysqli_query($conn, $sql);
    $data['num_clientes'] = mysqli_num_rows($result);
    $data['clientes'] = [];
    if ($data['num_clientes'] > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($data['clientes'], $row);
        }
    }
}
listarCLientes();
?>




<h1>Clientes</h1>
<form action="<?=base_url("clientes.php")?>">
    <input type="text" name="search">
    <button type="submit">Pesquisar</button>

</form>
<a class="btn btn-secondary btn-sm float-right mb-1" href="<?=base_url("novocliente.php")?>">Cadastrar</a>
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
        <?php for ($i = 0; $i < count($data['clientes']); $i++): ?>
        <tr>
            <td><a href="editarcliente.php?action=editar&cliente_id=<?=$data['clientes'][$i]['id_cliente']?>" >
                    <i style='font-size:14px; color:seagreen'' class='far'>&#xf044;</i>
                </a>
            </td>
            <td><?=$data['clientes'][$i]['id_cliente']?></td>
            <td>
                <a href="<?=base_url('clientepets.php?cliente_id=') . $data['clientes'][$i]['id_cliente']?>">
                    <i style="color:#000;" class="fa fa-paw" aria-hidden="true"></i>
                  </a>
            </td>
            <td><?=$data['clientes'][$i]['nome']?></td>
            <td><?=$data['clientes'][$i]['telefone']?></td>
            <td><?=$data['clientes'][$i]['email']?></td>
            <td>
                <a onclick="if(!confirm('Deseja realmente excluir esse cliente?')) return false;"

                href="excluircliente.php?action=excluir&cliente_id=<?=$data['clientes'][$i]['id_cliente']?>">
                <i class='fas'>&#xf2ed;</i>
                </a>
            </td>
        </tr>
        <?php endfor;?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="7">
            <?php 
                $anterior = "";
                $proximo = "";
                $url = "clientes.php?page=";
                $anterior .= (isset($data['search'])) ? $url.$data['anterior'].'&search='.$data['search'] :$url.$data['anterior'];
                $proximo .= (isset($data['search'])) ? $url.$data['proximo'].'&search='.$data['search'] :$url.$data['proximo']; 
            ?>


              <span>Total de: <?= $data['total_clientes']?> Regsitros</span>  
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