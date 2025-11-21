<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="type" value="<?=(!empty($dataRow->type))?$dataRow->type:$type; ?>" />

            <div class="col-md-12 form-group">
                <label for="title">Title</label>
                <input type="text" name="title" class="form-control req" value="<?=(!empty($dataRow->title))?$dataRow->title:"";?>" />
            </div>            
            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <textarea name="remark" id="remark" class="form-control"><?=(!empty($dataRow->remark))?$dataRow->remark:""?></textarea>
            </div>
        </div>
    </div>
</form>