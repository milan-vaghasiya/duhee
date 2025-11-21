<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
	<div class="container-fluid bg-container">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<div class="row">
							<div class="col-md-6">
								<h4 class="card-title">Final Inspection Report</h4>
							</div>
							<div class="col-md-6 ">
								<label class="text-danger float-right"><?= (empty($pdiStock->qty) || $pdiStock->qty <= 0) ? ' PDI is Compulsory ' : '' ?>[PDI Stock : <?= !empty($pdiStock->qty) ? $pdiStock->qty : 0 ?> <small>PCS</small>] </label>
							</div>

						</div>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-md-12">
								<form id="firLotForm">
									<div class="row">
										<!-- Column -->
										<div class="col-lg-12 col-xlg-12 col-md-12 mb-20">
											<table class="table table-bordered-dark">
												<tr class="bg-light">
													<th>FIR No</th>
													<th>Date</th>
													<th>FG Batch No</th>
													<th>Product </th>
													<th>Job No </th>
													<th>Qty </th>
												</tr>
												<tr>
													<td><?= !empty($dataRow->fir_number) ? $dataRow->fir_number : '' ?></td>
													<td><?= !empty($dataRow->fir_date) ? $dataRow->fir_date : '' ?></td>
													<td><?= !empty($dataRow->fg_batch_no) ? $dataRow->fg_batch_no  : '' ?></td>
													<td><?= !empty($dataRow->full_name) ? $dataRow->full_name : '' ?></td>
													<td><?= !empty($dataRow->job_number) ? $dataRow->job_number : '' ?></td>
													<td><?= floatval(!empty($dataRow->qty) ? $dataRow->qty : '') ?></td>
												</tr>
											</table>
										</div>
										<input type="hidden" id="fir_id" name="fir_id" value="<?= !empty($dataRow->id) ? $dataRow->id : '' ?>">
										<input type="hidden" id="qty" name="qty" value="<?= !empty($dataRow->qty) ? $dataRow->qty : '' ?>"><input type="hidden" id="job_card_id" name="job_card_id" value="<?= !empty($dataRow->job_card_id) ? $dataRow->job_card_id : $firData->job_card_id ?>">
										<input type="hidden" id="job_approval_id" name="job_approval_id" value="<?= !empty($dataRow->job_approval_id) ? $dataRow->job_approval_id : $firData->id ?>">

											<!-- <div class="col-md-2 form-group">
												<label for="total_ok_qty">Total Ok</label>
												<input type="text" class="form-control floatOnly " readOnly name="total_ok_qty" id="total_ok_qty" value="<?= (!empty(floatval($dataRow->total_ok_qty))) ? $dataRow->total_ok_qty : $dataRow->qty ?>">
											</div>
											<div class="col-md-2 form-group">
												<label for="total_rej_qty">Total Rejection</label>
												<input type="text" class="form-control floatOnly qtyCal" name="total_rej_qty" id="total_rej_qty" value="<?= !empty($dataRow->total_rej_qty) ? $dataRow->total_rej_qty : '' ?>">
											</div>
											<div class="col-md-2 form-group">
												<label for="total_rw_qty">Total Rework</label>
												<input type="text" class="form-control floatOnly qtyCal" name="total_rw_qty" id="total_rw_qty" value="<?= !empty($dataRow->total_rw_qty) ? $dataRow->total_rw_qty : '' ?>">
											</div> -->
										<!-- <div class="col-md-6 form-group">
											<label for="remark">Remark</label>
											<input type="text" class="form-control" name="remark" value="<?= !empty($dataRow->remark) ? $dataRow->remark : '' ?>">
										</div> -->
										<div class="col-lg-12 col-xlg-12 col-md-12">
											<div class="error general_error"></div>
											<div class="table-responsive">
												<table class="table jpExcelTable">
													<thead >
														<tr class="text-center" style="background:#eee;">
															<th style="width:3%">#</th>
															<th class="text-left">Special Char.</th>
															<th class="text-left">Product Parameter</th>
															<th>Product Specification</th>
															<th>Instrument</th>
															<th>Sample Freq.</th>
															<th>Inward Qty</th>
															<th>Date</th>
															<th >OK</th>
															<th>UD OK</th>
															<th>Rej</th>
															<th>RW</th>
															<th>Ins. By</th>
															<th>Action</th>
														</tr>
													</thead>
													<tbody>
														<?php
														if (!empty($firDimensionData)) :
															$i = 1;$j=0;
															foreach ($firDimensionData as $row) :
																$diamention = '';
																if ($row->requirement == 1) {
																	$diamention = $row->min_req . '/' .  $row->max_req;
																}
																if ($row->requirement == 2) {
																	$diamention = $row->min_req . ' ' .  $row->other_req;
																}
																if ($row->requirement == 3) {
																	$diamention = $row->max_req . ' ' .  $row->other_req;
																}
																if ($row->requirement == 4) {
																	$diamention = $row->other_req;
																}
																$prevInspQty = !empty($firDimensionData[$j+1]->inspected_qty)?floatval($firDimensionData[$j+1]->inspected_qty):0;
														?>
																<tr class="text-center">
																	<td><?= $i ?></td>
																	<td class="text-left"><?php if (!empty($row->char_class)) { ?><img style="width:25px;display:inline-block;vertical-align:middle;" src="<?= base_url('assets/images/symbols/' . $row->char_class . '.png') ?>"><?php } ?></td>
																	<td><?= $row->parameter ?></td>
																	<td><?= $diamention ?></td>
																	<td><?= $row->instrument_code ?></td>
																	<td><?= $row->potential_cause ?></td>
																	<td>
																		<?= floatval($row->in_qty) ?>
																		<div class="error inQty<?=$row->id?>"></div>
																	</td>
																	<td>
																		<input type="date" class="form-control floatOnly" id="trans_date_<?= $row->id ?>" name="trans_date[]" value="<?= !empty($row->trans_date) ? ($row->trans_date) : date("Y-m-d") ?>" min="<?= date("Y-m-d", strtotime("-1 Day")) ?>">
																	</td>
																	<td>
																		<input type="hidden" id="in_qty_<?=$row->id?>" value="<?= (!empty($row->in_qty)) ? $row->in_qty : '' ?>">
																		<input type="hidden" class="form-control " id="trans_id_<?= $row->id ?>" name="trans_id[]" value="<?= (!empty($row->id) && !empty($dataRow->id)) ? $row->id : '' ?>">
																		<input type="hidden" class="form-control " id="dimension_id_<?= $row->id ?>" name="dimension_id[]" value="<?= !empty($row->dimension_id) ? $row->dimension_id : $row->id ?>">
																		<input type="hidden" class="form-control" id="dim_remark_<?= $row->id ?>" name="dim_remark[]" value="<?= !empty($row->remark) ? floatval($row->remark) : '' ?>">
																		
																		<input type="text" class="form-control floatOnly" id="ok_qty_<?= $row->id ?>" name="ok_qty[]" value="<?= !empty($row->ok_qty) ? floatval($row->ok_qty) : '' ?>" <?=((!empty($dataRow->lot_type) && $dataRow->lot_type == 2)?:'readonly')?>>
																		
																		<div class="error insp_qty_<?= $row->id ?>"></div>
																	</td>
																	<td><input type="text" class="form-control floatOnly calOkQty" id="ud_ok_qty_<?= $row->id ?>" name="ud_ok_qty[]" data-row_id="<?=$row->id?>" value="<?= !empty($row->ud_ok_qty) ? floatval($row->ud_ok_qty) : '' ?>"> </td>
																	<td><input type="text" class="form-control floatOnly calOkQty" id="rej_qty_<?= $row->id ?>" name="rej_qty[]" data-row_id="<?=$row->id?>" value="<?= !empty($row->rej_qty) ? floatval($row->rej_qty) : '' ?>"></td>
																	<td><input type="text" class="form-control floatOnly calOkQty" id="rw_qty_<?= $row->id ?>" name="rw_qty[]" value="<?= !empty($row->rw_qty) ? floatval($row->rw_qty) : '' ?>" data-row_id="<?=$row->id?>"></td>
																	<td>
																		<select name="inspector_id[]" id="inspector_id_<?= $row->id ?>" class="form-control single-select">
																			<option value="">Select Inspector</option>
																			<?php if (!empty($empData)) {
																				foreach ($empData as $emp) {
																					$selected = (!empty($row->inspector_id) && $row->inspector_id == $emp->id) ? 'selected' : '';
																			?>
																					<option value="<?= $emp->id ?>" <?= $selected ?>><?= $emp->emp_name ?></option>
																			<?php }
																			} ?>
																		</select>
																		<div class="error inspector_id_<?= $row->id ?>"></div>
																	</td>
																	<td>
																		<?php
																		if(empty($prevInspQty)){
																		?>
																			<button  type="button" class="btn btn-sm btn-info" onclick="saveDimension(<?= $row->id ?>)">Save</button>
																		<?php
																		}
																		?>
																	</td>
																</tr>

															<?php $i++;$j++;
															endforeach;
														else : ?>
															<tr>
																<td colspan="12" class="text-center">No data available in table </td>
															</tr>
														<?php endif; ?>
													</tbody>
												</table>
											</div>

										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
					<div class="card-footer">
						<div class="col-md-12">
							<!-- <button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="saveLot('firLotForm','save');"><i class="fa fa-check"></i> Save</button> -->
							<a href="<?= base_url($headData->controller . '/firIndex') ?>" class="btn waves-effect waves-light btn-outline-secondary float-right save-form" style="margin-right:10px;"><i class="fa fa-times"></i> Close</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>



<?php $this->load->view('includes/footer'); ?>
<script>
	$(document).ready(function() {
		$(document).on("keyup", ".qtyCal", function() {
			var total_rej_qty = ($("#total_total_rej_qty").val() != '') ? $("#total_rej_qty").val() : 0;
			var total_rw_qty = ($("#total_rw_qty").val() != '') ? $("#total_rw_qty").val() : 0;

			var total_ok_qty = parseFloat($("#qty").val()) - total_rej_qty - total_rw_qty;

			$("#total_ok_qty").val(total_ok_qty);
		});

		$(document).on("keyup", ".calOkQty", function() {
			var id = $(this).data('row_id'); console.log(id);
			var in_qty=$("#in_qty_"+id).val();
			var ud_ok_qty=$("#ud_ok_qty_"+id).val();
			var rej_qty=$("#rej_qty_"+id).val();
			var rw_qty=$("#rw_qty_"+id).val();

			if(in_qty == ''){ in_qty=0; }
            if(ud_ok_qty == ''){ ud_ok_qty=0; }
            if(rej_qty == ''){ rej_qty=0; }
            if(rw_qty == ''){ rw_qty=0; }

			var okQty = parseFloat(in_qty)-parseFloat(ud_ok_qty)-parseFloat(rej_qty)-parseFloat(rw_qty);
			$("#ok_qty_"+id).val(okQty);
		});
	});

	function saveLot(formId, fnsave) {

		if (fnsave == "" || fnsave == null) {
			fnsave = "save";
		}
		var form = $('#' + formId)[0];
		var fd = new FormData(form);
		$.ajax({
			url: base_url + controller + '/' + fnsave,
			data: fd,
			type: "POST",
			processData: false,
			contentType: false,
			dataType: "json",
		}).done(function(data) {
			if (data.status === 0) {
				$(".errsor").html("");
				$.each(data.message, function(key, value) {
					$("." + key).html(value);
				});
			} else if (data.status == 1) {
				toastr.success(data.message, 'Success', {
					"showMethod": "slideDown",
					"hideMethod": "slideUp",
					"closeButton": true,
					positionClass: 'toastr toast-bottom-center',
					containerId: 'toast-bottom-center',
					"progressBar": true
				});
				window.location = base_url + controller + '/firIndex';
			} else {
				initTable(0);
				$('#' + formId)[0].reset();
				$(".modal").modal('hide');
				toastr.error(data.message, 'Error', {
					"showMethod": "slideDown",
					"hideMethod": "slideUp",
					"closeButton": true,
					positionClass: 'toastr toast-bottom-center',
					containerId: 'toast-bottom-center',
					"progressBar": true
				});
			}

		});
	}

	function saveDimension(id) {
		var in_qty=$("#in_qty_"+id).val();
		var ok_qty=$("#ok_qty_"+id).val();
		var ud_ok_qty=$("#ud_ok_qty_"+id).val();
		var rej_qty=$("#rej_qty_"+id).val();
		var remark=$("#dim_remark_"+id).val();
		var rw_qty=$("#rw_qty_"+id).val();
		var inspector_id=$("#inspector_id_"+id).val();
		var trans_date=$("#trans_date_"+id).val();
		var fir_id = $("#fir_id").val();

		if(in_qty == ''){ in_qty=0; }
		if(ok_qty == ''){ ok_qty=0; }
		if(ud_ok_qty == ''){ ud_ok_qty=0; }
		if(rej_qty == ''){ rej_qty=0; }
		if(rw_qty == ''){ rw_qty=0; }

		var fd ={id:id,in_qty:in_qty,ok_qty:ok_qty,ud_ok_qty:ud_ok_qty,rej_qty:rej_qty,remark:remark,rw_qty:rw_qty,inspector_id:inspector_id,fir_id:fir_id,trans_date:trans_date};
		console.log(fd);
		$.ajax({
			url: base_url + controller + '/saveDimension',
			data: fd,
			type: "POST",
			// processData: false,
			// contentType: false,
			dataType: "json",
		}).done(function(data) {
			if (data.status === 0) {
				$(".errsor").html("");
				$.each(data.message, function(key, value) {
					$("." + key).html(value);
				});
			} else if (data.status == 1) {
				toastr.success(data.message, 'Success', {
					"showMethod": "slideDown",
					"hideMethod": "slideUp",
					"closeButton": true,
					positionClass: 'toastr toast-bottom-center',
					containerId: 'toast-bottom-center',
					"progressBar": true
				});
				window.location.reload();
			} else {
				initTable(0);
				$('#' + formId)[0].reset();
				$(".modal").modal('hide');
				toastr.error(data.message, 'Error', {
					"showMethod": "slideDown",
					"hideMethod": "slideUp",
					"closeButton": true,
					positionClass: 'toastr toast-bottom-center',
					containerId: 'toast-bottom-center',
					"progressBar": true
				});
			}

		});
	}
</script>