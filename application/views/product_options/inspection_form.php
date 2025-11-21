
<div class="col-md-12">
    <form id="getPreInspection">
        <div class="row">
            <input type="hidden" name="id" id="id" class="id" value="" />
            <input type="hidden" name="item_id" id="item_id" class="item_id" value="<?=$item_id?>" />
            <input type="hidden" name="param_type" id="param_type" class="param_type" value="<?=$param_type?>" />

            <div class="col-md-6 form-group">
                <label for="parameter">Perameter</label>
                <input type="text" name="parameter" id="parameter" class="form-control req" value="" />
            </div>
            <div class="col-md-6 form-group">
                <label for="specification">Specification</label>
                <input type="text" name="specification" id="specification" class="form-control req" value="" />
            </div>
            <div class="col-md-3 form-group">
                <label for="lower_limit">Lower Limit</label>
                <input type="text" name="lower_limit" id="lower_limit" class="form-control req" value="" />
            </div>
            <div class="col-md-3 form-group">
                <label for="upper_limit">Upper Limit</label>
                <input type="text" name="upper_limit" id="upper_limit" class="form-control req" value="" />
            </div>
            <div class="col-md-4 form-group">
                <label for="measure_tech">Measure. Tech.</label>
                <input type="text" name="measure_tech" id="measure_tech" class="form-control req" value="" />
            </div>
            <div class="col-md-2">
                <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save mt-30" onclick="savePreInspection('getPreInspection','savePreInspectionParam');"><i class="fa fa-plus"></i> Add</button>
            </div>
        </div>
    </form>
    <hr>
    <div class="row">
        <div class="table-responsive">
            <table id="inspection" class="table table-bordered align-items-center">
                <thead class="thead-info">
                    <tr>
                        <th style="width:5%;">#</th>
                        <th>Perameter</th>
                        <th>Specification</th>
                        <th>Lower Limit</th>
                        <th>Upper Limit</th>
                        <th>Measure. Tech.</th>
                        <th class="text-center" style="width:10%;">Action</th>
                    </tr>
                </thead>
                <tbody id="inspectionBody">
                    <?php
                        if(!empty($paramData)):
                            $i=1;
                            foreach($paramData as $row):
                                echo '<tr>
                                            <td>'.$i++.'</td>
                                            <td>'.$row->parameter.'</td>
                                            <td>'.$row->specification.'</td>
                                            <td>'.$row->lower_limit.'</td>
                                            <td>'.$row->upper_limit.'</td>
                                            <td>'.$row->measure_tech.'</td>
                                            <td class="text-center">
                                                <button type="button" onclick="trashPreInspection('.$row->id.','.$row->item_id.');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="ti-trash"></i></button>
                                            </td>
                                        </tr>';
                            endforeach;
                        else:
                            echo '<tr><td colspan="7" style="text-align:center;">No Data Found</td></tr>';
                        endif;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function savePreInspection(formId,fnsave){
	// var fd = $('#'+formId).serialize();
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
            $("#inspectionBody").html(data.tbodyData);
            $("#parameter").val("");
            $("#specification").val("");
            $("#lower_limit").val("");
            $("#upper_limit").val("");
            $("#measure_tech").val("");
        }else{
			initTable();  $('#'+formId)[0].reset();$(".modal").modal('hide');   
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
        }
				
	});
}

function trashPreInspection(id,item_id,name='Record'){
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
						url: base_url + controller + '/deletePreInspection',
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data)
						{
							if(data.status==0)
							{
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
							else
							{
								initTable(); 
								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
                                $("#inspectionBody").html(data.tbodyData);
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