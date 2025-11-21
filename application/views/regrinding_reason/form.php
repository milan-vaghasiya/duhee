<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="type" value="5" />

            <div class="col-md-12 form-group">
                <label for="remark">Regrinding Reason</label>
                <textarea name="remark" class="form-control req" placeholder="Rejection Reason" ><?=(!empty($dataRow->remark))?$dataRow->remark:"";?></textarea>
            </div>
        </div>
    </div>
</form>