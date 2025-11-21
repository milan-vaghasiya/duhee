<form id="receiveChallan">
	<div class="col-md-12 row">
		<table class="table table-bordered-dark">
			<tr class="bg-light">
				<th>Item Name</th>
				<th>Qty</th>
				<th>Pending Qty</th>
			</tr>
			<tr>
				<td><?=$transData->item_name?></td>
				<td><?=floatval($transData->qty)?></td>
				<td><?=floatval($transData->qty - $transData->receive_qty)?></td>
			</tr>
		</table>
	</div>
	<div class="col-md-12 row">
		<input type="hidden" name="id" id="id" value="<?=$transData->id?>" />
		<div class="col-md-4">
			<label for="ref_batch">Challan No.</label>
			<input type="text" name="ref_batch" class="form-control req" value="" />
		</div>
	</div>
	
	<div class="error mt-2 general_error"></div>
	<div class="col-md-12 row">
		<table class="table table-bordered">
			<thead class="thead-info">
				<tr>
					<th>#</th>
					<th>Location</th>
					<th>Batch No.</th>
					<th>Stock Qty.</th>
					<th>Receive Qty.</th>
				</tr>
			</thead>
			<tbody id="batchData">
				<?php
					$i=1;
					if(!empty($challanData)){
						foreach($challanData as $row){
							if(!empty($row->qty) && abs($row->qty) > 0){
								echo '<tr>
									<td>'.$i.'</td>
									<td>['.$row->store_name.'] '.$row->location.'</td>
									<td>'.$row->batch_no.'</td>
									<td>'.floatVal(abs($row->qty)).'</td>
									<td>
										<input type="number" name="batch_quantity[]" class="form-control" min="0" value="" />
										<input type="hidden" name="batch_number[]" id="batch_number'.$i.'" value="'.$row->batch_no.'" />
										<input type="hidden" name="location[]" id="location'.$i.'" value="'.$row->location_id.'" />
										<input type="hidden" name="ref_no[]" id="ref_no'.$i.'" value="'.$row->ref_no.'" />
										<div class="error batch_qty'.$i.'"></div>
									</td>
								</tr>';
							$i++;
							}
						}
					}
				?>
			</tbody>
		</table>
	</div>
</form>
