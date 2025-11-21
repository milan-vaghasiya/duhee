<div class="col-md-12">
    <form id="control_plan">
        <div class="row">
            <input type="hidden" name="id" id="id"  value="" />
            <input type="hidden" name="ref_id" id="ref_id" class="ref_id" value="<?= $ref_id ?>" />
            <input type="hidden" name="fmea_type" id="fmea_type" value="3" />
            <div class="col-md-3 form-group">
                <label for="instrument_code">Measurement Tech.</label>
                <input type="text" id="instrument_code" name="instrument_code"  class="form-control  req">
                <div class="error instrument_code"></div>
            </div>
            <div class="col-md-3 form-group">
                <label for="process_prevention">Control Method</label>
                <select name="process_prevention" id="process_prevention" class="form-control req single-select">
                    <option value="">Select Control Method</option>
                    <?php
                    if (!empty($controlMethod)) {
                        foreach ($controlMethod as $row) {
                    ?><option value="<?= $row->control_method ?>" data-resp='<?= $row->resp_short_name ?>'><?= $row->control_method ?></option><?php
                                                                                                                                    }
                                                                                                                                }
                                                                                                                                        ?>
                </select>
                <div class="error process_prevention"></div>
            </div>
            <div class="col-md-3">
                <label for="process_detection">Responsibility</label>
                <input type="text" class="form-control req" id="process_detection" name="process_detection">
            </div>
            <div class="col-md-3">
                <label for="sev">Size</label>
                <input type="text" class="form-control floatOnly" name="sev" id="sev">
            </div>
            <div class="col-md-3">
                <label for="potential_cause">Freq.</label>
                <div class="input-group">
                    <input type="text" class="form-control" name="potential_cause" id="potential_cause">
                    <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save ml-2" onclick="saveControlMethod('control_plan');"><i class="fa fa-plus"></i> Add</button>
                </div>
            </div>

        </div>
    </form>
    <hr>
    <div class="row">
        <div class="table-responsive">
            <table id="prevTble" class="table table-bordered align-items-center">
                <thead class="thead-info">
                    <tr>
                        <th style="width:5%;">#</th>
                        <th>Measurement Tech.</th>
                        <th>Control Method</th>
                        <th>Responsibility</th>
                        <th>Size</th>
                        <th>Freq.</th>
                        <th class="text-center" style="width:10%;">Action</th>
                    </tr>
                </thead>
                <tbody id="prevBody">
                    <?= $tbody ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $(document).on('change', '#process_prevention', function() {
            var responsibility = $(this).find(":selected").data('resp');
            $("#process_detection").val(responsibility);
            ssTable.state.clear();
            initTable(0);
        });
    });

    function saveControlMethod(formId) {
        // var fd = $('#'+formId).serialize();
        setPlaceHolder();

        var form = $('#' + formId)[0];
        var fd = new FormData(form);
        $.ajax({
            url: base_url + controller + '/saveControlMethod',
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
                initTable(0);
                // $('#' + formId)[0].reset(); //$(".modal").modal('hide');   
                toastr.success(data.message, 'Success', {
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp",
                    "closeButton": true,
                    positionClass: 'toastr toast-bottom-center',
                    containerId: 'toast-bottom-center',
                    "progressBar": true
                });
                $("#prevBody").html(data.html);
                $("#id").val('');
                $("#potential_cause").val('');
                $("#sev").val('');
                $("#process_prevention").val('');
                $("#process_detection").val('');
                $("#potential_effect").val('');
                $("#detec").val('');
                $(".single-select").comboSelect();
                $("#instrument_code").val('');
                $("#insCodeMulSelect").val('');
                reInitMultiSelect();
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

    function deleteControlMethod(id,ref_id,name = 'Record') {
        var send_data = { id: id,ref_id:ref_id  };
        $.confirm({
            title: 'Confirm!',
            content: 'Are you sure want to Remove this Record? <br> All related records will be removed and will not be recovered',
            type: 'red',
            buttons: {
                ok: {
                    text: "ok!",
                    btnClass: 'btn waves-effect waves-light btn-outline-success',
                    keys: ['enter'],
                    action: function() {
                        $.ajax({
                            url: base_url + controller + '/deleteControlMethod',
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
                                    $("#prevBody").html(data.html);
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

    function editControlMethod(id,button){
        if(id){
            $.ajax({
                url: base_url + controller + '/editControlMethod',
                data: {id:id},
                type: "POST",
                dataType:'json',
                success:function(data){
                    $("#id").val(data.id);
                    $("#potential_cause").val(data.potential_cause);
                    $("#sev").val(data.sev);
                    $("#process_prevention").val(data.process_prevention);
                    $("#process_detection").val(data.process_detection);
                    $("#potential_effect").val(data.potential_effect);
                    $("#insCodeMulSelect").html("");
                    $("#insCodeMulSelect").html(data.insCodeOptions);
                    reInitMultiSelect();
                    $("#instrument_code").val(data.instrument_code);
                    $("#detec").val(data.detec);
                    $(".single-select").comboSelect();

                    var row = $(button).closest("TR");  
                    var table = $("#prevTble")[0];
                    table.deleteRow(row[0].rowIndex);
                    $('#prevBody tbody tr td:nth-child(1)').each(function(idx, ele) {
                        ele.textContent = idx + 1;
                    });
                }
            });
        }
    }
</script>