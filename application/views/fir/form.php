<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
            <div class="col-lg-12 col-xlg-12 col-md-12">
                <table class="table table-bordered-dark vendor_challan_table">
                    <tr >
                        <th>Product </th>
                        <td colspan="3"><?= !empty($dataRow->full_name) ? $dataRow->full_name : $firData->full_name ?></td>
                    <tr class="bg-light">
                        <th>Job No </th>
                        <th>FIR No</th>
                        <th>FG Batch No</th>
                        <!-- <th>Qty </th>
                        <th>Pending </th> -->
                    </tr>
                    <tr>
                        <td><?= !empty($dataRow->job_number) ? $dataRow->job_number : $firData->job_number ?></td>
                        <td><?= !empty($dataRow->fir_number) ? $dataRow->fir_number : ($fir_number) ?></td>
                        <td><?= !empty($dataRow->fg_batch_no) ? $dataRow->fg_batch_no  : $fg_batch_no ?></td>
                        <!-- <td><?= floatval($firData->accepted_qty) ?></td>
                        <td colspan="3"><?= floatval($firData->accepted_qty - $firData->fir_qty) ?></td> -->
                    </tr>
                </table>
            </div>
            <div class="col-md-12 row">
                <div class="col-md-4 form-group">
                    <label for="fir_date">Fir Date</label>
                    <input type="date" class="form-control" name="fir_date" value="<?= !empty($dataRow->fir_date) ? $dataRow->fir_date : date("Y-m-d") ?>">
                </div>
                <div class="col-md-4 form-group">
                    <label for="live_packing">Live Packing</label>
                    <select name="live_packing" id="live_packing" class="form-control ">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="table-responsive">
                    <div class="error orderError"></div><br>
                    <table id='jobTransTable' class="table table-bordered jpDataTable colSearch">
                        <thead class="thead-info">
                            <tr class="text-center">
                                <th class="text-center" style="width:5%;">#</th>
                                <th class="text-center" style="width:10%;">Date</th>
                                <th class="text-center">Process</th>
                                <th>Vendor</th>
                                <th class="text-center" style="width:10%;">Pending Qty.</th>
                                <th>Lot Qty.</th>
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
                                    <td class="text-cente fs-12">' . formatDate($row->entry_date) . '</td>
                                    <td class="text-center fs-12">' . $row->process_name . '</td>
                                    <td class="text-center fs-12">' . (!empty($row->party_name)?$row->party_name:'Inhouse') . '</td>
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
           
            <input type="hidden" id="id" name="id" value="<?= !empty($dataRow->id) ? $dataRow->id : '' ?>">
            <input type="hidden" id="fir_type" name="fir_type" value="1">
            <input type="hidden" id="job_card_id" name="job_card_id" value="<?= !empty($dataRow->job_card_id) ? $dataRow->job_card_id : $firData->job_card_id ?>">
            <input type="hidden" id="job_approval_id" name="job_approval_id" value="<?= !empty($dataRow->job_approval_id) ? $dataRow->job_approval_id : $firData->next_approval_id ?>">
            <!-- <input type="hidden" id="job_trans_id" name="job_trans_id" value="<?= !empty($dataRow->job_trans_id) ? $dataRow->job_trans_id : $firData->id ?>"> -->
            <input type="hidden" id="fir_prefix" name="fir_prefix" value="<?= !empty($dataRow->fir_prefix) ? $dataRow->fir_prefix : $fir_prefix ?>">
            <input type="hidden" id="fir_no" name="fir_no" value="<?= !empty($dataRow->fir_no) ? $dataRow->fir_no : $fir_no ?>">
            <input type="hidden" id="fir_number" name="fir_number" value="<?= !empty($dataRow->fir_number) ? $dataRow->fir_number : $fir_number ?>">
            <input type="hidden" id="fg_no" name="fg_no" value="<?= !empty($dataRow->fg_no) ? $dataRow->fg_no : $fg_no ?>">
            <input type="hidden" id="item_id" name="item_id" value="<?= !empty($dataRow->item_id) ? $dataRow->item_id : $firData->product_id ?>">

        </div>
    </div>
</form>
<script>
    $(document).ready(function() {
        $(document).on('change', "#job_card_id", function() {
            var job_card_id = $(this).val();
            var jobData = $("#job_card_id :selected").data('row');
            $("#stock_qty").html("Stock Qty : " + parseInt(jobData.qty));

            if (job_card_id) {
                $("#item_id").val(jobData.product_id);
                $.ajax({
                    url: base_url + controller + '/getLotNo',
                    data: {
                        job_card_id: job_card_id
                    },
                    type: "POST",
                    dataType: "json",
                    success: function(data) {
                        $("#fir_no").val(data.fir_no);
                        $("#fir_number").val("FIR/" + jobData.job_number + "/" + data.fir_no);
                    }
                });
            }
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