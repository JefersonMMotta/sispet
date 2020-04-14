<?php require_once "includes/functions.php";
require_once "includes/header.php";


function listarVendas()
{
    $sql = "SELECT * FROM tb_orders WHERE MONTH(data) = MONTH(CURDATE())";
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $tabela = $_POST['tabela'];
    $campo = $_POST['campo'];
    $comparacao = $_POST['comparacao'];
    $pesquisa = $_POST['pesquisa'];
    $sql = "SELECT * FROM tb_{$tabela} WHERE {$campo}";
    $sql .= ($comparacao == 'like') ? " like '%{$pesquisa}%' " : '';
    $sql .= ($comparacao == '=') ? " = '{$pesquisa}' " : '';
    $sql .= ($comparacao == 'in') ? " IN '{$pesquisa}' " : '';
    $sql .= ($comparacao == '>=') ? " >= '{$pesquisa}' " : '';
    $sql .= ($comparacao == '<=') ? " <= '{$pesquisa}' " : '';
    $sql .= ($comparacao == 'a%') ? " like '{$pesquisa}%' " : '';
    echo $sql;
}

?>
<h1>Relatórios</h1>
<form class="" action="<?= base_url('relatorios.php') ?>" method="post">

    <div class="form-group">
        <label for="tabela">Pesquisar em :</label>
        <select class="form-control" id="tabela" name="tabela">
            <option>Selecione</option>
            <option>Vendas</option>
            <option>Produtos</option>
            <option>Clientes</option>
            <option>Pets</option>
        </select>
    </div>

    <div class="form-group">
        <label for="campos">Campo:</label>
        <select class="form-control" id="campos" name="campo">
            <option value="">selecione</option>

        </select>
    </div>

    <div class="form-group">
        <div class="form-check">
            <label class="form-check-label">
                <input type="checkbox" class="form-check-input" value="NOT" name="not"> Não
            </label>
        </div>
        <label for="comparacao">Comparação:</label>
        <select class="form-control" id="comparacao" name="comparacao">
            <option value="like">Contém</option>
            <option value="=">É igual a</option>
            <option value="a%">Começa com</option>
            <option value="in">Esta em</option>
            <option value=">=">É em ou depois de </option>
            <option value="<=">É em ou antes de </option>
            <option value="between">Está entre </option>
        </select>
    </div>

    <div class="form-group">
        <label for="pesquisa">Pesquisa</label>
        <input type="text" class="form-control" name="pesquisa" id="pesquisa">
    </div>

    <button type="submit" class="btn btn-primary" id="myBtn">Pesquisar <i class="fa fa-search" aria-hidden="true"></i></button>
</form>

<script>
    let options = "";
    let vendas = {
        campos: ['data', 'forma_pagamento', 'total_bruto', 'total_liquido', 'valor_desconto', 'qtd_itens', 'data_cadastro']
    }
    let produtos = {
        campos: ['nome', 'preco_custo', 'preco_venda', 'unidade', 'embalagem', 'qtd_itens', 'data_cadastro']
    }
    let clientes = {
        campos: ['nome', 'email', 'data_cadastro']
    }
    let pets = {
        campos: ['nome', 'raca', 'sexo', 'especie', 'data_nascimento', 'cor', 'data_cadastro']
    }

    function mostrarCampos(campos) {
        $("#campos").empty();
        campos.forEach(campo => {
            console.log(campo);
            options += `<option value='${campo}'>${campo}</option>`;
        })
        $("#campos").append(options);

    }


    $("#tabela").on("change", function() {
        $("#campos").empty();
        options = "";
        let tabela = this.value;
        if (tabela == "Vendas") {
            mostrarCampos(vendas.campos)
        }
        if (tabela == "Produtos") {
            mostrarCampos(produtos.campos)
        }
        if (tabela == "Clientes") {
            mostrarCampos(clientes.campos)
        }
        if (tabela == "Pets") {
            mostrarCampos(pets.campos)
        }
    });
</script>
<?php require_once "includes/footer.php" ?>