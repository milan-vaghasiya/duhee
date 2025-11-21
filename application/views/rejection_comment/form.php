<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="type" value="1" />
            <div class="col-md-12 form-group">
                <label for="remark">Rejection Reason</label>
                <textarea name="remark" class="form-control req" placeholder="Rejection Reason" ><?=(!empty($dataRow->remark))?$dataRow->remark:"";?></textarea>
            </div>
            <div class="col-md-12 form-group">
                <label for="process_id">Production Process</label>
                <select name="processSelect" id="processSelect" data-input_id="process_id" class="form-control jp_multiselect" multiple="multiple">
                    <?php
                    foreach ($processDataList as $row) :
                        $selected = (!empty($dataRow->process_id) && (in_array($row->id,explode(',', $dataRow->process_id)))) ? "selected" : "";
                        echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->process_name . '</option>';
                    endforeach;
                    ?>
                </select>
                <input type="hidden" name="process_id" id="process_id" value="<?=(!empty($dataRow->process_id) ? $dataRow->process_id:"")?>" />
            </div>
        </div>
    </div>
</form>