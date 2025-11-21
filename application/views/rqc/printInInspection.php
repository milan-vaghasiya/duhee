<div class="row">
	<div class="col-12">
		<?php
		$pramIds = !empty($inInspectData->parameter_ids) ? explode(',', $inInspectData->parameter_ids) : '';
		$smplingQty = !empty($inInspectData->sampling_qty) ? (($inInspectData->sampling_qty > 0) ? $inInspectData->sampling_qty : 5) : '0';
		?>
		<table class="table item-list-bb text-left" style="margin-top:2px;">
			<tr>
				<th>AAPPL Code No</th>
				<td> <?= (!empty($inInspectData->item_code)) ? $inInspectData->item_code : "" ?></td>
				<th>Part No.</th>
				<td><?= (!empty($inInspectData->part_no)) ? $inInspectData->part_no : "" ?></td>
				<th>Part Description </th>
				<td> <?= (!empty($inInspectData->full_name)) ? $inInspectData->full_name : "" ?> </td>
				
			</tr>
			<tr>
				<th> Latest Rev. & ECO</th>
				<td><?= (!empty($inInspectData->rev_no)) ? $inInspectData->rev_no : "" ?></td>
				<th>Job Card No.</th>
				<td><?= !empty($inInspectData->job_number) ? $inInspectData->job_number : "" ?></td>
				
				<th>Sample Size</th>
				<td><?= (!empty($inInspectData->sampling_qty)) ? floatVal($inInspectData->sampling_qty) : ""  ?></td>
			</tr>
			<tr>
				<th>RQC No</th>
				<td><?= (!empty($inInspectData->trans_no)) ? $inInspectData->trans_no : "" ?></td>
				<th> RQC Date</th>
				<td><?= (!empty($inInspectData->trans_date)) ? date("d-m-Y", strtotime($inInspectData->trans_date)) : "" ?></td>
				<th>Receipt Qty</th>
				<td><?= (!empty(floatVal($inInspectData->lot_qty))) ? floatVal($inInspectData->lot_qty) : "" ?></td>
				
			</tr>

		</table>
		<table class="table item-list-bb" style="margin-top:2px;">
			<thead>
				<tr>
					<th rowspan="2">Sr. No.</th>
					<th rowspan="2">B.D. No.</th>
					<th rowspan="2">Product Characteristic</th>
					<th rowspan="2">Product Specification / Tolerance</th>
					<th rowspan="2">Evaluation / Measurement Technique</th>
					<th colspan="<?= $smplingQty ?>">Observation</th>
				</tr>
				<tr>
					<?php
					for ($i = 1; $i <= $smplingQty; $i++) {
						echo '<th>' . $i . '</th>';
					}
					?>
				</tr>
			</thead>
			<tbody>
				<?php
				$i = 1;
				if (!empty($paramData)) {
					foreach ($paramData as $param) :
						if (in_array($param->id, $pramIds)) :
							$os = json_decode($inInspectData->observation_sample);
							$diamention = '';
							if ($param->requirement == 1) {
								$diamention = $param->min_req . '/' . $param->max_req;
							}
							if ($param->requirement == 2) {
								$diamention = $param->min_req . ' ' . $param->other_req;
							}
							if ($param->requirement == 3) {
								$diamention = $param->max_req . ' ' . $param->other_req;
							}
							if ($param->requirement == 4) {
								$diamention = $param->other_req;
							}
				?>
							<tr>
								<td><?= $i ?></td>
								<td></td>
								<td><?= $param->parameter ?></td>
								<td><?= $diamention ?></td>
								<td><?= $param->category_name ?></td>
								<?php
								for ($j = 0; $j < $inInspectData->sampling_qty; $j++) {
								?><td><?= $os->{$param->id}[$j] ?></td>
								<?php
								}
								$countPrm = count($os->{$param->id});
								?>
							</tr>
				<?php
							$i++;
						endif;
					endforeach;
					$i = $i - 1;

				}
				?>
			</tbody>
		</table>
		<?php
		$chk = '<img src="' . base_url('assets/images/check-square.png') . '" style="width:20px;display:inline-block;vertical-align:middle;">';
		$unchk = '<img src="' . base_url('assets/images/uncheck-square.png') . '" style="width:20px;display:inline-block;vertical-align:middle;">';
		?>
		<table class="table item-list-bb">
			<tr>
				<td colspan="2"><b>Comment :</b><?= (!empty($inInspectData->remark)) ? $inInspectData->remark : "" ?></td>
			</tr>
			<tr>
				<td><b>Checked By :</b> <?= (!empty($inInspectData->emp_name)) ? $inInspectData->emp_name : "" ?></td>
				
				<td><b>Approved By :</b></td>
			</tr>
		</table>
	</div>
</div>