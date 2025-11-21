<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="party_category" id="party_category" value="1" />
			<input type="hidden" name="disc_per" value="<?=(!empty($dataRow->disc_per))?$dataRow->disc_per:""?>" />
			<input type="hidden" name="party_type" value="<?=(!empty($dataRow->party_type))?$dataRow->party_type:"2"?>" />

            <div class="col-md-3 form-group">
                <label for="party_name">Company Name</label>
                <input type="text" name="party_name" class="form-control text-capitalize req" value="<?=(!empty($dataRow->party_name))?$dataRow->party_name:""; ?>" />
            </div>

            <div class="col-md-2 form-group">
                <label for="party_code">Party Code</label>
                <input type="text" name="party_code" class="form-control" value="<?=(!empty($dataRow->party_code)) ? $dataRow->party_code : $party_code ?>" />
            </div>

            <div class="col-md-2 form-group">
                <label for="party_phone">Party Phone</label>
                <input type="text" name="party_phone" class="form-control numericOnly" value="<?=(!empty($dataRow->party_phone))?$dataRow->party_phone:""?>" />
            </div>

            <div class="col-md-3 form-group">
                <label for="contact_person">Contact Person</label>
                <input type="text" name="contact_person" class="form-control text-capitalize req" value="<?=(!empty($dataRow->contact_person))?$dataRow->contact_person:""?>" />
            </div>

            <div class="col-md-2 form-group">
                <label for="party_email">Party Email</label>
                <input type="email" name="party_email" class="form-control" value="<?=(!empty($dataRow->party_email))?$dataRow->party_email:""; ?>" />
            </div>
			
            <div class="col-md-9 form-group">
                <label for="party_address">Address</label>
                <textarea name="party_address" class="form-control req" rows="1"><?=(!empty($dataRow->party_address))?$dataRow->party_address:""?></textarea>
            </div>

            <div class="col-md-3 form-group">
                <label for="party_pincode">Address Pincode</label>
                <input type="text" name="party_pincode" class="form-control req" value="<?=(!empty($dataRow->party_pincode))?$dataRow->party_pincode:""?>" />
            </div>
            
            <div class="col-md-9 form-group">
                <label for="delivery_address">Delivery Address</label>
                <textarea name="delivery_address" class="form-control" rows="1"><?=(!empty($dataRow->delivery_address))?$dataRow->delivery_address:""?></textarea>
            </div>

            <div class="col-md-3 form-group">
                <label for="delivery_pincode">Delivery Pincode</label>
                <input type="text" name="delivery_pincode" class="form-control" value="<?=(!empty($dataRow->delivery_pincode))?$dataRow->delivery_pincode:""?>" />
            </div>
            
        </div>
        
    </div>
</form>
<script>
$(document).ready(function(){
    
    $(document).on('keyup','#partyCode',function(){
        var jjicode = addLeadingZero(parseInt($('#partyCode').val()),4);
        jjicode = jjicode.toString();
        var l = jjicode.length;
        if(l > 4){jjicode = jjicode.substring(0, l-1);}
        $('#partyCode').val(jjicode);
    });
    $('#partyCode').focus(function() { $(this).select(); } );
});

function addLeadingZero(value,max) {
  str = value.toString();
  return str.length < max ? addLeadingZero("0" + str, max) : str;
}
</script>