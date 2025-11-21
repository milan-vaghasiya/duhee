<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?= (!empty($packReqData->id)) ? $packReqData->id : "" ?>" />
            <input type="hidden" name="trans_main_id" value="<?= (!empty($packReqData->trans_main_id)) ? $packReqData->trans_main_id : "" ?>">
            <input type="hidden" name="trans_child_id" value="<?= (!empty($packReqData->trans_child_id)) ? $packReqData->trans_child_id : "" ?>">
            <div class="col-md-12">
                <table class="table">
                    <tr>
                        <th style="width: 20%;">Item :</th>
                        <td style="width: 50%;" class="text-left"><?=((!empty($packReqData->item_code)) ? '['.$packReqData->item_code.'] ' : ""). ((!empty($packReqData->item_name)) ? $packReqData->item_name : "") ?></td>
                        <th style="width: 20%;">Pending Qty :</th>
                        <td class="text-left"><?= $packReqData->request_qty - $packReqData->pack_link_qty ?></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="table-responsive">
                <div class="error general_error"></div>
                <table class="table table-bordered">
                    <thead class="thead-info">
                        <tr>
                            <th>#</th>
                            <th>Packing No</th>
                            <th>Qty</th>
                            <th>Link Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($packingTransData)) {
                            $i = 1;
                            foreach ($packingTransData as $row) {
                        ?>
                                <tr>
                                    <td><?= $i ?></td>
                                    <td><?= getPrefixNumber($row->trans_prefix, $row->trans_no) ?></td>
                                    <td><?= $row->pending_qty ?></td>
                                    <td>
                                        <input type="text" class="form-control floatOnly linkQty" name="link_qty[]" data-row_id="<?= $i ?>" data-stock_qty="<?= $row->pending_qty ?>">
                                        <input type="hidden" name="packing_trans_id[]" value="<?= $row->id ?>">
                                        <div class="error link_qty_error<?= $i ?>"></div>
                                    </td>
                                </tr>
                        <?php
                                $i++;
                            }
                        }else{
                            ?><tr><td class="text-center" colspan="5">No data available in table</td></tr><?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>
<script>
    $(document).ready(function() {
        $(document).on('keyup', ".linkQty", function() {
            var link_qty = parseFloat($(this).val());
            var row_id = $(this).data("row_id");
            var stock_qty = parseFloat($(this).data("stock_qty"));
            $(".error").html("");
            if (link_qty > stock_qty) {
                $(".link_qty_error" + row_id).html("Qty not available");
                $(this).val("");
            }
        });
    });
</script>