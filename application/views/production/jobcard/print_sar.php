<div class="row">
	<div class="col-12">
		<table class="table item-list-bb text-left">
			<tr>
				<th style="width:20%">Part Name</th>
				<td style="width:30%"><?= (!empty($dataRow->product_name) ? $dataRow->product_name : '') ?></td>
				<th style="width:20%">Date</th>
				<td style="width:30%"></td>
			</tr>
			<tr>
				<th>Part Number</th>
				<td><?= (!empty($dataRow->part_no) ? $dataRow->part_no : '') ?></td>
				<th>Customer Name / Code</th>
				<td><?= (!empty($dataRow->party_name) ? $dataRow->party_name : '') ?></td>
			</tr>
			<tr>
				<th>Part Code</th>
				<td><?= (!empty($dataRow->product_code) ? $dataRow->product_code : '') ?></td>
				<th>M/C No.</th>
				<td></td>
			</tr>
			<tr>
				<th>Lot No. / DT.</th>
				<td><?= (!empty($dataRow->wo_no) ? $dataRow->wo_no : '') ?></td>
				<th>Program No.</th>
				<td></td>
			</tr>
			<tr>
				<th>Grade</th>
				<td><?= (!empty($dataRow->material_grade) ? $dataRow->material_grade : '') ?></td>
				<th>Cycle Time (In Second)</th>
				<td></td>
			</tr>
			<tr>
				<th>GRN No.</th>
				<td></td>
				<th>Heat No.</th>
				<td><?= (!empty($dataRow->mill_heat_no) ? $dataRow->mill_heat_no : '') ?></td>
			</tr>
			<tr>
				<th>Drawing No.</th>
				<td><?= (!empty($dataRow->drawing_no) ? $dataRow->drawing_no : '') ?></td>
				<th>Rev. No.</th>
				<td><?= (!empty($dataRow->rev_no) ? $dataRow->rev_no : '') ?></td>
			</tr>
			<tr>
				<th>Dept. Name</th>
				<td><?= (!empty($processData->dept_name) ? $processData->dept_name : '') ?></td>
				<th>Process</th>
				<td><?= (!empty($processData->process_name) ? $processData->process_name : '') ?></td>
			</tr>
		</table>

		<table class="table item-list-bb" style="margin-top:2px;">
			<thead>
				<tr class="text-left">
					<th colspan="4" style="font-size:1.0rem;"><u>PROCESS PARAMETER</u></th>
				</tr>
				<tr>
					<th style="width:10%">No.</th>
					<th style="width:30%">Parameter</th>
					<th style="width:30%">Specification</th>
					<th style="width:30%">Actual</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$i=1;
				if (!empty($paramData)):
					foreach ($paramData as $param) :
						$diamention ='';
						if($param->requirement==1){ $diamention = $param->min_req.'/'.$param->max_req ; }
						if($param->requirement==2){ $diamention = $param->min_req.' '.$param->other_req ; }
						if($param->requirement==3){ $diamention = $param->max_req.' '.$param->other_req ; }
						if($param->requirement==4){ $diamention = $param->other_req ; }
						
						echo '<tr class="text-center">
								<td>'. $i .'</td>
								<td>'. $param->parameter .'</td>
								<td>'. $diamention .'</td>
								<td></td>
							</tr>';
						$i++;
					endforeach;
				else:
					echo '<tr class="text-center"><td colspan="4">Data not available.</td></tr>';
				endif;
				?>
			</tbody>
		</table>

		<table class="table item-list-bb" style="margin-top:2px;">
			<thead>
				<tr class="text-left">
					<th colspan="9" style="font-size:1.0rem;"><u>PRODUCT PARAMETER</u></th>
				</tr>
				<tr>
					<th style="width:10%" rowspan="2">No.</th>
					<th style="width:20%" rowspan="2">Parameter</th>
					<th style="width:20%" rowspan="2">Specification</th>
					<th colspan="6">FPA</th>
				</tr>
				<tr>
					<th style="width:10%">1</th>
					<th style="width:10%">2</th>
					<th style="width:10%">3</th>
					<th style="width:10%">4</th>
					<th style="width:10%">5</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$i=1;
				if (!empty($prodParamData)):
					foreach ($prodParamData as $param) :
						$diamention ='';
						if($param->requirement==1){ $diamention = $param->min_req.'/'.$param->max_req ; }
						if($param->requirement==2){ $diamention = $param->min_req.' '.$param->other_req ; }
						if($param->requirement==3){ $diamention = $param->max_req.' '.$param->other_req ; }
						if($param->requirement==4){ $diamention = $param->other_req ; }
						
						echo '<tr class="text-center">
								<td>'. $i .'</td>
								<td>'. $param->parameter .'</td>
								<td>'. $diamention .'</td>';
								for ($c = 0; $c < 5; $c++) :
									echo '<td>'.(!empty($obj->{$param->id}) ? $obj->{$param->id}[$c] : '').'</td>';
								endfor;
						echo '</tr>';
						$i++;
					endforeach;
				else:
					echo '<tr class="text-center"><td colspan="8">Data not available.</td></tr>';
				endif;
				?>
				<tr>
					<td colspan="5">FINAL RESULT : ACCEPTABLE / ACCEPTABLE WITH GRACE / REJECT</td>
					<td colspan="3" class="text-center">* All Dimensions are in mm.</td>
				</tr>
				<tr>
					<td colspan="8">Note :- <?= (!empty($dataRow->prod_remark) ? $dataRow->prod_remark : '') ?></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>