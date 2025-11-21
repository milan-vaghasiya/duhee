<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
							<div class="col-md-10"> 
								<h4 class="card-title">Product Details</h4>
							</div>
							<div class="col-md-2"> 
								<a href="<?= base_url($headData->controller) ?>" class="btn waves-effect waves-light btn-outline-dark float-right"><i class="fa fa-arrow-left"></i> Back</a>
							</div>
                        </div>
					</div>
					<div class="card-body">
						<div class="col-md-12">
							<div class="row">
								<div class="col-lg-4 col-xlg-4 col-md-4">
									<div class="card">
										<div class="card-body">
											<table class="table table-bordered">
												<thead class="thead-grey">
													<tr>
														<th style="width:35%;">Item Code</th>
														<td> <?= (!empty($productData->item_code)) ? $productData->item_code : "-"; ?> </td>
													</tr>
													<tr>
														<th>Item Name</th>
														<td> <?= (!empty($productData->item_name)) ? $productData->item_name : "-" ?> </td>
													</tr>
													<tr>
														<th>Part No.</th>
														<td> <?= (!empty($productData->part_no)) ? $productData->part_no : "-"; ?> </td>
													</tr>
													<tr>
														<th>Unit</th>
														<td> <?= (!empty($productData->unit_name)) ? $productData->unit_name : "-"; ?> </td>
													</tr>
													<tr>
														<th>Category</th>
														<td> <?= (!empty($productData->category_name)) ? $productData->category_name : "-"; ?> </td>
													</tr>
													<tr>
														<th>HSN</th>
														<td> <?= (!empty($productData->hsn_code)) ? $productData->hsn_code : "-"; ?> </td>
													</tr>
													<tr>
														<th>Material Grade</th>
														<td> <?= (!empty($productData->grade_name)) ? $productData->grade_name : "-"; ?> </td>
													</tr>
													<tr>
														<th>Weight in Kg</th>
														<td> <?= (!empty($productData->wkg)) ? $productData->wkg : "-"; ?> </td>
													</tr>
													<tr>
														<th>Drawing No</th>
														<td> <?= (!empty($productData->drawing_no)) ? $productData->drawing_no : "-"; ?> </td>
													</tr>
													<tr>
														<th>Drawing Date</th>
														<td> <?= (!empty($productData->drawing_date)) ? $productData->drawing_date : "-"; ?> </td>
													</tr>
													<tr>
														<th>Revision No</th>
														<td> <?= (!empty($productData->rev_no)) ? $productData->rev_no : "-"; ?> </td>
													</tr>
													<tr>
														<th>Revision Date</th>
														<td> <?= (!empty($productData->rev_date)) ? $productData->rev_date : "-"; ?> </td>
													</tr>
													<tr>
														<th>Product Description</th>
														<td> <?= (!empty($productData->note)) ? $productData->note : "-"; ?> </td>
													</tr>
												</thead>
											</table>
										</div>
									</div>
								</div>
								<div class="col-lg-8 col-xlg-8 col-md-8">
									<div class="card">
										<ul class="nav nav-pills custom-pills" id="pills-tab" role="tablist">
											<li class="nav-item">
												<a class="nav-link active" id="pills-bomDetails-tab" data-toggle="pill" href="#bomDetails" role="tab" aria-controls="pills-bomDetails" aria-selected="true" flow="up">Bill of Material</a>
											</li>
											<li class="nav-item">
												<a class="nav-link" id="pills-productProcess-tab" data-toggle="pill" href="#productProcess" role="tab" aria-controls="pills-productProcess" aria-selected="false">Product Process</a>
											</li>
											<!-- <li class="nav-item">
												<a class="nav-link" id="pills-paramDetails-tab" data-toggle="pill" href="#paramDetails" role="tab" aria-controls="pills-paramDetails" aria-selected="false">Inspection Details</a>
											</li> -->
											<li class="nav-item">
												<a class="nav-link" id="pills-itemImg-tab" data-toggle="pill" href="#itemImg" role="tab" aria-controls="pills-itemImg" aria-selected="false">Part Image</a>
											</li>
											<li class="nav-item">
												<a class="nav-link" id="pills-drawings-tab" data-toggle="pill" href="#drawings" role="tab" aria-controls="pills-drawings" aria-selected="false">Drawings</a>
											</li>
											
										</ul> 
										<div class="tab-content" id="pills-tabContent">
											<div class="tab-pane fade show active" id="bomDetails" role="tabpanel"aria-labelledby="pills-bomDetails-tab">
												<form id="bomDetails">
													<div class="card-body">
														<div class="table-responsive">
															<table id="inspection" class="table table-bordered align-items-center">
																<thead class="thead-info">
																	<tr>
																		<th class="text-center" style="width:5%;">#</th>
																		<th class="text-center">Item Name</th>
																		<th class="text-center">Qty.</th>
																	</tr>
																</thead>
																<tbody id="bomBody">
																	<?php
																		if(!empty($bomData)):
																			$i=1;
																			foreach($bomData as $row):
																				echo '<tr>
																						<td class="text-center">' .$i++. '</td>
																						<td class="text-center">'.$row->item_name.'</td>
																						<td class="text-center">'.$row->qty.'</td>
																					</tr>';
																			endforeach;
																		else:
																			echo '<tr><td colspan="3" style="text-align:center;">No Data Found</td></tr>';
																		endif;
																	?>
																</tbody>
															</table>
														</div>
													</div>
												</form>
											</div>

											<div class="tab-pane fade" id="productProcess" role="tabpanel" aria-labelledby="pills-productProcess-tab">
												<form id="productProcess">
													<div class="card-body">
														<div class="table-responsive">
															<table id="processDatatbl" class="table table-bordered align-items-center">
																<thead class="thead-info">
																	<tr>
																		<th class="text-center" style="width:5%;">#</th>
																		<th class="text-center">Process Name</th>
																		<th class="text-center">Time(In Second)</th>
																		<th class="text-center">Finished Weight</th>
																	</tr>
																</thead>
																<tbody id="processBody">
																	<?php
																		if (!empty($processData)) : $i = 1;
																			foreach ($processData as $row) :
																				echo '<tr>
																						<td class="text-center">' . $i++ . '</td>
																						<td class="text-center">' . $row->process_name . '</td>
																						<td class="text-center">' . $row->cycle_time . '</td>
																						<td class="text-center">' . $row->finished_weight . '</td>
																					</tr>';
																			endforeach;
																		else:
																			echo '<tr><td colspan="4" style="text-align:center;">No Data Found</td></tr>';
																		endif;
																	?>
																</tbody>
															</table>
														</div>
													</div>
												</form>
											</div>

											<div class="tab-pane fade" id="paramDetails" role="tabpanel" aria-labelledby="pills-paramDetails-tab">
												<form id="paramDetails">
													<div class="card-body">
														<div class="table-responsive">
															<table id="paramDatatbl" class="table table-bordered align-items-center">
																<thead class="thead-info">
																	<tr>
																		<th class="text-center" style="width:5%;">#</th>
																		<th class="text-center">Process</th>
																		<th class="text-center">Drg. Diameter</th>
																		<th class="text-center">Specification</th>
																		<th class="text-center">Min. Value</th>
																		<th class="text-center">Max. Value</th>
																		<th class="text-center">Instrument</th>
																		<th class="text-center">Rev No</th>
																	</tr>
																</thead>

																<tbody id="processBody">
																	<?php
																		if (!empty($paramData)) : $i = 1;
																			foreach ($paramData as $row) :
																				echo '<tr>
																						<td class="text-center">' . $i++ . '</td>
																						<td class="text-center">' . $row->process_name . '</td>
																						<td class="text-center">' . $row->drg_diameter . '</td>
																						<td class="text-center">' . $row->specification . '</td>
																						<td class="text-center">' . $row->min_value . '</td>
																						<td class="text-center">' . $row->max_value . '</td>
																						<td class="text-center">' . $row->inst_used . '</td>
																						<td class="text-center">' . $row->rev_no . '</td>
																					</tr>';
																			endforeach;
																		else:
																			echo '<tr><td colspan="8" style="text-align:center;">No Data Found</td></tr>';
																		endif;
																	?>
																</tbody>
															</table>
														</div>
													</div>
												</form>
											</div>

											<div class="tab-pane fade" id="itemImg" role="tabpanel" aria-labelledby="pills-itemImg-tab">
												<form id="itemImg" action="POST" enctype="multipart/form-data">
													<div class="card-body">
														<?php if(!empty($productData)): ?>
														<div class="pic-holder text-center">
															<img id="itemImg"  style="width:300px; height:300px;" class="pic" src="<?= base_url('assets/uploads/items/'.$productData->item_image) ?>">
														</div>
														<?php endif;?>
													</div>
												</form>
											</div>

											<div class="tab-pane fade" id="drawings" role="tabpanel" aria-labelledby="pills-drawings-tab">
												<form id="addProcessDocuments" enctype="multipart/form-data">
													<div class="col-md-12">
														<div class="row">   
															<input type="hidden" id="id" name="id" value="" />
															<input type="hidden" id="doc_type" name="doc_type" value=1 />
															<input type="hidden" id="item_id" name="item_id" value="<?=$item_id?>" />  

												
															<div class="col-md-4 form-group">
																<label for="process_id">Production Process</label>
																<select name="process_id" id="process_id" class="form-control single-select">
																	<option value = "0">Main Drawings</option>
																	<?php
																	if(!empty($processData)){
																		foreach ($processData as $row) :
																			echo '<option value="' . $row->process_id . '">' . $row->process_name . '</option>';
																		endforeach;
																	}
																	?>
																</select>
															</div>   
															<div class="col-md-3 form-group">
																<label for="prd_drg_no">Program/Drawing No.</label>
																<input type="text" name="prd_drg_no" id="prd_drg_no" class="form-control req" >
															</div>       
															<div class="col-md-3 form-group">
																<label for="file_upload">Choose File</label>
																<input type="file" name="file_upload[]" id="file_upload" class="form-control-file req" multiple="multiple">
															</div>       
															<div class="col-md-2 form-group">
																<label>&nbsp;</label>
																<button type="button" class="btn btn-outline-success btn-save float-right btn-block" onclick="saveProcessDocuments('addProcessDocuments','saveProcessDocuments');"><i class="fa fa-check"></i> Save</button>
															</div>
														</div>
													</div>
												</form>
												<hr>
												<div class="card-body">
													<div class="table-responsive">
														<table id="inspection" class="table table-bordered align-items-center">
														<thead class="thead-info">
															<tr class="text-center">
																<th style="width:5%;">#</th>
																<th style="width:20%;">Product Process</th>
																<th style="width:10%;">Program/Drawing No.</th>
																<th style="width:10%;">File</th>
																<th style="width:10%;">Action</th>
															</tr>
														</thead>
														<tbody id="processDocBody">
															<?php echo $processDocBody['tbodyData'] ?>
														</tbody>
														</table>
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
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script>
function saveProcessDocuments(){
    setPlaceHolder();
    var formId = 'addProcessDocuments';
	var form = $('#'+formId)[0];
	var fd = new FormData(form); 

    valid = 1;
    if(valid){
        $.ajax({
            url: base_url + controller + '/saveProcessDocuments',
            data:fd,
            type: "POST",
            processData:false,
            contentType:false,
            dataType:"json",
            success:function(data){
                if(data.status===0){
                    $(".error").html("");
                    $.each( data.message, function( key, value ) {$("."+key).html(value);});
                }else{
					$('#processDocBody').html("");
                    $("#processDocBody").html(data.tbodyData);
                    $("#process_id").val("");
			        $("#process_id").comboSelect();
                    $("#prd_drg_no").val("");
                    $("#prd_drg_no").val("");
                    $("#file_upload").val("");
                    toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
                }
            }
        });
    }
}

function trashProcessDocuments(id,item_id,name='Record'){
	var send_data = { id:id, item_id:item_id };
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to delete this '+name+'?',
		type: 'red',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + controller + '/deleteProcessDocuments',
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data)
						{
							if(data.status==0)
							{
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}else
                            {
                                $('#processDocBody').html("");
                                $("#processDocBody").html(data.tbodyData);
								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
						}
					});
				}
			},
			cancel: {
                btnClass: 'btn waves-effect waves-light btn-outline-secondary',
                action: function(){

				}
            }
		}
	});
}
</script>
