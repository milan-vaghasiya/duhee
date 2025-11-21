<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4><u>Setting Approval Report</u></h4>
                    </div>
                    <div class="card-body">
                        <form id="sarForm">
                            <div class="col-md-12">
                                <div class="row">
                                    <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />

                                    <div class="col-md-3 form-group">
                                        <label for="trans_date">Date</label>
                                        <input type="date" name="trans_date" id="trans_date" class="form-control req" value="<?=(!empty($dataRow->trans_date))?($dataRow->trans_date):date('Y-m-d')?>" />
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="job_card_id">Jobcard</label>
                                        <select name="job_card_id" id="job_card_id" class="form-control single-select getParam req">
                                            <option value="">Select Jobcard</option>
                                            <?php
                                            if(!empty($jobCardList)){
                                                foreach($jobCardList as $row){
                                                    $selected = (!empty($dataRow->job_card_id) && $dataRow->job_card_id == $row->id) ? "selected" : "";
                                                    echo '<option data-product_id="'.$row->product_id.'" value="'.$row->id.'" '.$selected.'>'.$row->job_number.'</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="process_id">Process</label>
                                        <select name="process_id" id="process_id" class="form-control single-select getParam req">
                                            <option value="">Select Process</option>
                                            <?=(!empty($options) ? $options : '')?>
                                        </select>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="machine_id">Machine</label>
                                        <select name="machine_id" id="machine_id" class="form-control single-select req">
                                            <option value="">Select Machine</option>
                                            <?php
                                            if(!empty($machineList)){
                                                foreach($machineList as $row){
                                                    $selected = (!empty($dataRow->machine_id) && $dataRow->machine_id == $row->id) ? "selected" : "";
                                                    echo '<option value="'.$row->id.'" '.$selected.'>['.$row->item_code.'] '.$row->item_name.'</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="setter_id">Setter</label>
                                        <select name="setter_id" id="setter_id" class="form-control single-select req">
                                            <option value="">Select Setter</option>
                                            <?php
                                            if(!empty($setterList)){
                                                foreach($setterList as $row){
                                                    $selected = (!empty($dataRow->setter_id) && $dataRow->setter_id == $row->id) ? "selected" : "";
                                                    echo '<option value="'.$row->id.'" '.$selected.'>'.$row->emp_name.'</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <label for="setting_time">Setting Time (In Minute)</label>
                                        <input type="text" name="setting_time" id="setting_time" class="form-control numericOnly req" value="<?=(!empty($dataRow->setting_time))?$dataRow->setting_time:""?>" >
                                    </div>                                
                                    <div class="col-md-7 form-group">
                                        <label for="remark">Remark</label>
                                        <input type="text" name="remark" id="remark" class="form-control" value="<?=(!empty($dataRow->remark))?$dataRow->remark:""?>" >
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="error general_error"></div>
                                    <div class="table-responsive">
                                        <table id="preDispatchtbl" class="table table-bordered generalTable">
											<thead class="thead-info" id="theadData">
                                                <tr style="text-align:center;">
                                                    <th style="width:5%;">#</th>
                                                    <th style="width:5%;">Operation No</th>
                                                    <th>Product/Process Char.</th>
                                                    <th>Specification</th>
                                                    <th>Measurement Tech.</th>
                                                    <th>Size</th>
                                                    <th>Freq.</th>
                                                    <th>Observation</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbodyData" class="scroll-tbody scrollable maxvh-60">       
                                            <?php
                                                $i = 1; $tbcnt = 1;                                        
                                                if (!empty($paramData)) :
                                                    foreach ($paramData as $row) :
                                                        $obj = new StdClass;
                                                        $cls = "";
                                                        if (!empty($row->lower_limit) or !empty($row->upper_limit)) :
                                                            $cls = "floatOnly";
                                                        endif;
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
                                                        if (!empty($dataRow)) :
                                                            $obj = json_decode($dataRow->observation);
                                                        endif;
                                                        $char_class=''; if(!empty($row->char_class)){ $char_class='<img src="' . base_url('assets/images/symbols/'.$row->char_class.'.png') . '" style="width:20px;display:inline-block;vertical-align:middle;" />'; }
                                        
                                                        echo '<tr>
                                                                <td style="text-align:center;">' . $i++ . '</td>
                                                                <td>' . $row->process_no.' '.$char_class . '</td>
                                                                <td>' . $row->parameter . '</td>
                                                                <td>' . $diamention . '</td>
                                                                <td>' . $row->category_name . '</td>
                                                                <td>' . $row->sev . '</td>
                                                                <td>' . $row->potential_cause . '</td>';
                                                                if (!empty($obj->{$row->id})) :
                                                                    echo '<td><input type="text" name="sample_'.$row->id.'" id="sample_'.$i.'" class="form-control text-center parameter_limit'.$cls.'" value="'.$obj->{$row->id}.'" data-min="'.$row->min_req.'" data-max="'.$row->max_req.'" data-requirement="'.$row->requirement.'" data-row_id="'.$i.'"></td>';
                                                                else :
                                                                    echo '<td><input type="text" name="sample_'.$row->id.'" id="sample_'.$i.'" class="form-control text-center parameter_limit'.$cls.'" value="" data-min="'.$row->min_req.'" data-max="'.$row->max_req.'" data-requirement="'.$row->requirement.'" data-row_id="'.$i.'"></td>';
                                                                endif;
                                                        echo '</tr>';
                                                    endforeach;
                                                else:
                                                    echo '<tr class="text-center"><td colspan="8">Data not available.</td></tr>';
                                                endif;
                                                $tbcnt++;
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
                            <button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="saveSar('sarForm');" ><i class="fa fa-check"></i> Save</button>
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
$(document).ready(function(){

    $(document).on('change',"#job_card_id",function(){
        var job_card_id = $(this).val();

        if(job_card_id){
            $.ajax({
                url: base_url + controller + '/getJobcardProcessList',
                data: { job_card_id:job_card_id },
                type: "POST",
                dataType: 'json',
                success: function (data) {
                    $("#process_id").html("");
                    $("#process_id").html(data.options);
                    $("#process_id").comboSelect();
                }
            });
        }
	});

    $(document).on('change',"#process_id",function(){
        var process_id = $(this).val();
        var job_card_id = $("#job_card_id").val();

        if(process_id){
            $.ajax({
                url: base_url + controller + '/getProcessParam',
                data: { job_card_id:job_card_id, process_id:process_id },
                type: "POST",
                dataType: 'json',
                success: function (data) {
                    $("#tbodyData").html("");
                    $("#tbodyData").html(data.tbodyData);
                }
            });
        }
	});

});

function saveSar(formId){
	var fd = $('#'+formId)[0];
    var formData = new FormData(fd);
	$.ajax({
		url: base_url + controller + '/save',
		data:formData,
        processData: false,
        contentType: false,
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