<form>    
    <div class="row">
        <input type="hidden" name="id" id="id" value="1" />
        <div class="col-md-12 form-group">
            <label for="cl_charge">Lunch</label>
            <input type="text" name="cl_charge" id="cl_charge" class="form-control floatOnly" value="<?=(!empty($dataRow->cl_charge))?$dataRow->cl_charge:""?>">
        </div>
        <div class="col-md-12 form-group">
            <label for="cd_charge">Dinner</label>
            <input type="text" name="cd_charge" id="cd_charge" class="form-control floatOnly" value="<?=(!empty($dataRow->cd_charge))?$dataRow->cd_charge:""?>">
        </div>
        <div class="col-md-12 form-group">
            <label for="bf_charge">Breakfast</label>
            <input type="text" name="bf_charge" id="bf_charge" class="form-control floatOnly" value="<?=(!empty($dataRow->bf_charge))?$dataRow->bf_charge:""?>">
        </div>
    </div>
<form>