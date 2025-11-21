<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4>
                            <u>CP Process Diamention</u>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="col-md-12">
                            <form id="dimenstionForm">
                                <div class="row">
                                    <input type="hidden" name="trans_main_id" value="<?= !empty($cpData->id) ? $cpData->id : '' ?>">
                                    <input type="hidden" name="item_id" value="<?= !empty($cpData->item_id) ? $cpData->item_id : $item_id ?>">
                                    <input type="hidden" name="pfc_id" value="<?= !empty($cpData->ref_id) ? $cpData->ref_id :'' ?>">
                                    <input type="hidden" name="edit_mode" value="<?= !empty($editMode) ? $editMode :'' ?>">
                                    <div class="col-md-4 form-group">
                                        <label for="trans_number">FMEA No.</label>
                                        <input type="text" name="trans_number" id="trans_number" class="form-control req" value="<?= !empty($dataRow->trans_number) ? $dataRow->trans_number :(!empty($cpData->trans_number)?$cpData->trans_number:'') ?>" readOnly>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <button type="button" class="btn btn-outline-success btn-save float-right mt-30" onclick="addRow()"><i class="fa fa-plus"></i> Add</button>
                                    </div>
                                    <div class="error general_error"></div>
                                    <div class="table-responsive" style="height:50vh;overflow-y:scroll;">
                                        <table id="fmeatbl" class="table table-bordered ">
                                            <thead class="thead-info">
                                                <tr>
                                                    <th style="width:5px;">#</th>
                                                    <th style="width:5px;">Parameters Type</th>
                                                    <th style="width:5px;">Parameters</th>
                                                    <th style="width:150px;">Requirement</th>
                                                    <th style="width:50px;">Min.</th>
                                                    <th style="width:150px;">Max</th>
                                                    <th style="width:50px;">Text</th>
                                                    <th style="width:50px;">Class</th>
                                                    <th style="width:50px;">Instrument Range</th>
                                                    <th style="width:50px;">Least Count</th>
                                                    <th class="text-center" style="width:13px;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="fmeaTbody">
                                                <tr id="noData">
                                                    <td colspan="8" align="center">No data available in table</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="col-md-12">
                            <button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="saveCPDimension('dimenstionForm','saveCPDimension');"><i class="fa fa-check"></i> Save</button>
                            <a href="<?= base_url($headData->controller . '/cpDiamentionList/' . $cpData->id ) ?>" class="btn waves-effect waves-light btn-outline-secondary float-right save-form" style="margin-right:10px;"><i class="fa fa-times"></i> Cancel</a>
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

        $(document).on('change', ".requirement", function() {
            var countRow = $(this).find(":selected").data('count_row');
            var requirement = $(this).val();
            if (requirement == 1) {
                $('#min_req' + countRow).show();
                $('#max_req' + countRow).show();
                $('#other_req' + countRow).show();
            } else if (requirement == 2) {
                $('#min_req' + countRow).show();
                $('#max_req' + countRow).hide();
                $('#other_req' + countRow).show();
            } else if (requirement == 3) {
                $('#min_req' + countRow).hide();
                $('#max_req' + countRow).show();
                $('#other_req' + countRow).show();
            } else if (requirement == 4) {
                $('#min_req' + countRow).hide();
                $('#max_req' + countRow).hide();
                $('#other_req' + countRow).show();
            }
        });
    });


    function saveCPDimension(formId, fnsave) {
        // var fd = $('#'+formId).serialize();

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
                $(".errsor").html("");
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
                window.location = data.url;
                $("#parameter").val("");
                $("#requirement").val("");
                $("#requirement").comboSelect();
                $("#min_req").val("");
                $("#max_req").val("");
                $("#other_req").val("");

                $("#fmeaTbody").html("");

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

    function addRow(data = {}) {
        $('table#fmeatbl tr#noData').remove()
        //Get the reference of the Table's TBODY element.
        var tblName = "fmeatbl";

        var tBody = $("#" + tblName + " > TBODY")[0];

        //Add Row.
        row = tBody.insertRow(-1);

        //Add index cell
        var countRow = $('#' + tblName + ' tbody tr:last').index() + 1;
        var cell = $(row.insertCell(-1));
        cell.html(countRow);
        cell.attr("style", "width:5%;");

        var idIP = $("<input/>", {
            type: "hidden",
            name: "trans_id[]",
            value: data.id
        });
        var options = '<option value="1" '+((data.parameter_type == 1) ? 'selected' : '')+' data-count_row = "' + countRow + '" >Product</option><option value="2" '+((data.parameter_type == 2) ? 'selected' : '')+' data-count_row = "' + countRow + '">Process</option>';
        var parameterTypeIP = $("<select/>", {
            type: "text",
            name: "parameter_type[]",
            value: data.parameter_type,
            class: "form-control parameter_type"
        }).append(options);
        cell = $(row.insertCell(-1));
        cell.append(parameterTypeIP);

        cell.append("<div class='error parameter_type" + countRow + "'></div>");
       
        var parameterIP = $("<input/>", {
            type: "text",
            name: "parameter[]",
            value: data.parameter,
            class: "form-control"
        });
        cell = $(row.insertCell(-1));
        cell.html(parameterIP);
        cell.append(idIP);
        cell.append("<div class='error parameter" + countRow + "'></div>");

        var options = '<option value="">Select Requirement</option><option value="1" '+((data.requirement == 1) ? 'selected' : '')+' data-count_row = "' + countRow + '" >Range</option><option value="2" '+((data.requirement == 2) ? 'selected' : '')+' data-count_row = "' + countRow + '">Minimum</option><option value="3" '+((data.requirement == 3) ? 'selected' : '')+' data-count_row = "' + countRow + '">Maximum</option><option value="4" '+((data.requirement == 4) ? 'selected' : '')+' data-count_row = "' + countRow + '">Other</option>';
        var requirementIP = $("<select/>", {
            type: "text",
            name: "requirement[]",
            value: data.requirement,
            class: "form-control requirement"
        }).append(options);
        cell = $(row.insertCell(-1));
        cell.html(requirementIP);
        cell.append("<div class='error requirement" + countRow + "'></div>");

        var min_reqIP = $("<input/>", {
            type: "text",
            name: "min_req[]",
            value: data.min_req,
            id: "min_req"+countRow,
            class: "form-control floatOnly min_req min_req" + countRow
        });
        cell = $(row.insertCell(-1));
        cell.html(min_reqIP);
        cell.append("<div class='error minReq" + countRow + "'></div>");

        var max_reqIP = $("<input/>", {
            type: "text",
            name: "max_req[]",
            value: data.max_req,
            id: "max_req"+countRow,
            class: "form-control max_req max_req" + countRow
        });
        cell = $(row.insertCell(-1));
        cell.html(max_reqIP);

        var other_reqIP = $("<input/>", {
            type: "text",
            name: "other_req[]",
            value: data.other_req,
            id: "other_req"+countRow,
            class: "form-control other_req other_req" + countRow
        });
        cell = $(row.insertCell(-1));
        cell.html(other_reqIP);
        cell.append("<div class='error other_req" + countRow + "'></div>");

        var classIP = $("<select/>", {
            type: "text",
            name: "char_class[]",
            class: "form-control symbol-select"
        });
        var classArray = <?php echo json_encode($classArray); ?>;
        $.each(classArray, function(key, value) {
            if (key == '') {
                classIP.append('<option value="">Select Class</option>');
            } else {
                selected = (data.char_class == key) ? 'selected' : '';
                var options = '<option value="' + key + '" data-img_path="' + base_url + '/assets/images/symbols/' + key + '.png")" ' + selected + '>' + value + '</option>';
                classIP.append(options);
            }
        });

        cell = $(row.insertCell(-1));
        cell.html(classIP);


        var insRangeIP = $("<input/>", {
            type: "text",
            name: "instrument_range[]",
            value: data.instrument_range,
            class: "form-control"
        });
        cell = $(row.insertCell(-1));
        cell.html(insRangeIP);
        cell.append("<div class='error instrument_range" + countRow + "'></div>");

        var leastCountIP = $("<input/>", {
            type: "text",
            name: "least_count[]",
            value: data.least_count,
            class: "form-control"
        });
        cell = $(row.insertCell(-1));
        cell.html(leastCountIP);
        cell.append("<div class='error least_count" + countRow + "'></div>");

        //Add Button cell.
        cell = $(row.insertCell(-1));
        var btnRemove = $('<button sy><i class="ti-trash"></i></button>');
        btnRemove.attr("type", "button");
        btnRemove.attr("onclick", "Remove(this);");
        btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");
        cell.append(btnRemove);

        // cell.attr("class", "text-center");
        // cell.attr("style", "width:5%;");

        setTimeout(function() {
            $('.symbol-select').select2({
                templateResult: formatSymbol
            })
        }, 300);
        $(".single-select").comboSelect();


        initMultiSelect();
    }

    function Remove(button) {
        //Determine the reference of the Row using the Button.

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
                        var row = $(button).closest("TR");
                        var table = $("#fmeatbl")[0];
                        table.deleteRow(row[0].rowIndex);
                        $('#fmeatbl tbody tr td:nth-child(1)').each(function(idx, ele) {
                            ele.textContent = idx + 1;
                        });

                        $('#fmeatbl tbody tr td:nth-child(1) select').each(function(idx, ele) {
                            let newIdx = parseFloat(idx) + 1;

                            $(this).attr('id', 'vendor_select' + newIdx);
                            $(this).attr('data-input_id', 'vendor_id' + newIdx);
                        });
                        $('#fmeatbl tbody tr td:nth-child(3)  .requirement').each(function(idx, ele) {
                            let newIdx = parseFloat(idx) + 1;
                            $(this).attr('id', 'requirement' + newIdx);
                        });
                        $('#fmeatbl tbody tr td:nth-child(4)  .min_req').each(function(idx, ele) {

                            let newIdx = parseFloat(idx) + 1;
                            $(this).attr('id', 'min_req' + newIdx);
                        });
                        $('#fmeatbl tbody tr td:nth-child(5)  .max_req').each(function(idx, ele) {

                            let newIdx = parseFloat(idx) + 1;
                            $(this).attr('id', 'max_req' + newIdx);
                        });
                        $('#fmeatbl tbody tr td:nth-child(6)  .other_req').each(function(idx, ele) {

                            let newIdx = parseFloat(idx) + 1;
                            $(this).attr('id', 'other_req' + newIdx);
                        });
                        var countTR = $('#fmeatbl tbody tr:last').index() + 1;
                        if (countTR == 0) {
                            $("#fmeaTbody").html('<tr id="noData"><td colspan="10" align="center">No data available in table</td></tr>');
                        }
                    }
                },
                cancel: {
                    btnClass: 'btn waves-effect waves-light btn-outline-secondary',
                    action: function() {

                    }
                }
            }
        });

        
    };
</script>
<?php
if (!empty($transData)) {
    foreach ($transData as $row) {
        echo "<script>addRow(" . json_encode($row) . ");</script>";
    }
}
?>