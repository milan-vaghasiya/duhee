<?php $this->load->view('includes/header'); ?>
<style>
	.titleText {
		color: #000000;
		font-size: 1.2rem;
		text-align: center;
		padding: 5px;
		background: #45729f;
		color: #ffffff;
		font-weight: 600;
		letter-spacing: 1px;
	}

	.card-body {
		padding: 20px 10px;
	}

	.jpFWTab nav>div a.nav-item.nav-link.active:after {
		left: -18% !important;
	}

	.ui-sortable-handle {
		cursor: move;
	}

	.ui-sortable-handle:hover {
		background-color: #daeafa;
		border-color: #9fc9f3;
		cursor: move;
	}
</style>
<div class="page-wrapper">
	<div class="container-fluid bg-container">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<div class="row">
							<div class="col-md-6">
								<h4 class="card-title">Job Card View [ Status : <?= $dataRow->order_status ?> ]</h4>
							</div>
							<div class="col-md-6">
								<a href="<?= base_url($headData->controller) ?>" class="btn waves-effect waves-light btn-outline-dark float-right"><i class="fa fa-arrow-left"></i> Back</a>
								<a href="<?= base_url($headData->controller) ?>/printDetailedRouteCard/<?= $dataRow->id ?>" class="btn waves-effect waves-light btn-outline-primary float-right mr-2" target="_blank"><i class="fa fa-print"></i> Print</a>
								<?php
								// if ($dataRow->job_order_status == 4 && $dataRow->scrap_status == 0):
								?>
								<!-- <button type="button" class="btn waves-effect waves-light btn-outline-warning float-right addScrap mr-2" data-button="both" data-modal_id="modal-lg" data-job_card_id="<?= $dataRow->id ?>" data-scrap_qty="<?= $totalScrapQty ?>" data-function="generateScrap" data-form_title="Scrap Management" data-fnsave="saveProductionScrape"><i class="fa fa-plus"></i> Generate Scrap</button>	 -->
								<?php
								// endif;
								?>
							</div>
						</div>
					</div>
					<div class="card-body">
						<div class="col-md-12">
							<div class="row">
								<!-- Column -->
								<div class="col-lg-12 col-xlg-12 col-md-12">
									<table class="table table-bordered-dark">
										<tr>
											<th>Job Card No.</th>
											<td><?= ($dataRow->job_number) ?></td>
											<th>Job Date </th>
											<td><?= date("d-m-Y", strtotime($dataRow->job_date)) ?></td>
											<th>Job Quantity </th>
											<td><?= floatVal($dataRow->qty) ?> <small><?= $dataRow->unit_name ?></small></td>
										</tr>
										<tr>
											<th>Product </th>
											<td><?= $dataRow->full_name ?></td>
											<th>Customer </th>
											<td colspan="3"><?= $dataRow->party_name ?></td>
										</tr>
										<tr>
											<th colspan="1">Remark </th>
											<td colspan="5"><?= $dataRow->remark ?></td>
										</tr>
										<?php
										$supplier_name = (!empty($reqMaterials['supplier_name'])) ? '<br><small>(' . $reqMaterials['supplier_name'] . ')</small>' : '';
										?>
										<tr hidden>
											<th>Material Name</th>
											<td><?= (!empty($reqMaterials['material_name'])) ? $reqMaterials['material_name'] . $supplier_name : ''; ?></td>
											<th class="text-center">Batch No.
												<hr style="margin:5px;border-color:#000000;">Heat No.
											</th>
											<!-- <td><?= (!empty($reqMaterials['heat_no'])) ? $reqMaterials['heat_no'] : ''; ?></td> -->
											<td class="text-center">
												<?= (!empty($reqMaterials['batch_no'])) ? $reqMaterials['batch_no'] : ''; ?>
												<hr style="margin:5px;border-color:#000000;">
												<?= (!empty($reqMaterials['heat_no'])) ? $reqMaterials['heat_no'] : ''; ?>
											</td>
											<th>Issue Qty. </th>
											<td><?= (!empty($reqMaterials['issue_qty'])) ? $reqMaterials['issue_qty'] : ''; ?></td>
										</tr>
									</table>
								</div>
								<div class="col-lg-12 col-xlg-12 col-md-12">
									<div class="card jpFWTab">
										<nav>
											<div class="nav nav-tabs nav-fill tabLinks" id="nav-tab" role="tablist">
												<a class="nav-item nav-link active productionTab" data-toggle="tab" href="#production_detail" role="tab" aria-controls="nav-home" aria-selected="true"> Production </a>

												<a class="nav-item nav-link" data-toggle="tab" href="#req_material" role="tab" aria-controls="nav-profile" aria-selected="false"> Material Detail</a>

												<!-- <a class="nav-item nav-link" data-toggle="tab" href="#production_stages" role="tab" aria-controls="nav-profile" aria-selected="false"> Production Stages</a> -->
											</div>
										</nav>
										<div class="tab-content py-3 px-3 px-sm-0" id="pills-tabContent">
											<!-- Process Approval Start -->
											<div class="tab-pane fade show active scrollable" style="height:60vh;" id="production_detail" role="tabpanel" aria-labelledby="pills-production_detail-tab">
												<div class="card-body">
													<div class="table-responsive">
														<table class="table table-striped table-bordered ">
															<thead class="thead-info">
																<tr class="text-center">
																	<th>Action</th>
																	<th>#</th>
																	<th class="text-left">Process Name</th>
																	<th class="text-left" hidden>Vendor</th>
																	<th>Unaccepted</th>
																	<th>Accepted</th>
																	<th>Prod. Ok</th>
																	<th>Pend. Prod.</th>
																	<th>Pend. Movement</th>
																	<th>Rejection Found </th>
																	<th>Rejection <br> Belongs To</th>
																	<th>Total Rework</th>
																	<th>Total Hold</th>
																	<th>Scrap</th>
																	<th>Status</th>
																</tr>
															</thead>
															<tbody>
																<?php
																if (!empty($dataRow->processData)) :
																    $i = 1;$processAuthList = !empty($this->processAuth)?explode(",",$this->processAuth):[];

																	foreach ($dataRow->processData as $row) :
																?>
																		<tr class="text-center">
																			<td>
																				<?php
																				$unaccpetedQty = 0;
																				if (!empty($row->process_approvel_data)) :

																					$approvalData = $row->process_approvel_data;

																					$button = "";
																					if(in_array($this->userRole,[1,-1]) || in_array($row->process_id,$processAuthList)){
    																					if ( $approvalData->stage_type !=3 && $approvalData->stage_type !=7) {
    																						if ($approvalData->status == 0 || $approvalData->status == 1) {
    																							/* Movement Button */
    																							$moveParam = "{'id' : " . $approvalData->id . ", 'modal_id' : 'modal-xl', 'form_id' : 'movement', 'title' : 'Move To Next Process','button':'close','fnsave' : 'saveProcessMovement', 'fnedit' : 'processMovement','btnSave':'other'}";
    																							if ($approvalData->ok_qty  > 0 && !empty($approvalData->out_process_id)) :
    																								$button .= '<a class="btn btn-warning btn-edit" datatip="Move to Next Process" flow="up" onclick="processMovement(' . $moveParam . ');"><i class="fa fa-step-forward"></i></a>';
    																							endif;
    
    																							/* Material Receive from store */
    																							$receiveParam = "{'job_approval_id' : " . $approvalData->id . ",'job_card_id':" . $approvalData->job_card_id . ",'modal_id' : 'modal-xl', 'form_id' : 'receiveStoredMaterial', 'title' : 'Material Receive From Store','fnsave' : 'saveReceiveStoredMaterial', 'fnedit' : 'receiveStoredMaterial'}";
    																							$button .= '<a href="javascript:void(0)" class="btn btn-success" datatip="Material Receive From Store" flow="up" onclick="receiveStoredMaterial(' . $receiveParam . ');"> <i class="fa fa-reply" aria-hidden="true"></i> </a>';
    
    																							/* Accept Button */
    																						
    																							$acceptParam = "{'id' : " . $approvalData->id . ", 'modal_id' : 'acceptInward','pending_qty':" . $row->unaccepted_qty . "}";
    																							if ($row->unaccepted_qty > 0) :
    																								$button .= '<a class="btn btn-success btn-edit" datatip="Accept Inward" flow="up" onclick="acceptInward(' . $acceptParam . ')"><i class="fa fa-check"></i></a>';
    																							endif;
    
    																							/* Production Log Button */
    																							$outParam = "{'id' : " . $approvalData->id . ", 'modal_id' : 'modal-xxl', 'form_id' : 'outWard', 'title' : 'Process Moved','button' : 'close'}";
    																							if (!empty($approvalData->in_process_id)) :
    																								$button .= '<a class="btn btn-info btn-edit" href="javascript:void(0)" datatip="Production Log" flow="up" onclick="outward(' . $outParam . ');"><i class="fas fa-file"></i></a>';
    																							endif;
    
    																							/* Store Location Button */
    																							$storeLocationParam = "{'id' : " . $approvalData->job_card_id . ",'transid' : " . $approvalData->id . ", 'modal_id' : 'modal-lg', 'form_id' : 'storeLocation', 'title' : 'Store Location','button' : 'close'}";
    																							if ($approvalData->out_process_id == 0 && $row->ok_qty > 0) :
    																								$button .= '<a class="btn btn-warning btn-edit" href="javascript:void(0)" datatip="Store Location" flow="up" onclick="storeLocation(' . $storeLocationParam . ');"><i class="fas fa-paper-plane"></i></a>';
    																							endif;
    																						}
    																					}
																					}
																					echo '<div class="actionWrapper" style="position:relative;">
																					<div class="actionButtons actionButtonsRight">
																						<a class="mainButton btn-instagram" href="javascript:void(0)"><i class="fa fa-cog"></i></a>
																						<div class="btnDiv" style="left:85%;">
																							' . $button . '
																						</div>
																					</div>
																				</div>';
																				endif;
                                                                                
																				?>
																			</td>

																			<td><?= $i++ ?></td>
																			<td class="text-left"><?= $row->process_name ?></td>
																			<td hidden><?= $row->vendor ?></td>
																			<td><?= floatval($row->unaccepted_qty) ?></td>
																			<td><?= floatval($row->accepted_qty) ?></td>
																			<td><?= floatval($row->ok_qty) ?></td>
																			<td><?= floatval($row->pending_prod_qty) ?></td>
																			<td><?= floatval($row->pending_prod_movement) ?></td>
																			<td><?= floatval($row->total_rejection_qty) ?></td>
																			<td><?= floatval($row->total_rej_belongs) ?> </td>
																			<td><?= floatval($row->total_rework_qty) ?></td>
																			<td><?= floatval($row->total_hold_qty) ?></td>
																			<td><?= floatval($row->scrap_qty) ?></td>
																			<td><?= $row->status ?></td>
																		</tr>
																	<?php endforeach;
																else : ?>
																	<tr>
																		<td colspan="11" class="text-center">No data available in table </td>
																	</tr>
																<?php endif; ?>
															</tbody>
														</table>
													</div>
												</div>
											</div>
											<!-- Process Approval End -->

											<!-- Material Detail Start -->
											<div class="tab-pane fade scrollable" style="height:60vh;" id="req_material" role="tabpanel" aria-labelledby="pills-req_material-tab">
												<div class="card-body">
													<?php if ($dataRow->job_order_status == 0) : ?>
														<div class="col-md-12 form-group">
															<form id="job_bom_data">
																<div class="row">
																	<input type="hidden" name="bom_job_card_id" id="bom_job_card_id" value="<?= $dataRow->id ?>">
																	<input type="hidden" name="bom_product_id" id="bom_product_id" value="<?= $dataRow->product_id ?>">
																	<input type="hidden" name="bom_process_id" id="bom_process_id" value="0">
																	<div class="col-md-6 form-group">
																		<label for="bom_item_id">Item Name</label>
																		<select name="bom_item_id" id="bom_item_id" class="form-control single-select req">
																			<option value="">Select Item Name</option>
																			<?php
																			foreach ($rawMaterial as $row) :
																				echo '<option value="' . $row->id . '">' . $row->item_name . '</option>';
																			endforeach;
																			?>
																		</select>
																	</div>
																	<div class="col-md-3 form-group">
																		<label for="bom_qty">Weight/Pcs</label>
																		<input type="number" name="bom_qty" id="bom_qty" class="form-control floatOnly req" min="0" value="" />
																	</div>
																	<div class="col-md-3 form-group">
																		<label for="">&nbsp;</label>
																		<button type="button" id="addJobBom" class="btn btn-outline-success waves-effect btn-block"><i class="fa fa-plus"></i> Add</button>
																	</div>
																</div>
															</form>
														</div>
													<?php endif; ?>
													<div class="table-responsive">
														<table class="table table-bordered">
															<thead class="thead-info">
																<tr class="text-center">
																	<th>#</th>
																	<th class="text-left">Item Name</th>
																	<th>Weight/Pcs</th>
																	<th>Supplier No</th>
																	<th>Batch No/Heat No</th>
																	<th>Required Qty.</th>
																	<th>Issue Qty.</th>
																	<th>Used Qty.</th>
																	<th>Stock Qty.</th>
																	<th>Action</th>
																</tr>
															</thead>
															<tbody id="requiredItems">

																<?php
																if (!empty($jobBom)) :
																	echo $jobBom;
																else :
																	echo '<tr><td colspan="8" class="text-center">No result found.</td></tr>';
																endif;
																?>
															</tbody>
														</table>
													</div>
												</div>
											</div>
											<!-- Material Detail End -->

											<!-- Production Stage Start -->
											<div class="tab-pane fade scrollable" style="height:60vh;" id="production_stages" role="tabpanel" aria-labelledby="pills-production_stages-tab">
												<div class="card-body">
													<div class="col-md-12">
														<div class="row">
															<div class="col-md-9 form-group">
																<label for="stage_id">Production Stages</label>
																<select name="stage_id" id="stage_id" data-input_id="process_id1" class="form-control single-select">
																	<option value="">Select Stage</option>
																	<?php
																	$productProcess = explode(",", $dataRow->process);
																	foreach ($processDataList as $row) :
																		if (!empty($productProcess) && (!in_array($row->id, $productProcess))) :
																			echo '<option value="' . $row->id . '">' . $row->process_name . '</option>';
																		endif;
																	endforeach;
																	?>
																</select>
																<input type="hidden" name="jobID" id="jobID" value="<?= $dataRow->id ?>">
																<input type="hidden" id="rnstages" value="<?= implode(',', $stageData['rnStages']) ?>">
																<input type="hidden" name="item_id" id="item_id" value="<?= $dataRow->product_id ?>" />
															</div>
															<div class="col-md-3 form-group">
																<label>&nbsp;</label>
																<button type="button" class="btn btn-success waves-effect add-process btn-block addJobStage" data-jobid="<?= $dataRow->id ?>">+ Add</a>
															</div>
														</div>
													</div>
													<div class="table-responsive">
														<!--<table id="<?= $dataRow->tblId ?>" class="table excel_table table-bordered">-->
														<table id="jobStages" class="table excel_table table-bordered">
															<thead class="thead-info">
																<tr>
																	<th style="width:10%;text-align:center;">#</th>
																	<th style="width:65%;">Process Name</th>
																	<th style="width:15%;">Preference</th>
																	<th style="width:10%;">Remove</th>
																</tr>
															</thead>
															<tbody id="stageRows">
																<?php
																if (!empty($stageData)) :
																	$i = 1;
																	foreach ($stageData['stages'] as $row) :
																		echo '<tr id="' . $row['process_id'] . '">
																				<td class="text-center">' . $i++ . '</td>
																				<td>' . $row['process_name'] . '</td>
																				<td class="text-center">' . ($row['sequence'] + 1) . '</td>
																				<td class="text-center">
																					<button type="button" data-pid="' . $row['process_id'] . '" class="btn btn-outline-danger waves-effect waves-light permission-remove removeJobStage"><i class="ti-trash"></i></button>
																				</td>
																			  </tr>';
																	endforeach;
																else :
																	echo '<tr><td colspan="3" class="text-center">No Data Found.</td></tr>';
																endif;
																?>
															</tbody>
														</table>
													</div>
												</div>
											</div>
											<!-- Production Stage End -->


										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="acceptInward" role="dialog" tabindex="-1" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog" role="document">
		<div class="modal-content animated slideDown">
			<div class="modal-header">
				<h4 class="modal-title" id="exampleModalLabel1">Accept Inward</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<form id="acceptedInwatd">
					<div class="col-md-12">
						<div class="row">
							<input type="hidden" name="job_approval_id" id="job_approval_id" value="">
							<input type="hidden" name="trans_type" value="1">
							<div class="col-md-12 form-group">
								<label for="in_qty">Quantity</label>
								<span class="float-right">Unaccepted Qty. : <span id="pending_act_qty">0</span></span>
								<input type="text" name="in_qty" id="in_qty" class="form-control floatOnly" value="">
							</div>
						</div>
					</div>

				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn waves-effect waves-light btn-outline-secondary btn-close save-form" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
				<button type="button" onclick="saveAcceptedQty('acceptedInwatd');" class="btn waves-effect waves-light btn-outline-success btn-save save-form"><i class="fa fa-check"></i> Save</button>
			</div>
		</div>
	</div>
</div>


<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url(); ?>assets/js/custom/production/job-card-view.js?v=<?= time() ?>"></script>