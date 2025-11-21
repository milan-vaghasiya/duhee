<form>
	<div class="col-md-12">
		<div class="row">
			<input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
			<input type="hidden" name="error_msg" id="error_msg" value="">
			<input type="hidden" id="delivery_date" name="delivery_date" class="form-control" placeholder="dd-mm-yyyy" min="<?= (!empty($dataRow->job_date)) ? $dataRow->job_date : date("Y-m-d") ?>" value="<?= (!empty($dataRow->delivery_date)) ? $dataRow->delivery_date : date("Y-m-d") ?>" />
			<input type="hidden" name="job_prefix" value="<?=(!empty($dataRow->job_prefix))?$dataRow->job_prefix:""?>">
			<input type="hidden" name="job_no" value="<?=(!empty($dataRow->job_no))?$dataRow->job_no:""?>">		
			<input type="hidden" name="heat_treatment" id="heat_treatment" value="<?=(!empty($dataRow->heat_treatment))?$dataRow->heat_treatment:""?>">
			<input type="hidden" name="is_npd" id="is_npd" value="<?=(!empty($dataRow->is_npd))?$dataRow->is_npd:"0"?>">

			<div class="col-md-2 form-group">
				<label for="job_no">Job Card No.</label>
				<input type="text" id="job_no" class="form-control req" value="<?= (!empty($dataRow)) ? ($dataRow->job_prefix . $dataRow->job_no) : ($jobPrefix . $jobNo) ?>" readonly />
			</div>
			<div class="col-md-2 form-group">
				<label for="wo_no">Duhee Job Card No.</label>
				<input type="text" id="wo_no" name="wo_no" class="form-control req" value="<?= (!empty($dataRow->wo_no))?$dataRow->wo_no:''?>"  />
			</div>
			<div class="col-md-2 form-group">
				<label for="job_date">Job Card Date</label>
				<input type="date" id="job_date" name="job_date" class="form-control req" placeholder="dd-mm-yyyy" value="<?= (!empty($dataRow->job_date)) ? $dataRow->job_date : $maxDate ?>" min="<?= $startYearDate ?>" max="<?= $maxDate ?>" />
			</div>
			
			<div class="col-md-3 form-group">
				<label for="party_id">Customer</label>
				<select name="party_id" id="party_id" class="form-control single-select req" autocomplete="off">
					<option value="">Select Customer</option>
					<option value="0" <?= (!empty($dataRow->id) && $dataRow->party_id == 0) ? "selected" : "" ?>>Self Stock</option>
					<?php
					foreach ($customerData as $row) :
						$selected = (!empty($dataRow->party_id) && $dataRow->party_id == $row->party_id) ? "selected" : "";
						echo '<option value="' . $row->party_id . '" ' . $selected . '>' . $row->party_code . '</option>';
					endforeach;
					?>
				</select>
			</div>
			<div class="col-md-3 form-group">
				<label for="sales_order_id">Sales Order No.</label>
				<select name="sales_order_id" id="sales_order_id" class="form-control single-select">
					<option value="">Select Order No.</option>
					<?php
					if (!empty($dataRow)) :
						foreach ($customerSalesOrder as $row) :
							$selected = (!empty($dataRow->sales_order_id) && $dataRow->sales_order_id == $row->id) ? "selected" : "";
							echo '<option value="' . $row->id . '" ' . $selected . '>' . getPrefixNumber($row->trans_prefix, $row->trans_no) . '</option>';
						endforeach;
					endif;
					?>
				</select>
			</div>

			<div class="col-md-6 form-group">
				<label for="item_id">Part Name</label>
				<select name="product_id" id="item_id" class="form-control single-select req" autocomplete="off">
					<option value="">Select Product</option>
					<?php
					if (!empty($dataRow->id)) :
						echo $productData['htmlData'];
					endif;
					?>
				</select>
			</div>

			<div class="col-md-3 form-group">
				<label for="job_category">Job Work Type</label>
				<select name="job_category" id="job_category" class="form-control req">
					<option value="0" data-job_no="<?= (!empty($dataRow)) ? $dataRow->job_prefix. $dataRow->job_no : $jobPrefix. $jobNo ?>" <?= (!empty($dataRow) && $dataRow->job_category == 0) ? "selected" : "" ?> <?= (!empty($dataRow->sales_order_id) && $dataRow->job_category != 0) ? "disabled='disabled'" : "" ?>>Manufacturing</option>
					<option value="1" data-job_no="<?= (!empty($dataRow)) ? $dataRow->job_prefix. $dataRow->job_no : $jobwPrefix. $jobwNo ?>" <?= (!empty($dataRow) && $dataRow->job_category == 1) ? "selected" : "" ?> <?= (!empty($dataRow->sales_order_id) && $dataRow->job_category != 1) ? "disabled='disabled'" : "" ?>>Job Work</option>
				</select>
			</div>
			<div class="col-md-3 form-group">
				<label for="qty">Quatity</label>
				<input type="text" name="qty" id="qty" class="form-control numericOnly countWeight req" min="0" placeholder="Enter Qty." value="<?= (!empty($dataRow->qty)) ? $dataRow->qty : "" ?>" />
			</div>

			<!--<div class="col-md-3 form-group">
				<label for="is_npd">Is NPD?</label>
				<select name="is_npd" id="is_npd" class="form-control">
					<option value="0" <?= (!empty($dataRow->id) && $dataRow->is_npd == 0) ? "selected" : "" ?>>No</option>
					<option value="1" <?= (!empty($dataRow->id) && $dataRow->is_npd == 1) ? "selected" : "" ?>>Yes</option>
				</select>
			</div>-->

			<div class="col-md-12 mt-3 form-group" id="processDiv">
				<label for="process">Select Process</label>
				<div id="processData">
					<?php
					if(!empty($dataRow->process)) :
						echo $productProcessAndRaw['htmlData'];
					endif;
					?>
				</div>
				<div class="error process"></div>
			</div>
			<!--<div class="col-md-3 mt-3 form-group">
				<label for="heat_treatment">Heat Treatment?</label>
				<select name="heat_treatment" id="heat_treatment" class="form-control">
					<option value="">Select</option>
					<option value="0" <?= (!empty($dataRow->id) && $dataRow->heat_treatment == 0) ? "selected" : "" ?>>No</option>
					<option value="1" <?= (!empty($dataRow->id) && $dataRow->heat_treatment == 1) ? "selected" : "" ?>>Yes</option>
				</select>
			</div>-->
			<div class="col-md-12 mt-3 form-group">
				<label for="remark">Remark</label>
				<input type="text" name="remark" id="remark" class="form-control" value="<?= (!empty($dataRow->remark)) ? $dataRow->remark : "" ?>" />
			</div>
			<input type="hidden" name="used_at" id="used_at" value="">
			<input type="hidden" name="handover_to" id="handover_to" value="">
			<!-- <div class="col-md-3 mt-3 form-group">
				<label for="used_at">Dispatch To</label>
				<select name="used_at" id="used_at" class="form-control">
					<option value="0">Inhouse</option>
					<option value="1">Vendor</option>
				</select>
			</div> -->
			<!-- <div class="col-md-3 mt-3 form-group">
				<label for="handover_to">Dispatch Location</label>
				<select name="handover_to" id="handover_to" class="form-control single-select">
					<option value="">Select Whome to handover</option>
					<?php
						if (!empty($machineList)) {
							foreach ($machineList as $row) {
					?>
						<option value="<?= $row->id ?>"><?= '[' . $row->item_code . '] ' . $row->item_name ?></option>
					<?php
							}
						}
					?>
				</select>
			</div> -->
			<div class="error error_msg"></div>
		</div>
		<hr>
		<?php if(!empty($allocatedMaterial)): ?>
		<div class="row">
			<div class="col-md-12">
				<h4>Material Allocated : </h4>
			</div>
			<div class="col-md-12">
				<div class="table-responsive">
					<table class="table table-bordered align-items-center">
						<thead class="thead-info">
							<tr>
								<th>#</th>
								<th>Item Name</th>
								<th>Location</th>
								<th>Batch/Heat  No.</th>
								<th>Qty</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$i=1;
								foreach($allocatedMaterial as $row):
							?>
								<tr>
									<td><?=$i++?></td>
									<td><?=$row->item_full_name?></td>
									<td><?="[ ".$row->store_name." ] ".$row->location?></td>
									<td><?=$row->batch_no?></td>
									<td><?=$row->qty?></td>
								</tr>
							<?php
								endforeach;
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<hr>
		<?php endif; ?>
		<div class="row">
			<div class="col-md-12">
				<h4>Stock Details : </h4>
			</div>
			<div class="col-md-12">
				<div class="table-responsive">
					<table id="requestItems" class="table table-bordered align-items-center">
						<?php
							if (!empty($dataRow->id)) :
								echo $productProcessAndRaw['BomTable'];
							else :
						?>
							<thead class="thead-info">
								<tr>
									<th>Item Name</th>
									<th>Bom Qty (PCS)</th>
									<th>Required Qty (PCS)</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td colspan="3" class="text-center">No Data Found.</td>
								</tr>
							</tbody>
						<?php
							endif;
						?>
					</table>
				</div>
			</div>
		</div>
	</div>
</form>