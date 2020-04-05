<?php
require_once "includes/functions.php";
require_once "includes/header.php";

function listarPets()
{
    global $data;
    $data['pets'] = [];
    $search = (isset($_GET['search'])) ? $_GET['search'] : null;
     $sql = "SELECT *, (SELECT nome FROM clientes WHERE id_cliente = cod_cliente) as 'cliente' FROM tb_pets";
    if($search){
        $sql .= " WHERE (nome like '%{$search}%') "; 
    }
  
    $conn = conectar();
    $result = mysqli_query($conn, $sql);
    while($row = mysqli_fetch_assoc($result)){
        array_push($data['pets'], $row);
    }
    mysqli_close($conn);    
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
        <th></th>
        <th>Nome</th>
        <th>Raça </th>
        <th>Espécie</th>
        <th>Cor</th>
        <th>Data Nascimento</th>
        <th>Cliente</th>
    </thead>
    <tbody>
        <?php for ($i=0; $i < count($data['pets']) ; $i++):?>
            <tr>
                <td>
                    <a href="<?= base_url('editarpet?action=editar&id_pet='.$data['pets'][$i]['id_pet'])?>">
                    <i style='font-size:14px' class='far'>&#xf044;</i>
                    </a>               
                </td>
                <td><?= $data['pets'][$i]['nome']?></td>
                <td><?= $data['pets'][$i]['raca']?></td>
                <td><?= $data['pets'][$i]['especie']?></td>
                <td><?= $data['pets'][$i]['cor']?></td>
                <td><?= converterData($data['pets'][$i]['data_nascimento']); ?></td>
                <td><?= $data['pets'][$i]['cliente']?></td>
            </tr>
            <?php endfor?>

    </tbody>
</table>

<?php require_once "includes/footer.php"; ?>