<form>
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12">
                <h5 class="text-dark"><span id="productName"></span></h5>
                <div class="error gerenal_error"></div>
            </div>
            <input type="hidden" name="item_id" class="item_id" value="<?=$item_id?>" />
            
            <div class="col-md-4">
                <label for="output_item_id">Item</label>
                <select id="output_item_id" class="form-control single-select req">
                    <option value="">Select Item</option>                
                    <?php
                        foreach($itemList as $row):
                            $selected = ($item_id == $row->id)?'selected':'';
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->full_name.'</option>';
                        endforeach;
                    ?>                   
                </select>
            </div>
          
            <div class="col-md-3">
                <label for="op_qty">Quantity</label>
                <input type="text" id="op_qty" class="form-control floatOnly req" value="" min="0" />
            </div>
            <div class="col-md-3">
                <label for="op_qty">Production Type</label>
                <select  id="production_type" class="form-control">
                    <option value="1">Finished</option>
                    <option value="2">Sami Finished</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-outline-success waves-effect waves-light mt-30 save-form" onclick="addOutputItemRow();" ><i class="fa fa-plus"></i> Add Item</button>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="table-responsive">
            <table id="productKit" class="table table-bordered align-items-center">
                <thead class="thead-info">
                    <tr>
                        <th style="width:5%;">#</th>
                        <th>Item Name</th>
                        <th>Qty</th>
                        <th>Production Type</th>
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
                                                '.$row->full_name.'
                                                <input type="hidden" name="output_item_id[]"  value="'.$row->output_item_id.'">
                                                <input type="hidden" name="id[]" value="'.$row->id.'">
                                            </td>
                                            <td>
                                                '.$row->qty.'
                                                <input type="hidden" name="qty[]" value="'.$row->qty.'">
                                            </td>
                                            <td>
                                                '.(($row->production_type == 1)?'Finished':'Semi Finished').'
                                                <input type="hidden" name="production_type[]" value="'.$row->production_type.'">
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
    </div>
</form>