<div class="row">
	<div class="col-12">

		<?php
		$sampleQty = !empty($sampleReportData)?count($sampleReportData):0
		?>
		<table class="table item-list-bb text-left" style="margin-top:2px;">
			<tr>
				<th>AAPPL Code</th>
				<td><?=$setupData->item_code?></td>
				<th>Part Description</th>
				<td colspan="3"><?=$setupData->full_name?></td>
			</tr>
			<tr>
				<th>Machine</th>
				<td><?='['.$setupData->machine_code.']'.$setupData->machine_name?></td>
				<th>Process</th>
				<td><?=$setupData->process_name?></td>
				<th>Jobcard</th>
				<td><?=$setupData->job_number?></td>
			</tr>
			<tr>
				<th>Req No.</th>
				<td><?=sprintf($setupData->req_prefix."%03d",$setupData->req_no)?></td>
				<th>Req Date</th>
				<td><?=formatDate($setupData->created_at)?></td>
				<th>Req By</th>
				<td><?=$setupData->emp_name?></td>
			</tr>
		</table>
		<table class="table item-list-bb" style="margin-top:2px;">
			<thead>
				<tr>
					<th rowspan="2">Sr. No.</th>
					<th rowspan="2">Operation No</th>
					<th rowspan="2">Product Characteristic</th>
					<th rowspan="2">Product Specification / Tolerance</th>
					<th rowspan="2">Evaluation / Measurement Technique</th>
					<th colspan="<?= $sampleQty ?>">Observation</th>
				</tr>
				<tr>
					<?php
					if(!empty($sampleQty)){
						for ($i = 1; $i <= $sampleQty; $i++) {
							echo '<th>' . $i . '</th>';
						}
					}else{
						echo "<th>1</th>";
					}
					?>
				</tr>
			</thead>
			<tbody>
				<?php
				$i=1;$inspectorComment = [];
				foreach ($paramData as $param) :
						$diamention ='';
						if($param->requirement==1){ $diamention = $param->min_req.'/'.$param->max_req ; }
						if($param->requirement==2){ $diamention = $param->min_req.' '.$param->other_req ; }
						if($param->requirement==3){ $diamention = $param->max_req.' '.$param->other_req ; }
						if($param->requirement==4){ $diamention = $param->other_req ; }
						$char_class=''; if(!empty($param->char_class)){ $char_class='<img src="' . base_url('assets/images/symbols/'.$param->char_class.'.png') . '" style="width:20px;display:inline-block;vertical-align:middle; />'; }

				?>
						<tr>
							<td><?= $i ?></td>
							<td><?=$param->process_no.' '.$char_class?></td>
							<td><?= $param->parameter ?></td>
							<td><?= $diamention ?></td>
							<td><?= $param->category_name ?></td>
							<?php

							if(!empty($sampleQty)){
								for ($j = 0; $j < $sampleQty; $j++) {
									$os = json_decode($sampleReportData[$j]->dimension_report)
								?><td class="text-center"><?= !empty($os->{$param->id})?$os->{$param->id}:''?></td>
								<?php
								
									if($i==1){
										
										if(!empty($sampleReportData[$j]->qci_note)){
											$inspectorComment[]=$sampleReportData[$j]->qci_note;
											
										}
									}
								}
							}else{
								?><td></td><?php
							}
							?>
						</tr>
				<?php
						$i++;
				endforeach;
				?>
				<tr >
					<th>Comment : </th><td  colspan="<?=(4+$sampleQty)?>"> <?=implode(",<br>",$inspectorComment)?></td>
				</tr>
			</tbody>
		</table>

		


	</div>
</div>