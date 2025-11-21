<form enctype="multpart/form-data">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" /> 
            <input type="hidden" name="item_type" value="<?= (!empty($dataRow->item_type)) ? $dataRow->item_type : ""; ?>" />

            <div class="col-md-6 form-group">
                <label for="hsn_req">HSN Req?</label>
                <select name="hsn_req" id="hsn_req" class="form-control">
                    <option value="1" <?= (!empty($dataRow->hsn_req) && $dataRow->hsn_req == 1) ? "selected" : "" ?>>Yes</option>
                    <option value="0" <?= (!empty($dataRow->hsn_req) && $dataRow->hsn_req == 0) ? "selected" : "" ?>>No</option>
                </select>
            </div>
            <div class="col-md-6 form-group">
                <label for="hsn_code">HSN Code</label>
                <select name="hsn_code" id="hsn_code" class="form-control single-select req">
                    <option value="">Select HSN Code</option>
                    <?php
                    foreach ($hsnData as $row) :
                        $selected = (!empty($dataRow->hsn_code) && $dataRow->hsn_code == $row->hsn) ? "selected" : "";
                        $desc = '';
                        if (!empty($row->description)) {
                            $desc = ' - ' . $row->description;
                        }
                        echo '<option value="' . floatVal($row->hsn) . '" ' . $selected . '>' . floatVal($row->hsn) . $desc . '</option>';
                    endforeach;
                    ?>
                </select>
            </div>
        </div>
    </div>
</form>