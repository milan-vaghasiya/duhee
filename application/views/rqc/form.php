<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4><u>Inprocess (Patrol) Inspection Report</u></h4>
                    </div>
                    <div class="card-body">
                        <form autocomplete="off" id="pir_form">

                            <div class="col-md-12">
                                <?php
                                $sample_size =(!empty($rqcData->sampling_qty) ? $rqcData->sampling_qty : '');
                                ?>
                                <div class="row">
                                    <input type="hidden" name="id" id="id" value="<?= (!empty($rqcData->id) ? $rqcData->id : '') ?>">
                                   
                                    <div class="col-lg-12 col-xlg-12 col-md-12">
                                        <table class="table table-bordered-dark">
                                            <tr>
                                                <th>Job Card No</th>
                                                <th>Product </th>
                                                <th>Qty </th>
                                            </tr>
                                            <tr>
                                                <td><?= (!empty($rqcData->job_number)? $rqcData->job_number :'' ) ?></td>
                                                <td><?= (!empty($rqcData->full_name)?$rqcData->full_name:'') ?></td>
                                                <td><?= (!empty($rqcData->lot_qty)?$rqcData->lot_qty:'') ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <input type="hidden" name="item_id" value="<?= (!empty($rqcData->item_id)?$rqcData->item_id:'') ?>">
                                    <div class="col-md-4 form-group">
                                        <label for="trans_date">Date</label>
                                        <input type="date" name="trans_date" id="trans_date" class="form-control" value="<?= (!empty($rqcData->trans_date)) ? $rqcData->trans_date : date("Y-m-d") ?>" readonly>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <label for="remark">Remark</label>
                                        <input type="text" name="remark" id="remark" class="form-control" value="<?= (!empty($rqcData->remark)) ? $rqcData->remark : '' ?>">
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="col-md-12">
                                <div class="error general"></div>
                            </div>
                            <div class="col-md-12 mt-3">
                                <div class="row form-group">
                                    <div class="table-responsive">

                                        
                                        <table id="pirTable" class="table table-bordered generalTable">
                                            <thead class="thead-info" id="theadData">
                                                <tr style="text-align:center;">
                                                    <th rowspan="2" style="width:5%;">#</th>
                                                    <th rowspan="2" style="width:5%;">Operation No</th>
                                                    <th rowspan="2">Product/Process Char.</th>
                                                    <th rowspan="2">Specification</th>
                                                    <th rowspan="2">Measurement Tech.</th>
                                                    <th rowspan="2">Size</th>
                                                    <th rowspan="2">Freq.</th>
                                                    <th colspan="<?= $sample_size ?>">Observation on Samples</th>
                                                </tr>
                                                <tr style="text-align:center;">
                                                    <?php
                                                    $reportTime = !empty($rqcData->result)?explode(',',$rqcData->result):[];
                                                    for ($c = 0; $c < $sample_size; $c++) :
                                                    ?>
                                                        <th><?=$c+1?></th>
                                                    <?php
                                                    endfor;
                                                    ?>

                                                </tr>
                                            </thead>
                                            <tbody id="tbodyData">
                                                <?php
                                                $tbodyData = "";
                                                $i = 1;

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
                                                        if (!empty($rqcData)) :
                                                            $obj = json_decode($rqcData->observation_sample);
                                                        endif;
                                                        $char_class=''; if(!empty($row->char_class)){ $char_class='<img src="' . base_url('assets/images/symbols/'.$row->char_class.'.png') . '" style="width:20px;display:inline-block;vertical-align:middle;" />'; }

                                                        $tbodyData .= '<tr>
                                                                        <td style="text-align:center;">' . $i++ . '</td>
                                                                        <td>' . $row->process_no.' '.$char_class . '</td>
                                                                        <td>' . $row->parameter . '</td>
                                                                        <td>' . $diamention . '</td>
                                                                        <td>' . $row->category_name . '</td>
                                                                        <td>' . $row->sev . '</td>
                                                                        <td>' . $row->potential_cause . '</td>';
                                                        for ($c = 0; $c < $sample_size; $c++) :
                                                            if (!empty($obj->{$row->id})) :
                                                                $tbodyData .= '<td><input type="text" name="sample' . ($c + 1) . '_' . $row->id . '" id="sample' . ($c + 1) . '_' . $i . '" class="form-control text-center parameter_limit' . $cls . '" value="' . $obj->{$row->id}[$c] . '" data-min="' . $row->min_req . '" data-max="' . $row->max_req . '" data-requirement="' . $row->requirement . '" data-row_id ="' . $i . '" ></td>';
                                                            else :
                                                                $tbodyData .= '<td><input type="text" name="sample' . ($c + 1) . '_' . $row->id . '" id="sample' . ($c + 1) . '_' . $i . '" class="form-control text-center parameter_limit' . $cls . '" value=""  data-min="' . $row->min_req . '" data-max="' . $row->max_req . '" data-requirement="' . $row->requirement . '" data-row_id ="' . $i . '"></td>';
                                                            endif;
                                                        endfor;

                                                    endforeach;
                                                endif;
                                                echo $tbodyData;
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
                            <button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="saveReport('pir_form');"><i class="fa fa-check"></i> Save</button>
                            <a href="<?= base_url('rqc/rqcIndex') ?>" class="btn waves-effect waves-light btn-outline-secondary float-right save-form" style="margin-right:10px;"><i class="fa fa-times"></i> Cancel</a>
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
        $(document).on('change', '#mir_id', function(e) {
            var job_card_id = $(this).val();
            var jobData = $('#mir_id :selected').data('row');
            $("#item_id").val(jobData.product_id);
            if (job_card_id) {
                $.ajax({
                    url: base_url + controller + '/getPFCNo',
                    data: {
                        job_card_id: job_card_id,
                        job_process: jobData.process,
                        item_id: jobData.product_id
                    },
                    type: "POST",
                    dataType: 'json',
                    success: function(data) {
                        $("#mir_trans_id").html(data.options);
                        $("#mir_trans_id").comboSelect();
                    }
                });
            }
        });

        $(document).on('change', '#mir_trans_id', function(e) {
            var pfc_id = $(this).val();
            var item_id = $('#item_id').val();
            if (pfc_id) {
                $.ajax({
                    url: base_url + controller + '/getPirDimensionData',
                    data: {
                        pfc_id: pfc_id,
                        item_id: item_id
                    },
                    type: "POST",
                    dataType: 'json',
                    success: function(data) {
                        $("#theadData").html(data.theadData);
                        $("#tbodyData").html(data.tbodyData);
                        $("#tbodyData").html(data.tbodyData);
                        $("#sampling_qty").val(data.sample_size);
                    }
                });
            }
        });
        
        $(document).on('keyup change','.parameter_limit',function(){
            var requirement = $(this).data('requirement');
            var min = $(this).data('min');
            var max = $(this).data('max');
            var sample_value = $(this).val();
            if(parseFloat(requirement) == 1){
                if(parseFloat(max) >= parseFloat(sample_value) && parseFloat(min) <= parseFloat(sample_value)){
                    $(this).removeClass('bg-danger');
                }else{
                    if(parseFloat(sample_value) > 0){$(this).addClass('bg-danger');}else{$(this).removeClass('bg-danger');}
                }					
            }
            if(parseFloat(requirement) == 2){
                if(parseFloat(min) <= parseFloat(sample_value)){
                    $(this).removeClass('bg-danger');
                }else{
                    if(parseFloat(sample_value) > 0){$(this).addClass('bg-danger');}else{$(this).removeClass('bg-danger');}
                }
            }

            if(parseFloat(requirement) == 3){
                if(parseFloat(max) >= parseFloat(sample_value)){
                    $(this).removeClass('bg-danger');
                }else{
                    if(parseFloat(sample_value) > 0){$(this).addClass('bg-danger');}else{$(this).removeClass('bg-danger');}
                }
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
                window.location = base_url + controller+'/rqcIndex';
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