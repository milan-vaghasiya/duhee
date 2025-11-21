<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" id="id" name="id" value="<?= !empty($dataRow->id) ? $dataRow->id : '' ?>">
            <input type="hidden" id="packing_type" name="packing_type" value="4">
            <input type="hidden" id="item_id" name="item_id" value="<?= !empty($packData->item_id) ? $packData->item_id : '' ?>">
            <input type="hidden" id="packing_id" name="packing_id"  value="<?= !empty($packData->id) ? $packData->id : '' ?>">
            
            <div class="col-md-2 form-group">
                <label for="trans_number">LB No</label>
                <input type="text" name="trans_number" id="trans_number" class="form-control" readonly value="<?= !empty($dataRow->trans_number) ? $dataRow->trans_number : sprintf("PFB%03d", $trans_no) ?>">
            
            </div>
            <div class="col-md-2 form-group">
                <label for="entry_date">Date</label>
                <input type="date" name="entry_date" id="entry_date" class="form-control req" value="<?= !empty($dataRow->entry_date) ? $dataRow->entry_date : date("Y-m-d") ?>">
            </div>
           
            <div class="col-md-2 form-group">
                <label for="max_qty_per_box">Qty in Box</label>
                <input type="text" id="max_qty_per_box" name="max_qty_per_box" class="form-control" value="<?=!empty($dimensionData['max_qty_per_box'])?$dimensionData['max_qty_per_box']:''?>" readonly>
            </div>
            <div class="col-md-3 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="<?= !empty($dataRow->remark) ? $dataRow->remark : '' ?>">
            </div>
            <hr> 
            <div class="col-md-6">
                <div class="table-responsive">
                    <table class="table table-bordered item-list-bb ">
                        <thead class="thead-info">
                            <tr >
                                <th colspan="5" class="text-center">Pending Packing Stock</th>
                            </tr>
                            <tr >
                                <th></th>
                                <th>Packing No.</th>
                                <th>Batch No</th>
                                <th>Stock Qty</th>
                                <th>Qty</th>
                            </tr>
                        </thead>
                        <tbody id="packingData">
                            <?php
                            if (!empty($stockData)) {
                                echo $stockData['packBatchHtml'];
                            } else { ?>
                                <tr> <th colspan="5" class="text-center">No data available.</th> </tr>
                            <?php } ?>
                        </tbody> 
                    </table>
                </div>
            </div>
          
            <div class="error general_error"></div>
            <div class="error fir_stock_detail"></div>
            <div class="col-md-6 mt-3">
                <div class="row form-group">
                    <div class="table-responsive">
                        <table id="pirTable" class="table table-bordered item-list-bb">
                            <thead id="theadData" class="thead-info">
                                <tr >
                                    <th><input type="checkbox" id="master_checkbox" class="filled-in chk-col-success" value="true">
                                                                <label for="master_checkbox" class="mr-3"></th>
                                    <th>Product/Process Char.</th>
                                    <th>Specification</th>
                                    <th>Qty Per (Bag/Box)</th>
                                    <th>Measurement Tech.</th>
                                </tr> 
                            </thead>
                            <tbody id="tbodyData">
                                <?php
                                if (!empty($dimensionData['html'])) :
                                    echo $dimensionData['html'];
                                else :
                                    echo "<tr><th colspan='8' class='text-center'>No data available.</th></tr>";
                                endif;
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
    $(document).ready(function() {
        $(document).on('change', "#item_id", function(e) {
            e.preventDefault();
            var item_id = $(this).val();
            if (item_id) {
                $.ajax({
                    url: base_url + controller + '/getPackingDimension',
                    data: {
                        item_id: item_id
                    },
                    type: "POST",
                    dataType: "json",
                    success: function(data) {
                        $("#tbodyData").html(data.tbodyData);
                        $("#packingData").html(data.packingData);
                        $("#firData").html(data.liveFirHtml);
                        $("#max_qty_per_box").val(data.max_qty_per_box);
                        $(".single-select").comboSelect();
                    }
                });
            }
        });
        $(document).on('change', ".firCheck", function() {
            var checkedStatus = false;
            if(this.checked){ checkedStatus = true; }
            $(".firCheck").prop('checked', false);
            var id = $(this).data('rowid');
            $(".fgBatchQty").attr('disabled', 'disabled');
            $(".firId").attr('disabled', 'disabled');

            if(checkedStatus){
                $(this).prop('checked', true);
                $("#fg_batch_qty" + id).removeAttr('disabled');
                $("#fir_id" + id).removeAttr('disabled');
                var fir_id = $("#fir_id" + id).val();
                $(".fir_stock_detail").html();
                $.ajax({
                    url: base_url + controller + '/checkBoxStock',
                    data: {
                        fir_id: fir_id
                    },
                    type: "POST",
                    dataType: "json",
                    success: function(data) {
                        $(".fir_stock_detail").html(data.notification);
                    }
                });
            }
        });
        $(document).on("click", ".batchCheck", function() {
            var id = $(this).data('rowid');
            $(".error").html("");
            if (this.checked) {
                $("#batch_qty" + id).removeAttr('disabled');
                $("#ref_id" + id).removeAttr('disabled');
            } else {
                $("#batch_qty" + id).attr('disabled', 'disabled');
                $("#ref_id" + id).attr('disabled', 'disabled');
            }
        });


        $(document).on('click','#master_checkbox',function(){
            if($(this).prop('checked') == true){
                $(".dimensionCheck").prop('checked', true);  
            }else{
                $(".dimensionCheck").prop('checked', false);
            }
        });

        $(document).on('keyup','.calQtyPerBox',function(){
            var qtyPerBoxArr=[];
            qtyPerBoxArr = $("input[name='qty_per_box[]']").map(function(){return $(this).val();}).get();
            var maxQty =  Math.max.apply( Math, qtyPerBoxArr );
            $("#max_qty_per_box").val(maxQty);
        });
    });
</script>