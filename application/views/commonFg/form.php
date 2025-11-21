<form>
    <div class="col-md-12">
        <div class="row">
			<input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <div class="col-md-4 form-group">
                <label for="item_code">Item Code</label>
                <input type="text" name="item_code" class="form-control req" value="<?=(!empty($dataRow->item_code))?$dataRow->item_code:""; ?>" />
            </div>
            
            <div class="col-md-4 form-group">
                <label for="item_name">Item Name</label>
                <input type="text" name="item_name" class="form-control req" value="<?=(!empty($dataRow->item_name))?$dataRow->item_name:""; ?>" />
            </div>
            
            <div class="col-md-4 form-group">
                <label for="finishGoodsSelect">Finish Goods</label>
                <select name="finishGoodsSelect" id="finishGoodsSelect" data-input_id="make_brand" class="form-control jp_multiselect req" multiple="multiple">
                    <?php
                    foreach ($finishGoodsList as $row) :
                        $selected = (!empty($dataRow->make_brand) && (in_array($row->id,explode(',', $dataRow->make_brand)))) ? "selected" : "";
                        echo '<option value="' . $row->id . '" ' . $selected . '>['.$row->item_code.'] ' . $row->item_name . '</option>';
                    endforeach;
                    ?>
                </select>
                <input type="hidden" name="make_brand" id="make_brand" value="<?=(!empty($dataRow->make_brand) ? $dataRow->make_brand :"")?>" />
            </div>
            
			<div class="col-md-12 form-group">
                <label for="description">Remark</label>
                <input type="text" name="description" class="form-control" value="<?= (!empty($dataRow->description)) ? $dataRow->description : "" ?>" />
            </div>
        </div>    
       
        <hr>
        <h5>BOM Detalis</h5>
        <hr>
        
        <div class="row">
            <div class="col-md-5 form-group">
                <label for="ref_item_id">Raw Material</label>
                <select name="ref_item_id" id="ref_item_id" class="form-control single-select req">
                    <option value="">Select Raw Material</option>
                    <?php
                        foreach($rawMaterialList as $row):
                            echo '<option value="' . $row->id . '">' . $row->item_name . '</option>';
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-5">
                <label for="qty">Quantity</label>
                <input type="text" id="qty" class="form-control floatOnly req" value="" />
            </div>

            <div class="col-md-2">
                <button type="button" class="btn btn-outline-success waves-effect waves-light mt-30 save-form" onclick="AddKitRow();" ><i class="fa fa-plus"></i> Add Bom</button>
            </div>
        </div>

        <hr>
        <div class="error item_name_error"></div>
        <div class="row">
            <div class="table-responsive">
            <table id="productKit" class="table table-bordered align-items-center">
                <thead class="thead-info">
                    <tr>
                        <th style="width:5%;">#</th>
                        <th>Row Material</th>
                        <th>Qty</th>
                        <th class="text-center" style="width:10%;">Action</th>
                    </tr>
                </thead>
                <tbody id="kitItems">
                    <?php
                        if(!empty($productKitData)):
                            $i=1;
                            foreach($productKitData as $row):
                                echo '<tr>
                                            <td>'.$i++.'</td>
                                            <td>
                                                '.$row->item_name.'
                                                <input type="hidden" name="ref_item_id[]" value="'.$row->ref_item_id.'">
                                                <input type="hidden" name="kit_id[]" value="'.$row->id.'">
                                            </td>
                                            <td>
                                                '.$row->qty.'
                                                <input type="hidden" name="qty[]" value="'.$row->qty.'">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" onclick="RemoveKit(this);" class="btn btn-outline-danger waves-effect waves-light permission-remove"><i class="ti-trash"></i></button>
                                            </td>
                                        </tr>';
                            endforeach;
                        endif;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</form>