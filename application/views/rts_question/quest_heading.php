<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:''?>" />
            <input type="hidden" name="type" id="type" value="2">

            <div class="col-md-12 form-group">
				<label for="description">Question Heading</label>
				<input type="text" id="description" name="description" class="form-control req" value="<?=(!empty($dataRow->description))?$dataRow->description:''?>" />
			</div>
            
        </div>
    </div>
</form>