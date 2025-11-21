<?php $this->load->view('includes/header'); ?>
<style> 
	.typeahead.dropdown-menu{width:95.5% !important;padding:0px;border: 1px solid #999999;box-shadow: 0 2px 5px 0 rgb(0 0 0 / 26%);}
	.typeahead.dropdown-menu li{border-bottom: 1px solid #999999;}
	.typeahead.dropdown-menu li .dropdown-item{padding: 8px 1em;margin:0;}
</style>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4><u>Pre Dispatch Inspection Report</u></h4>
                    </div>
                    <div class="card-body">
                        <form autocomplete="off" id="savePdi">
                            <div class="col-md-12">

								<input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />
								<div class="row form-group">

									<div class="col-md-2">
                                        <label for="trans_number">Report No.</label>
                                        <div class="input-group mb-3">
                                            <input type="text" name="trans_prefix" id="trans_prefix" class="form-control req" value="<?=(!empty($dataRow->trans_prefix))?$dataRow->trans_prefix:$trans_prefix?>" readonly />
                                            <input type="text" name="trans_no" id="trans_no" class="form-control" value="<?=(!empty($dataRow->trans_no))?$dataRow->trans_no:$nextTransNo?>" readonly />
                                        </div>
									</div>

									<div class="col-md-2">
										<label for="trans_date">Report Date</label>
                                        <input type="date" id="trans_date" name="trans_date" class=" form-control req" placeholder="dd-mm-yyyy" aria-describedby="basic-addon2" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:$maxDate?>" min="<?=$startYearDate?>" max="<?=$maxDate?>" />	
									</div>

									<div class="col-md-3">
										<label for="party_id">Customer Name</label>
										<select name="party_id" id="party_id" class="form-control single-select req">
											<option value="">Select Customer</option>
											<?php
												foreach($customerData as $row):
													$selected = "";
													if(!empty($dataRow->party_id) && $dataRow->party_id == $row->id){$selected = "selected";}
													echo "<option value='".$row->id."' ".$selected.">".$row->party_name."</option>";
												endforeach;
											?>
										</select>
									</div>
									<div class="col-md-3 form-group">
										<label for="item_id">Part</label>
										<select name="item_id" id="item_id" class="form-control single-select req">
											<option value="">Select Part</option>
											<?php 
												foreach($partData as $row):
													$selected = "";
													if(!empty($dataRow->item_id) && $dataRow->item_id == $row->id){$selected = "selected";}
													echo "<option value='".$row->id."' ".$selected.">[".$row->item_code.'] '.$row->item_name."</option>";
												endforeach;
											?>
										</select>
									</div>
									<div class="col-md-2 form-group">
										<label for="challan_no">Ch/Inv/MRR/GRR No.</label>
										<input type="text" name="challan_no" id="challan_no" class="form-control" value="<?=(!empty($dataRow->challan_no))?$dataRow->challan_no:""?>" />
									</div>
									<div class="col-md-2 form-group">
										<label for="challan_date">Ch/Inv/MRR/GRR Date</label>
										<input type="date" name="challan_date" id="challan_date" class="form-control" value="<?=(!empty($dataRow->challan_date))?$dataRow->challan_date:date("Y-m-d")?>" />
									</div>
									<div class="col-md-2 form-group">
										<label for="po_no">PO/DC No.</label>
										<input type="text" name="po_no" id="po_no" class="form-control" value="<?=(!empty($dataRow->po_no))?$dataRow->po_no:""?>" />
									</div>
									<div class="col-md-2 form-group">
										<label for="po_date">PO/DC Date</label>
										<input type="date" name="po_date" id="po_date" class="form-control" value="<?=(!empty($dataRow->po_date))?$dataRow->po_date:date("Y-m-d")?>" />
									</div>
									<div class="col-md-2 form-group">
										<label for="drawing_no">Drawing No.</label>
										<input type="text" name="drawing_no" id="drawing_no" class="form-control" value="<?=(!empty($dataRow->drawing_no))?$dataRow->drawing_no:""?>" />
									</div>
									<div class="col-md-2 form-group">
										<label for="rev_no">Revision No.</label>
										<input type="text" name="rev_no" id="rev_no" class="form-control" value="<?=(!empty($dataRow->rev_no))?$dataRow->rev_no:""?>" />
									</div>
									<div class="col-md-2 form-group">
										<label for="rev_date">Revision Date</label>
										<input type="date" name="rev_date" id="rev_date" class="form-control" value="<?=(!empty($dataRow->rev_date))?$dataRow->rev_date:date("Y-m-d")?>" />
									</div>
									<div class="col-md-3 form-group">
										<label for="job_no">Job No.</label>
										<input type="text" name="job_no" id="job_no" class="form-control" value="<?=(!empty($dataRow->job_no))?$dataRow->job_no:""?>" />
									</div>
									<div class="col-md-3 form-group">
										<label for="operation_no">Operation No.</label>
                                        <input type="text" id="operation_no" name="operation_no" class="form-control" value="<?=(!empty($dataRow->operation_no))?$dataRow->operation_no:""?>" />	
									</div>
									<div class="col-md-3 form-group">
										<label for="mill_tc_no">Mill TC No.</label>
										<input type="text" id="mill_tc_no" name="mill_tc_no" class="form-control" value="<?=(!empty($dataRow->mill_tc_no))?$dataRow->mill_tc_no:""?>" />	
									</div>
									<div class="col-md-3 form-group">
										<label for="heat_code">Heat Code</label>
										<input type="text" name="heat_code" id="heat_code" class="form-control" value="<?=(!empty($dataRow->heat_code))?$dataRow->heat_code:""?>" />
									</div>
									<div class="col-md-3 form-group">
										<label for="material">Material</label>
										<select name="material" id="material" class="form-control single-select">
											<option value="">Select Material</option>
											<?php 
												foreach($materialData as $row):
													$selected = "";
													if(!empty($dataRow->material) && $dataRow->material == $row->id){$selected = "selected";}
													echo "<option data-material_grade='".$row->material_grade."' value='".$row->id."' ".$selected.">[".$row->item_code.'] '.$row->item_name."</option>";
												endforeach;
											?>
										</select>	
									</div>
									<div class="col-md-3 form-group">
										<label for="material_grade">Material Grade</label>
										<input type="text" name="material_grade" id="material_grade" class="form-control" value="<?=(!empty($dataRow->material_grade))?$dataRow->material_grade:""?>" />
									</div>
									<div class="col-md-3 form-group">
										<label for="grade_dia">Grade & Dia</label>
										<input type="text" id="grade_dia" name="grade_dia" class="form-control" value="<?=(!empty($dataRow->grade_dia))?$dataRow->grade_dia:""?>" />	
									</div>
									<div class="col-md-3 form-group">
										<label for="type">Type</label>
										<input type="text" name="type" id="type" class="form-control" value="<?=(!empty($dataRow->type))?$dataRow->type:""?>" />
									</div>
									<div class="col-md-2 form-group">
										<label for="inv_qty">Invoice Qty.</label>
										<input type="text" name="inv_qty" id="inv_qty" class="form-control floatOnly" value="<?=(!empty($dataRow->inv_qty))?$dataRow->inv_qty:""?>" />
									</div>
									<div class="col-md-2 form-group">
										<label for="lot_qty">Lot Qty.</label>
										<input type="text" name="lot_qty" id="lot_qty" class="form-control floatOnly req" value="<?=(!empty($dataRow->lot_qty))?$dataRow->lot_qty:""?>" />
									</div>
									<div class="col-md-2 form-group">
										<label for="reject_qty">Rejection Qty.</label>
										<input type="text" name="reject_qty" id="reject_qty" class="form-control floatOnly" value="<?=(!empty($dataRow->reject_qty))?$dataRow->reject_qty:""?>" />
									</div>
									<div class="col-md-2 form-group">
										<label for="rework_qty">Rework Qty.</label>
										<input type="text" name="rework_qty" id="rework_qty" class="form-control floatOnly" value="<?=(!empty($dataRow->rework_qty))?$dataRow->rework_qty:""?>" />
									</div>
									<div class="col-md-2 form-group">
										<label for="condition_accept_qty">Conditionally Accepted Qty.</label>
										<input type="text" name="condition_accept_qty" id="condition_accept_qty" class="form-control floatOnly" value="<?=(!empty($dataRow->condition_accept_qty))?$dataRow->condition_accept_qty:""?>" />
									</div>
									<div class="col-md-2 form-group">
										<label for="sample_qty">Sample Qty.</label>
										<input type="text" name="sample_qty" id="sample_qty" class="form-control floatOnly" value="<?=(!empty($dataRow->sample_qty))?$dataRow->sample_qty:""?>" />
									</div>
									<div class="col-md-2 form-group">
										<label for="verify_qty">Verified Qty.</label>
										<input type="text" name="verify_qty" id="verify_qty" class="form-control floatOnly" value="<?=(!empty($dataRow->verify_qty))?$dataRow->verify_qty:""?>" />
									</div>
									<div class="col-md-4">
										<label for="insp_by">Inspected By/Verify By DASP/Prepared By</label>
										<select name="insp_by" id="insp_by" class="form-control single-select">
											<option value="">Select Employee</option>
											<?php
												foreach($empData as $row):
													$selected = "";
													if(!empty($dataRow->insp_by) && $dataRow->insp_by == $row->id){$selected = "selected";}
													echo "<option value='".$row->id."' ".$selected.">".$row->emp_name."</option>";
												endforeach;
											?>
										</select>
									</div>
									<div class="col-md-3">
										<label for="app_by">Approved By/Verify By DWPL/Checked By</label>
										<select name="app_by" id="app_by" class="form-control single-select">
											<option value="">Select Employee</option>
											<?php
												foreach($empData as $row):
													$selected = "";
													if(!empty($dataRow->app_by) && $dataRow->app_by == $row->id){$selected = "selected";}
													echo "<option value='".$row->id."' ".$selected.">".$row->emp_name."</option>";
												endforeach;
											?>
										</select>
									</div>
									<div class="col-md-3 form-group">
										<label for="surface_treat">Surface Treatment/Equipment Used</label>
										<input type="text" id="surface_treat" name="surface_treat" class="form-control" value="<?=(!empty($dataRow->surface_treat))?$dataRow->surface_treat:""?>" />	
									</div>
									<div class="col-md-6 form-group">
										<label for="tech_date1" style="width:33%">Tech. Date 1</label>
										<label for="tech1" style="width:33%">Technician 1</label>
										<label for="in_charge1">IN CHARGE 1</label>
										<div class="input-group-append">
											<input type="date" name="tech_date1" id="tech_date1" class="form-control"  style="width:33%" value="<?=(!empty($dataRow->tech_date1))?$dataRow->tech_date1:date("Y-m-d")?>" />
											
											<select name="tech1" id="tech1" class="form-control single-select"  style="width:33%">
												<option value="">Select Employee</option>
												<?php
													foreach($empData as $row):
														$selected = "";
														if(!empty($dataRow->tech1) && $dataRow->tech1 == $row->id){$selected = "selected";}
														echo "<option value='".$row->id."' ".$selected.">".$row->emp_name."</option>";
													endforeach;
												?>
											</select>
											<select name="in_charge1" id="in_charge1" class="form-control single-select"  style="width:33%">
												<option value="">Select Employee</option>
												<?php
													foreach($empData as $row):
														$selected = "";
														if(!empty($dataRow->in_charge1) && $dataRow->in_charge1 == $row->id){$selected = "selected";}
														echo "<option value='".$row->id."' ".$selected.">".$row->emp_name."</option>";
													endforeach;
												?>
											</select>
										</div>
									</div>
									<div class="col-md-6 form-group">
										<label for="tech_date2" style="width:32%">Tech. Date 2</label>
										<label for="tech2" style="width:33%">Technician 2</label>
										<label for="in_charge2" style="width:33%">IN CHARGE 2</label>
										<div class="input-group-append">
											<input type="date" name="tech_date2" id="tech_date2" class="form-control"  style="width:33%" value="<?=(!empty($dataRow->tech_date2))?$dataRow->tech_date2:date("Y-m-d")?>" />
											<select name="tech2" id="tech2" class="form-control single-select" style="width:33%">
												<option value="">Select Employee</option>
												<?php
													foreach($empData as $row):
														$selected = "";
														if(!empty($dataRow->tech2) && $dataRow->tech2 == $row->id){$selected = "selected";}
														echo "<option value='".$row->id."' ".$selected.">".$row->emp_name."</option>";
													endforeach;
												?>
											</select>
											<select name="in_charge2" id="in_charge2" class="form-control single-select" style="width:33%">
												<option value="">Select Employee</option>
												<?php
													foreach($empData as $row):
														$selected = "";
														if(!empty($dataRow->in_charge2) && $dataRow->in_charge2 == $row->id){$selected = "selected";}
														echo "<option value='".$row->id."' ".$selected.">".$row->emp_name."</option>";
													endforeach;
												?>
											</select>
										</div>
									</div>
									<div class="col-md-6 form-group">
										<label for="sub_contract_remark">Sub-Contractor's Remark/Supplier Remark</label>
										<input type="text" id="sub_contract_remark" name="sub_contract_remark" class="form-control" value="<?=(!empty($dataRow->sub_contract_remark))?$dataRow->sub_contract_remark:""?>" />	
									</div>
									<div class="col-md-6 form-group">
										<label for="master_remark">Remark</label>
										<input type="text" id="master_remark" name="master_remark" class="form-control" value="<?=(!empty($dataRow->master_remark))?$dataRow->master_remark:""?>" />	
									</div>
								</div>
							</div>
							<hr>
                            <div class="col-md-12 row">
                                <h4>Measurement Details : </h4>
                            </div>														
							<div class="col-md-12 mt-3">
								<div class="error item_name_error"></div>
								<div class="row form-group">
									<div class="table-responsive">
										<table id="salesEnqItems" class="table table-bordered">
											<thead class="thead-info">
												<tr class="text-center">
													<th rowspan="2" style="width:5%;">#</th>
													<th rowspan="2" style="width:10%;">Parameter</th>
													<th rowspan="2" style="width:20%;">Diamention</th>
													<th rowspan="2" style="width:10%;">Instrument</th>
													<th colspan="5">Observations</th>
													<th rowspan="2" style="width:15%;">Remark</th>
												</tr>
												<tr class="text-center">
													<th>Sample 1</th>
													<th>Sample 2</th>
													<th>Sample 3</th>
													<th>Sample 4</th>
													<th>Sample 5</th>
												</tr>
											</thead>
											<tbody id="tbodyData">
												<?php
												if(!empty($dataRow->itemData))
												{
													$i=1;
													foreach($dataRow->itemData as $row)
													{
														$diamention = '';
														if ($row->requirement == 1) {
															$diamention = $row->min_req . '/' . $row->max_req;
														}
														if ($row->requirement == 2) {
															$diamention = $row->min_req . ' ' . $row->other_req;
														}
														if ($row->requirement == 3) {
															$diamention = $row->max_req . ' ' . $row->other_req;
														}
														if ($row->requirement == 4) {
															$diamention = $row->other_req;
														}
														?>
														<tr class="text-center">
															<td>
																<?=$i++?>
																<input type="hidden" name="trans_id[]" id="trans_id" value="<?=(!empty($row->id) ? $row->id : '')?>">
															</td>
															<td>
																<?=$row->parameter?>
																<input type="hidden" name="param_id[]" id="param_id" value="<?=(!empty($row->param_id) ? $row->param_id : '')?>">
															</td>
															<td><?=$diamention?></td>
															<td><?=$row->instrument_code?></td>
															<td>														
																<input type="text" name="sample_1[]" id="sample_1" class="form-control text-center" value="<?=(!empty($row->sample_1) ? $row->sample_1 : '')?>">
															</td>
															<td>														
																<input type="text" name="sample_2[]" id="sample_2" class="form-control text-center" value="<?=(!empty($row->sample_2) ? $row->sample_2 : '')?>">
															</td>
															<td>														
																<input type="text" name="sample_3[]" id="sample_3" class="form-control text-center" value="<?=(!empty($row->sample_3) ? $row->sample_3 : '')?>">
															</td>
															<td>														
																<input type="text" name="sample_4[]" id="sample_4" class="form-control text-center" value="<?=(!empty($row->sample_4) ? $row->sample_4 : '')?>">
															</td>
															<td>														
																<input type="text" name="sample_5[]" id="sample_5" class="form-control text-center" value="<?=(!empty($row->sample_5) ? $row->sample_5 : '')?>">
															</td>
															<td>														
																<input type="text" name="remark[]" id="remark" class="form-control text-center" value="<?=(!empty($row->remark) ? $row->remark : '')?>">
															</td>	
														</tr>
														<?php
													}
												}
												?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <div class="col-md-12">
                            <button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="savePdiParam('savePdi');" ><i class="fa fa-check"></i> Save</button>
                            <a href="<?=base_url($headData->controller)?>" class="btn waves-effect waves-light btn-outline-secondary float-right save-form" style="margin-right:10px;"><i class="fa fa-times"></i> Cancel</a>
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
		$(document).on('change', '#item_id', function() {
            var item_id = $(this).val();
			if(item_id)
            {
				$.ajax({
					url: base_url + controller + '/getMeasurementData',
					data: { item_id : item_id },
					type: "POST",
					dataType: 'json',
					success: function(data) {
						$('#tbodyData').html("");
						$('#tbodyData').html(data.tbody);
					}
				});
			}
        });

		$(document).on('change', '#material', function() {
            var material = $(this).val();
            var material_grade = $(this).find(":selected").data('material_grade');
            $("#material_grade").val(material_grade);
		});
	});

    function savePdiParam(formId){
        var fd = $('#'+formId).serialize();
        $.ajax({
            url: base_url + controller + '/save',
            data:fd,
            type: "POST",
            dataType:"json",
        }).done(function(data){
            if(data.status===0){
                $(".error").html("");
                $.each( data.message, function( key, value ) {
                    $("."+key).html(value);
                });
            }else if(data.status==1){
                toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
                window.location = data.url;
            }else{
                toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
            }				
        });
    }
</script>