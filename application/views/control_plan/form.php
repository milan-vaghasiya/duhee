<div class="col-md-12">
    <form id="getPreInspection">
        <div class="row">
            <input type="hidden" name="id" id="id" class="id" value="" />
            <input type="hidden" name="item_id" id="item_id" class="item_id" value="<?= $item_id ?>" />
            <input type="hidden" name="item_type" id="item_type" class="item_type" value="3" />

            <div class="col-md-3 form-group">
                <label for="process_id">Process</label>
                <select name="process_id" class="form-control single-select req">
                    <option value="">Select Process</option>
                    <option value="999">Incoming Inspection</option>
                    <?php
                    foreach ($processData as $row) :
                        echo '<option value="' . $row->id . '">' . $row->process_name . '</option>';
                    endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="product_char">Product Char.</label>
                <input type="text" name="product_char" class="form-control">
            </div>
            <div class="col-md-3">
                <label for="process_char">Process Char.</label>
                <input type="text" name="process_char" class="form-control">
            </div>
            <div class="col-md-3 form-group">
                <label for="param_type">Used In</label>
                <select id="parameter_type" data-input_id="param_type" class="form-control jp_multiselect req" placeholder="Used In" multiple="multiple">
                    <option value="1" selected>IIR</option>
                    <option value="2" selected>IPR</option>
                    <option value="3" selected>FIR</option>
                </select>
                <input type="hidden" name="param_type" id="param_type">
            </div>
            <div class="col-md-4 form-group">
                <label for="specification">Specification</label>
                <input type="text" name="specification" id="specification" class="form-control req" value="" />
            </div>
            <div class="col-md-4 form-group">
                <label for="lower_limit">Dimension Range</label>
                <div class="input-group">
                    <input type="text" name="lower_limit" id="lower_limit" class="form-control req floatOnly" value="" placeholder="Lower Limit" />
                    <input type="text" name="upper_limit" id="upper_limit" class="form-control req floatOnly" value="" placeholder="Upper Limit" />
                </div>
            </div>
            <div class="col-md-4 form-group">
                <label for="measure_tech">Measurement Tech.</label>
                <select name="measure_tech" class="from-control single-select req" >
                    <option value="">Select Measure. Tech.</option>
                    <?php
                        foreach($instruments as $row):
                            echo '<option value="'.$row->measurement_technique.'">'.$row->measurement_technique.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="sample">Sample</label>
                <input type="text" class="form-control" name="sample">
            </div>
            <div class="col-md-4">
                <label for="control_method">Control Method</label>
                <input type="text" class="form-control" name="control_method">
            </div>
            <div class="col-md-4">
                <label for="responsibility">Responsibility</label>
                <div class="input-group">
                    <input type="text" class="form-control" name="responsibility"><button type="button" class="btn waves-effect waves-light btn-outline-success btn-save ml-2" onclick="savePreInspection('getPreInspection','savePreInspectionParam');"><i class="fa fa-plus"></i> Add</button>
                </div>
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
                        <th>Perameter Type</th>
                        <th>Process</th>
                        <th>Product Char.</th>
                        <th>Process Char.</th>
                        <th>Specification</th>
                        <th>Dimension Range</th>
                        <th>Measurement Tech.</th>
                        <th>Sample</th>
                        <th>Control Method</th>
                        <th>Responsibility</th>
                        <th class="text-center" style="width:10%;">Action</th>
                    </tr>
                </thead>
                <tbody id="inspectionBody">
                    <?php
                    if (!empty($paramData)) :
                        $i = 1;
                        foreach ($paramData as $row) :
                            $parameter =explode(",",$row->param_type);
                            $parameter_type=array();
                            foreach($parameter as $key=>$value){
                                if($value == 1){ $parameter_type[] = 'IIR'; }
                                if($value == 2){ $parameter_type[] = 'IPR'; } 
                                if($value == 3){ $parameter_type[] = 'FIR'; } 
                            }
                            
                            
                            echo '<tr>
                                            <td>' . $i++ . '</td>
                                            <td>' . (implode(',',$parameter_type)) . '</td>
                                            <td>' . $row->process_name . '</td>
                                            <td>' . $row->product_char . '</td>
                                            <td>' . $row->process_char . '</td>
                                            <td>' . $row->specification . '</td>
                                            <td>' . $row->lower_limit.'-'.$row->upper_limit . '</td>
                                            <td>' . $row->measure_tech . '</td>
                                            <td>' . $row->sample . '</td>
                                            <td>' . $row->control_method . '</td>
                                            <td>' . $row->responsibility . '</td>
                                            <td class="text-center">
                                                <button type="button" onclick="trashPreInspection(' . $row->id . ',' . $row->item_id . ');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="ti-trash"></i></button>
                                            </td>
                                        </tr>';
                        endforeach;
                    else :
                        echo '<tr><td colspan="12" style="text-align:center;">No Data Found</td></tr>';
                    endif;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function savePreInspection(formId, fnsave) {
        // var fd = $('#'+formId).serialize();
        setPlaceHolder();
        if (fnsave == "" || fnsave == null) {
            fnsave = "save";
        }
        var form = $('#' + formId)[0];
        var fd = new FormData(form);
        $.ajax({
            url: base_url + controller + '/' + fnsave,
            data: fd,
            type: "POST",
            processData: false,
            contentType: false,
            dataType: "json",
        }).done(function(data) {
            if (data.status === 0) {
                $(".error").html("");
                $.each(data.message, function(key, value) {
                    $("." + key).html(value);
                });
            } else if (data.status == 1) {
                initTable(0); $('#'+formId)[0].reset();//$(".modal").modal('hide');   
                toastr.success(data.message, 'Success', {
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp",
                    "closeButton": true,
                    positionClass: 'toastr toast-bottom-center',
                    containerId: 'toast-bottom-center',
                    "progressBar": true
                });
                $("#inspectionBody").html(data.tbodyData);
                $("#parameter").val("");
                $("#specification").val("");
                $("#lower_limit").val("");
                $("#upper_limit").val("");
                $("#measure_tech").val("");
            } else {
                initTable(0);
                $('#' + formId)[0].reset();
                $(".modal").modal('hide');
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

    function trashPreInspection(id, item_id, name = 'Record') {
        var send_data = {
            id: id,
            item_id: item_id
        };
        $.confirm({
            title: 'Confirm!',
            content: 'Are you sure want to delete this ' + name + '?',
            type: 'red',
            buttons: {
                ok: {
                    text: "ok!",
                    btnClass: 'btn waves-effect waves-light btn-outline-success',
                    keys: ['enter'],
                    action: function() {
                        $.ajax({
                            url: base_url + controller + '/deletePreInspection',
                            data: send_data,
                            type: "POST",
                            dataType: "json",
                            success: function(data) {
                                if (data.status == 0) {
                                    toastr.error(data.message, 'Sorry...!', {
                                        "showMethod": "slideDown",
                                        "hideMethod": "slideUp",
                                        "closeButton": true,
                                        positionClass: 'toastr toast-bottom-center',
                                        containerId: 'toast-bottom-center',
                                        "progressBar": true
                                    });
                                } else {
                                    initTable(0);
                                    toastr.success(data.message, 'Success', {
                                        "showMethod": "slideDown",
                                        "hideMethod": "slideUp",
                                        "closeButton": true,
                                        positionClass: 'toastr toast-bottom-center',
                                        containerId: 'toast-bottom-center',
                                        "progressBar": true
                                    });
                                    $("#inspectionBody").html(data.tbodyData);
                                }
                            }
                        });
                    }
                },
                cancel: {
                    btnClass: 'btn waves-effect waves-light btn-outline-secondary',
                    action: function() {

                    }
                }
            }
        });
    }
</script>