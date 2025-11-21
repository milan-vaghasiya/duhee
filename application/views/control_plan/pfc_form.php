<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4>
                            <u>PFC</u>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="col-md-12">
                            <form id="pfcForm">
                                <div class="row">
                                    <input type="hidden" name="id" value="<?= (!empty($dataRow->id) && empty($revision)) ? $dataRow->id : '' ?>">
                                    <input type="hidden" name="ref_id" value="<?= (!empty($revision)) ? $dataRow->id : '' ?>">
                                    <input type="hidden" id="item_id" name="item_id" value="<?= !empty($dataRow->item_id) ? $dataRow->item_id : $item_id ?>">
                                    <div class="col-md-2 form-group">
                                        <label for="trans_number">PFC No.</label>
                                        <input type="text" name="trans_number" id="trans_number" class="form-control req" value="<?= !empty($dataRow->trans_number) ? $dataRow->trans_number : '' ?>" readOnly>
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <label for="trans_number">Rev. No.</label>
                                        <input type="text" name="app_rev_no" class="form-control" value="<?= (!empty($revision)) ? $dataRow->app_rev_no + 1 : (!empty($dataRow->app_rev_no) ? $dataRow->app_rev_no : 0)  ?>" readOnly>

                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="app_rev_date">Date</label>
                                        <input type="date" name="app_rev_date" id="app_rev_date" class="form-control req" value="<?= !empty($dataRow->app_rev_date) ? $dataRow->app_rev_date : date("Y-m-d") ?>">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="core_team">Core Team</label>
                                        <select id="employee_id" class="form-control jp_multiselect" data-input_id="core_team" multiple="multiple">
                                            <option value="">Select Core Team</option>
                                            <?php
                                            if (!empty($empList)) {
                                                foreach ($empList as $row) {
                                                    $selected = (!empty($dataRow->core_team) && in_array($row->id, explode(",", $dataRow->core_team))) ? 'selected' : ''

                                            ?><option value="<?= $row->id ?>" <?= $selected ?>><?= !empty($row->emp_alias) ? '[' . $row->emp_alias . '] ' : '' . $row->emp_name ?></option>
                                            <?php }
                                            } ?>
                                        </select>
                                        <input type="hidden" name="core_team" id="core_team" value="<?= !empty($dataRow->core_team) ? $dataRow->core_team : '' ?>">
                                        <div class="error core_team"></div>
                                    </div>

                                    <div class="col-md-2 form-group">
                                        <label for="cust_rev_no">Cust. Rev. No.</label>
                                        <input type="text" name="cust_rev_no" id="cust_rev_no" class="form-control " value="<?= !empty($dataRow->cust_rev_no) ? $dataRow->cust_rev_no : '' ?>">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="core_team">Approved Supplier</label>
                                        <select id="supplier" class="form-control jp_multiselect" data-input_id="supplier_id" multiple="multiple">
                                            <option value="">Select Supplier</option>
                                            <?php
                                            if (!empty($supplierList)) {
                                                foreach ($supplierList as $row) {
                                                    $selected = (!empty($dataRow->supplier_id) && in_array($row->id, explode(",", $dataRow->supplier_id))) ? 'selected' : ''

                                            ?><option value="<?= $row->id ?>" <?= $selected ?>><?= (!empty($row->party_code) ? '[' . $row->party_code . '] ' : '') . $row->party_name ?></option>
                                            <?php }
                                            } ?>
                                        </select>
                                        <input type="hidden" name="supplier_id" id="supplier_id" value="<?= !empty($dataRow->supplier_id) ? $dataRow->supplier_id : '' ?>">
                                        <div class="error supplier_id"></div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-12 form-group">
                                        <button type="button" class="btn btn-outline-success btn-save float-right" onclick="addRow()"><i class="fa fa-plus"></i> Add</button>
                                    </div>
                                    <div class="error general_error"></div>
                                    <div class="table-responsive" style="height:50vh;overflow-y:scroll;">
                                        <table id="pfctbl" class="table table-bordered " style="font-size: 11px !important;">
                                            <thead class="thead-info">
                                                <tr>
                                                    <th style="width:5px;">#</th>
                                                    <th >Process No.</th>
                                                    <th>Machine Type</th>
                                                    <th >Process Description</th>
                                                    <th > Symbol 1</th>
                                                    <th >Symbol 2</th>
                                                    <th >Symbol 3</th>
                                                    <th >Special Char. Class</th>
                                                    <th >Output</th>
                                                    <th >Reaction Plan</th>
                                                    <th >Location</th>
                                                    <th >Vendor</th>
                                                    <th >Jig Fixture No.</th>
                                                    <th>Stage type</th>
                                                    <th class="text-center" style="width:13px;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="pfcTbody">

                                                <tr id="noData">
                                                    <td colspan="15" align="center">No data available in table</td>
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
                            <button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="savePfc('pfcForm','savePfc');"><i class="fa fa-check"></i> Save</button>
                            <a href="<?= base_url($headData->controller . '/pfcList/' . (!empty($dataRow->item_id) ? $dataRow->item_id : $item_id) . '') ?>" class="btn waves-effect waves-light btn-outline-secondary float-right save-form" style="margin-right:10px;"><i class="fa fa-times"></i> Cancel</a>
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

        // $(".symbol-select").select2({
        //     templateResult: formatSymbol
        // });

        $(document).on('change', '#item_id', function() {
            var itemData = $(this).find(":selected").data('row');
            console.log(itemData);
            var pfc_number = 'PFC/' + itemData.item_code + '/' + ((itemData.app_rev_no != null) ? itemData.app_rev_no : '') + '/' + ((itemData.rev_no != null) ? itemData.rev_no : '');
            $("#trans_number").val(pfc_number);
        });

        $(document).on('change', '.location_id', function() {
            var countRow = $(this).find(":selected").data('count_row');
            console.log(countRow);
            var location = $(this).val();
            if (location == 1) {
                console.log("vendor_select" + countRow);
                $("#vendor_select" + countRow).hide();
            } else {
                $("#vendor_select" + countRow).show();
            }

            initMultiSelect();
        });

    });


    function savePfc(formId, fnsave) {
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
                window.location = data.url;

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
        $('table#pfctbl tr#noData').remove()
        //Get the reference of the Table's TBODY element.
        var tblName = "pfctbl";

        var tBody = $("#" + tblName + " > TBODY")[0];

        //Add Row.
        row = tBody.insertRow(-1);

        //Add index cell
        var countRow = $('#' + tblName + ' tbody tr:last').index() + 1;
        var cell = $(row.insertCell(-1));
        cell.html(countRow);

        var idIP = $("<input/>", {
            type: "hidden",
            name: "trans_id[]",
            value: data.id
        });
        var processNoIP = $("<input/>", {
            type: "text",
            name: "process_no[]",
            value: data.process_no,
            class: "form-control"
        });
        cell = $(row.insertCell(-1));
        cell.html(processNoIP);
        cell.append(idIP);


        var mcTypeIP = $("<input/>", {
            type: "text",
            name: "machine_type[]",
            value: data.machine_type,
            class: "form-control"
        });
        // var machineTypes = <?php echo json_encode($machineTypes); ?>;
        // mcTypeIP.append("<option value=''>Select Machine Type</option>");
        // for (var i = 0; i < machineTypes.length; i++) {
        //     var selected = (data.machine_type == machineTypes[i].id) ? true : false
        //     $('<option />', {
        //         value: machineTypes[i].id,
        //         text: machineTypes[i].typeof_machine,
        //         selected: selected
        //     }).appendTo(mcTypeIP);
        // }
        cell = $(row.insertCell(-1));
        cell.html(mcTypeIP);

        var processDescrIP = $("<input/>", {
            type: "text",
            name: "parameter[]",
            value: data.parameter,
            class: "form-control"
        });
        cell = $(row.insertCell(-1));
        cell.html(processDescrIP);



        var symbol1IP = $("<select/>", {
            type: "text",
            name: "symbol_1[]",
            class: "form-control symbol-select"
        });

        var symbol2IP = $("<select/>", {
            type: "text",
            name: "symbol_2[]",
            class: "form-control symbol-select"
        });
        var symbol3IP = $("<select/>", {
            type: "text",
            name: "symbol_3[]",
            class: "form-control symbol-select"
        });
        var symbolArray = <?php echo json_encode($symbolArray); ?>;

        $.each(symbolArray, function(key, value) {
            if (key == '') {
                symbol1IP.append('<option value="">Select Symbol 1</option>');
                symbol2IP.append('<option value="">Select Symbol 2</option>');
                symbol3IP.append('<option value="">Select Symbol 3</option>');
            } else {
                selectedOpt1 = (data.symbol_1 == key) ? 'selected' : '';
                selectedOpt2 = (data.symbol_2 == key) ? 'selected' : '';
                selectedOpt3 = (data.symbol_3 == key) ? 'selected' : '';

                var options1 = '<option value="' + key + '" ' + selectedOpt1 + '  data-img_path="' + base_url + 'assets/images/symbols/' + key + '.png")">' + value + '</option>';
                var options2 = '<option value="' + key + '" ' + selectedOpt2 + '  data-img_path="' + base_url + 'assets/images/symbols/' + key + '.png")">' + value + '</option>';
                var options3 = '<option value="' + key + '" ' + selectedOpt3 + '  data-img_path="' + base_url + 'assets/images/symbols/' + key + '.png")">' + value + '</option>';

                symbol1IP.append(options1);
                symbol2IP.append(options2);
                symbol3IP.append(options3);
            }
        });
        cell = $(row.insertCell(-1));
        cell.html(symbol1IP);

        cell = $(row.insertCell(-1));
        cell.html(symbol2IP);

        cell = $(row.insertCell(-1));
        cell.html(symbol3IP);

        if ($('select').data('select2')) {
            $(".symbol-select").select2("destroy").select2();
        }
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

        var outputIP = $("<input/>", {
            type: "text",
            name: "output_operation[]",
            value: data.output_operation,
            class: "form-control"
        });
        cell = $(row.insertCell(-1));
        cell.html(outputIP);

        var reactionPlanIP = $("<select/>", {
            type: "text",
            name: "reaction_plan[]",
            class: "form-control single-select",

        });
        var reactionPlan = <?php echo json_encode($reactionPlan); ?>;
        reactionPlanIP.append("<option value=''>Select Reaction Plan</option>");
        for (var i = 0; i < reactionPlan.length; i++) {
            var selected = (data.reaction_plan && data.reaction_plan == reactionPlan[i].title) ? true : false
            $('<option />', {
                value: reactionPlan[i].title,
                text: reactionPlan[i].title,
                selected: selected
            }).appendTo(reactionPlanIP);
        }
        cell = $(row.insertCell(-1));
        cell.html(reactionPlanIP);


        var locationIP = $("<select/>", {
            type: "text",
            name: "location[]",
            class: "form-control location_id"
        }).attr("data-countRow", countRow);
        locationIP.append('<option value="1" ' + ((data.location == 1) ? 'selected' : '') + ' data-count_row="' + countRow + '">Inhouse</option><option value="2" ' + ((data.location == 2) ? 'selected' : '') + '  data-count_row="' + countRow + '">Outsource</option>');


        cell = $(row.insertCell(-1));
        cell.html(locationIP);

        var vendorIP = $("<select/>", {
            id: "vendor_select" + countRow,
            class: "form-control jp_multiselect",
            multiple: 'multiple'
        }).attr('data-input_id', 'vendor_id' + countRow);
        var vendorList = <?php echo json_encode($vendorList); ?>;
        var vndr = [];
        if (data.vendor_id) {
            vndr = data.vendor_id.split(",");
        }
        for (var i = 0; i < vendorList.length; i++) {
            selected = (jQuery.inArray(vendorList[i].id, vndr) != -1) ? 'selected' : '';
            vendorIP.append('<option value="' + vendorList[i].id + '"  ' + selected + '>' + vendorList[i].party_name + '</option>');
        }
        var vendorIdIP = $("<input/>", {
            type: "hidden",
            name: "vendor_id[]",
            id: 'vendor_id' + countRow,
            value: data.vendor_id,
            class: 'vendor'

        });
        cell = $(row.insertCell(-1));
        cell.html(vendorIP);
        cell.append(vendorIdIP);

        var jigFixIP = $("<input/>", {
            type: "text",
            name: "jig_fixture_no[]",
            value: data.jig_fixture_no,
            class: "form-control"
        });
        cell = $(row.insertCell(-1));
        cell.html(jigFixIP);
        
        var stageIP = $("<select/>", {
            type: "text",
            name: "stage_type[]",
            class: "form-control"
        });
        stageIP.append('<option value="1" ' + ((data.stage_type == 1) ? 'selected' : '') + '">IIR</option><option value="2" ' + ((data.stage_type == 2) ? 'selected' : '') + ' >Production</option><option value="3" ' + ((data.stage_type == 3) ? 'selected' : '') + ' >FIR</option><option value="4" ' + ((data.stage_type == 4) ? 'selected' : '') + '  >PDI</option><option value="5" ' + ((data.stage_type == 5) ? 'selected' : '') + ' >Packing</option><option value="6" ' + ((data.stage_type == 6) ? 'selected' : '') + '>Dispatch</option><option value="7" ' + ((data.stage_type == 7) ? 'selected' : '') + '>RQC</option><option value="8" ' + ((data.stage_type == 8) ? 'selected' : '') + '>PRE FIR</option>');
        cell = $(row.insertCell(-1));
        cell.html(stageIP);

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
                        var table = $("#pfctbl")[0];
                        table.deleteRow(row[0].rowIndex);
                        $('#pfctbl tbody tr td:nth-child(1)').each(function(idx, ele) {
                            ele.textContent = idx + 1;
                        });

                        $('#pfctbl tbody tr td:nth-child(12) select').each(function(idx, ele) {
                            let newIdx = parseFloat(idx) + 1;

                            $(this).attr('id', 'vendor_select' + newIdx);
                            $(this).attr('data-input_id', 'vendor_id' + newIdx);
                        });
                        $('#pfctbl tbody tr td:nth-child(12)  .vendor').each(function(idx, ele) {

                            let newIdx = parseFloat(idx) + 1;
                            $(this).attr('id', 'vendor_id' + newIdx);
                        });

                        var countTR = $('#pfctbl tbody tr:last').index() + 1;
                        if (countTR == 0) {
                            $("#pfcTbody").html('<tr id="noData"><td colspan="24" align="center">No data available in table</td></tr>');
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
        $row->id = (empty($revision)) ? $row->id : '';
        echo "<script>addRow(" . json_encode($row) . ");</script>";
    }
}
?>