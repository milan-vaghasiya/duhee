<form>
<div class="col-md-12">
                <table class="table">
                    <tr>
                        <th>Appointment Date</th>
                        <td> : <?=formatDate($appointmentData->appointment_date,'d-m-Y ').formatDate($appointmentData->appointment_time,'H:i A')?></td>
                    </tr>
                    <tr>
                        <th>Mode</th>
                        <td> : <?=$appointmentMode[$appointmentData->mode]?></td>
                    </tr>
                    <tr>
                        <th>Contact Person</th>
                        <td> : <?=$appointmentData->contact_person?></td>
                    </tr>
                    <tr>
                        <th>Purpose</th>
                        <td> : <?=$appointmentData->notes?></td>
                    </tr>
                </table>
            </div>
            <hr>
    <div class="col-md-12">
        <div class="row">

            <input type="hidden" name="id" id="id" value="<?=$appointmentData->id?>">
            
            
            <div class="col-md-12 form-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="0">Open</option>
                    <option value="1">Cancel</option>
                    <option value="2">Complete</option>
                </select>
            </div>
            <div class="col-md-12 form-group">
                <label for="notes">Note</label>
                <textarea name="notes" id="notes" class="form-control req"></textarea>
                <div class="error notes"></div>
            </div>
        </div>
    </div>    
</form>