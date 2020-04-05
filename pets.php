<?php
require_once "includes/functions.php";
require_once "includes/header.php";

function listarPets()
{
    global $data;
    $data['pets'] = [];
    $search = (isset($_GET['search'])) ? $_GET['search'] : null;
     $sql = "SELECT *, (SELECT nome FROM clientes WHERE id_cliente = cod_cliente) as 'cliente' FROM tb_pets";
    $sql .= " WHERE ativo = 1 ";
     if($search){
        $sql .= " AND (nome like '%{$search}%') "; 
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
                 <i style='font-size:14px; color:red' class='fas'>&#xf12d;</i>
                </a>
            </td>
            </tr>
            <?php endfor?>
    </tbody>
</table>

<?php require_once "includes/footer.php"; ?>