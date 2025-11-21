<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <?php
                $penalty_hrs = 'Penalty Hours'; $minute_day = 'Max. Minutes/Day'; $day_month = 'Max. Day/Month';
                if(!empty($dataRow->policy_type) && $dataRow->policy_type == 3):
                    $penalty_hrs = 'Full Leave(Day)'; 
                    $minute_day = 'Short Leave(Hours)'; 
                    $day_month = 'Half Leave(Hours)';
                endif;
            ?>

            <div class="col-md-12 form-group">
                <label for="policy_type">Policy Type</label>
                <select name="policy_type" id="policy_type" class="form-control single-select">
                    <?php
                    foreach ($policyType as $key => $value) :
                        $selected = (!empty($dataRow->policy_type) && $dataRow->policy_type == $key) ? "selected" : "";
                        echo '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
                    endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-12 form-group">
                <label for="policy_name">Policy Name</label>
                <input type="text" name="policy_name" class="form-control req" value="<?=(!empty($dataRow->policy_name))?$dataRow->policy_name:""; ?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="penalty">Penalty?</label>
                <select name="penalty" id="penalty" class="form-control single-select req">
                    <option value="0" <?=(!empty($dataRow->penalty) && $dataRow->penalty == 0)?'selected':'';?>>No</option>
                    <option value="1" <?=(!empty($dataRow->penalty) && $dataRow->penalty == 1)?'selected':'';?>>Yes</option>
                </select>
            </div>
            <div class="col-md-6 form-group">
                <label for="penalty_hrs" id="penalty_hrs"><?=$penalty_hrs?></label>
                <input type="text" name="penalty_hrs" class="form-control numericOnly req" value="<?=(!empty($dataRow->penalty_hrs))?$dataRow->penalty_hrs:"0"; ?>" /> 
            </div>
            <div class="col-md-6 form-group">
                <label for="minute_day" id="minute_day"><?=$minute_day?></label>
                <input type="text" name="minute_day" class="form-control numericOnly req" value="<?=(!empty($dataRow->minute_day))?$dataRow->minute_day:"0"; ?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="day_month" id="day_month"><?=$day_month?></label>
                <input type="text" name="day_month" class="form-control numericOnly req" value="<?=(!empty($dataRow->day_month))?$dataRow->day_month:"0"; ?>" />
            </div>

        </div>
    </div>
</form>
<script>
$(document).ready(function(){
    $(document).on('change','#policy_type',function(){
        var policy_type = $(this).val();
        if (policy_type != 3) {
            $('#penalty_hrs').html('');
            $('#penalty_hrs').html('Penalty Hours <strong class="text-danger">*</strong>');
            
            $('#minute_day').html('');
            $('#minute_day').html('Max. Minutes/Day <strong class="text-danger">*</strong>');
            
            $('#day_month').html('');
            $('#day_month').html('Max. Day/Month <strong class="text-danger">*</strong>');
        } else {
            $('#penalty_hrs').html('');
            $('#penalty_hrs').html('Full Leave(Day) <strong class="text-danger">*</strong>');
            
            $('#minute_day').html('');
            $('#minute_day').html('Short Leave(Hours) <strong class="text-danger">*</strong>');
            
            $('#day_month').html('');
            $('#day_month').html('Half Leave(Hours) <strong class="text-danger">*</strong>');
        }
    });
});
</script>