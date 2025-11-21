<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
			<div class="col-md-12 form-group">
                <label for="family_name">Parameters</label>
                <input type="text" name="family_name" class="form-control req" value="<?=(!empty($dataRow->family_name))?$dataRow->family_name:""?>" />
            </div>
			<div class="col-md-12 form-group">
                <label for="type">Type</label>
                <select name="type" id="type" class="form-control single-select">
                    <?php
                        foreach ($typeArr as $row) :
                            $selected = (!empty($dataRow->type) && $dataRow->type == $row['key']) ? "selected" : "";
                            echo '<option value="' . $row['key'] . '" ' . $selected . '>' . $row['val'] . '</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <textarea name="remark" class="form-control" rows="3" ><?=(!empty($dataRow->remark))?$dataRow->remark:"";?></textarea>
            </div>
        </div>
    </div>
</form>