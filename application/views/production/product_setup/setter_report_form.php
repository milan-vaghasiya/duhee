<form autocomplete="off" id="InInspection">
    <div class="col-md-12">
        <input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : "" ?>" />
        <input type="hidden" name="setup_id" value="<?= (!empty($dataRow->setup_id)) ? $dataRow->setup_id : $setup_id ?>" />
        <input type="hidden" name="setter_id" id="setter_id" value="<?= (!empty($dataRow->setter_id)) ? $dataRow->setter_id : $setter_id ?>" />

        <div class="row">
            <div class="col-md-2 form-group">
                <label for="setup_start_time">Setup Start Time</label>
                <input type="datetime-local" value="<?= !empty($dataRow->setup_start_time)?date("Y-m-d\TH:i:s",strtotime($dataRow->setup_start_time)):date("Y-m-d\TH:i:s") ?>" name="setup_start_time" id="setup_start_time" class="form-control">
            </div>
            <div class="col-md-2 form-group">
                <label for="setup_end_time">Setup End Time</label>
                <input type="datetime-local" value="<?= !empty($dataRow->setup_end_time)?date("Y-m-d\TH:i:s",strtotime($dataRow->setup_end_time)):date("Y-m-d\TH:i:s") ?>" name="setup_end_time" id="setup_end_time" class="form-control">
            </div>
            <div class="col-md-2 form-group">
                <label for="submit_to_qc">Submit To QC</label>
                <select name="submit_to_qc" id="submit_to_qc" class="form-control">
                    <option value="0" <?= (!empty($dataRow->submit_to_qc) && $dataRow->submit_to_qc == 0) ? 'selected' : '' ?>>No</option>
                    <option value="1" <?= (!empty($dataRow->submit_to_qc) && $dataRow->submit_to_qc == 1) ? 'selected' : '' ?>>Yes</option>
                </select>
            </div>
            <div class="col-md-6 form-group">
                <label for="setter_note">Note</label>
                <input type="text" name="setter_note" id="setter_note" class="form-control" value="<?= (!empty($dataRow->setter_note)) ? $dataRow->setter_note : '' ?>">
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
                        $i = 1;$p='A';$j=1;
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
                                if (!empty($dataRow->dimension_report)) :
                                    $obj = json_decode($dataRow->dimension_report);
                                endif;

                                $tbodyData .= '<tr>
                                                    <td style="text-align:center;">' .(($row->parameter_type == 1)?$i++:$p++) . '</td>
                                                    <td>' . $row->parameter . '</td>
                                                    <td>' . $diamention . '</td>
                                                    <td>' . $row->category_name . '</td>';
                                if (!empty($obj->{$row->id})) :
                                    $tbodyData .= '<td><input type="text" name="sample' . '_' . $row->id . '" id="sample'  . '_' . $j . '" class="form-control text-center parameter_limit' . $cls . '" value="' . $obj->{$row->id} . '" data-min="' . $row->min_req . '" data-max="' . $row->max_req . '" data-requirement="' . $row->requirement . '" data-row_id ="' . $j . '"  ' .(($row->parameter_type == 1)?'readOnly':'') . '></td>';
                                else :
                                    $tbodyData .= '<td><input type="text" name="sample'  . '_' . $row->id . '" id="sample'  . '_' . $j . '" class="form-control text-center parameter_limit' . $cls . '" value=""  data-min="' . $row->min_req . '" data-max="' . $row->max_req . '" data-requirement="' . $row->requirement . '" data-row_id ="' . $j . '"  ' .(($row->parameter_type == 1)?'readOnly':'') . '></td>';
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