<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
	<div class="container-fluid bg-container">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header text-center">
						<h4>
							<u>JobWork Invoice</u>
							<!--<span style="float:right">-->
							<!--	<a href="javascript:void(0)" class="clickMic" ><i class="fas fa-microphone fs-15"></i></a>-->
							<!--</span>-->
						</h4>
					</div>
					<div class="card-body">
						<form autocomplete="off" id="savePurchaseInvoice">
							<div class="col-md-12">
								<input type="hidden" name="id" value="<?= (!empty($invoiceData->id)) ? $invoiceData->id : "" ?>" />
								<input type="hidden" name="entry_type" id="entry_type" value="19">
								<input type="hidden" name="reference_entry_type" id="reference_entry_type" value="<?= (!empty($invoiceData->from_entry_type)) ? $invoiceData->from_entry_type : "" ?>">
								<input type="hidden" name="reference_id" value="<?= (!empty($invoiceData->ref_id)) ? $invoiceData->ref_id : ((!empty($ref_id))?$ref_id:"") ?>">
								<input type="hidden" name="gst_type" id="gst_type" value="<?= (!empty($invoiceData->gst_type)) ? $invoiceData->gst_type : (!empty($gst_type)?$gst_type:"") ?>">
								<input type="hidden" name="inv_prefix" id="inv_prefix" class="form-control req" value="<?= (!empty($invoiceData->trans_prefix)) ? $invoiceData->trans_prefix : $trans_prefix ?>" />
								<input type="hidden" name="inv_no" id="inv_no" class="form-control" placeholder="Enter Invoice No." value="<?= (!empty($invoiceData->trans_no)) ? $invoiceData->trans_no : ((!empty($nextTransNo))?$nextTransNo:"") ?>" />
								<div class="row form-group">
									<div class="col-md-2">
										<label for="doc_no">Invoice No.</label>
										<input type="text" name="doc_no" id="doc_no" class="form-control" value="<?= (!empty($invoiceData->doc_no)) ? $invoiceData->doc_no : "" ?>" />
									</div>
									<div class="col-md-2">
										<label for="inv_date">Invoice Date</label>
										<input type="date" id="inv_date" name="inv_date" class=" form-control req inv_date" placeholder="dd-mm-yyyy" value="<?= (!empty($invoiceData->trans_date)) ? $invoiceData->trans_date : date("Y-m-d") ?>" />
									</div>
									
									<div class="col-md-4">
										<label for="party_id">Party Name</label>
										<div for="party_id1" class="float-right">
											<a href="javascript:void(0)" class="text-primary font-bold createJobWorkInvoice permission-write1" datatip="JobWork" flow="down">+JobWork</a>
										</div>
										<select name="party_id" id="party_id" class="form-control single-select partyOptions req">
											<option value="">Select Party</option>
											<?php
											foreach ($vendorList as $row) :
												$selected = (!empty($party_id) && $party_id == $row->id) ? "selected" : ((!empty($invoiceData->party_id) && $invoiceData->party_id == $row->id) ? "selected" : "");
												echo "<option data-row='" . json_encode($row) . "' value='" . $row->id . "' " . $selected . ">" . $row->party_name . "</option>";
											endforeach;
											?>
										</select>
										<input type="hidden" name="party_name" id="party_name" value="<?= (!empty($invoiceData->party_name)) ? $invoiceData->party_name : ((!empty($party_name)) ? $party_name : "") ?>">
										<input type="hidden" name="party_state_code" id="party_state_code" value="<?= (!empty($invoiceData->party_state_code)) ? $invoiceData->party_state_code : ((!empty($invMaster->gstin)) ? substr($invMaster->gstin, 0, 2) : "") ?>">
										<input type="hidden" name="gstin" id="gstin" value="<?=(!empty($invoiceData->gstin))?$invoiceData->gstin:((!empty($invMaster->gstin))?$invMaster->gstin:"")?>">
									</div>
									<div class="col-md-2 form-group">
										<label for="gst_applicable">GST Applicable</label>
										<select name="gst_applicable" id="gst_applicable" class="form-control req">
											<option value="1" <?= (!empty($invoiceData) && $invoiceData->gst_applicable == 1) ? "selected" : "" ?>>Yes</option>
											<option value="0" <?= (!empty($invoiceData) && $invoiceData->gst_applicable == 0) ? "selected" : "" ?>>No</option>
										</select>
									</div>
									<div class="col-md-2 form-group">
										<label for="apply_round">Round Off ?</label>
										<select name="apply_round" id="apply_round" class="form-control single-select">
											<option value="0" <?= (!empty($invoiceData) && $invoiceData->apply_round == 0) ? "selected" : "" ?>>Yes</option>
											<option value="1" <?= (!empty($invoiceData) && $invoiceData->apply_round == 1) ? "selected" : "" ?>>No</option>
										</select>
									</div>

									<input type="hidden" name="sales_type" id="sales_type" value="<?= (!empty($invoiceData->sales_type)) ? $invoiceData->sales_type : 3 ?>">

									<input type="hidden" name="challan_no" class="form-control" value="<?= (!empty($invoiceData->challan_no)) ? $invoiceData->challan_no : (isset($orderData) && !empty(($orderData->challan_no)) ? $orderData->challan_no : "") ?>" />
								</div>
								
							</div>
							<hr>
							<div class="col-md-12 row">
								<div class="col-md-12">
									<h4>Item Details : </h4>
								</div>
								<!-- <div class="col-md-6"><button type="button" class="btn btn-outline-success waves-effect float-right add-item"><i class="fa fa-plus"></i> Add Item</button></div> -->
							</div>
							<div class="col-md-12 mt-3">
								<div class="error item_name_error"></div>
								<div class="row form-group">
									<div class="table-responsive ">
										<table id="jobinvoiceItems" class="table table-striped table-borderless">
											<thead class="table-info">
												<tr>
													<th style="width:5%;">#</th>
													<th>Item Name</th>
													<th>HSN Code</th>
													<th>Qty.</th>
													<th>Unit</th>
													<th>Price</th>
													<th>GST Per</th>
													<th class="igstCol">IGST</th>
													<th class="cgstCol">CGST</th>
													<th class="sgstCol">SGST</th>
													<th>Disc.</th>
													<th class="amountCol">Amount</th>
													<th class="netAmtCol">Amount</th>
													<!-- <th>Remark</th> -->
													<th class="text-center" style="width:10%;">Action</th>
												</tr>
											</thead>
											<tbody id="tempItem" class="temp_item">
													<tr id="noData">
														<td colspan="14" class="text-center">No data available in table</td>
													</tr>
											</tbody>
										</table>
									</div>
								</div>
								<hr>
								<div class="col-md-12 row mb-3">
									<div class="col-md-6">
										<h4>Summary Details : </h4>
									</div>
									<div class="col-md-6 text-right">
										<button type="button" class="btn btn-outline-primary waves-effect" data-toggle="modal" data-target="#expModel"><i class="fa fa-plus"></i> Add Expense</button>
									</div>
								</div>
								<!-- Created By Mansee @ 29-12-2021 -->
								<div class="row form-group">
									<div style="width:100%;">
										<table id="summaryTable" class="table" >
											<thead class="table-info">
												<tr>
													<th style="width: 30%;">Descrtiption</th>
													<th style="width: 10%;">Percentage</th>
													<th style="width: 10%;">Amount</th>
													<th style="width: 20%;">Net Amount</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td>Sub Total</td>
													<td></td>
													<td></td>
													<td>
														<input type="text" name="taxable_amount" id="taxable_amount" class="form-control summaryAmount" value="0" readonly />
													</td>
												</tr>
												<?php
												$beforExp = "";
												$afterExp = "";
												$tax = "";
												$invExpenseData = (!empty($invoiceData->expenseData))?$invoiceData->expenseData:array();
												
												foreach ($expenseList as $row) :
													$expPer = 0;
													$expAmt = 0;
													$perFiledName = $row->map_code."_per"; 
													$amtFiledName = $row->map_code."_amount";
													if(!empty($invExpenseData) && $row->map_code != "roff"):	
														$expPer = $invExpenseData->{$perFiledName};
														$expAmt = $invExpenseData->{$amtFiledName};
													endif;
													$expHiddenInput = '';
													$expHiddenInput = '<input type="hidden" name="' . $row->map_code . '_acc_id" id="' . $row->map_code . '_acc_id" value="'.$row->acc_id.'">';
													$expTrId = ($row->map_code != "roff")?'exp_tr_'.$row->map_code:'';
													$expTrStyle = ($row->map_code != "roff")?(($expAmt<=0)?'display:none;':''):'';
													/* $options = '<select class="form-control single-select" name="' . $row->map_code . '_acc_id" id="' . $row->map_code . '_acc_id">';
													
													foreach ($ledgerList as $ledgerRow) :
														if ($ledgerRow->group_code != "DT") :
															$filedName = $row->map_code."_acc_id";
															if(!empty($invExpenseData->{$filedName})):
																if($row->map_code != "roff"):
																	$selected = ($ledgerRow->id == $invExpenseData->{$filedName})?"selected":(($ledgerRow->id == $row->acc_id) ? 'selected' : '');
																else:
																	$selected = ($ledgerRow->id == $invoiceData->round_off_acc_id)?"selected":(($ledgerRow->id == $row->acc_id) ? 'selected' : '');
																endif;
															else:
																$selected = ($ledgerRow->id == $row->acc_id) ? 'selected' : '';
																$expHiddenInput .= '<input type="hidden" name="' . $row->map_code . '_acc_id" id="' . $row->map_code . '_acc_id" value="">';
															endif;
															//$options .= '<option value="' . $ledgerRow->id . '" ' . $selected . '>' . $ledgerRow->party_name . '</option>';
														endif;
													endforeach; */
													//$options .= '</select>';
													if ($row->position == 1) :														
														$beforExp .= '<tr id="'.$expTrId.'" style="'.$expTrStyle.'">
															<td>' . $row->exp_name.$expHiddenInput.'</td>
															
															<td>';
														
														$readonly = "";
														$perBoxType = "number";
														$calculateSummaryPer = "calculateSummary";
														$calculateSummaryAmt = "calculateSummary";
														if($row->calc_type != 1):
															$perBoxType = "number";
															$readonly = "readonly";
															$calculateSummaryPer = "calculateSummary";
															$calculateSummaryAmt = "";
														else:
															$perBoxType = "hidden";
															$readonly = "";
															$calculateSummaryPer = "";
															$calculateSummaryAmt = "calculateSummary";
														endif;
														
														$beforExp .= "<input type='".$perBoxType."' name='" . $row->map_code . "_per' id='" . $row->map_code . "_per' data-row='".json_encode($row)."' value='".$expPer."' class='form-control ".$calculateSummaryPer."'> ";
														$beforExp .= "</td>
														<td><input type='number' id='".$row->map_code."_amt' class='form-control ".$calculateSummaryAmt."' data-sm_type='exp' data-row='".json_encode($row)."' value='".$expAmt."' ".$readonly."></td>
														<td><input type='number' name='" . $row->map_code . "_amount' id='" . $row->map_code . "_amount'  value='0' class='form-control summaryAmount' readonly /> <input type='hidden' id='other_" . $row->map_code . "_amount' class='otherGstAmount' value='0'> </td>
														</tr>";
													else :
														
														$afterExp .= '<tr id="'.$expTrId.'" style="'.$expTrStyle.'">
															<td>' . $row->exp_name.$expHiddenInput . '</td>
															<td>';
														$readonly = "";
														$perBoxType = "number";
														$calculateSummaryPer = "calculateSummary";
														$calculateSummaryAmt = "calculateSummary";
														if($row->map_code != "roff" && $row->calc_type != 1):
															$perBoxType = "number";
															$readonly = "readonly";
															$calculateSummaryPer = "calculateSummary";
															$calculateSummaryAmt = "";
														else:
															$perBoxType = "hidden";
															$readonly = "";
															$calculateSummaryPer = "";
															$calculateSummaryAmt = "calculateSummary";
														endif;
														$afterExp .= "<input type='".$perBoxType."' name='" . $row->map_code . "_per' id='" . $row->map_code . "_per' data-row='".json_encode($row)."' value='".$expPer."' class='form-control ".$calculateSummaryPer."'> ";
														$readonly = ($row->map_code == "roff")?"readonly":$readonly;
														$amtType = ($row->map_code == "roff")?"hidden":"number";
														$afterExp .= "</td>
														<td><input type='".$amtType."' id='".$row->map_code."_amt' class='form-control ".$calculateSummaryAmt."' data-sm_type='exp' data-row='".json_encode($row)."' value='".$expAmt."' ".$readonly."></td>
														<td><input type='number' name='" . $row->map_code . "_amount' id='" . $row->map_code . "_amount' value='0' class='form-control ".(($row->map_code == "roff")?"":"summaryAmount")."' readonly /> </td>
														</tr>";
													endif;
												endforeach;
												foreach ($taxList as $taxRow) :
													$taxHiddenInput = '';
													$taxHiddenInput = '<input type="hidden" name="' . $taxRow->map_code . '_acc_id" id="' . $taxRow->map_code . '_acc_id" value="'.$taxRow->acc_id.'">';
													
													/* $options = '<select class="form-control single-select" name="' . $taxRow->map_code . '_acc_id" id="' . $taxRow->map_code . '_acc_id">';
													foreach ($ledgerList as $ledgerRow) :
														if ($ledgerRow->group_code == "DT") :
															$filedName = $taxRow->map_code."_acc_id";
															if(!empty($invoiceData->{$filedName})):			
																$selected = ($ledgerRow->id == $invoiceData->{$filedName})?"selected":(($ledgerRow->id == $taxRow->acc_id) ? 'selected' : '');
															else:
																$selected = ($ledgerRow->id == $taxRow->acc_id) ? 'selected' : '';
															endif;
															$options .= '<option value="' . $ledgerRow->id . '" ' . $selected . '>' . $ledgerRow->party_name . '</option>';
														endif;
													endforeach;
													$options .= '</select>'; */
													$taxClass = "";
													$perBoxType = "number";
													$calculateSummary = "calculateSummary";
													$taxPer = 0;
													$taxAmt = 0;
													if(!empty($invoiceData->id)):
														$taxPer = $invoiceData->{$taxRow->map_code.'_per'};
														$taxAmt = $invoiceData->{$taxRow->map_code.'_amount'};
													endif;
													if($taxRow->map_code == "cgst"):
														$taxClass = "cgstCol";
														$perBoxType = "hidden";
														$calculateSummary = "";
													elseif($taxRow->map_code == "sgst"):
														$taxClass = "sgstCol";
														$perBoxType = "hidden";
														$calculateSummary = "";
													elseif($taxRow->map_code == "igst"):
														$taxClass = "igstCol";
														$perBoxType = "hidden";
														$calculateSummary = "";
													endif;
													$tax .= '<tr class="'.$taxClass.'">
														<td>' . $taxRow->name.$taxHiddenInput . '</td>
														
														<td>';
													$tax .= "<input type='".$perBoxType."' name='" . $taxRow->map_code . "_per' id='" . $taxRow->map_code . "_per' data-row='".json_encode($taxRow)."' value='".$taxPer."' class='form-control ".$calculateSummary."'> ";
														
													$tax .= "</td>
														<td><input type='".$perBoxType."' id='".$taxRow->map_code."_amt' class='form-control' data-sm_type='tax'data-row='".json_encode($taxRow)."' value='".$taxAmt."' readonly ></td>
														<td><input type='number' name='" . $taxRow->map_code . "_amount' id='" . $taxRow->map_code . "_amount'  value='0' class='form-control summaryAmount' readonly /> </td>
													</tr>";
												endforeach;
												echo $beforExp;
												echo $tax;
												echo $afterExp;
												?>
												
											</tbody>
											<tfoot class="table-info">
												<tr >
													<th>Net. Amount</th>
													<th></th>
													<th></th>
													<td>
														<input type="text" name="net_inv_amount" id="net_inv_amount" class="form-control" value="0" readonly />
													</td>
												</tr>
											</tfoot>
										</table>
									</div>
								</div>
								<hr>
								<div class="row form-group">
									<div class="col-md-12">
										<div class="row">
											<div class="col-md-9 form-group">
												<label for="remark">Remark</label>
												<input type="text" name="remark" class="form-control" value="<?= (!empty($invoiceData->remark)) ? $invoiceData->remark : "" ?>" />
											</div>
											<div class="col-md-3 form-group">
												<label for="">&nbsp;</label>
												<button type="button" class="btn btn-outline-success waves-effect btn-block" data-toggle="modal" data-target="#termModel">Terms & Conditions (<span id="termsCounter">0</span>)</button>
												<div class="error term_id"></div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="modal fade" id="termModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
								<div class="modal-dialog modal-lg" role="document" style="max-width:70%;">
									<div class="modal-content animated slideDown">
										<div class="modal-header">
											<h4 class="modal-title">Terms & Conditions</h4>
											<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										</div>
										<div class="modal-body">
											<div class="col-md-12 mb-10">
												<table id="terms_condition" class="table table-bordered dataTable no-footer">
													<thead class="thead-info">
														<tr>
															<th style="width:10%;">#</th>
															<th style="width:25%;">Title</th>
															<th style="width:65%;">Condition</th>
														</tr>
													</thead>
													<tbody>
														<?php
														if (!empty($terms)) :
															$termaData = (!empty($invoiceData->terms_conditions)) ? json_decode($invoiceData->terms_conditions) : array();
															$i = 1;
															$j = 0;
															foreach ($terms as $row) :
																$checked = "";
																$disabled = "disabled";
																if (in_array($row->id, array_column($termaData, 'term_id'))) :
																	$checked = "checked";
																	$disabled = "";
																	$row->conditions = $termaData[$j]->condition;
																	$j++;
																endif;
														?>
																<tr>
																	<td style="width:10%;">
																		<input type="checkbox" id="md_checkbox<?= $i ?>" class="filled-in chk-col-success termCheck" data-rowid="<?= $i ?>" check="<?= $checked ?>" <?= $checked ?> />
																		<label for="md_checkbox<?= $i ?>"><?= $i ?></label>
																	</td>
																	<td style="width:25%;">
																		<?= $row->title ?>
																		<input type="hidden" name="term_id[]" id="term_id<?= $i ?>" value="<?= $row->id ?>" <?= $disabled ?> />
																		<input type="hidden" name="term_title[]" id="term_title<?= $i ?>" value="<?= $row->title ?>" <?= $disabled ?> />
																	</td>
																	<td style="width:65%;">
																		<input type="text" name="condition[]" id="condition<?= $i ?>" class="form-control" value="<?= $row->conditions ?>" <?= $disabled ?> />
																	</td>
																</tr>
															<?php
																$i++;
															endforeach;
														else :
															?>
															<tr>
																<td class="text-center" colspan="3">No data available in table</td>
															</tr>
														<?php
														endif;
														?>
													</tbody>
												</table>
											</div>
										</div>
										<div class="modal-footer">
											<button type="button" class="btn waves-effect waves-light btn-outline-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
											<button type="button" class="btn waves-effect waves-light btn-outline-success" data-dismiss="modal"><i class="fa fa-check"></i> Save</button>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
					<div class="card-footer">
						<div class="col-md-12">
							<button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="saveInvoice('savePurchaseInvoice');"><i class="fa fa-check"></i> Save</button>
							<a href="<?= base_url($headData->controller) ?>" class="btn waves-effect waves-light btn-outline-secondary float-right save-form" style="margin-right:10px;"><i class="fa fa-times"></i> Cancel</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="itemModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog" role="document">
		<div class="modal-content animated slideDown">
			<div class="modal-header">
				<h4 class="modal-title">Add or Update Item</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<form id="invoiceItemForm">
					<div class="col-md-12">
						<div class="row form-group">
							<input type="hidden" name="trans_id" id="trans_id" value="" />
							<input type="hidden" name="unit_name" id="unit_name" class="form-control" value="" />
							<input type="hidden" name="unit_id" id="unit_id" value="">
							<input type="hidden" name="item_type" id="item_type" value="">
							<input type="hidden" name="gst_per" id="gst_per" value="">
							<input type="hidden" name="disc_per" id="disc_per" value="0">
							<input type="hidden" name="row_index" id="row_index" value="">
							<div class="col-md-12 form-group">
								<label for="item_id">Item Name</label>
								<div class="input-group">
                                    <input type="text" id="item_name" name="item_name" class="form-control" value="" readonly style="width: 90%;" />
                                    <button type="button" class="btn btn-outline-primary" onclick="searchFGItems(0);"><i class="fa fa-plus"></i></button>
									<input type="hidden" name="item_id" id="item_id" value="">
								</div>
								<input type="hidden" name="po_trans_id" id="po_trans_id" value="" />
								<input type="hidden" name="fgitem_id" id="fgitem_id" value="" />
								<input type="hidden" name="color_code" id="color_code" value="" />
								<input type="hidden" name="location_id" id="location_id" value="" />
								<input type="hidden" name="qty" id="qty" value="" />
								<input type="hidden" name="qty_kg" id="qty_kg" value="" />
							</div>
							<div class="col-md-4 form-group">
								<label for="price">Price</label>
								<input type="text" name="price" id="price" class="form-control floatOnly" value="0">
							</div>
							<div class="col-md-12 form-group">
								<label for="item_remark">Item Remark</label>
								<input type="text" name="item_remark" id="item_remark" class="form-control" value="">
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn waves-effect waves-light btn-outline-success saveItem btn-save" data-fn="save"><i class="fa fa-check"></i> Save</button>
				<button type="button" class="btn waves-effect waves-light btn-outline-warning saveItem btn-save-close" data-fn="save_close"><i class="fa fa-check"></i> Save & Close</button>
				<button type="button" class="btn waves-effect waves-light btn-outline-secondary btn-close" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="orderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg" role="document" style="min-width:70%;">
		<div class="modal-content animated slideDown">
			<div class="modal-header">
				<h4 class="modal-title" id="createInvoice">GRN</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<form id="party_so" method="post" action="">
				<div class="modal-body scrollable" style="height:60vh;">
					<input type="hidden" name="party_id" id="party_id_pinv" value="">
					<input type="hidden" name="party_name" id="party_name_pinv" value="">
					<div class="col-md-12">
						<div class="row mb-2">
							<div class="col-md-4 float-right">
							</div>
							<div class="col-md-4 float-right">
								<input type="date" name="from_date" id="from_date" class="form-control"  value="<?=date('Y-m-d')?>" />
								<div class="error fromDate"></div>
							</div>
							<div class="col-md-4">  
                                <div class="input-group">
                                    <input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-d')?>" />
                                    <div class="input-group-append ml-2">
                                        <button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" title="Load Data">
									        <i class="fas fa-sync-alt"></i> Load
								        </button>
                                    </div>
                                </div>
                                <div class="error toDate"></div>
                            </div>   
						</div>
						<div class="error general"></div>
						<div class="table-responsive">
							<table id='reportTable' class="table table-bordered">
								<thead class="thead-info">
									<tr>
										<th class="text-center" style="width:5%;"><input type="checkbox" name ="masterSelect" id="masterSelect" class="filled-in chk-col-success bulkTags" value=""><label for="masterSelect">ALL</label></th>
										<th class="text-center" style="width:15%;">Item Name</th>
										<th class="text-center" style="width:20%;">Date</th>
										<th class="text-center" style="width:10%;">Challan No.</th>
										<th class="text-center">Challan Qty</th>
										<th class="text-center">Bill Qty</th>
										<th class="text-center">Remaining Qty</th>
										<th class="text-center" style="width:10%;">Rejection Qty</th>
										<th class="text-center" style="width:10%;">Without Process Qty</th>
									</tr>
								</thead>
								<tbody id="orderData">
									<tr>
										<td class="text-center" colspan="5">No Data Found</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn waves-effect waves-light btn-outline-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
					<button type="submit" class="btn waves-effect waves-light btn-outline-success" id="btn-create"><i class="fa fa-check"></i> Create JobWork Invoice</button>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- Expense Model -->
<div class="modal fade" id="expModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content animated slideDown">
			<div class="modal-header">
				<h4 class="modal-title">Expense</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<div class="col-md-12 mb-10">
					<table id="expenseList" class="table table-bordered dataTable no-footer">
						<thead class="thead-info">
							<tr>
								<th style="width:20%;">#</th>
								<th style="width:80%;">Expense Name</th>
							</tr>
						</thead>
						<tbody>
							<?php
							if (!empty($expenseList)) :
								$i=1;
								foreach ($expenseList as $row) :
									if($row->map_code != "roff"):
										$expAmt = 0;
										$expAmtFiledName = $row->map_code."_amount";
										if(!empty($invExpenseData)):	
											$expAmt = $invExpenseData->{$expAmtFiledName};
										endif;
										$expChecked = ($expAmt > 0)?'checked':'';
							?>
									<tr>
										<td style="width:20%;">
											<input type="checkbox" id="exp_checkbox_<?=$row->id?>" class="filled-in chk-col-success expCheck" data-map_code="<?= $row->map_code ?>" check="<?= $expChecked ?>" <?= $expChecked ?> />
											<label for="exp_checkbox_<?=$row->id?>"><?= $i ?></label>
										</td>
										<td style="width:80%;">
											<?= $row->exp_name ?>
										</td>
									</tr>
							<?php
									$i++;
									endif;
								endforeach;
							else :
							?>
								<tr>
									<td class="text-center" colspan="2">No data available in table</td>
								</tr>
							<?php
							endif;
							?>
						</tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn waves-effect waves-light btn-outline-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
				<button type="button" id="addExp" class="btn waves-effect waves-light btn-outline-success" data-dismiss="modal"><i class="fa fa-plus"></i> Add</button>
			</div>
		</div>
	</div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url(); ?>assets/js/custom/jobwork-invoice.js?v=<?= time() ?>"></script>
<script src="<?php echo base_url(); ?>assets/js/custom/master-form.js?v=<?= time() ?>"></script>
<script>
	gstType();	
</script>
<?php
/* Edit Invoice */
if (!empty($invoiceData->itemData)) :
	$i = 0;
	foreach ($invoiceData->itemData as $row) :
		$hsnBox = '';
		$hsnBox = '<select name="hsn_code['.$i.']" class="form-control single-select hsnSelection">';
		$hsnBox .= '<option value="">Select HSN Code</option>';
		foreach($hsnData as $hsnRow):
			$selected  = ($row->hsn_code == $hsnRow->hsn)?"selected":"";
			$hsnBox .= '<option value="'.$hsnRow->hsn.'" '.$selected.'>'.$hsnRow->hsn.'</option>';
		endforeach;
		$hsnBox .= '</select>';
		$row->row_index = "";
		$row->trans_id = $row->id;
		$row->disc_amt = round($row->disc_amount,2);
		$row->cgst_amt = round($row->cgst_amount,2);
		$row->sgst_amt = round($row->sgst_amount,2);
		$row->igst_amt = round($row->igst_amount,2);
		$row->hsn_code = $hsnBox;
		echo '<script>AddRow(' . json_encode($row) . ');</script>';
		$i++;
	endforeach;
endif;

/* Create Jobwork Invoice */
if (!empty($orderItems)) :
	$i = 0;
	foreach ($orderItems as $row) :
		$hsnBox = '';
		$hsnBox = '<select name="hsn_code['.$i.']" class="form-control single-select hsnSelection">';
		$hsnBox .= '<option value="">Select HSN Code</option>';
		foreach($hsnData as $hsnRow):
			$selected  = ($row->hsn_code == $hsnRow->hsn)?"selected":"";
			$hsnBox .= '<option value="'.$hsnRow->hsn.'" '.$selected.'>'.$hsnRow->hsn.'</option>';
		endforeach;
		$hsnBox .= '</select>';
		$row->row_index = "";
		$row->trans_id = "";
		$row->item_name = "[" . $row->item_code . "] " . $row->item_name;
		$row->hsn_code = $hsnBox;
		$row->ref_id = $row->id;
		$row->qty = ($row->com_qty - $row->bill_qty);
		//$row->qty = round(($row->com_qty), 2);
		$row->qty_kg = "0.000";
		$row->amount = round($row->price * $row->com_qty,2);
		$row->disc_per = 0;
		$row->disc_amt = 0;
		$row->cgst_per = round($row->cgst,2);
		$row->sgst_per = round($row->sgst,2);
		$row->igst_per = round($row->igst,2);
		$row->gst_per = round($row->igst,2);
		$row->cgst_amt = round((($row->amount * $row->cgst) / 100),2);
		$row->sgst_amt = round((($row->amount * $row->sgst) / 100),2);
		$row->igst_amt = round((($row->amount * $row->igst) / 100),2);
		$row->stateCode = "";
		$row->gst_type = $gst_type;
		$row->net_amount = round(($row->cgst_amt + $row->sgst_amt + $row->amount - $row->disc_amt),2);
		unset($row->entry_type);
		echo '<script>AddRow(' . json_encode($row) . ');</script>';
		$i++;
	endforeach;

endif;

?>
<script>
	$(document).ready(function(){
		//$(".calculateSummary").trigger('keyup');
		//$("#party_id").trigger('change');
		$(".hsnSelection").comboSelect();
		
		$(document).on('click','.loaddata',function(e){
			$(".error").html("");
			var valid = 1;
			var party_id = $('#party_id_pinv').val();
			var from_date = $('#from_date').val();
			var to_date = $('#to_date').val();
			if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
			if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
			if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}
			if(valid)
			{
				$.ajax({
					url: base_url + controller + '/getVendorJobWork',
					data: {from_date:from_date, to_date:to_date,party_id:party_id},
					type: "POST",
					dataType:'json',
					success:function(data){
						
						$("#orderData").html("");
						$("#reportTable").dataTable().fnDestroy();
						$("#orderData").html(data.htmlData);
					}
				});
			}
    	});

		$(document).on('click','.bulkTags',function(){
			var id = $(this).data('rowid');
			var items =document.getElementsByName('ref_id[]');
			if($(this).attr('id') == "masterSelect"){
				if ($(this).prop('checked') == true) { 
					$('input[name="ref_id[]"]').prop('checked',true);                
				}
				else{
					$('input[name="ref_id[]"]').prop('checked',false);
				}
			}else{
				if($('input[name="ref_id[]"]').not(':checked').length != $('input[name="ref_id[]"]').length)
				{
					$('#masterSelect').prop('checked',false);
				}

				if($('input[name="ref_id[]"]:checked').length == $('input[name="ref_id[]"]').length)
				{
					$('#masterSelect').prop('checked',true);
				}
				else{
					$('#masterSelect').prop('checked',false);
				}
				$('input[name="ref_id[]"]').each(function(){
					
				});
			}
		});
	});
</script>