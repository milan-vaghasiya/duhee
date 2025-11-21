<form>
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12">
                <h5 class="text-dark"><span id="productName"></span></h5>
                <div class="error gerenal_error"></div>
            </div>
            <input type="hidden" name="item_id" class="item_id" value="" />
            
            <div class="col-md-3">
                <label for="process_id">Process</label>
                <select id="process_id" class="form-control single-select req">
                    <option value="">Select Process</option>  
                    <option value="0">Initial Stage</option>                  
                    <?php
                        foreach($process as $row):
                            echo '<option value="'.$row->id.'">'.$row->process_name.'</option>';
                        endforeach;
                    ?>                   
                </select>
            </div>
            <div class="col-md-4">
                <label for="kit_item_id">Used To Be Item</label>
                <select id="kit_item_id" class="form-control single-select req">
                    <option value="">Select Item</option>
                    <?php
                        foreach($rawMaterial as $row):
                            echo '<option value="'.$row->id.'" data-unit_id="'.$row->unit_id.'">'.$row->item_code.' -'.$row->item_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="kit_item_qty">Weight Per Piece</label>
                <input type="number" id="kit_item_qty" class="form-control floatOnly req" value="" min="0" />
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-outline-success waves-effect waves-light mt-30 save-form" onclick="AddKitRow();" ><i class="fa fa-plus"></i> Add Item</button>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="table-responsive">
            <table id="productKit" class="table table-bordered align-items-center">
                <thead class="thead-info">
                    <tr>
                        <th style="width:5%;">#</th>
                        <th>Process</th>
                        <th>Item Name</th>
                        <th>Weight Per Piece</th>
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
                                                '.((!empty($row->process_id))?$row->process_name:"Initial Stage").'
                                                <input type="hidden" name="process_id[]" value="'.$row->process_id.'">
                                            </td>
                                            <td>
                                                '.$row->item_name.'
                                                <input type="hidden" name="ref_item_id[]" class="processItem'.$row->process_id.'" value="'.$row->ref_item_id.'">
                                                <input type="hidden" name="id[]" value="'.$row->id.'">
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
    </div>
</form>