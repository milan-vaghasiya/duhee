<div class="row">
    <div class="col-12">
        <?php
		$pramIds = explode(',', $dataRow->parameter_ids);
		$smplingQty = ($dataRow->sampling_qty > 0) ? $dataRow->sampling_qty : 0;
		?>

        <table class="table item-list-bb">
            <tr class="bg-light text-left">
                <th style="width:15%;">Date</th><td style="width:25%;"><?= ((!empty($dataRow->trans_date)) ? formatDate($dataRow->trans_date) : "") ?></td>
                <th style="width:15%;">Lot No. / DT.</th><td style="width:15%;"><?= (!empty($dataRow->job_number)? $dataRow->job_number : '') ?></td>
                <th style="width:15%;">Customer Name / Code</th><td style="width:15%;"><?= (!empty($dataRow->party_name)? $dataRow->party_name : '') ?></td>
            </tr>
            <tr class="bg-light text-left">
                <th>Part Name</th><td><?= (!empty($dataRow->full_name)?$dataRow->full_name:'') ?></td>
                <th>Grade</th><td><?= (!empty($dataRow->material_grade)?$dataRow->material_grade:'') ?></td>
                <th>M/C No.</th><td><?= ((!empty($dataRow->machine_code)) ? $dataRow->machine_code : "") ?></td>
            </tr>
            <tr class="bg-light text-left">
                <th>Part Number</th><td><?= (!empty($dataRow->part_no)?$dataRow->part_no:'') ?></td>
                <th>GRN No.</th><td></td>
                <th>Shift</th><td></td>
            </tr>
            <tr class="bg-light text-left">
                <th>Part Code</th><td><?= (!empty($dataRow->item_code)?$dataRow->item_code:'') ?></td>
                <th>Heat No.</th><td><?= (!empty($dataRow->heat_no)? $dataRow->heat_no : '') ?></td>
                <th>Dept. Name</th><td><?= (!empty($dataRow->name)?$dataRow->name:'') ?></td>
            </tr>
            <tr class="bg-light text-left">
                <th>Operator Name</th><td></td>
                <th>Inspector Name</th><td><?= (!empty($dataRow->emp_name)?$dataRow->emp_name:'') ?></td>
                <th>Process</th><td><?= (!empty($dataRow->process_name)?$dataRow->process_name:'') ?></td>
            </tr>
        </table>

        <table id="pirTable" class="table item-list-bb" style="margin-top:5px;">
            <thead class="thead-info" id="theadData">
                <?php $sample_size = 12; ?>
                
                <tr style="text-align:center;" class="bg-light">
                    <th rowspan="2" style="width:3%;">#</th>
                    <th rowspan="2" style="width:10%;">Parameter</th>
                    <th rowspan="2" style="width:10%;">Specification</th>
					<th colspan="<?= $smplingQty ?>">Observation</th>
                </tr>
                <tr>
					<?php
					$reportTime = !empty($dataRow->result)?explode(',',$dataRow->result):[];
					for ($i = 0; $i < $smplingQty; $i++) {
						echo '<th>' . (!empty($reportTime[$i])?date("h:ia",strtotime($reportTime[$i])):'') . '</th>';
					}
					?>
				</tr>
            </thead>
            <tbody id="tbodyData">
                <?php
                $tbodyData = "";
                $i = 1; $tbcnt = 1; $sample = array();

                if (!empty($paramData)) :
                    foreach ($paramData as $row) :
                        if (in_array($row->id, $pramIds)) :
                            $os = json_decode($dataRow->observation_sample);
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
                        
                            $tbodyData .= '<tr>
                                            <td style="text-align:center;">' . $i++ . '</td>
                                            <td>' . $row->parameter . '</td>
                                            <td>' . $diamention . '</td>';
                            for ($c = 0; $c < $smplingQty; $c++) :
                                $tbodyData .= '<td class="text-center">'.$os->{$row->id}[$c].'</td>';
                            endfor;
                        endif;
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
                <td colspan="<?= $smplingQty ?>"></td>
			</tr>
            <tr>
                <td rowspan="2" style="width:3%;"></td>
				<td rowspan="2" style="width:10%;"> Qc Remarks & Sign.</td>
                <td style="width:10%;">Line Inspector.</td>
                <td colspan="<?= $smplingQty ?>"></td>                
			</tr>
            <tr>
                <td style="width:10%;">Qc Engineer</td>
                <td colspan="<?= $smplingQty ?>"></td>
			</tr>
            <tr>
                <td colspan="<?= ($smplingQty + 3) ?>">Observations Category:-1) Re-Setting 2) Insert Change 3) 100% Inspection Required 4) Need to Rework 5) Need to Set UDA Gauges Properly 6) Master Calibration Required 7) Pass</td>
			</tr>
            <tr>
                <td colspan="<?= ($smplingQty + 3) ?>">Note :</td>
			</tr>
		</table>

        <table class="table item-list-bb" style="margin-top:2px;border: 1px solid #000000;border-collapse:collapse !important;">
            <tr>
                <td style="width:25%" class="text-left">Inspected By</td>
                <td style="width:25%"><?= (!empty($dataRow->emp_name)?$dataRow->emp_name:'') ?></td>
                <td style="width:25%" class="text-left">Approved By</td>
                <td style="width:25%"></td>
			</tr>
        </table>
    </div>
</div>