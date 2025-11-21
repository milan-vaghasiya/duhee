<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="ext_ht_id" id="ext_ht_id" value="<?=$ext_ht_id ?>" />
            <div class="col-md-12 form-group">
                <label for="approve_date">Approved Date </label>
                <input type="date" name="approve_date" class="form-control req" value="<?=(!empty($dataRow->approve_date))?$dataRow->approve_date:date("Y-m-d")?>" />
            </div>
            <div class="col-md-12 form-group">
                <label for="approve_by">Approved By</label>
                <input type="text" name="approve_by" class="form-control req" value="<?=(!empty($dataRow->approve_by))?$dataRow->approve_by:""?>" />
            </div>
            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <textarea type="text" name="remark" class="form-control req"><?=(!empty($dataRow->remark))?$dataRow->remark:""?></textarea>
            </div>
        </div>
    </div>
</form>