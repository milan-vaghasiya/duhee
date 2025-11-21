<form>
    <div class="col-md-12">
        <div class="row form-group">
            <div class="col-md-12 error general_error"></div>
            <div class="col-md-12">
                <div style="width:100%;">
                    <table id="itemTable" class="table jpExcelTable">
                        <thead class="table-info">
                            <tr>
                                <th>#</th>
                                <th style="width: 30%;">Item</th>
                                <th style="width: 10%;">Batch No</th>
                                <th style="width: 10%;">Qty</th>
                                <th>Receive Qty</th>
                                <th>Receive Date</th>
                                <th>In Challan No</th>
                                <th>Size</th>
                                <th>Received Size</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($itemData)) {
                                $i = 1;
                                foreach ($itemData as $row) {
                                   $pending_qty = $row->qty - $row->dispatch_qty;
                                        ?>
                                        <tr>
                                            <td><?= $i ?></td>
                                            <td><?= $row->full_name ?></td>
                                            <td><?= $row->batch_no ?></td>
                                            <td><?= floatval($row->qty)?></td>
                                            <td><?= floatval($row->dispatch_qty)?></td>                                            
                                            <td>
                                               <?=!empty($row->cod_date)?formatDate($row->cod_date):''?>
                                            </td>
                                            <td>
                                                <?=!empty($row->drg_rev_no)?$row->drg_rev_no:''?>
                                            </td>
                                            <td>
                                                <?=!empty($row->rev_no)?$row->rev_no:''?>
                                            </td>
                                            <td>
                                                <?=!empty($row->grn_data)?$row->grn_data:''?>
                                            </td>
                                        </tr>
                                        <?php
                                        $i++;
                                   }
                            }
                            ?>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
    $(document).ready(function() {
        $(document).on("keyup", ".challanQty", function() {
            var pending_qty = $(this).data('pending_qty');
            var receive_qty = $(this).val();
            if (parseFloat(receive_qty) > parseFloat(pending_qty)) {
                $(this).val('0');
            }
        });
    });
</script>