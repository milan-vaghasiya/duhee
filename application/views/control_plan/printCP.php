<div class="row">
	<div class="col-12">
		<table class="table item-list-bb text-left" style="margin-top:2px;">
			<tr>
				<th>Supplier Name</th>
				<td><?= (!empty($companyData->company_name)) ? $companyData->company_name : "" ?></td>
				<th>Supplier Code </th>
				<td><?= (!empty($cpData->vendor_code)) ? $cpData->vendor_code : "" ?></td>
				<th>Supplier Approval Date</th>
				<td></td>
			</tr>
			<tr>
				<th>Key Contact</th>
				<td></td>
				<th>Date (Org) </th>
				<td></td>
				<th >Core Team</th>
				<td><?= (!empty($cpData->core_team)) ? $cpData->core_team : "" ?></td>
			</tr>
			<tr>
				<th>AAPPL Code No.</th>
				<td><?= (!empty($cpData->item_code)) ? $cpData->item_code : "" ?></td>
				<th>Part Description</th>
				<td><?= (!empty($cpData->full_name)) ? $cpData->full_name : "" ?></td>
				<th>Cust. Rev. & Part No.</th>
				<td><?= (!empty($cpData->part_no) ? $cpData->part_no : "") . '/' . (!empty($cpData->rev_no) ? $cpData->rev_no : '')   ?></td>
			</tr>
			<tr>
				<th>Cust. Rev. Date & ECO No.</th>
				<td></td>
				<th>Control Plan Number</th>
				<td><?= (!empty($cpData->trans_number)) ? $cpData->trans_number : "" ?></td>
				<th>CP Revision No. & Date</th>
                <td><?= ((!empty($cpData->app_rev_no) AND $cpData->app_rev_no != '')) ? sprintf('%02d', $cpData->app_rev_no) . '/' . formatDate($cpData->app_rev_date) : "" ?></td>
			</tr>
			<tr>
				<th>Customer Engg. Approval /<br> Date (If Req.)</th>
				<td></td>
				<th>Customer Quality Approval /<br> Date (If Req.)</th>
				<td></td>
				<th>Customer Other Approval / <br>Date  (If Req.)</th>
				<td></td>
			</tr>
		</table>
		<table class="table item-list-bb text-left" style="margin-top:2px;">
			<tr>
				<td><b>Process No : </b><?= (!empty($cpData->process_no)) ? $cpData->process_no : "" ?> </td>
				<td><b>Process Name : </b><?= (!empty($cpData->parameter)) ? $cpData->parameter : "" ?></td>
				<td><b>Machine No : </b><?= (!empty($cpData->typeof_machine)) ? $cpData->typeof_machine : "" ?> </td>
				<td><b>Jig Fixture No. : </b><?= (!empty($cpData->jig_fixture_no)) ? $cpData->jig_fixture_no : "" ?> </td>
			</tr>
		</table>
		<table class="table item-list-bb" style="margin-top:10px;" >
		    <thead>
			<tr>
				<th colspan="5">Characteristics</th>
				<th colspan="8">Methods </th>
			</tr>
			<tr>
				<th rowspan="2">Sr. No.</th>
				<th rowspan="2">Product</th>
				<th rowspan="2">Process</th>
				<th rowspan="2">Special Char. Class</th>
				<th rowspan="2">Product / Process,Specification / Tolerance</th>
				<th rowspan="2">Instrument range</th>
				<th rowspan="2">Least Count</th>
				<th rowspan="2">Evaluation / Measurement Technique</th>
				<th colspan="2">Sample</th>
				<th rowspan="2">Control Method </th>
				<th rowspan="2">Responsibility </th>
			</tr>
			<tr>
				<th>Size</th>
				<th>Frequency </th>
			</tr>
			</thead>
			<tbody>
			<?php
			if (!empty($cpTrans)) {
				$i = 1;
				foreach ($cpTrans as $row) {
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
						$diamention = nl2br($row->other_req);
					}

			?>
					<tr>
						<td rowspan="<?= !empty($row->controlMethod) ? (count($row->controlMethod)) : '' ?>"><?= $i++ ?></td>
						<td rowspan="<?= !empty($row->controlMethod) ? (count($row->controlMethod)) : '' ?>"><?= ($row->parameter_type==1)?nl2br($row->parameter):'-' ?></td>
						<td rowspan="<?= !empty($row->controlMethod) ? (count($row->controlMethod)) : '' ?>"><?=  ($row->parameter_type==2)?nl2br($row->parameter):'-' ?></td>
						<td rowspan="<?= !empty($row->controlMethod) ? (count($row->controlMethod)) : '' ?>">
						<?php if(!empty($row->char_class)){ ?><img style="width:25px;display:inline-block;vertical-align:middle;" src="<?= base_url('assets/images/symbols/'.$row->char_class.'.png')?>"><?php } ?>
						</td>
						<td rowspan="<?= !empty($row->controlMethod) ? (count($row->controlMethod)) : '' ?>"><?= $diamention ?></td>
						<td rowspan="<?= !empty($row->controlMethod) ? (count($row->controlMethod)) : '' ?>"><?= $row->instrument_range ?></td>
						<td rowspan="<?= !empty($row->controlMethod) ? (count($row->controlMethod)) : '' ?>"><?= $row->least_count ?></td>

						<?php
						if (!empty($row->controlMethod)) {
							$j = 1;
							foreach ($row->controlMethod as $cm) {
						?>
								<td><?= $cm->instrument_code ?></td>
								<td><?= $cm->sev ?></td>
								<td><?= $cm->potential_cause ?></td>
								<td><?= $cm->process_prevention ?></td>
								<td><?= $cm->process_detection ?></td>

								<?php
								if (((count($row->controlMethod)) != $j)) {
								?>
					</tr>
					<tr><?php
								}
								$j++;
							}
						}else{
							?><td></td><td></td><td></td><td></td><td></td><?php
						}
					}
				}
						?>
		
			</tbody>
		</table>
		<table class="table item-list-bb" style="margin-top:2px;">
			<tr>
				<td rowspan="4">
					<b>Abbvriation</b>

				</td>
				<td colspan="3">OPR : Operator / SET : Setter / ICP : Incharge - Production / ICQ : Incharge Quality </td>
				<td> FPA-First Piece Approval Process. Round Marked Indicate For FPA In PIR Report</td>
				<th rowspan="2"> Prepared By :</th>
			</tr>
			<tr>
				<td colspan="3">INQ : Inspector - Quality / ICML : Incharge - Metallurgical Laboratry</td>
				<td>01) Product Parameters (Numbering System : 01,02,..) and Process Parameters (Numbering System : A,B,C,...)</td>
			</tr>
			<tr>
				<td colspan="3">ICM : Incharge - Maintenance / WI : Work Instruction</td>
				<td>
					02) No Recoding Will Be Done For Inspection Activities Caried Out By Operator During The Process.
				</td>

				<th rowspan="2">Approved By : </th>
			</tr>
			<tr>
				<td><img style="width:25px;display:inline-block;vertical-align:middle;" src="<?= base_url('assets/images/symbols/critical.png') ?>"> <span style="">Critical Characteristic </span> </td>
				<td><img style="width:25px;display:inline-block;vertical-align:middle;" src="<?= base_url('assets/images/symbols/major.png') ?>"> <span style="">Major </span></td>
				<td><img style="width:25px;display:inline-block;vertical-align:middle;" src="<?= base_url('assets/images/symbols/minor.png') ?>"> <span style="">Minor</span></td>
				<td>Process Drawing Required :- Yes / No, If Yes Refer Process Drawing</td>
			</tr>
		</table>
	</div>
</div>