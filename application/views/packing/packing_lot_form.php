<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" id="id" name="id" value="<?= !empty($dataRow->id) ? $dataRow->id : '' ?>">
            <input type="hidden" id="item_id" name="item_id" value="<?= !empty($dataRow->item_id) ? $dataRow->item_id : (!empty($approvalData)?$approvalData->product_id:'') ?>">
            <input type="hidden" id="job_approval_id" name="job_approval_id" value="<?= !empty($dataRow->next_approval_id) ? $dataRow->next_approval_id : (!empty($approvalData)?$approvalData->next_approval_id:'') ?>">
            <input type="hidden" id="job_card_id" name="job_card_id" value="<?= !empty($dataRow->job_card_id) ? $dataRow->job_card_id : (!empty($approvalData)?$approvalData->job_card_id:'') ?>" >
            <input type="hidden" name="batch_no" value="<?= !empty($batch_no) ? $batch_no : '' ?>">
            <div class="col-md-2 form-group">
                <label for="entry_date">Date</label>
                <input type="date" name="entry_date" id="entry_date" class="form-control req" value="<?= !empty($dataRow->entry_date) ? $dataRow->entry_date : date("Y-m-d") ?>">
            </div>
          
            <div class="col-md-7 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control">
            </div>
            <hr>
            <div class="error general_error col-md-12"></div>
          
            <div class="col-md-12">
                <div class="table-responsive">
                    <div class="error orderError"></div><br>
                    <table id='jobTransTable' class="table table-bordered jpDataTable colSearch">
                        <thead class="thead-info">
                            <tr class="text-center">
                                <th class="text-center" style="width:5%;">#</th>
                                <th class="text-center" style="width:10%;">Date</th>
                                <th class="text-center">Process</th>
                                <th class="text-center" style="width:10%;">Pending Qty.</th>
                                <th style="width:20%;">Lot Qty.</th>
                            </tr>
                        </thead>
                        <tbody id="jobTransData">
                            <?php
                            if (!empty($movementList)) {
                                $i=1;
                                foreach ($movementList as $row) {
                                    echo '<tr>
                                    <td class="text-center fs-12">
                                        <input type="checkbox" id="md_checkbox_' . $i . '" name="job_trans_id[]" class="filled-in chk-col-success trans_check" data-rowid="' . $i . '" value="' . $row->id . '"  ><label for="md_checkbox_' . $i . '" class="mr-3"></label>
                                    </td>
                                    <td class="text-center fs-12">' . formatDate($row->entry_date) . '</td>
                                    <td class="text-center fs-12">' . $row->process_name . '</td>
                                    <td class="text-center fs-12">' . floatVal($row->accepted_qty-$row->fir_qty) . '</td>
                                    <td class="text-center fs-12">
                                        <input type="hidden" id="pending_qty' . $i . '" value="' . floatVal($row->accepted_qty-$row->fir_qty) . '">                   
                                        <input type="text" id="lot_qty' . $i . '" name="lot_qty[]" data-rowid="' . $i . '" class="form-control firLotQty floatOnly" value="0" disabled>
                                        <div class="error lotQty' . $row->id . '"></div>
                                    </td>
                                </tr>';
                                    $i++;
                                }
                            } else {
                            ?>
                                <tr>
                                    <td colspan="7" class="text-center">No data available in table</td>
                                </tr>
                            <?php
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
        
       
        $(document).on("keyup",".totalLotQty", function(){ 
            var total_box = $("#total_box").val();
            var first_box = $("#first_box").val();
            var max_qty_per_box = $("#max_qty_per_box").val();

            var pending_box = parseFloat(total_box) - parseFloat(first_box);
            var pending_qty = parseFloat(pending_box)*parseFloat(max_qty_per_box);
            var total_qty = parseFloat(total_box)*parseFloat(max_qty_per_box);
            $("#pending_box_qty").val(pending_box);
            $("#lot_qty").val(pending_qty);
            $("#qty").val(total_qty);
        });

        $(document).on('keyup','.calQtyPerBox',function(){
            var qtyPerBoxArr=[];
            qtyPerBoxArr = $("input[name='qty_per_box[]']").map(function(){return $(this).val();}).get();
            var maxQty =  Math.max.apply( Math, qtyPerBoxArr );
            $("#max_qty_per_box").val(maxQty);
            $(".totalLotQty").trigger('keyup');

        });

        $(document).on("click", ".trans_check", function() {
            var id = $(this).data('rowid');
            $(".error").html("");
            if (this.checked) {
                $("#lot_qty" + id).removeAttr('disabled');
            } else {
                $("#lot_qty" + id).attr('disabled', 'disabled');
            }
        });

        $(document).on("keyup", ".firLotQty", function() {
            var id = $(this).data('rowid');
            var lot_qty = $("#lot_qty" + id).val();
            var pending_qty = $("#pending_qty" + id).val();
            if (parseFloat(lot_qty) > parseFloat(pending_qty)) {
                $("#lot_qty" + id).val('0');
            }
        });
    });
</script>