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
                                $sample_size =5 //(!empty($dataRow->sampling_qty) ? $dataRow->sampling_qty : (!empty($paramData[0]->sev)?$paramData[0]->sev:1));
                                ?>
                                <div class="row">
                                    <input type="hidden" name="id" id="id" value="<?= (!empty($dataRow->id) ? $dataRow->id : '') ?>">
                                    <input type="hidden" name="trans_type" id="trans_type" value="2">
                                    <input type="hidden" name="item_id" id="item_id" value="<?= (!empty($dataRow->item_id) ? $dataRow->item_id : $jobData->product_id) ?>">
                                    <input type="hidden" name="sampling_qty" id="sampling_qty" value="<?= $sample_size ?>">
                                    <input type="hidden" name="mir_id" id="mir_id" value="<?= (!empty($dataRow->mir_id)) ? $dataRow->mir_id : $job_card_id ?>">
                                    <input type="hidden" name="mir_trans_id" id="mir_trans_id" value="<?= (!empty($dataRow->mir_trans_id)) ? $dataRow->mir_trans_id : $process_id ?>">
                                    <input type="hidden" name="party_id" id="party_id" value="<?= (!empty($dataRow->party_id)) ? $dataRow->party_id : $machine_id ?>">
                                    <input type="hidden" name="job_approval_id" id="job_approval_id" value="<?= (!empty($dataRow->job_approval_id)) ? $dataRow->job_approval_id : $job_approval_id ?>">


                                    <div class="col-lg-12 col-xlg-12 col-md-12">
                                        <table class="table table-bordered-dark">
                                            <tr>
                                                <th>Job Card No</th>
                                                <th>Product </th>
                                                <th>Machine </th>
                                                <th>Process </th>
                                            </tr>
                                            <tr>
                                                <td><?= (!empty($dataRow->job_number)? $dataRow->job_number : $jobData->job_number) ?></td>
                                                <td><?= (!empty($dataRow->full_name)?$dataRow->full_name:$jobData->full_name) ?></td>
                                                <td><?= (!empty($dataRow->party_id)?'[' . $dataRow->machine_code . '] ' . $dataRow->machine_name:'[' . $machine_code . '] ' . $machine_name) ?></td>
                                                <td><?= (!empty($dataRow->process_name)?$dataRow->process_name:$process_name) ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label for="trans_date">trans_date</label>
                                        <input type="date" name="trans_date" id="trans_date" class="form-control" value="<?= (!empty($dataRow->trans_date)) ? $dataRow->trans_date : date("Y-m-d") ?>">
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <label for="remark">Remark</label>
                                        <input type="text" name="remark" id="remark" class="form-control" value="<?= (!empty($dataRow->remark)) ? $dataRow->remark : '' ?>">
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
                                                    $reportTime = !empty($dataRow->result)?explode(',',$dataRow->result):[];
                                                    for ($c = 0; $c < $sample_size; $c++) :
                                                    ?>
                                                        <th><input type="time" class="form-control" name="report_time[]" value="<?=!empty($reportTime[$c])?$reportTime[$c]:''?>"></th>
                                                    <?php
                                                    endfor;
                                                    ?>

                                                </tr>
                                            </thead>
                                            <tbody id="tbodyData">
                                                <?php
                                                $tbodyData = "";
                                                $i = 1;$tbcnt=1;

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
                                                            $obj = json_decode($dataRow->observation_sample);
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
                                                                $tbodyData .= '<td><input type="text" tabindex="'.(($c*10)+$tbcnt).'" name="sample' . ($c + 1) . '_' . $row->id . '" id="sample' . ($c + 1) . '_' . $i . '" class="form-control text-center parameter_limit' . $cls . '" value="' . $obj->{$row->id}[$c] . '" data-min="' . $row->min_req . '" data-max="' . $row->max_req . '" data-requirement="' . $row->requirement . '" data-row_id ="' . $i . '" ></td>';
                                                            else :
                                                                $tbodyData .= '<td><input type="text" tabindex="'.(($c*10)+$tbcnt).'" name="sample' . ($c + 1) . '_' . $row->id . '" id="sample' . ($c + 1) . '_' . $i . '" class="form-control text-center parameter_limit' . $cls . '" value=""  data-min="' . $row->min_req . '" data-max="' . $row->max_req . '" data-requirement="' . $row->requirement . '" data-row_id ="' . $i . '"></td>';
                                                            endif;
                                                        endfor;
                                                    endforeach;
                                                endif;
                                                $tbcnt++;
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
                            <a href="<?= base_url('production/pir/pirIndex') ?>" class="btn waves-effect waves-light btn-outline-secondary float-right save-form" style="margin-right:10px;"><i class="fa fa-times"></i> Cancel</a>
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
                window.location = base_url + controller+'/pirIndex';
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