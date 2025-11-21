<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
                
            <div class="col-md-6 form-group">
                <label for="char_id">Characteristics</label>
                <select name="char_id" id="char_id" class="form-control single-select req">
                    <option value="">Select</option>
                    <?php
                        foreach ($charNames as $row):
                            $selected = (!empty($dataRow->char_id) && $dataRow->char_id == $row->id) ? "selected" : "";
                            echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->characteristics . '</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-6 form-group">
                <label for="param_name">Param Name</label>
                <input type="text" name="param_name" class="form-control req" value="<?=(!empty($dataRow->param_name))?$dataRow->param_name:""; ?>" />
            </div>
            <div class="col-md-12 form-group">
                <label for="specification">Default Specification</label>
                <input type="text" name="specification" class="form-control" value="<?=(!empty($dataRow->specification))?$dataRow->specification:""; ?>" />
            </div>
            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <textarea name="remark" id="remark" rows="2" class="form-control"><?=(!empty($dataRow->remark))?$dataRow->remark:""; ?></textarea>
            </div>
        </div>
    </div>
</form>
