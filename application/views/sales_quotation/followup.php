<form>
    <div class="col-md-12">
        <div class="row">

            <input type="hidden" name="id" id="id" value="">
            <input type="hidden" name="ref_id" id="ref_id" value="<?=$ref_id?>">
            <input type="hidden" name="party_id" id="party_id" value="<?=$followupData->party_id?>">
            <input type="hidden" name="entry_type" id="entry_type" value="1">
            <input type="hidden" name="from_entry_type" id="from_entry_type" value="3">

            <div class="col-md-4 form-group">
                <label for="appointment_date">Followup Date</label>
                <input type="date" name="appointment_date" id="appointment_date" max="<?=date("Y-m-d")?>" class="form-control" value="<?=(!empty($dataRow->appointment_date))?$dataRow->appointment_date:date("Y-m-d")?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="mode">Mode</label>			
                <select name="mode" id="mode" class="form-control req single-select">
                    <?php
                        foreach($appointmentMode as $key=>$row):
							$selected = (!empty($dataRow->mode) and $dataRow->mode == $row)?"selected":"";
                            echo '<option value="'.$key.'" '.$selected .'>'.$row.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="contact_person">Contact Person</label>
                <input type="text" name="contact_person" id="contact_person" class="form-control text-capitalize req" value="<?=(!empty($dataRow->contact_person))?$dataRow->contact_person:""?>" />
            </div>
            <div class="col-md-12 form-group">
                <label for="notes">Notes</label>
                <textarea name="notes" id="notes" class="form-control"><?=(!empty($dataRow->notes))?$dataRow->notes:""?></textarea>
            </div>
        </div>
    </div>    
</form>