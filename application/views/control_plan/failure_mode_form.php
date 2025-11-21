<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4>
                            <u>FMEA Failure Mode</u>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="col-md-12">
                            <form id="fmeaForm">
                                <div class="row">
                                    <input type="hidden" name="id" value="<?= !empty($dataRow->id) ? $dataRow->id : '' ?>">
                                    <input type="hidden" name="fmea_id" value="<?= !empty($dataRow->fmea_id) ? $dataRow->fmea_id : $trans_id ?>">
                                    <input type="hidden" name="pfc_id" value="<?= !empty($fmeaData->pfc_id) ? $fmeaData->pfc_id : ''?>">
                                    <input type="hidden" name="item_id" value="<?= !empty($fmeaData->item_id) ? $fmeaData->item_id : ''?>">
                                    <input type="hidden" name="edit_mode" value="<?=!empty($editMode) ? $editMode : ''?>">
                                    <table class="table jp-table bg-light-info">
                                        <tr>
                                            <th>FMEA Number</th>
                                            <td><?=$fmeaData->trans_number?></td>
                                            <th>Parameter</th>
                                            <td><?=$fmeaData->parameter?></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 form-group">
                                        <button type="button" class="btn btn-outline-success btn-save float-right mt-30" onclick="addRow()"><i class="fa fa-plus"></i> Add</button>
                                    </div>
                                    <div class="error general_error"></div>
                                    <div class="table-responsive" style="height:50vh;overflow-y:scroll;">
                                        <table id="fmeatbl" class="table table-bordered ">
                                            <thead class="thead-info">
                                                <tr>
                                                    <th style="width:3%;">#</th>
                                                    <th style="width:15%;">Potential Failure Mode</th>
                                                    <th style="width:20%;">Customer</th>
                                                    <th style="width:5%;">Cst. Sev</th>
                                                    <th style="width:20%;">Manufacture</th>
                                                    <th style="width:5%;">Mfg. Sev</th>
                                                    <th style="width:22%;">Detection</th>
                                                    <th style="width:5%;">Detec</th>
                                                    <th class="text-center" style="width:5%;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="fmeaTbody">
                                                <tr id="noData">
                                                    <td colspan="10" align="center">No data available in table</td>
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
                            <button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="saveFailureMode('fmeaForm','saveFailureMode');"><i class="fa fa-check"></i> Save</button>
                            <a href="<?= base_url($headData->controller . '/fmeaFailView/'.$trans_id) ?>" class="btn waves-effect waves-light btn-outline-secondary float-right save-form" style="margin-right:10px;"><i class="fa fa-times"></i> Cancel</a>
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

       

        $(document).on('change', '#item_id', function() {
            var item_id = $(this).val();
            var itemData = $(this).find(":selected").data('row');
            var process_no = ($("#pfc_id").val())?$("#pfc_id").find(":selected").data('process_no'):'';
            var trans_number = 'FMEA/' + itemData.item_code + '/' + ((itemData.rev_no != null) ? itemData.rev_no : '')+'/'+process_no;
            $("#trans_number").val(trans_number);
            $.ajax({
                url: base_url + controller + '/getItemWisePfcData',
                data: {
                    item_id: item_id
                },
                type: "POST",
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    $("#pfc_id").html(data.options);
                    $("#pfc_id").comboSelect();
                }
            });
        });

        $(document).on('change', '#pfc_id', function() {
            var item_id = $(this).val();
            var itemData = $("#item_id").find(":selected").data('row');
            var process_no = $("#pfc_id").find(":selected").data('process_no');
            var trans_number = 'FMEA/' + itemData.item_code + '/' + ((itemData.rev_no != null) ? itemData.rev_no : '')+'/'+process_no;
            $("#trans_number").val(trans_number);
            $("#process_no").val(process_no);
        });


        $(document).on('change', "#requirement", function() {
            var requirement = $(this).val();
            if (requirement == 1) {
                $('.min_req').show();
                $('.max_req').show();
                $('.other_req').show();
            } else if (requirement == 2) {
                $('.min_req').show();
                $('.max_req').hide();
                $('.other_req').show();
            } else if (requirement == 3) {
                $('.min_req').hide();
                $('.max_req').show();
                $('.other_req').show();
            } else if (requirement == 4) {
                $('.min_req').hide();
                $('.max_req').hide();
                $('.other_req').show();
            }
        });
    });


    function saveFailureMode(formId, fnsave) {
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
        var failureModeIP = $("<input/>", {
            type: "text",
            name: "failure_mode[]",
            value: data.failure_mode,
            class: "form-control"
        });
        cell = $(row.insertCell(-1));
        cell.html(failureModeIP);
        cell.append(idIP);
        cell.append("<div class='error failure_mode"+countRow+"'></div>");

        var customerIP = $("<input/>", {
            type: "text",
            name: "customer[]",
            value: data.customer,
            class: "form-control"
        });
        cell = $(row.insertCell(-1));
        cell.html(customerIP);

        var customerSevIP = $("<input/>", {
            type: "text",
            name: "cust_sev[]",
            value: data.cust_sev,
            class: "form-control floatOnly"
        });
        cell = $(row.insertCell(-1));
        cell.html(customerSevIP);
        cell.append("<div class='error cust_sev"+countRow+"'></div>");

        var mfgIP = $("<input/>", {
            type: "text",
            name: "manufacturer[]",
            value: data.manufacturer,
            class: "form-control"
        });
        cell = $(row.insertCell(-1));
        cell.html(mfgIP);

        var mfgSaveIP = $("<input/>", {
            type: "text",
            name: "mfg_sev[]",
            value: data.mfg_sev,
            class: "form-control floatOnly"
        });
        cell = $(row.insertCell(-1));
        cell.html(mfgSaveIP);
        cell.append("<div class='error mfg_sev"+countRow+"'></div>");


        var processDetectionIP = $("<input/>", {
            type: "text",
            name: "process_detection[]",
            value: data.process_detection,
            class: "form-control"
        });
        cell = $(row.insertCell(-1));
        cell.html(processDetectionIP);

        var detecIP = $("<input/>", {
            type: "text",
            name: "detec[]",
            value: data.detec,
            class: "form-control floatOnly",
        });
        cell = $(row.insertCell(-1));
        cell.html(detecIP);

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