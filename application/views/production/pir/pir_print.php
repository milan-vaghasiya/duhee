<div class="row">
	<div class="col-12">
		<?php
		$pramIds = explode(',', $pirData->parameter_ids);
		$smplingQty = ($pirData->sampling_qty > 0) ? $pirData->sampling_qty : 0;
		?>
		<table class="table item-list-bb text-left" style="margin-top:2px;">
			<tr>
				<th>Item Code</th>
				<th>Part Name</th>
				<th>Jobcard</th>
				<th>Process</th>
				<th>Machine</th>
				<th>PIR Date</th>
				<th>PIR No</th>
			</tr>
			<tr>
				<td > <?= (!empty($pirData->item_code)) ? $pirData->item_code : "" ?></td>
				<td style="width:30%"><?= (!empty($pirData->full_name)) ?$pirData->full_name : "" ?></td>
				<td><?= (!empty($pirData->job_number)) ?$pirData->job_number : "" ?></td>
				<td><?= (!empty($pirData->process_name)) ? $pirData->process_name : "" ?></td>
				<td > <?= ((!empty($pirData->machine_code)) ? '['.$pirData->machine_code.'] ' : "").$pirData->machine_name ?> </td>
				<td > <?= ((!empty($pirData->trans_date)) ? formatDate($pirData->trans_date) : "") ?> </td>
				<td > <?= ((!empty($pirData->trans_no)) ? $pirData->trans_no : "") ?> </td>
			</tr>
		</table>
		<table class="table item-list-bb" style="margin-top:4px;">
			<thead>
				<tr>
					<th rowspan="2">Sr. No.</th>
					<th rowspan="2">Operation No</th>
					<th rowspan="2">Product Characteristic</th>
					<th rowspan="2">Product Specification / Tolerance</th>
					<th rowspan="2">Evaluation / Measurement Technique</th>
					<th rowspan="2">Size</th>
					<th rowspan="2">Freq.</th>
					<th colspan="<?= $smplingQty ?>">Observation</th>
				</tr>
				<tr>
					<?php
					$reportTime = !empty($pirData->result)?explode(',',$pirData->result):[];
					for ($i = 0; $i < $smplingQty; $i++) {
						echo '<th>' . (!empty($reportTime[$i])?date("h:ia",strtotime($reportTime[$i])):'') . '</th>';
					}
					?>
				</tr>
			</thead>
			<tbody>
				<?php
				$i = 1; $sample = array();
				foreach ($paramData as $param) :
					if (in_array($param->id, $pramIds)) :
						$os = json_decode($pirData->observation_sample);
						$diamention ='';
						if($param->requirement==1){ $diamention = $param->min_req.'/'.$param->max_req ; }
						if($param->requirement==2){ $diamention = $param->min_req.' '.$param->other_req ; }
						if($param->requirement==3){ $diamention = $param->max_req.' '.$param->other_req ; }
						if($param->requirement==4){ $diamention = $param->other_req ; }
						$char_class=''; if(!empty($param->char_class)){ $char_class='<img src="' . base_url('assets/images/symbols/'.$param->char_class.'.png') . '" style="width:20px;display:inline-block;vertical-align:middle;" />'; }

				?>
						<tr>
							<td><?= $i ?></td>
							<td><?=$param->process_no.' '.$char_class?></td>
							<td><?= $param->parameter ?></td>
							<td><?= $diamention ?></td>
							<td><?= $param->category_name ?></td>
							<td><?= $param->sev ?></td>
							<td><?= $param->potential_cause ?></td>
							<?php
							for ($j = 0; $j < $pirData->sampling_qty; $j++) {
							?><td><?= $os->{$param->id}[$j] ?></td>
							<?php
							}
							?>
						</tr>
				<?php
						$i++;
					endif;
				endforeach;
				?>
				<tr>
					<th>Comment</th>
					<td colspan="<?=$pirData->sampling_qty+3?>"><?= ((!empty($pirData->remark)) ? $pirData->remark : "") ?></td>
					<td colspan="3">First Piece Approval Symbol</td>
				</tr>
			</tbody>
		</table>
	


	</div>
</div>