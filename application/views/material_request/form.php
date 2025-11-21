<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />
            
            <div class="col-md-6 form-group">
                <label for="req_date">Request Date</label>
                <input type="date" name="req_date" id="req_date" class="form-control req" value="<?=(!empty($dataRow->id))?$dataRow->req_date:date("Y-m-d")?>" max="<?=date("Y-m-d")?>" >
            </div>
            <div class="col-md-6 form-group">
                <label for="material_type">Material Type</label>
                <select name="material_type" id="material_type" class="form-control">
                    <option value="3" <?=(!empty($dataRow) && $dataRow->material_type == 3)?"selected":""?>>Raw Material</option>
                    <option value="2" <?=(!empty($dataRow) && $dataRow->material_type == 2)?"selected":""?>>Consumable</option>
                </select>            
            </div>
            <div class="col-md-6 form-group">
                <label for="">Job Card No.</label>
                <select name="job_card_id" id="job_card_id" class="form-control single-select">
                    <option value="">Select Job Card No.</option>
                    <option value="-1" <?=(!empty($dataRow->id) && $dataRow->job_card_id == "-1")?"selected":""?>>General Request</option>
                    <?php
                        foreach($jobCardData as $row):
                            $selected = (!empty($dataRow->id) && $dataRow->job_card_id == $row->id)?"selected":"";
                            if(!empty($dataRow)):
                                echo '<option value="'.$row->id.'" '.$selected.'>'.$row->job_prefix.$row->job_no.'</option>';
                            else:
                                if($row->order_status != 4 && $row->order_status != 5):
                                    echo '<option value="'.$row->id.'" '.$selected.'>'.$row->job_prefix.$row->job_no.'</option>';
                                endif;
                            endif;
                        endforeach;
                    ?>
                </select>
                
            </div>
            <div class="col-md-6 form-group">
                <label for="process_id">Process Name</label>
                <select name="process_id" id="process_id" class="form-control">
                    <option value="">Select Process Name</option>
                    <?php
                        foreach($processList as $row):
                            $selected = (!empty($dataRow->process_id) && $dataRow->process_id == $row->id)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->process_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-12 form-group">
                <label for="req_item_id">Request Item Name</label>
                <select name="req_item_id" id="req_item_id" class="form-control single-select req">
                    <option value="">Select Item Name</option>
                    <?php    
                        $stock = "";                  
                        foreach($itemData as $row):
                            $selected = "";
                            if(empty($dataRow)):
                                if($row->item_type == 3):
                                    echo '<option value="'.$row->id.'" data-stock="'.$row->qty.' '.$row->unit_name.'" '.$selected.'>'.$row->item_name.'</option>';  
                                endif;  
                            else:
                                $selected = ($dataRow->req_item_id == $row->id)?"selected":"";
                                $stock =  ($dataRow->req_item_id == $row->id)?$row->qty.' '.$row->unit_name:"";  
                                if($row->item_type == $dataRow->material_type):             
									echo '<option value="'.$row->id.'" data-stock="'.$row->qty.' '.$row->unit_name.'" '.$selected.'>'.$row->item_name.'</option>';     
								endif;                       
                            endif;                          
                        endforeach;
                    ?>
                </select>      
            </div>
            <div class="col-md-6 form-group">
                <label for="stock_qty">Stock Qty.</label>
                <input type="text" id="stock_qty" placeholder="Item Stock Qty." class="form-control" value="<?=$stock?>" readonly />
            </div>
            <div class="col-md-6 form-group">
                <label for="req_qty">Request Qty.</label>
                <input type="number" name="req_qty" id="req_qty" class="form-control floatOnly req" min="0" value="<?=(!empty($dataRow->req_qty))?$dataRow->req_qty:""?>">
                
            </div>
            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="<?=(!empty($dataRow->remark))?$dataRow->remark:""?>">
            </div>
        </div>
    </div>
</form>