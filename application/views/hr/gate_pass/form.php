<form>
<div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />   
          
            <div class="col-md-6 form-group">
            <label for="emp_id">Employee</label>
                <select name="emp_id" id="emp_id" class="form-control single-select req">
                    <option value="">Select Employee</option>
                    <?php
                        foreach($empData as $row):
                            $selected = (!empty($dataRow->emp_id) && $row->id == $dataRow->emp_id)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>['.$row->emp_code.'] '.$row->emp_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-6 form-group">
                <label for="out_time">Out Time</label>
                <input type="datetime-local" name="out_time" id="out_time" class="form-control" value="<?=(!empty($dataRow->out_time))?$dataRow->out_time:date("Y-m-d H:i:s")?>">
            </div>
            <div class="col-md-12 form-group">
                <label for="reason">Reason</label>
                <input type="text" name="reason" class="form-control" value="<?= (!empty($dataRow->reason)) ? $dataRow->reason : "" ?>" />
            </div>   
        </div>
    </div>
</form>