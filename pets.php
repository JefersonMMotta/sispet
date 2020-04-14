<?php
require_once "includes/functions.php";
require_once "includes/header.php";


function contarPets()
{
    $conn = conectar();
    $sql = "SELECT * FROM tb_pets WHERE ativo = 1";
    $result = mysqli_query($conn, $sql);
    $total_pets = mysqli_num_rows($result);
    mysqli_close($conn);
    return $total_pets;
}

function listarPets()
{
    global $data;
    $page = (isset($_GET['page'])) ? $_GET['page'] : 1;
    $total = contarPets();

    paginar($page, 10, $total);
    $conn = conectar();
    $sql = "SELECT *,
     (SELECT nome FROM clientes where cod_cliente = id_cliente) as cliente
     FROM tb_pets   
     WHERE ativo = 1 ";
    if (isset($_GET['search']) && strlen($_GET['search']) > 2) {
        $search = trim(addslashes(filter_var($_GET['search'], FILTER_SANITIZE_STRING)));
        $sql .= "AND nome like '%{$search}%' ";

    }
    $sql .= "LIMIT {$data['pagination']['inicio']},{$data['pagination']['per_page']}";
    $result = mysqli_query($conn, $sql);
    $data['num_pets'] = mysqli_num_rows($result);
    $data['pets'] = [];
    if ($data['num_pets'] > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($data['pets'], $row);
        }
    }

}

listarPets();

?>
<h1>Pets</h1>
<form action="<?= base_url("pets.php")?>">
    <input type="text" name="search">
    <button type="submit">Pesquisar</button>

</form>
<table class="table table-bordered table-sm">
    <thead>
        <th>Editar</th>
        <th>vacinas</th>
        <th>Nome</th>
        <th>Raça </th>
        <th>Sexo</th>
        <th>Espécie</th>
        <th>Cor</th>
        <th>Data Nascimento</th>
        <th>Cliente</th>
        <th></th>
    </thead>
    <tbody>
        <?php for ($i=0; $i < count($data['pets']) ; $i++):?>
            <tr>
                <td>
                    <a href="<?= base_url('editarpet.php?action=editar&id_pet='.$data['pets'][$i]['id_pet'])?>">
                    <i style='font-size:14px; color:seagreen' class='far'>&#xf044;</i>
                    </a>               
                </td>
                <td>
                    <a href="<?= base_url('vacinaspet.php?id_pet='.$data['pets'][$i]['id_pet'])?>">
                    <i class="fas fa-syringe"></i>
                    </a>               
                </td>
                <td><?= $data['pets'][$i]['nome']?></td>
                <td><?= $data['pets'][$i]['raca']?></td>
                <td><?= $data['pets'][$i]['sexo']?></td>
                <td><?= $data['pets'][$i]['especie']?></td>
                <td><?= $data['pets'][$i]['cor']?></td>
                <td><?= converterData($data['pets'][$i]['data_nascimento']); ?></td>
                <td><?= $data['pets'][$i]['cliente']?></td>
                <td>
                <a onclick="if(!confirm('Deseja realmente excluir esse PET?')) return false;"                
                href="excluirpet.php?action=excluir&id_pet=<?=  $data['pets'][$i]['id_pet'] ?>">
                <i class='fas'>&#xf2ed;</i>
                </a>
            </td>
            </tr>
            <?php endfor?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="10">
            <ul  class="pagination justify-content-end" style="margin:20px 0">
            <?php if($data['atual'] != $data['anterior']):?>
                <li class="page-item"><a  class="page-link" href="pets.php?page=<?=$data['anterior']?>"><?=$data['anterior']?></a></li>
             <?php endif;?>       
                <li class="page-item active">  <a  class="page-link" href="#"><?=$data['atual']?></a></li>
                <?php if($data['atual'] != $data['proximo']):?>
                <li class="page-item">  <a class="page-link" href="pets.php?page=<?=$data['proximo']?>"><?=$data['proximo']?></a></li>
                <?php endif;?>    
            </ul>
            </td>
        </tr>
    </tfoot>
</table>

<?php require_once "includes/footer.php"; ?>