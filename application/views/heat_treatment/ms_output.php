<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4><u>MS Result Output</u></h4>
                    </div>
                    <div class="card-body">
                        <form autocomplete="off" id="pir_form">
                            <div class="col-md-12">
                                <div class="row">
                                    <input type="hidden" name="id" id="id" value="<?= (!empty($dataRow->id) ? $dataRow->id : '') ?>">
                                    <input type="hidden" name="ht_id" id="ht_id" value="<?= (!empty($htData->id) ? $htData->id : '') ?>">
                                    <table class="table table-bordered text-left">
                                        <tr>
                                            <th>Carb Drawing No.:</th>
                                            <td style="width:10%"><?= !empty($htData->carb_drg_no) ? $htData->carb_drg_no : '' ?></td>
                                           <th>Part Name:</th>
                                            <td style="width:10%" colspan="3"><?= !empty($htData->full_name) ? $htData->full_name : '' ?></td>
                                            
                                        </tr>
                                        <tr>
                                            <th>Part No.:</th>
                                            <td style="width:12%" ><?= !empty($htData->part_no) ? $htData->part_no : '' ?></td>
                                            <th>RMTC No.:</th>
                                            <td style="width:10%"></td>
                                            <th>LOT No.:</th>
                                            <td style="width:12%"><?= (!empty($htData->wo_no) ? ($htData->wo_no) : '') ?></td>
                                           
                                        </tr>
                                        <tr>
                                            <th>Heat No.:</th>
                                            <td style="width:10%"><?= (!empty($htData->mill_heat_no)?$htData->mill_heat_no:'') ?></td>
                                            <th>Carb Batch No.:</th>
                                            <td style="width:10%"><?= !empty($htData->trans_number) ? ($htData->trans_number) : '' ?></td>
                                            <th>Carb Batch Qty.:</th>
                                            <td style="width:10%"><?= !empty($htData->qty) ? ($htData->qty) : '' ?></td>
                                           
                                        </tr>
                                        <tr>
                                            <th>Grade:</th>
                                            <td style="width:10%"><?= (!empty($htData->material_grade)?$htData->material_grade:'') ?></td>
                                            <th>TIMKEN Ref Std No.:</th>
                                            <td style="width:10%">3.2</td>
                                            <th>MS Cutting :</th>
                                            <td style="width:10%"></td>
                                        </tr>
                                    </table>
                                    <div class="col-md-2 form-group">
                                        <label for="inspection_date">Production Date</label>
                                        <input type="date" name="inspection_date" id="inspection_date" class="form-control req" value="<?= (!empty($dataRow->inspection_date)) ? $dataRow->inspection_date :"" ?>">
                                    </div>
									<div class="col-md-4 form-group">
                                        <label for="ms_cutting">MS Cutting</label>
                                        <input type="text" name="ms_cutting" id="ms_cutting" class="form-control" value="<?= (!empty($dataRow->ms_cutting)) ? $dataRow->ms_cutting : '' ?>">
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label for="inv_no">Invoice No & Date</label>
                                        <input type="text" name="inv_no" id="inv_no" class="form-control" value="<?= (!empty($dataRow->inv_no)) ? $dataRow->inv_no : '' ?>">
                                    </div>
									<div class="col-md-2 form-group">
                                        <label for="glass_wool">Glass wool used</label>
                                        <select id="glass_wool" name="glass_wool"  class="form-control">
                                            <option <?= (!empty($dataRow->glass_wool) && $dataRow->glass_wool == "Yes") ? "selected" : "";?> value="Yes">Yes</option>
                                            <option <?= (!empty($dataRow->glass_wool) && $dataRow->glass_wool == "No") ? "selected" : "";?> value="No">No</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12 form-group">
                                        <label for="remark">Remark</label>
                                        <input type="text" name="remark" id="remark" class="form-control" value="<?= (!empty($dataRow->remark)) ? $dataRow->remark : '' ?>">
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="col-md-12">
                                <div class="error general"></div>
                            </div>
                            <h4>Specification</h4>
                            <div class="col-md-12 mt-3">
                                <div class="row form-group">
                                    <div class="table-responsive">
                                        <table id="pirTable" class="table table-bordered generalTable">
                                            <thead class="thead-info" id="theadData">
                                                <tr style="text-align:center;">
                                                    <th style="width:5%;">#</th>
                                                    <th>Case Aim</th>
                                                    <th>0.80% C(min)</th>
                                                    <th>0.50% C (min)</th>
                                                    <th>0.50% C (max)</th>
                                                    <th>Material Spec.</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbodyData">
                                                <?php
                                                $i=1;
                                                	if(!empty($htData)):
                                                        echo '<tr>
                                                            <td>'.$i++.'</td>
                                                            <td>'.$htData->case_aim.'</td>
                                                            <td><input type="text" name="c_80min" class="form-control xl_input maxw-250 text-center" value="'. (!empty($dataRow->c_80min) ? $dataRow->c_80min : '').'"></td>
                                                            <td><input type="text" name="c_50min" class="form-control xl_input maxw-250 text-center" value="'. (!empty($dataRow->c_50min) ? $dataRow->c_50min : '').'"></td>
                                                            <td><input type="text" name="c_50max" class="form-control xl_input maxw-250 text-center" value="'. (!empty($dataRow->c_50max) ? $dataRow->c_50max : '').'"></td>
                                                            <td><input type="text" name="material_spec" class="form-control xl_input maxw-250 text-center" value="'. (!empty($dataRow->material_spec) ? $dataRow->material_spec : '').'"></td>
                                                        </tr>';
                                                    endif;
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <h4>Observation</h4>
                            <div class="col-md-12 mt-3">
                                <div class="row form-group">
                                    <div class="table-responsive">
                                        <table id="pirTable" class="table table-bordered generalTable">
                                            <thead class="thead-info" id="theadData">
                                                <tr style="text-align:center;">
                                                    <th rowspan="2" style="width:5%;">#</th>
                                                    <th rowspan="2">Specification</th>
                                                    <th colspan="10">Observation On Samples</th>
                                                </tr>
                                                <tr style="text-align:center;">
													<th>1</th>
													<th>2</th>
													<th>3</th>
													<th>4</th>
													<th>5</th>
													<th>6</th>
													<th>7</th>
													<th>8</th>
													<th>9</th>
													<th>10</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbodyData">
                                                <?php
                                                 $obj="";
                                                    if(!empty($dataRow)):
                                                       $obj = json_decode($dataRow->observation_sample); 
                                                    endif;
                                                        $i=1; 
                                                        foreach($spArray as $key=>$value):
                                                            $c=0;
                                                            echo '<tr>
                                                                    <td style="text-align:center;">'.$i++.'</td>
                                                                    <td>'.$value.'</td>';
                                                            for($c=1;$c<=10;$c++):
                                                                echo '<td ><input type="text" name="sample'.($c).'_'.$key.'" class="xl_input maxw-150 text-center" value="'.(!empty($dataRow) ? $obj->{$key}[$c-1] : "").'"></td>';
                                                            endfor;
                                                        endforeach;
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
                            <a href="<?= base_url('heatTreatment') ?>" class="btn waves-effect waves-light btn-outline-secondary float-right save-form" style="margin-right:10px;"><i class="fa fa-times"></i> Cancel</a>
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

    });

    function saveReport(formId) {
        var fd = $('#' + formId)[0];
        var formData = new FormData(fd);
        $.ajax({
            url: base_url + controller + '/saveMsOutput',
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
                window.location = base_url + controller+'/index';
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