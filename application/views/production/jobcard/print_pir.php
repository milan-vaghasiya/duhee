<div class="row">
    <div class="col-12">

        <table class="table item-list-bb">
            <tr class="text-left">
                <th class="bg-light" style="width:15%;">Date</th><td style="width:25%;"></td>
                <th class="bg-light" style="width:15%;">Lot No. / DT.</th><td style="width:15%;"><?= (!empty($dataRow->wo_no)? $dataRow->wo_no : $jobData->wo_no) ?></td>
                <th class="bg-light" style="width:15%;">Customer Name / Code</th><td style="width:15%;"><?= (!empty($dataRow->party_name)? $dataRow->party_name : $jobData->party_name) ?></td>
            </tr>
            <tr class="text-left">
                <th class="bg-light">Part Name</th><td><?= (!empty($dataRow->full_name)?$dataRow->full_name:$jobData->full_name) ?></td>
                <th class="bg-light">Grade</th><td><?= (!empty($dataRow->material_grade)?$dataRow->material_grade:$jobData->material_grade) ?></td>
                <th class="bg-light">M/C No.</th><td></td>
            </tr>
            <tr class="text-left">
                <th class="bg-light">Part Number</th><td><?= (!empty($dataRow->part_no)?$dataRow->part_no:$jobData->part_no) ?></td>
                <th class="bg-light">GRN No.</th><td></td>
                <th class="bg-light">Shift</th><td></td>
            </tr>
            <tr class="text-left">
                <th class="bg-light">Part Code</th><td><?= (!empty($dataRow->item_code)?$dataRow->item_code:$jobData->product_code) ?></td>
                <th class="bg-light">Heat No.</th><td><?= (!empty($dataRow->mill_heat_no)? $dataRow->mill_heat_no : $jobData->mill_heat_no) ?></td>
                <th class="bg-light">Dept. Name</th><td><?= (!empty($name)?$name:'') ?></td>
            </tr>
            <tr class="text-left">
                <th class="bg-light">Operator Name</th><td></td>
                <th class="bg-light">Inspector Name</th><td></td>
                <th class="bg-light">Process</th><td><?= (!empty($dataRow->process_name)?$dataRow->process_name:$process_name) ?></td>
            </tr>
        </table>

        <table id="pirTable" class="table item-list-bb" style="margin-top:5px;">
            <thead class="thead-info" id="theadData">
                <?php $sample_size = 12; ?>
                
                <tr style="text-align:center;" class="bg-light">
                    <th rowspan="2" style="width:3%;">#</th>
                    <th rowspan="2" style="width:10%;">Parameter</th>
                    <th rowspan="2" style="width:10%;">Specification</th>
                    <th colspan="<?= $sample_size ?>" style="width:52%">Observation ( All Dimensions are in mm.) Hourly Inspection Qty 5pcs & Recorded 2 Pcs</th>
                </tr>
                <tr style="text-align:center;" class="bg-light">
                    <th colspan="2">8-9</th>
                    <th colspan="2">9-10</th>
                    <th colspan="2">10-11</th>
                    <th colspan="2">11-12</th>
                    <th colspan="2">12-13</th>
                    <th colspan="2">13-14</th>
                </tr>
            </thead>
            <tbody id="tbodyData">
                <?php
                $tbodyData = "";
                $i = 1;$tbcnt=1;

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
                        if (!empty($dataRow)) :
                            $obj = json_decode($dataRow->observation_sample);
                        endif;
                       
                        $tbodyData .= '<tr>
                                        <td style="text-align:center;">' . $i++ . '</td>
                                        <td>' . $row->parameter . '</td>
                                        <td>' . $diamention . '</td>';
                        for ($c = 0; $c < $sample_size; $c++) :
                            $tbodyData .= '<td></td>';
                        endfor;
                    endforeach;
                endif;
                $tbcnt++;
                echo $tbodyData;
                ?>
                  
            </tbody>
          
        </table>
        <table class="table item-list-bb" style="margin-top:2px;border: 1px solid #000000;border-collapse:collapse !important;">
			<tr>
				<td style="width:3%;"> </td>
				<td colspan="2"> Observations As per below Category No.</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
			</tr>
            <tr>
                <td rowspan="2" style="width:3%;"></td>
				<td rowspan="2" style="width:10%;"> Qc Remarks & Sign.</td>
                <td style="width:10%;">Line Inspector.</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
			</tr>
            <tr>
                <td style="width:10%;">Qc Engineer</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
			</tr>
		</table>
        <table id="pirTable" class="table item-list-bb" style="margin-top:5px;">
        <thead class="thead-info" id="theadData">
                <?php $sample_size = 12; ?>

                <tr style="text-align:center;" class="bg-light">
                    <th rowspan="2" style="width:3%;">#</th>
                    <th rowspan="2" style="width:10%;">Parameter</th>
                    <th rowspan="2" style="width:10%;">Specification</th>
                    <th colspan="<?= $sample_size ?>" style="width:52%">Observation ( All Dimensions are in mm.) Hourly Inspection Qty 5pcs & Recorded 2 Pcs</th>
                </tr>
                <tr style="text-align:center;" class="bg-light">
                    <th colspan="2">14-15</th>
                    <th colspan="2">15-16</th>
                    <th colspan="2">16-17</th>
                    <th colspan="2">17-18</th>
                    <th colspan="2">18-19</th>
                    <th colspan="2">19-20</th>
                </tr>
            </thead>
            <tbody id="tbodyData">
                <?php
                $tbodyData = "";
                $i = 1;$tbcnt=1;

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
                        if (!empty($dataRow)) :
                            $obj = json_decode($dataRow->observation_sample);
                        endif;
                       
                        $tbodyData .= '<tr>
                                        <td style="text-align:center;">' . $i++ . '</td>
                                        <td>' . $row->parameter . '</td>
                                        <td>' . $diamention . '</td>';
                        for ($c = 0; $c < $sample_size; $c++) :
                            $tbodyData .= '<td></td>';
                        endfor;
                    endforeach;
                endif;
                $tbcnt++;
                echo $tbodyData;
                ?>
            </tbody>
        </table>
        <table class="table item-list-bb" style="margin-top:2px;border: 1px solid #000000;border-collapse:collapse !important;">
			<tr>
				<td style="width:3%;"> </td>
				<td colspan="2"> Observations As per below Category No.</td>
                <td colspan="2" ></td>
                <td colspan="2"></td>
                <td colspan="2"></td>
                <td colspan="2"></td>
                <td colspan="2"></td>
                
               
			</tr>
            <tr>
                <td rowspan="2" style="width:3%;"></td>
				<td rowspan="2" style="width:10%;"> Qc Remarks & Sign.</td>
                <td style="width:10%;">Line Inspector.</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                
			</tr>
            <tr>
                <td style="width:10%;">Qc Engineer</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
               
                
			</tr>
            <tr>
                <td colspan="15">Observations Category:-1) Re-Setting 2) Insert Change 3) 100% Inspection Required 4) Need to Rework 5) Need to Set UDA Gauges Properly 6) Master Calibration Required 7) Pass</td>
			</tr>
            <tr>
                <td colspan="15">Note :</td>
			</tr>
            <tr>
                <td colspan="7" class="text-left">Inspected By </td>
                <td colspan="8" class="text-left">Approved By </td>
			</tr>

		</table>
    </div>
</div>