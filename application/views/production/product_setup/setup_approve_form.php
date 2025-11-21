<form autocomplete="off" id="InInspection">
    <div class="col-md-12">
        <input type="hidden" name="id" value="<?= (!empty($setupData->id)) ? $setupData->id : "" ?>" />
        <input type="hidden" name="setup_id" value="<?= (!empty($setupData->setup_id)) ? $setupData->setup_id : "" ?>" />

        <div class="row">
            <div class="col-md-2 form-group">
                <label for="inspection_start_date">Inspection Start Time</label>
                <input type="datetime-local" value="<?= date("Y-m-d\TH:i:s") ?>" name="inspection_start_date" id="inspection_start_date" class="form-control">
            </div>
            <div class="col-md-2 form-group">
                <label for="inspection_end_time">Inspection End Time</label>
                <input type="datetime-local" value="<?= date("Y-m-d\TH:i:s") ?>" name="inspection_end_time" id="inspection_end_time" class="form-control">
            </div>
            <div class="col-md-2 form-group">
                <label for="setup_status">Status</label>
                <select name="setup_status" id="setup_status" class="form-control">
                    <option value="">Select Status</option>
                    <option value="5">Approved</option>
                    <option value="6">Reset up</option>
                    <!-- <option value="7">On Hold</option> -->
                </select>
                <div class="error setup_status"></div>
            </div>
            <div class="col-md-6 form-group">
                <label for="qci_note">Note</label>
                <input type="text" name="qci_note" id="qci_note" class="form-control" value="">
            </div>
        </div>
    </div>
    <hr>
    <div class="col-md-12">
        <div class="error general"></div>
    </div>
    <div class="col-md-12 mt-3">
        <div class="row form-group">
            <div class="table-responsive">
                <table id="dimensionTbl" class="table table-bordered generalTable">
                    <thead class="thead-info">
                        <tr style="text-align:center;">
                            <th style="width:5%;">#</th>
                            <th>Product/Process Char.</th>
                            <th>Specification</th>
                            <th>Measurement Tech.</th>
                            <th>Observation on Sample</th>
                        </tr>

                    </thead>
                    <tbody id="tbodyData">
                        <?php
                        $tbodyData = "";
                        $i = 1;$j=1;$p='A';
                        if (!empty($paramData)) :
                            foreach ($paramData as $row) :
                                $obj = new StdClass;
                                $cls = "";
                                if (!empty($row->lower_limit) or !empty($row->upper_limit)) :
                                    $cls = "floatOnly";
                                endif;
                                $diamention = '';
                                if ($row->requirement == 1) {
                                    $diamention = $row->min_req . '/' . $row->max_req;
                                }
                                if ($row->requirement == 2) {
                                    $diamention = $row->min_req . ' ' . $row->other_req;
                                }
                                if ($row->requirement == 3) {
                                    $diamention = $row->max_req . ' ' . $row->other_req;
                                }
                                if ($row->requirement == 4) {
                                    $diamention = $row->other_req;
                                }
                                if (!empty($setupData)) :
                                    $obj = json_decode($setupData->dimension_report);
                                endif;

                                $tbodyData .= '<tr>
                                                <td style="text-align:center;">' . (($row->parameter_type == 1)?$i++:$p++) . '</td>
                                                <td>' . $row->parameter . '</td>
                                                <td>' . $diamention . '</td>
                                                <td>' . $row->category_name . '</td>';
                                                if (!empty($obj->{$row->id})) :
                                                    $tbodyData .= '<td><input type="text" name="sample' . '_' . $row->id . '" id="sample'  . '_' . $j . '" class="form-control text-center parameter_limit' . $cls . '" value="' . $obj->{$row->id} . '" data-min="' . $row->min_req . '" data-max="' . $row->max_req . '" data-requirement="' . $row->requirement . '" data-row_id ="' . $j . '"  ' .(($row->parameter_type == 2)?'readOnly':'') . '></td>';
                                                else :
                                                    $tbodyData .= '<td><input type="text" name="sample'  . '_' . $row->id . '" id="sample'  . '_' . $j . '" class="form-control text-center parameter_limit' . $cls . '" value=""  data-min="' . $row->min_req . '" data-max="' . $row->max_req . '" data-requirement="' . $row->requirement . '" data-row_id ="' . $j . '"  ' .(($row->parameter_type == 2)?'readOnly':'') . '></td>';
                                                endif;
                                                $j++;

                            endforeach;
                        endif;
                        echo $tbodyData;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>