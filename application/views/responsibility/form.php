<form>
	<div class="col-md-12">
        <div class="row">
			<input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />
			<input type="hidden" name="type" value="6" />
			
			<div class="col-md-12 form-group">
				<label for='remark' class="control-label">Responsibility</label>
				<input type="text" id="remark" name="remark" class="form-control req" value="<?=(!empty($dataRow->remark))?$dataRow->remark:""?>">				
			</div>
		</div>
	</div>	
</form>