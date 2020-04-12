<div class="modal fade" id="myModalClientes">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Clientes</h4>
          <button type="button" class="close" data-dismiss="modal">×</button>
        </div>

        <!-- Modal body -->
        <div class="modal-body">
            <table class="table table-sm">
            <thead>
                <th>Adicionar</th>
                <th>Nome</th>                
            </thead>
            <tbody>
             <?php if(isset($data['clientes'])):?> 
            <?php for ($i = 0; $i < count($data['clientes']); $i++): ?>
                <tr>
                <td>
                    <a href="<?=base_url("cadastrarPedido.php?action=addCliente&id_cliente="
                     . $data['clientes'][$i]['id_cliente']."&cliente_nome="). $data['clientes'][$i]['nome']?>">
                    <i class="fas fa-user-plus"></i>
                    </a>
                </td>
                    <td><?=$data['clientes'][$i]['nome']?></td>                   
                </tr>
            <?php endfor;?>
            <?php endif; ?>
            </tbody>
            </table>
            </div>

        <!-- Modal footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>

      </div>
    </div>
  </div