<?php require_once "includes/functions.php";
require_once "includes/header.php";

function listar($sql)
{
    global $data;
    $data['vendas'] = [];
    $data['total_liquido'] = 0;
    $data['total_bruto'] = 0;
    $data['desconto'] = 0;
    $data['qtd_itens'] = 0;
    $conn = conectar();
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        array_push($data['vendas'], $row);
        $data['total_liquido'] += $row['total_liquido'];
        $data['total_bruto'] += $row['total_bruto'];
        $data['desconto'] += $row['valor_desconto'];
        $data['qtd_itens'] += $row['qtd_itens'];
    }
    mysqli_close($conn);
}

if (isset($_GET['relatorio'])) {
    $relatorio = trim(addslashes(filter_var($_GET['relatorio'])));
    $data['relatorio'] = $relatorio;
    $sql = "";
    switch ($relatorio) {
        case 'dia':
            $sql = "SELECT * FROM tb_orders WHERE DAY(data) = DAY(CURDATE())";
            break;
        case 'mes':
            $sql = "SELECT * FROM tb_orders WHERE MONTH(data) = MONTH(CURDATE())";
            break;
        case 'ano':
            $sql = "SELECT * FROM tb_orders WHERE YEAR(data) = YEAR(CURDATE())";
            break;
        default:
            $sql = "SELECT * FROM tb_orders WHERE DAY(data) = DAY(CURDATE())";
            break;
    }
    listar($sql);
}


?>
<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link <?= ($data['relatorio'] == 'dia') ? 'active' : '' ?>" href="<?= base_url('relatorios.php?relatorio=dia') ?>">Vendas do dia</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= ($data['relatorio'] == 'mes') ? 'active' : '' ?>" href="<?= base_url('relatorios.php?relatorio=mes') ?>">Vendas do Mês</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= ($data['relatorio'] == 'ano') ? 'active' : '' ?>" href="<?= base_url('relatorios.php?relatorio=ano') ?>">Vendas do Ano</a>
    </li>
    <li class="nav-item">
        <a class="nav-link " href="<?= base_url('relatorioAvancado.php') ?>">Pesquisa avançada</a>
    </li>
</ul>

<table class="table table-sm table-bordered table-stripped">
    <thead>
        <th></th>
        <th>Data</th>
        <th>Forma de Pagamento</th>
        <th>Total bruto</th>
        <th>Desconto</th>
        <th>Total Liquido</th>
        <th>Total itens</th>
    </thead>
    <tbody>
        <?php if (isset($data['vendas'])) : ?>
            <?php for ($i = 0; $i < count($data['vendas']); $i++) : ?>
                <tr>
                    <td></td>
                    <td><?= converterData($data['vendas'][$i]['data']) ?></td>
                    <td><?= $data['vendas'][$i]['forma_pagamento'] ?></td>
                    <td>R$<?= formatarMoeda($data['vendas'][$i]['total_bruto']) ?></td>
                    <td>R$<?= formatarMoeda($data['vendas'][$i]['valor_desconto']) ?></td>
                    <td>R$<?= formatarMoeda($data['vendas'][$i]['total_liquido']) ?></td>
                    <td><?= $data['vendas'][$i]['qtd_itens'] ?></td>
                </tr>
            <?php endfor; ?>
        <?php else : ?>
            <tr>
                <td colspan="7">Nenhuma venda realizada!</td>
            </tr>

        <?php endif; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="6" class="text-right"><b>Total Bruto:</b></td>
            <td>R$<?= (isset($data['total_bruto'])) ? formatarMoeda($data['total_bruto']) : 0;  ?></td>
        </tr>
        <tr>
            <td colspan="6" class="text-right"><b>Total de desconto:</b></td>
            <td>R$<?= (isset($data['desconto'])) ? formatarMoeda($data['desconto']) :0 ?></td>
        </tr>
        <tr>
            <td colspan="6" class="text-right"><b>Total liquido:</b></td>
            <td>R$<?= (isset($data['total_liquido'])) ? formatarMoeda($data['total_liquido']) : 0 ?></td>
        </tr>
    </tfoot>
</table>


<?php require_once "includes/footer.php"; ?>