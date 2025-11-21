<div class="col-md-12">
    <form>
        <div class="row">
            <input type="hidden" name="id" id="id" value="" />
            <input type="hidden" name="type" id="type" value="2" />

            <div class="col-md-6 form-group">
                <label for="title">Title </label>
                <input type="text" name="title" id="title" class="form-control req" value="<?=!empty($dataRow->title)?$dataRow->title:''?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="control_method">Control Method</label>
                <select name="control_method" id="control_method" class="form-control single-select req">
                    <option value="">Select Method</option>
                    <?php
                        foreach($controlMethod as $row):
                            $selected = (!empty($dataRow->control_method) && $dataRow->control_method==$row->control_method)?'selected':'';
                            echo '<option value="'.$row->control_method.'" '.$selected.'>'.$row->control_method.'</option>';
                        endforeach;
                    ?>
                </select>
                <div class="error control_method"></div>
            </div>
            <div class="col-md-5">
                <label for="lot_size">Lot Size</label>
                <div class="input-group mb-3">
                    <input type="text" name="min_lot_size" id="min_lot_size" class="form-control numericOnly" placeholder="min" value=""  />
                    <input type="text" name="max_lot_size" id="max_lot_size" class="form-control req numericOnly" placeholder="max" value=""  />
                </div>
            </div>
            <div class="col-md-4 form-group">
                <label for="sample_size">Sample Size</label>
                <input type="text" name="sample_size" id="sample_size"  class="form-control req numericOnly" value="" />
            </div>
            <div class="col-md-3 form-group">
                <button type="button" class="btn btn-outline-success waves-effect waves-light mt-30" onclick="saveSamplingPlan('addSamplingPlan','save');"><i class="fa fa-plus"></i> Add Plan</button>
            </div>
        </div>
    </form>
    <div class="row">
        <div class="table-responsive">
            <table id="samplingplantbl" class="table table-bordered align-items-center">
                <thead class="thead-info">
                    <tr>
                        <th style="width:5%;">#</th>
                        <th>Title</th>
                        <th>Control Mathod </th>
                        <th>Lot Size </th>
                        <th>Sample Size</th>
                        <th class="text-center" style="width:10%;">Action</th>
                    </tr>
                </thead>
                <tbody id="samplingPlanBody">
                    <?php
                    if (!empty($samplingPlanData)) :
                            $i = 1;
                            foreach ($samplingPlanData as $row) :
                                echo '<tr>
                                            <td>' . $i++ . '</td>
                                            <td>
                                                ' . $row->title . '
                                                </td>
                                                <td>
                                                ' . $row->control_method . '
                                                </td>
                                            <td>
                                                ' . $row->min_lot_size . ' - '.(!empty($row->max_lot_size)?$row->max_lot_size:'above').'
                                                </td>
                                            <td>
                                                ' . $row->sample_size . '
                                            </td>
                                            <td class="text-center">
                                                <button type="button" onclick="trashPlan('.$row->id.');" class="btn btn-outline-danger waves-effect waves-light"><i class="ti-trash"></i></button>
                                            </td>
                                        </tr>';
                            endforeach;
                        endif;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
function saveSamplingPlan(formId,fnsave){
	//var fd = $('#'+formId).serialize();
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
            $("#samplingPlanBody").html(data.tbodyData);
            $("#control_method").val($("#control_method :selected").val());
            $("#control_method").comboSelect();
            $("#min_lot_size").val("");
            $("#max_lot_size").val("");
            $("#sample_size").val("");
        }else{
			initTable();  $('#'+formId)[0].reset();$(".modal").modal('hide');   
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
        }
				
	});
}
</script>