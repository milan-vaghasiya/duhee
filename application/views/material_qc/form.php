<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4><u>Material Quality Control</u></h4>
                    </div>
                    <div class="card-body">
                        <form autocomplete="off" id="report_form">
                           
                            <div class="col-md-12">
                                <table class="table  jpExcelTable">
                                    <thead style="background:#eee;">
                                        <tr>
                                            <th>Item</th>
                                            <th>Raw Material</th>
                                            <th>Material Grade</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><?=$item_full_name?></td>
                                            <td><?=$rm_code?></td>
                                            <td><?=$material_grade?><div class="error grade_id"></div></td>
                                        </tr>
                                    </tbody>

                                </table>
                                <input type="hidden" name="id" value="" />
                                <input type="hidden" name="item_id" id="item_id" value="<?= (!empty($item_id)) ? $item_id : "" ?>" />
                                <input type="hidden" name="grade_id" id="grade_id" value="<?= (!empty($grade_id)) ? $grade_id : "" ?>" />
                                <div class="row">
                                    <div class="col-md-2 form-group">
                                        <label for="trans_number">Report No.</label>
                                        <input type="text" id="trans_number" name="trans_number" readOnly value="<?=!empty($trans_number)?$trans_number:''?>" class="form-control">
                                        <input type="hidden" id="trans_no" name="trans_no" readOnly value="<?=!empty($trans_no)?$trans_no:''?>" class="form-control">
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <label for="trans_date">Report Date</label>
                                        <input type="date" id="trans_date" name="trans_date"  value="<?=!empty($dataRow->trans_date)?$dataRow->trans_date:date("Y-m-d")?>" class="form-control">
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <label for="process_id">Process</label>
                                        <select name="process_id" id="process_id" class="form-control single-select">
                                            <option value="">Select Process</option>
                                            <?php
                                            if(!empty($processList)){
                                                foreach($processList as $row){
                                                    $selected = (!empty($parameterList[0]->process_id) && $parameterList[0]->process_id == $row->id)?'selected':''
                                                ?><option value="<?=$row->id?>" <?=$selected?>> <?=$row->family_name?></option><?php
                                                }
                                            }
                                            ?>
                                        </select>
                                        <div class="error process_id"></div>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="remark">Remark</label>
                                        <input type="text" id="remark" name="remark" value="<?=!empty($dataRow->remark)?$dataRow->remark:''?>" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <hr>

                            <div class="col-md-12">
                                <div class="error general_error"></div>
                                <div class="table-responsive">
                                    <table class="table  jpExcelTable" id="reportTable">
                                        <thead style="background:#eee;">
                                            <tr>
                                                <th>#</th>
                                                <th>Parameter</th>
                                                <th>Description</th>
                                                <th>Inspection Method</th>
                                                <th>Obsevation</th>
                                                <th>Remark</th>
                                            </tr>
                                        </thead>
                                        <tbody id="reportTbody">
                                            <tr>
                                                <th class="text-center" colspan="6">No data available.</th>
                                            </tr>
                                        </tbody>

                                    </table>
                                </div>
                            </div>
                            <hr>
                        </form>
                    </div>
                    <div class="card-footer">
                        <div class="col-md-12">
                            <button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="saveReport('report_form');"><i class="fa fa-check"></i> Save</button>
                            <a href="<?= base_url('materialQc') ?>" class="btn waves-effect waves-light btn-outline-secondary float-right save-form" style="margin-right:10px;"><i class="fa fa-times"></i> Cancel</a>
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
        $(document).on("change","#process_id",function(){
            var process_id = $(this).val();
            var grade_id = $("#grade_id").val();
            var valid = 1;
            if(process_id == "" ){
                $(".process_id").html("Please select Process.");
                valid = 0;
            }
            if(grade_id == "" ){
                $(".grade_id").html("Grade is required.");
                valid = 0;
            }
            if(valid)
            {
                $.ajax({
                    url:base_url + controller + "/getQcParameters",
                    type:'post',
                    data:{process_id:process_id,grade_id:grade_id},
                    dataType:'json',
                    success:function(data){
                        $("#reportTbody").html("");
                        $("#reportTbody").html(data.tbodyData);
                    }
                });
            }
	    });
    });

    function saveReport(formId) {
        var fd = $('#' + formId)[0];
        var formData = new FormData(fd);
        $.ajax({
            url: base_url + controller + '/save',
            data: formData,
            processData: false,
            contentType: false,
            type: "POST",
            dataType: "json",
        }).done(function(data) {
            if (data.status === 0) {
                $(".error").html("");
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
                window.location = base_url + controller ;
            } else {
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