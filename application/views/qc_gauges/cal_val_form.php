<form>
    <div class="col-md-12">
        <div class="row"> 
            <input type="hidden" name="id" value="" />
            <input type="hidden" name="item_id" id="item_id" value="<?= (!empty($calData->item_id)) ? $calData->item_id : ''; ?>" />
            <input type="hidden" name="challan_id" id="challan_id" value="<?= (!empty($calData->challan_id)) ? $calData->challan_id : ''; ?>" />
            <input type="hidden" name="challan_trans_id" id="challan_trans_id" value="<?= (!empty($calData->id)) ? $calData->id : ''; ?>" />
            <input type="hidden" name="batch_no" id="batch_no" value="<?= (!empty($calData->item_code)) ? $calData->item_code : ''; ?>" />
            <input type="hidden" name="cal_agency" id="cal_agency" value="<?= (!empty($calData->party_id)) ? $calData->party_id : ''; ?>" />
            <input type="hidden" name="cal_agency_name" id="cal_agency_name" value="<?= (!empty($calData->party_name)) ? $calData->party_name : 'IN-HOUSE'; ?>" />

      
            <div class="col-md-6 form-group">
                <label for="cal_date">Calibration Date</label>
                <input type="date" name="cal_date" id="cal_date" class="form-control floatOnly req" value="<?= date("Y-m-d") ?>">
            </div>
          
            <div class="col-md-6 form-group">
                <label for="to_location">Receive Location</label>
                <select name="to_location" id="to_location" class="form-control single-select">
                    <option value="">Select Location</option>
                    <?php
                        foreach ($locationList as $row) :
                            echo '<option value="' . $row->id . '">[' .$row->store_name. '] '.$row->location.'</option>';
                        endforeach;
                    ?>
                </select>
             </div>
        </div>
    </div>
    <table class="table table-bordered align-items-center">
            <thead class="thead-info">
                <tr>
                    <th rowspan="2" class="text-center">Instrument Name</th>
                    <th colspan="10" class="text-center">Required Value</th>
                </tr>
                <tr>
                    <th class="text-center">1</th>
                    <th class="text-center">2</th>
                    <th class="text-center">3</th>
                    <th class="text-center">4</th>
                    <th class="text-center">5</th>
                    <th class="text-center">6</th>
                    <th class="text-center">7</th>
                    <th class="text-center">8</th>
                    <th class="text-center">9</th>
                    <th class="text-center">10</th>
                </tr>
                <tr>
                    <td class="text-center"><?=$calData->item_name?></td>
                    <td><input type="text" name="val1" class="form-control floatOnly" value="<?= (!empty($calData->val1)) ? $calData->val1 : ''; ?>" /><div class="error val1"></div></td>
                    <td><input type="text" name="val2" class="form-control floatOnly" value="<?= (!empty($calData->val2)) ? $calData->val2 : ''; ?>" /></td>
                    <td><input type="text" name="val3" class="form-control floatOnly" value="<?= (!empty($calData->val3)) ? $calData->val3 : ''; ?>" /></td>
                    <td><input type="text" name="val4" class="form-control floatOnly" value="<?= (!empty($calData->val4)) ? $calData->val4 : ''; ?>" /></td>
                    <td><input type="text" name="val5" class="form-control floatOnly" value="<?= (!empty($calData->val5)) ? $calData->val5 : ''; ?>" /></td>
                    <td><input type="text" name="val6" class="form-control floatOnly" value="<?= (!empty($calData->val6)) ? $calData->val6 : ''; ?>" /></td>
                    <td><input type="text" name="val7" class="form-control floatOnly" value="<?= (!empty($calData->val7)) ? $calData->val7 : ''; ?>" /></td>
                    <td><input type="text" name="val8" class="form-control floatOnly" value="<?= (!empty($calData->val8)) ? $calData->val8 : ''; ?>" /></td>
                    <td><input type="text" name="val9" class="form-control floatOnly" value="<?= (!empty($calData->val9)) ? $calData->val9 : ''; ?>" /></td>
                    <td><input type="text" name="val10" class="form-control floatOnly" value="<?= (!empty($calData->val10)) ? $calData->val10 : ''; ?>" /></td>
                </tr>
            </thead>
    </table>
</form>


