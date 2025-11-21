<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />

            <div class="col-md-4 form-group">
                <label for="dispatch_date">Issue Date</label>
                <input type="date" name="dispatch_date" id="dispatch_date" class="form-control" min="<?=(!empty($dataRow))?$dataRow->req_date:$this->startYearDate?>" max="<?=date("Y-m-d")?>" value="<?=date("Y-m-d")?>">
            </div>

            <div class="col-md-4 form-group">
                <label for="material_type">Material Type</label>
                <?php if(empty($dataRow->id)): ?>
                    <select name="material_type" id="material_type" class="form-control">
                        <option value="3">Raw Material</option>
                        <option value="2">Consumable</option>
                    </select>
                <?php else: ?>
                    <input type="text" class="form-control" value="<?=(!empty($dataRow->id) && $dataRow->material_type == 3)?"Raw Material":"Consumable"?>" readonly />
                    <input type="hidden" name="material_type" value="<?=(!empty($dataRow->id))?$dataRow->material_type:""?>">
                <?php endif; ?>                
            </div>

            <div class="col-md-4 form-group">
                <label for="collected_by">Material Collected By</label>
                <input type="text" name="collected_by" id="collected_by" placeholder="Enter Person Name" class="form-control"  value="<?=(!empty($dataRow->collected_by))?$dataRow->collected_by:""?>" />
            </div>

            <div class="col-md-2 form-group">
                <label for="">Job Card No.</label>
                <select name="job_card_id" id="job_card_id" class="form-control single-select">
                    <option value="">Select Job Card No.</option>
                    <option value="-1" <?=(!empty($dataRow->id) && $dataRow->job_card_id == "-1")?"selected":""?>>General Issue</option>
                    <?php
                        foreach($jobCardData as $row):
                            $selected = (!empty($dataRow->id) && $dataRow->job_card_id == $row->id)?"selected":"";
                            if(!empty($dataRow)):
                                echo '<option value="'.$row->id.'" '.$selected.'>'.$row->job_prefix.$row->job_no.'</option>';
                            else:
                                if($row->order_status != 2):
                                    echo '<option value="'.$row->id.'" '.$selected.'>'.$row->job_prefix.$row->job_no.'</option>';
                                endif;
                            endif;
                        endforeach;
                    ?>
                </select>                
            </div>    
            
            <div class="col-md-3 form-group">
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

            <div class="col-md-3 form-group">
                <label for="dispatch_item_id">Issue Item Name</label>
                <select name="dispatch_item_id" id="dispatch_item_id" class="form-control single-select req">
                    <option value="">Select Item Name</option>
                    <?php                    
                        foreach($itemData as $row):
                            $selected = "";
                            if(empty($dataRow)):
                                if($row->item_type == 3):
                                    echo '<option value="'.$row->id.'" '.$selected.'>'.$row->item_name.'</option>';  
                                endif;  
                            else:
                                $selected = (!empty($dataRow->dispatch_item_id) && $dataRow->dispatch_item_id == $row->id)?"selected":(($dataRow->req_item_id == $row->id)?"selected":"disabled");  
                                if($row->item_type == $dataRow->material_type):             
									echo '<option value="'.$row->id.'" '.$selected.'>'.$row->item_name.'</option>';     
								endif;                 
                            endif;                          
                        endforeach;
                    ?>
                </select>                
            </div>

            <div class="col-md-2 form-group">
                <label for="dispatch_qty">Request Qty.</label>
                <input type="number" id="req_qty" class="form-control" value="<?=(!empty($dataRow->req_qty))?$dataRow->req_qty:0?>" readonly />
                <input type="hidden" name="dispatch_qty" id="dispatch_qty" class="form-control floatOnly" min="0" value="<?=(!empty($dataRow))?(($dataRow->dispatch_qty != "0.000")?$dataRow->dispatch_qty:"0"):"0"?>">                
            </div>

            <div class="col-md-2 form-group">
                <label for="pending_qty">Pending Qty.</label>
                <input type="number" id="pending_qty" class="form-control" value="<?=(!empty($dataRow->req_qty))?($dataRow->req_qty-$dataRow->dispatch_qty):0?>" readonly />
            </div>
            
            <div class="col-md-3 form-group lc">
                <label for="location_id">Store Location</label>
                <select id="location_id" class="form-control single-select1 model-select2 req">
                    <option value=""  data-store_name="">Select Location</option>
                    <?php
                        foreach($locationData as $lData):                            
                            echo '<optgroup label="'.$lData['store_name'].'">';
                            foreach($lData['location'] as $row):
                                echo '<option value="'.$row->id.'" data-store_name="'.$lData['store_name'].'">'.$row->location.' </option>';
                            endforeach;
                            echo '</optgroup>';
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label for="batch_no">Heat/Batch No.</label>
                <select id="batch_no" class="form-control single-select req">
                    <option value="">Select Batch No.</option>
                </select>
            </div>

            <div class="col-md-2 form-group">
                <label for="batch_stock">Stock Qty.</label>
                <input type="text" id="batch_stock" class="form-control" value="" readonly />
            </div>

            <div class="col-md-2 form-group">
                <label for="batch_qty">Issue Qty.</label>
                <input type="numbet" id="batch_qty" class="form-control floatOnly req" value="" />
            </div>

            <div class="col-md-2 form-group">
                <label>&nbsp;</label>
                <button type="button" class="btn waves-effect waves-light btn-primary btn-block addRow"><i class="fas fa-plus"></i> Add</button>
            </div>

            <div class="col-md-12 form-group">
                <div class="error general_batch_no"></div>
                <div class="table-responsive ">
                    <table id="issueItems" class="table table-striped table-borderless">
                        <thead class="thead-info">
                            <tr>
                                <th style="width:5%;">#</th>
                                <th>Location</th>
                                <th>Batch No.</th>
                                <th>Qty.</th>
                                <th style="width:10%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="tempItem">
                            <tr id="noData">
                                <td class="text-center" colspan="5">No Data Found</td>
                            </tr>
                        </tbody>
                    </table>
                    <?php
                        if(!empty($dataRow)):
                            if(!empty($batchTrans)):
                                foreach($batchTrans as $row):
                                    echo '<script>var postData={id:"",batch_no:"'.$row->batch_no.'",qty:"'.abs($row->qty).'",location_id:"'.$row->location_id.'",location_name:"[ '.$row->store_name.' ] '.$row->location.'"}; addRow(postData);</script>';
                                endforeach;
                            endif;
                        endif;
                    ?>
                </div>
            </div>

            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="<?=(!empty($dataRow->remark))?$dataRow->remark:""?>">
            </div>
        </div>
    </div>
</form>
<script>
$(document).ready(function(){
	$('.model-select2').select2({
		dropdownParent: $('.model-select2').parent()
	});
});
</script>