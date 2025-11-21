<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <div class="col-md-6 form-group">
                <label for="holiday_date">Holiday Date</label>
                <input type="date" name="holiday_date" class="form-control req" value="<?=(!empty($dataRow->holiday_date))?$dataRow->holiday_date:date('Y-m-d'); ?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="holiday_type">Holiday Type</label>
                <select name="holiday_type" id="holiday_type" class="form-control req" >
                    <option value="1" <?=(!empty($dataRow->holiday_type) && $dataRow->holiday_type == "1")?"selected":""?>>Public Holiday</option>
                    <option value="2" <?=(!empty($dataRow->holiday_type) && $dataRow->holiday_type == "2")?"selected":""?>>Special Holiday</option>
                </select>
            </div>
            <div class="col-md-12 form-group">
                <label for="title">Title</label>
                <input type="text" name="title" class="form-control req" value="<?=(!empty($dataRow->title))?$dataRow->title:""; ?>" />
            </div>
        </div>
    </div>
</form>