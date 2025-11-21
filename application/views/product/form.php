<form enctype="multpart/form-data">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
            <input type="hidden" name="item_type" value="<?= (!empty($dataRow->item_type)) ? $dataRow->item_type : 1; ?>" />

            <div class="col-md-3 form-group">
                <label for="item_code">Item Code</label>
                <input type="text" name="item_code" class="form-control" value="<?= (!empty($dataRow->item_code)) ? $dataRow->item_code : ""; ?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="item_name">Item Name</label>
                <input type="text" name="item_name" class="form-control req" value="<?=htmlentities((!empty($dataRow->item_name)) ? $dataRow->item_name : "")?>" />
                <input type="hidden" name="full_name" class="form-control " value="" />
            </div>
            <div class="col-md-3 form-group">
                <label for="part_no">Item No.</label>
                <input type="text" name="part_no" class="form-control" value="<?=htmlentities((!empty($dataRow->part_no)) ? $dataRow->part_no : "")?>" />
            </div>

            <div class="col-md-3">
                <label for="unit_id">Unit</label>
                <select name="unit_id" id="unit_id" class="form-control single-select req">
                    <option value="0">--</option>
                    <?php
                    foreach ($unitData as $row) :
                        $selected = (!empty($dataRow->unit_id) && $dataRow->unit_id == $row->id) ? "selected" : "";
                        echo '<option value="' . $row->id . '" ' . $selected . '>[' . $row->unit_name . '] ' . $row->description . '</option>';
                    endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label for="category_id">Category</label>
                <select name="category_id" id="category_id" class="form-control single-select req">
                    <option value="0">Select</option>
                    <?php
                        foreach ($categoryList as $row) :
                            $selected = (!empty($dataRow->category_id) && $dataRow->category_id == $row->id) ? "selected" : "";
                            echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->category_name . '</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="fg_id">Product Type</label>
                <select name="fg_id" id="fg_id" class="form-control single-select">
                    <option value="1" <?=(!empty($dataRow->fg_id) && $dataRow->fg_id == 1)?"selected":""?>>Semi Finish</option>
                    <option value="2" <?=(!empty($dataRow->fg_id) && $dataRow->fg_id == 2)?"selected":""?>>Finish</option>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="hsn_code">HSN Code</label>
                <select name="hsn_code" id="hsn_code" class="form-control single-select">
                    <option value="">Select HSN Code</option>
                    <?php
                        foreach ($hsnData as $row) :
                            $selected = (!empty($dataRow->hsn_code) && $dataRow->hsn_code == $row->hsn) ? "selected" : "";
                            echo '<option value="' . floatVal($row->hsn) . '" ' . $selected . '>' . floatVal($row->hsn) . '</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="active">Active</label>
                <select name="active" id="active" class="form-control">
                    <option value="1" <?=(!empty($dataRow->active) && $dataRow->active == 1)?"selected":""?>>Active</option>
                    <option value="0" <?=(!empty($dataRow->active) && $dataRow->active == 0)?"selected":""?>>De-active</option>
                    <option value="2" <?=(!empty($dataRow->active) && $dataRow->active ==2)?"selected":((!empty($active) && $active == 2)?'selected':'')?>>Enquiry</option>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label for="deactive_reason">Reason (If Deactive)</label>
                <input type="text" name="deactive_reason" class="form-control" value="<?=htmlentities((!empty($dataRow->deactive_reason)) ? $dataRow->deactive_reason : "")?>" />
            </div>
            

            <div class="col-md-2 form-group">
                <label for="batch_stock">Batchwise Stock ?</label>
                <select name="batch_stock" id="batch_stock" class="form-control">
                    <option value="1" <?=(!empty($dataRow->batch_stock) && $dataRow->batch_stock == 1)?"selected":""?>>Yes</option>
                    <option value="0" <?=(!empty($dataRow->batch_stock) && $dataRow->batch_stock == 0)?"selected":""?>>No</option>
                </select>
            </div>

            <div class="col-md-2 form-group">
                <label for="serial_stock">Serial No Stock ?</label>
                <select name="serial_stock" id="serial_stock" class="form-control">
                    <option value="1" <?=(!empty($dataRow->serial_stock) && $dataRow->serial_stock == 1)?"selected":""?>>Yes</option>
                    <option value="0" <?=(!empty($dataRow->serial_stock) && $dataRow->serial_stock == 0)?"selected":""?>>No</option>
                </select>
            </div>
            <div class="col-md-2 form-group">
                <label for="item_status">Item Status</label>
                <select name="item_status" id="item_status" class="form-control">
                    <option value="1" <?=(!empty($dataRow->item_status) && $dataRow->item_status == 1)?"selected":""?>>In Used</option>
                    <option value="2" <?=(!empty($dataRow->item_status) && $dataRow->item_status == 2)?"selected":""?>>Hold</option>
                    <option value="3" <?=(!empty($dataRow->item_status) && $dataRow->item_status == 3)?"selected":""?>>Phase Out</option>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="serial_prefix">Serial No. Prefix (if yes)</label>
                <input type="text" name="serial_prefix" class="form-control" value="<?=htmlentities((!empty($dataRow->serial_prefix)) ? $dataRow->serial_prefix : "")?>" />
            </div>

            <div class="col-md-2 form-group">
                <label for="drawing_no">Drawing No</label>
                <input type="text" name="drawing_no" class="form-control" value="<?= (!empty($dataRow->drawing_no)) ? $dataRow->drawing_no : "" ?>" />
            </div>
        
            <div class="col-md-2 form-group">
                <label for="rev_no">Revision No</label>
                <input type="text" name="rev_no" class="form-control" value="<?= (!empty($dataRow->rev_no)) ? $dataRow->rev_no : "" ?>" />
            </div>

            <div class="col-md-2 form-group">
                <label for="wt_pcs">Weight Per Pcs</label>
                <input type="text" name="wt_pcs" class="form-control" value="<?= (!empty($dataRow->wt_pcs)) ? $dataRow->wt_pcs : "" ?>" />
            </div>
           
            <div class="col-md-3 form-group">
                <label for="material_grade">Material Grade</label>
                <select name="material_grade" id="material_grade" class="form-control single-select">
                    <option value="">Select Material Grade</option>
                    <?php
                        foreach($materialGrade as $row):
                            $selected = (!empty($dataRow->material_grade) && $dataRow->material_grade == $row->material_grade)?"selected":"";
                            echo '<option value="'.$row->material_grade.'" '.$selected.'>'.$row->material_grade.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-3 form-group">
				<label for="heat_treatment">Heat Treatment</label>
				<select name="heat_treatment" id="heat_treatment" class="form-control">
					<option value="">Select</option>
					<option value="0" <?= (!empty($dataRow->id) && $dataRow->heat_treatment == 0) ? "selected" : "" ?>>No</option>
					<option value="1" <?= (!empty($dataRow->id) && $dataRow->heat_treatment == 1) ? "selected" : "" ?>>Yes</option>
				</select>
			</div>
            <div class="col-md-3 form-group">
                <label for="item_image">Item Image</label>
                <input type="file" name="item_image" class="form-control-file" />
            </div>
            
            <div class="col-md-9 form-group">
                <label for="description">Product Description</label>
                <textarea name="note" id="note" class="form-control" rows="1"><?=(!empty($dataRow->note))?$dataRow->note:""?></textarea>
            </div>
            <div class="col-md-12 form-group">
                <label for="note">Remark</label>
                <textarea name="note" id="note" class="form-control" rows="1"><?=(!empty($dataRow->note))?$dataRow->note:""?></textarea>
            </div>
           
        </div>
    </div>
</form>
<script src="<?php echo base_url();?>assets/js/custom/master-form.js?v=<?=time()?>"></script>