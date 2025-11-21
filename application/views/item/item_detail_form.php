<form enctype="multpart/form-data">
    <div class="col-md-12">
        <div class="row">
            <?php
            $itype = 1;
            $itype = (!empty($dataRow->item_type)) ? $dataRow->item_type : $item_type;
            ?>
            <input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
            <input type="hidden" name="item_type" value="<?= $itype ?>" />

            <div class="col-md-3 form-group">
                <label for="item_code">Item Code</label>
                <input type="text" name="item_code" class="form-control" value="<?= (!empty($dataRow->item_code)) ? $dataRow->item_code : ""; ?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="item_name">Item Name</label>
                <input type="text" name="item_name" class="form-control req" value="<?= htmlentities((!empty($dataRow->item_name)) ? $dataRow->item_name : "") ?>" />
                <input type="hidden" name="full_name" class="form-control " value="" />
            </div>
            <div class="col-md-2 form-group">
                <label for="part_no">Item No.</label>
                <input type="text" name="part_no" class="form-control req" value="<?= htmlentities((!empty($dataRow->part_no)) ? $dataRow->part_no : "") ?>" />
            </div>
            <div class="col-md-2">
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
            <div class="col-md-2 form-group">
                <label for="price">Price</label>
                <input type="text" name="price" class="form-control floatOnly" value="<?=htmlentities((!empty($dataRow->price)) ? $dataRow->price : "")?>" />
            </div>

            <div class="col-md-3 form-group">
                <label for="family_id">Family Group</label>
                <select name="family_id" id="family_id" class="form-control single-select">
                    <option value="0">Select</option>
                    <?php
                    foreach ($familyGroup as $row) :
                        $selected = (!empty($dataRow->family_id) && $dataRow->family_id == $row->id) ? "selected" : "";
                        echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->family_name . '</option>';
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
                <label for="application_industry">Industry</label>
                <select name="application_industry" id="application_industry" class="form-control single-select req">
                    <option value="0">Select Application Industry</option>
                    <?php
                    foreach ($industryList as $row) :
                        $selected = (!empty($dataRow->application_industry) && $dataRow->application_industry == $row->id) ? "selected" : "";
                        echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->title . '</option>';
                    endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label for="class">Class</label>
                <select name="class" id="class" class="form-control single-select req">
                    <option value="0">Select Class</option>
                    <?php
                    foreach ($classList as $row) :
                        $selected = (!empty($dataRow->class) && $dataRow->class == $row->id) ? "selected" : "";
                        echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->title . '</option>';
                    endforeach;
                    ?>
                </select>
            </div>
            

            <div class="col-md-3 form-group">
                <label for="active">Active</label>
                <select name="active" id="active" class="form-control">
                    <option value="1" <?= (!empty($dataRow) && $dataRow->active == 1) ? "selected" : "" ?>>Active</option>
                    <option value="0" <?= (!empty($dataRow) && $dataRow->active == 0) ? "selected" : "" ?>>De-active</option>
                </select>
            </div>

            <div class="col-md-9 form-group">
                <label for="deactive_reason">Reason (If Deactive)</label>
                <input type="text" name="deactive_reason" class="form-control req" value="<?= htmlentities((!empty($dataRow->deactive_reason)) ? $dataRow->deactive_reason : "") ?>" />
            </div>

            <div class="col-md-3 form-group">
                <label for="item_status">Item Status</label>
                <select name="item_status" id="item_status" class="form-control">
                    <option value="1" <?= (!empty($dataRow->item_status) && $dataRow->item_status == 1) ? "selected" : "" ?>>In Use</option>
                    <option value="2" <?= (!empty($dataRow->item_status) && $dataRow->item_status == 2) ? "selected" : "" ?>>Hold</option>
                    <option value="3" <?= (!empty($dataRow->item_status) && $dataRow->item_status == 3) ? "selected" : "" ?>>Phase Out</option>
                </select>
            </div>

            <div class="col-md-9 form-group">
                <label for="item_status_reason">Reason <small>(If other then In Use)</small></label>
                <input type="text" name="item_status_reason" class="form-control req" value="<?= htmlentities((!empty($dataRow->item_status_reason)) ? $dataRow->item_status_reason : "") ?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="item_image">Item Image</label>
                <input type="file" name="item_image" class="form-control-file" accept="image/*" />
            </div>
            <div class="col-md-9 form-group">
                <label for="note">Product Description</label>
                <textarea name="note" id="note" class="form-control" rows="1"><?= (!empty($dataRow->note)) ? $dataRow->note : "" ?></textarea>
            </div>
            <div class="col-md-12 form-group">
                <label for="description">Remark</label>
                <textarea name="description" id="description" class="form-control" rows="1"><?= (!empty($dataRow->description)) ? $dataRow->description : "" ?></textarea>
            </div>

        </div>
    </div>
</form>
<script src="<?php echo base_url(); ?>assets/js/custom/master-form.js?v=<?= time() ?>"></script>