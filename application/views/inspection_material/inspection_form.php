<form>
    <div class="col-md-12">
        <div class="row form-group">
            <div class="col-md-12 error general_error"></div>
            <div class="col-md-12">
                <div style="width:100%;">
                    <table class="table jpExcelTable text-center">
                        <tr>
                            <th>Item</th>
                            <th>Serial No</th>
                            <th>Size</th>
                            <th>Received Size</th>
                            <th>Regrinding Reason</th>
                        </tr>
                        <tr>
                            <td> <?=$dataRow->full_name?> </td>
                            <td> <?=$dataRow->batch_no?> </td>
                            <td> <?=$dataRow->rev_no?> </td>
                            <td> <?=$dataRow->grn_data?> </td>
                            <td> <?=$dataRow->regrinding_reason?> </td>
                        </tr>
                    </table>
                </div>
            </div>
            <input type="hidden" name="id" id="id" value="<?=$dataRow->id?>">
            <div class="col-md-3 form-group">
                <label for="trans_status">Decision</label>
                <select name="trans_status" id="trans_status" class="form-control single-select req" >
                    <option value="">Select</option>
                    <option value="2">Approved</option>
                    <option value="3">Scrap</option>
                    <option value="4">Reregrinding</option>
                    <option value="5">Convert To Other</option>
                </select>
                <div class="error trans_status"></div>
            </div>
            <div class="col-md-4 form-group req conversionDiv" style="display: none;">
                <label for="converted_item_id">Converted Item</label>
                <select name="converted_item_id" id="converted_item_id" class="form-control large-select2 req" data-item_type="" data-category_id="" data-family_id="" autocomplete="off" data-default_id="<?= (!empty($dataRow->req_item_id)) ? $dataRow->req_item_id : "" ?>" data-default_text="<?= (!empty($dataRow->full_name)) ? $dataRow->full_name : "" ?>" data-url="items/getDynamicItemList">
                    <option value="">Select Item</option>
                </select>
            </div>
            <div class="col-md-9 from-group remarkDiv">
                <label for="item_remark">Remark</label>
                <input type="text" class="form-control" name="item_remark" id="item_remark">
            </div>  
        </div>
    </div>
</form>
<script>
    $(document).ready(function() {
        $(document).on("change", "#trans_status", function() {
            var trans_status = $(this).val();
            if(trans_status == 5){
                $(".conversionDiv").show();
                $(".remarkDiv").attr("class",'col-md-5 from-group remarkDiv');
                let dataSet = {};
                setTimeout(function(){
                    getDynamicItemList(dataSet);
                },20);
            }else{
                $(".conversionDiv").hide();
                $(".remarkDiv").attr("class",'col-md-9 from-group remarkDiv');
            }

        });
    });
</script>