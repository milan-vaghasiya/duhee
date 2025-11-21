<form enctype="multpart/form-data">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />   
            <div class="col-md-6 form-group">
                <label for="hsn">HSN</label>
                <input type="text" name="hsn" class="form-control numericOnly req" value="<?= (!empty($dataRow->hsn)) ? $dataRow->hsn : "" ?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="cgst">Cgst</label>
                <input type="number" name="cgst" class="form-control floatOnly" value="<?= (!empty($dataRow->cgst)) ? $dataRow->cgst : "" ?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="sgst">Sgst</label>
                <input type="number" name="sgst" class="form-control floatOnly" value="<?= (!empty($dataRow->sgst)) ? $dataRow->sgst : "" ?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="igst">Igst</label>
                <input type="number" name="igst" class="form-control floatOnly" value="<?= (!empty($dataRow->igst)) ? $dataRow->igst : "" ?>" />
            </div> 
             <div class="col-md-12 form-group">
                <label for="description">Description</label>
                <textarea name="description" class="form-control " rows="3"><?=(!empty($dataRow->description))?$dataRow->description:""?></textarea>
            </div>
            
           
        </div>
    </div>
</form>
