<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" id="id" name="id" value="<?= !empty($dataRow->id) ? $dataRow->id : '' ?>">
            <input type="hidden" id="packing_type" name="packing_type" value="<?=$packing_type?>">
            <input type="hidden" id="item_id" name="item_id" value="<?= !empty($dataRow->item_id) ? $dataRow->item_id : '' ?>">
            <input type="hidden" id="packing_id" name="packing_id" value="<?= !empty($dataRow->packing_id) ? $dataRow->packing_id : '' ?>">
            <div class="col-md-3 form-group">
                <label for="entry_date">Date</label>
                <input type="date" name="entry_date" id="entry_date" class="form-control req" value="<?= !empty($dataRow->entry_date) ? $dataRow->entry_date : date("Y-m-d") ?>">
            </div>
            <div class="col-md-9 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="<?= !empty($dataRow->remark) ? $dataRow->remark : '' ?>">
            </div>
            <div class="error general_error"></div>
            <div class="col-md-12 mt-3">
                <div class="row form-group">
                    <div class="table-responsive">
                        <table id="pirTable" class="table table-bordered item-list-bb">
                            <thead id="theadData" class="thead-info">
                                <tr >
                                    <th>
                                        <input type="checkbox" id="master_checkbox" class="filled-in chk-col-success" value="true">
                                        <label for="master_checkbox" class="mr-3">
                                    </th>
                                    <th>Product/Process Char.</th>
                                    <th>Specification</th>
                                    <th>Qty Per (Bag/Box)</th>
                                    <th>Measurement Tech.</th>
                                   
                                </tr> 
                            </thead>
                            <tbody id="tbodyData">
                                <?php
                                if (!empty($tbodyData)) :
                                    echo $tbodyData;
                                else :
                                    echo "<tr><th colspan='5' class='text-center'>No data available.</th></tr>";
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
    $(document).ready(function(){
        $(document).on('click','#master_checkbox',function(){
            if($(this).prop('checked') == true){
                $(".dimensionCheck").prop('checked', true);  
            }else{
                $(".dimensionCheck").prop('checked', false);
            }
        });
    });
</script>