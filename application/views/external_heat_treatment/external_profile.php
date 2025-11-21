<?php $this->load->view('includes/header'); 

?>
<link href="<?=base_url();?>assets/css/icard.css?v=<?=time()?>" rel="stylesheet" type="text/css">
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">
									<?= (!empty($htData->item_code)) ? '['.$htData->item_code.'] '.$htData->part_no : "External Heat Treatment "; ?>
								</h4>
                            </div>
                            <div class="col-md-6">
								<a href="<?= base_url($headData->controller) ?>" class="btn waves-effect waves-light btn-outline-dark float-right"><i class="fa fa-arrow-left"></i> Back</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-lg-3 col-xlg-3 col-md-3">
                                    <div class="card">
                                        <div class="card-body p-3">
                                            <strong>Part type</strong>
                                            <p class="text-muted"><?= (!empty($htData->category_name)) ? $htData->category_name : "-"; ?></p>
                                            <strong>Drg No.</strong>
                                            <p class="text-muted"><?= (!empty($htData->drawing_no)) ? $htData->drawing_no : "-" ?></p>
                                            <strong>Rev No.</strong>
                                            <p class="text-muted"><?= (!empty($htData->rev_no)) ?$htData->rev_no : "-"; ?></p>
                                            <strong>Material Grade</strong>
                                            <p class="text-muted"><?= (!empty($htData->material_grade)) ?$htData->material_grade : "-"; ?></p>
                                     
                                            <div>
                                                <label for="bottom_layer">Bottom Layer Photo</label>
                                                <div class="input-group">
                                                    <input type="file" name="bottom_layer" id="bottom_layer" class="form-control-file" style="width:70%;" />

                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-sm btn-outline-success uploadBottomFile" datatip="Upload" flow="down" data-id =<?=$htData->id?> data-item_id=<?=$htData->item_id?>  type="button"><i class="ti-upload"></i></button>
                                                        <?php
                                                            if(!empty($htData->bottom_layer)):
                                                                echo '<a href="'.base_url('assets/uploads/bottom_layer/'.$htData->bottom_layer).'" class="btn btn-sm btn-outline-primary" datatip="Download" flow="down" target="_blank"><i class="ti-download"></i></a>';
                                                            endif; 
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <div>
                                                <label for="batch_no">Full Batch Photo</label>
                                                <div class="input-group">
                                                    <input type="file" name="batch_no" id="batch_no" class="form-control-file" style="width:70%;" />
                                                    <div class="input-group-append">
                                                        <a href="javascript:void(0);" class="btn btn-sm btn-outline-success uploadBatchFile" datatip="Upload" flow="down" data-id =<?=$htData->id?> data-item_id=<?=$htData->item_id?>  type="button"><i class="ti-upload"></i> </a>
                                                        <?php
                                                            if(!empty($htData->batch_no)):
                                                                echo '<a href="'.base_url('assets/uploads/batch_no/'.$htData->batch_no).'" class="btn btn-sm btn-outline-primary" datatip="Download" flow="down" target="_blank"><i class="ti-download"></i></a>';
                                                            endif;
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-lg-9 col-xlg-9 col-md-9">
                                    <div class="card">
                                        <!-- Tabs -->
                                        <ul class="nav nav-pills custom-pills" id="pills-tab" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" id="pills-batchQty-tab" data-toggle="pill" href="#batchQty" role="tab" aria-controls="pills-batchQty" aria-selected="false">Batch Quantity</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="pills-processingDetail-tab" data-toggle="pill" href="#processingDetail" role="tab" aria-controls="pills-batchQty" aria-selected="false">Processing Details</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="pills-otherCast-tab" data-toggle="pill" href="#otherCast" role="tab" aria-controls="pills-otherCast" aria-selected="false"> Other Cast</a>
                                            </li>
                                        </ul>
                                        <!-- Tabs -->
                                        <div class="tab-content" id="pills-tabContent">
                                            <div class="tab-pane fade show active" id="batchQty" role="tabpanel" aria-labelledby="pills-batchQty-tab">
                                                <form id="getBatchQtyDetails">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <input type="hidden" name="id" id="id" value="<?=(!empty($htData->id))?$htData->id:""; ?>" />
                                                            <input type="hidden" name="item_id" id="item_id" value="<?=(!empty($htData->item_id))?$htData->item_id:""; ?>" />

                                                            <div class="col-md-6 form-group">
                                                                <label for="noof_ring">No of Rings per Layer (Nos)</label>
                                                                <input type="text" name="noof_ring" id="noof_ring" class="form-control" value="<?=(!empty($htData->noof_ring))?$htData->noof_ring:""; ?>" />
                                                            </div>
                                                            <div class="col-md-6 form-group">
                                                                <label for="noof_layer">No of Layers per compartment (Nos)</label>
                                                                <input type="text" name="noof_layer" id="noof_layer" class="form-control" value="<?=(!empty($htData->noof_layer))?$htData->noof_layer:""; ?>" />
                                                            </div>
                                                             <div class="col-md-6 form-group">
                                                                <label for="noof_compart">No of compartment (Nos)</label>
                                                                <input type="text" name="noof_compart" id="noof_compart" class="form-control" value="<?=(!empty($htData->noof_compart))?$htData->noof_compart:""; ?>" />
                                                            </div>
                                                           
                                                            <div class="col-md-6 form-group">
                                                                <label for="fixture_wt">Fixture Wt (in Kg)</label>
                                                                <input type="text" name="fixture_wt" id="fixture_wt" class="form-control" value="<?=(!empty($htData->fixture_wt))?$htData->fixture_wt:""; ?>" />
                                                            </div>
                                                            <div class="col-md-6 form-group">
                                                                <label for="load_style">Loading Style</label>
                                                                <input type="text" name="load_style" id="load_style" class="form-control req" value="<?=(!empty($htData->load_style))?$htData->load_style:""; ?>" />
                                                            </div>
                                                            <div class="col-md-6 form-group">
                                                                <label for="batch_qty">Qty /Batch (in Nos)	</label>
                                                                <input type="text" name="batch_qty" id="batch_qty" class="form-control req" value="<?=(!empty($htData->batch_qty))?$htData->batch_qty:""; ?>" />
                                                            </div>
                                                          
                                                        </div>
                                                    </div>
                                                    <div class="card-footer" align="right">
                                                        <button type="button" class="btn btn-outline-success btn-save" onclick="save('getBatchQtyDetails','saveBatchQtyDetails');"><i class="fa fa-check"></i> Save</button>
                                                    </div>
                                                </form>
                                                <hr>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table id="inspection" class="table table-bordered-dark">
                                                            <thead class="thead-info">
                                                                <tr>
                                                                    <th style="width:5%;">#</th>
                                                                    <th class="text-center">No of Rings per Layer (Nos)</th>
                                                                    <th class="text-center">No of Layers per compartment (Nos)</th>
                                                                    <th class="text-center">No of compartment (Nos)</th>
                                                                    <th class="text-center">Loading Style</th>
                                                                    <th class="text-center">Qty /Batch (in Nos)</th>
                                                                    <th class="text-center">Net Wt of parts /Batch (in Kg)</th>
                                                                    <th class="text-center">Fixture Wt (in Kg)</th>
                                                                    <th class="text-center">Gross Wt (in Kg)</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="htBody">
                                                                <?php
                                                                    if(!empty($htData)):
                                                                        $i=1;
                                                                            echo '<tr>
                                                                                        <td class="text-center">'.$i++.'</td>
                                                                                        <td class="text-center">'.$htData->noof_ring.'</td>
                                                                                        <td class="text-center">'.$htData->noof_layer.'</td>
                                                                                        <td class="text-center">'.$htData->noof_compart.'</td>
                                                                                        <td class="text-center">'.$htData->load_style.'</td>
                                                                                        <td class="text-center">'.$htData->batch_qty.'</td>
                                                                                        <td class="text-center">'.$htData->batch_wt.'</td>
                                                                                        <td class="text-center">'.$htData->fixture_wt.'</td>
                                                                                        <td class="text-center">'.$htData->batch_gross_wt.'</td>
                                                                                    </tr>';
                                                                    else:
                                                                        echo '<tr><td colspan="9" style="text-align:center;">No Data Found</td></tr>';
                                                                    endif;
                                                                ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="tab-pane fade" id="processingDetail" role="tabpanel" aria-labelledby="pills-processingDetail-tab">
                                                <form id="getProcessingDetail">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <input type="hidden" name="id" id="id" value="<?=(!empty($htData->id))?$htData->id:""; ?>" />
                                                            <input type="hidden" name="item_id" id="item_id" value="<?=(!empty($htData->item_id))?$htData->item_id:""; ?>" />

                                                            <div class="col-md-6 form-group">
                                                                <label for="cp_per">CP%</label>
                                                                <input type="text" name="cp_per" id="cp_per" class="form-control" value="<?=(!empty($htData->cp_per))?$htData->cp_per:""; ?>" />
                                                            </div>
                                                            <div class="col-md-6 form-group">
                                                                <label for="ct_ht">CTHT</label>
                                                                <input type="text" name="ct_ht" id="ct_ht" class="form-control" value="<?=(!empty($htData->ct_ht))?$htData->ct_ht:""; ?>" />
                                                            </div>
                                                             <div class="col-md-6 form-group">
                                                                <label for="uniformity">Uniformity</label>
                                                                <input type="text" name="uniformity" id="uniformity" class="form-control" value="<?=(!empty($htData->uniformity))?$htData->uniformity:""; ?>" />
                                                            </div>
                                                           
                                                            <div class="col-md-6 form-group">
                                                                <label for="carbur_time">Carburizing Time</label>
                                                                <input type="text" name="carbur_time" id="carbur_time" class="form-control" value="<?=(!empty($htData->carbur_time))?$htData->carbur_time:""; ?>" />
                                                            </div>
                                                            <div class="col-md-6 form-group">
                                                                <label for="carbur_temp">Carburizing Temp</label>
                                                                <input type="text" name="carbur_temp" id="carbur_temp" class="form-control" value="<?=(!empty($htData->carbur_temp))?$htData->carbur_temp:""; ?>" />
                                                            </div>
                                                            <div class="col-md-6 form-group">
                                                                <label for="diffusion_time">Diffusion (N2) Time</label>
                                                                <input type="text" name="diffusion_time" id="diffusion_time" class="form-control" value="<?=(!empty($htData->diffusion_time))?$htData->diffusion_time:""; ?>" />
                                                            </div>
                                                            <div class="col-md-6 form-group">
                                                                <label for="quench_temp">Quenching Temp</label>
                                                                <input type="text" name="quench_temp" id="quench_temp" class="form-control" value="<?=(!empty($htData->quench_temp))?$htData->quench_temp:""; ?>" />
                                                            </div>
                                                            <div class="col-md-6 form-group">
                                                                <label for="quench_time">Quenching Time</label>
                                                                <input type="text" name="quench_time" id="quench_time" class="form-control" value="<?=(!empty($htData->quench_time))?$htData->quench_time:""; ?>" />
                                                            </div>
                                                          
                                                        </div>
                                                    </div>
                                                    <div class="card-footer" align="right">
                                                        <button type="button" class="btn btn-outline-success btn-save" onclick="save('getProcessingDetail','saveProcessingDetail');"><i class="fa fa-check"></i> Save</button>
                                                    </div>
                                                </form>
                                                <hr>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table id="inspection" class="table table-bordered-dark">
                                                            <thead class="thead-info">
                                                                <tr>
                                                                    <th style="width:5%;">#</th>
                                                                    <th class="text-center">CP%</th>
                                                                    <th class="text-center">CTHT</th>
                                                                    <th class="text-center">Uniformity</th>
                                                                    <th class="text-center">Carburizing Time</th>
                                                                    <th class="text-center">Carburizing Temp</th>
                                                                    <th class="text-center">Diffusion (N2) Time</th>
                                                                    <th class="text-center">Quenching Temp</th>
                                                                    <th class="text-center">Quenching Time</th>
                                                                    <th class="text-center">Total time</th>
                                                                    <th class="text-center">Per batch cost</th>
                                                                    <th class="text-center">Per Pc cost</th>
                                                                    <th class="text-center">Per Kg cost</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="proBody">
                                                                <?php
                                                                    if(!empty($htData)):
                                                                        $i=1;
                                                                            echo '<tr>
                                                                                        <td class="text-center">'.$i++.'</td>
                                                                                        <td class="text-center">'.$htData->cp_per.'</td>
                                                                                        <td class="text-center">'.$htData->ct_ht.'</td>
                                                                                        <td class="text-center">'.$htData->uniformity.'</td>
                                                                                        <td class="text-center">'.$htData->carbur_time.'</td>
                                                                                        <td class="text-center">'.$htData->carbur_temp.'</td>
                                                                                        <td class="text-center">'.$htData->diffusion_time.'</td>
                                                                                        <td class="text-center">'.$htData->quench_temp.'</td>
                                                                                        <td class="text-center">'.$htData->quench_time.'</td>
                                                                                        <td class="text-center">'.$htData->total_time.'</td>
                                                                                        <td class="text-center">'.$htData->batch_cost.'</td>
                                                                                        <td class="text-center">'.$htData->cost_pcs.'</td>
                                                                                        <td class="text-center">'.$htData->cost_kg.'</td>
                                                                                    </tr>';
                                                                    else:
                                                                        echo '<tr><td colspan="13" style="text-align:center;">No Data Found</td></tr>';
                                                                    endif;
                                                                ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="tab-pane fade" id="otherCast" role="tabpanel" aria-labelledby="pills-otherCast-tab">
                                                <form id="getOtherCast">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <input type="hidden" name="id" id="id" value="<?=(!empty($htData->id))?$htData->id:""; ?>" />  
                                                            <input type="hidden" name="item_id" id="item_id" value="<?=(!empty($htData->item_id))?$htData->item_id:""; ?>" />

                                                            <div class="col-md-6 form-group">
                                                                <label for="glass_qty">Glass Qty</label>
                                                                <input type="text" name="glass_qty" id="glass_qty" class="form-control" value="<?=(!empty($htData->glass_qty))?$htData->glass_qty:""; ?>" />
                                                            </div>
                                                            <div class="col-md-6 form-group">
                                                                <label for="glass_cost">Glass Cost</label>
                                                                <input type="text" name="glass_cost" id="glass_cost" class="form-control" value="<?=(!empty($htData->glass_cost))?$htData->glass_cost:""; ?>" />
                                                            </div>
                                                             <div class="col-md-6 form-group">
                                                                <label for="wire_qty">Wire Qty</label>
                                                                <input type="text" name="wire_qty" id="wire_qty" class="form-control" value="<?=(!empty($htData->wire_qty))?$htData->wire_qty:""; ?>" />
                                                            </div>
                                                           
                                                            <div class="col-md-6 form-group">
                                                                <label for="wire_cost">Wire Cost</label>
                                                                <input type="text" name="wire_cost" id="wire_cost" class="form-control" value="<?=(!empty($htData->wire_cost))?$htData->wire_cost:""; ?>" />
                                                            </div>
                                                            <div class="col-md-6 form-group">
                                                                <label for="separator_qty">Separator Qty</label>
                                                                <input type="text" name="separator_qty" id="separator_qty" class="form-control" value="<?=(!empty($htData->separator_qty))?$htData->separator_qty:""; ?>" />
                                                            </div>
                                                            <div class="col-md-6 form-group">
                                                                <label for="separator_cost">Separator Cost</label>
                                                                <input type="text" name="separator_cost" id="separator_cost" class="form-control" value="<?=(!empty($htData->separator_cost))?$htData->separator_cost:""; ?>" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-footer" align="right">
                                                        <button type="button" class="btn btn-outline-success btn-save" onclick="save('getOtherCast','saveOtherCast');"><i class="fa fa-check"></i> Save</button>
                                                    </div>
                                                </form>
                                                <hr>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table id="inspection" class="table table-bordered-dark">
                                                            <thead class="thead-info">
                                                                <tr>
                                                                    <th style="width:5%;">#</th>
                                                                    <th class="text-center">Glass Qty</th>
                                                                    <th class="text-center">Glass Cost</th>
                                                                    <th class="text-center">Wire Qty</th>
                                                                    <th class="text-center">Wire Cost</th>
                                                                    <th class="text-center">Separator Qty</th>
                                                                    <th class="text-center">Separator Cost</th>
                                                                    <th class="text-center">Total Cost</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="otherBody">
                                                                <?php
                                                                    if(!empty($htData)):
                                                                        $i=1;
                                                                            echo '<tr>
                                                                                        <td class="text-center">'.$i++.'</td>
                                                                                        <td class="text-center">'.$htData->glass_qty.'</td>
                                                                                        <td class="text-center">'.$htData->glass_cost.'</td>
                                                                                        <td class="text-center">'.$htData->wire_qty.'</td>
                                                                                        <td class="text-center">'.$htData->wire_cost.'</td>
                                                                                        <td class="text-center">'.$htData->separator_qty.'</td>
                                                                                        <td class="text-center">'.$htData->separator_cost.'</td>
                                                                                        <td class="text-center">'.$htData->total_cost.'</td>
                                                                                    </tr>';
                                                                    else:
                                                                        echo '<tr><td colspan="8" style="text-align:center;">No Data Found</td></tr>';
                                                                    endif;
                                                                ?>
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
$(document).ready(function(){ 
    
    $(document).on('click', '.uploadBottomFile', function() {
        $(this).attr("disabled", "disabled");
        var id =$(this).data('id');
        var item_id =$(this).data('item_id'); 
        var fd = new FormData();
        fd.append("bottom_layer", $("#bottom_layer")[0].files[0]);
        fd.append("id",id);
        fd.append("item_id",item_id);
        $.ajax({
            url: base_url + controller + '/uploadBottomFile',
            data: fd,
            type: "POST",
            processData: false,
            contentType: false,
            dataType: "json",
        }).done(function(data) {
            if (data.status === 0) {
                $(".error").html("");
                var error='';
                $.each(data.message, function(key, value) {
                    error+=' '+value;
                });
                $(".msg").html(error);
            } else if (data.status == 1) {
                toastr.success(data.message, 'Success', {
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp",
                    "closeButton": true,
                    positionClass: 'toastr toast-bottom-center',
                    containerId: 'toast-bottom-center',
                    "progressBar": true
                });
            }
            $(this).removeAttr("disabled");
            $("#bottom_layer").val(null);
        });
    });

    $('body').on('click', '.uploadBatchFile', function() {
        $(this).attr("disabled", "disabled");
        var id =$(this).data('id');
        var item_id =$(this).data('item_id'); 
        var fd = new FormData();
        fd.append("batch_no", $("#batch_no")[0].files[0]);
        fd.append("id",id);
        fd.append("item_id",item_id);
        $.ajax({
            url: base_url + controller + '/uploadBatchFile',
            data: fd,
            type: "POST",
            processData: false,
            contentType: false,
            dataType: "json",
        }).done(function(data) {
            if (data.status === 0) {
                $(".error").html("");
                var error='';
                $.each(data.message, function(key, value) {
                    error+=' '+value;
                });
                $(".msg").html(error);
            } else if (data.status == 1) {
                //$("#tbodyData").html(data.html);
                toastr.success(data.message, 'Success', {
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp",
                    "closeButton": true,
                    positionClass: 'toastr toast-bottom-center',
                    containerId: 'toast-bottom-center',
                    "progressBar": true
                });
            }
            $(this).removeAttr("disabled");
            $("#batch_no").val(null);
        });
    });   
});


function save(formId,fnsave){
	setPlaceHolder();
	if(fnsave == "" || fnsave == null){fnsave="save";}
	var form = $('#'+formId)[0];
	var fd = new FormData(form); 
	$.ajax({
		url: base_url + controller + '/' + fnsave,
		data:fd,
		type: "POST",
		processData:false,
		contentType:false,
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {$("."+key).html(value);});
		}else if(data.status==1){
			initTable(); //$('#'+formId)[0].reset();$(".modal").modal('hide'); 
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
            $("#htBody").html(data.tbodyData);
            $("#proBody").html(data.tbodyData);
            $("#otherBody").html(data.tbodyData);
            window.location.reload();
        }else{
			initTable();  $('#'+formId)[0].reset();$(".modal").modal('hide');   
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
        }
				
	});
}

   
</script>