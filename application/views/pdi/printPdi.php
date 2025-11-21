<div class="row">
		<?php
			$pramIds = explode(',', $pdiData->parameter_ids);
			$smplingQty = ($pdiData->sampling_qty > 0) ? $pdiData->sampling_qty : 5;
		?>
	<div class="col-12">
		<table class="table item-list-bb text-left" style="margin-top:2px;">
			<tr>
				<th>Supplier Code:-</th>
				<td>112434 / 310397</td>
				<th>Customer Name:-</th>
				<td><?=!empty($pdiData->party_name)?$pdiData->party_name:''?></td>
				<th>AAPPL Code No.:-</th>
				<td><?=!empty($pdiData->item_code)?$pdiData->item_code:''?></td>
				<th>Part No.:-</th>
				<td><?=!empty($pdiData->part_no)?$pdiData->part_no:''?></td>
				<th>Cust Drawing No.:-</th>
				<td><?=!empty($pdiData->drawing_no)?$pdiData->drawing_no:''?></td>
			</tr>
			<tr>
				<th>Part Description:-</th>
				<td colspan="2"><?=!empty($pdiData->full_name)?$pdiData->full_name:''?></td>
				<th>Latest Rev. Change Level.:-</th>
				<td colspan="2"><?=!empty($pdiData->rev_no)?$pdiData->rev_no:''?></td>
				<th>PDI Report No.:-</th>
				<td><?=!empty($pdiData->batch_no)?$pdiData->batch_no:''?></td>
				<th>PDI Report Date.:-</th>
				<td><?=!empty($pdiData->trans_date)?formatDate($pdiData->trans_date):''?></td>
			</tr>
			<tr>
				<th>Lot No.:-</th>
				<td><?=!empty($pdiData->trans_no)?formatDate($pdiData->trans_no):''?></td>
				<th>Job Card No.:-</th>
				<td></td>
				<th>Inv No.:-</th>
				<td></td>
				<th>Inv Qty.:-</th>
				<td></td>
				<th>Inv Date.:-</th>
				<td></td>
			</tr>
		</table>
		<table class="table item-list-bb " style="margin-top:10px;" >
		    <thead>
				<tr>
					<th rowspan="2">Sr. No.</th>
					<th rowspan="2">Special Char.</th>
					<th rowspan="2">Product Characteristic</th>
					<th rowspan="2">Product Specification/Tolerance</th>
					<th rowspan="2">Evaluation/Measurement Technique</th>
					<th colspan="5">Observation</th>
					<th rowspan="2">Decision<br>(Ok/Not Ok)</th>
				</tr>
				<tr>
					<th>1</th>
					<th>2</th>
					<th>3</th>
					<th>4</th>
					<th>5</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$i = 1; $sample = array();
				foreach ($paramData as $param) :
					if (in_array($param->id, $pramIds)) :
						$os = json_decode($pdiData->observation_sample);
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
							<?php
							for ($j = 0; $j < 5; $j++) {
							?><td class="text-center"><?= $os->{$param->id}[$j] ?></td>
							<?php
							}
							?>
							<td></td>
						</tr>
				<?php
						$i++;
					endif;
				endforeach;
				?>
			</tbody>
		</table>
		<table class="table item-list-bb" style="margin-top:2px;">
			<tr>
				<td colspan="3"><b>Comment :- </b> <?=!empty($pdiData->remark)?$pdiData->remark:''?></td>
				<td ><b>Checked By :- </b>  <?=!empty($pdiData->emp_name)?$pdiData->emp_name:''?></td>
			</tr>
		
			<tr>
				<td><img style="width:25px;display:inline-block;vertical-align:middle;" src="<?= base_url('assets/images/symbols/critical.png') ?>"> <span style="">Critical Characteristic </span> </td>
				<td><img style="width:25px;display:inline-block;vertical-align:middle;" src="<?= base_url('assets/images/symbols/major.png') ?>"> <span style="">Major </span></td>
				<td><img style="width:25px;display:inline-block;vertical-align:middle;" src="<?= base_url('assets/images/symbols/minor.png') ?>"> <span style="">Minor</span></td>
				<td><b>Approved By:-</b></td>
			</tr>
		</table>
	</div>
</div>