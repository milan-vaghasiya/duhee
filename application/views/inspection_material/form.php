<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="" />
            <input type="hidden" name="trans_type" id="trans_type" value="1" />

            <div class="col-md-4 form-group">
                <label for="trans_date">Inspection Date</label>
                <input type="date" name="trans_date" class="form-control" id="trans_date" value="<?= date("Y-m-d") ?>">
            </div>

            <div class="col-md-8 form-group">
                <label for="ref_id">Issue No.</label>
                <select name="ref_id" id="ref_id" class="form-control single-select">
                    <option value="">Select Issue No.</option>
                    <?php
                    // if (!empty($issueData)) {
                    //     foreach ($issueData as $row) {
                    // ?>
                    <!--        <option value="<?= $row->id ?>" data-item_id="<?= $row->req_item_id ?>" data-item_name="<?= $row->full_name ?>"> <?= sprintf("ISU%05d", $row->log_no) . ' [ Item : ' . $row->full_name . ' , Issue Qty : ' . $row->req_qty . ']' ?></option> -->
                     <?php
                    //     }
                    // }
                    ?>
                    <?=$issueDataOptions?>
                </select>
                <input type="hidden" id="req_item_id" name="req_item_id">
            </div>
            <div class="col-md-12 inspectData"></div>
        </div>

        <div class="row">

            <div class="col-md-3 form-group">
                <label for="return_status">Inspection Status</label>
                <select id="return_status" class="form-control single-select">
                    <option value="">Select Status</option>
                    <option value="1">Used</option>
                    <option value="2">Fresh</option>
                    <option value="3">Scrap</option>
                    <option value="4">Regrinding</option>
                    <option value="5">Convert to Other Item</option>
                    <option value="6">Missed</option>
                </select>
            </div>
            <div class="col-md-3 form-group otherReason">
                <label for="qty">Qty</label>
                <input type="text" class="form-control numericOnly" id="qty">
            </div>

            <div class="col-md-3 form-group otherItem otherReason" style="display: none;">
                <label for="other_item">Item</label>
                <select id="other_item" class="form-control large-select2 req" data-item_type="3" data-category_id="" data-family_id="" autocomplete="off" data-default_id="<?= (!empty($dataRow->req_item_id)) ? $dataRow->req_item_id : "" ?>" data-default_text="<?= (!empty($dataRow->full_name)) ? $dataRow->full_name : "" ?>" data-url="items/getDynamicItemList">
                    <option value="">Select Item</option>
                </select>
            </div>

            <div class="col-md-3 form-group otherReason">
                <label for="location_id">Location</label>
                <select id="location_id" class="form-control single-select1 model-select2 req">
                    <option value="" data-store_name="">Select Location</option>
                    <?php
                    foreach ($locationData as $lData) :
                        echo '<optgroup label="' . $lData['store_name'] . '">';
                        foreach ($lData['location'] as $row) :
                            $selected = (!empty($dataRow->location_id) && $dataRow->location_id == $row->id) ? 'selected' : '';
                            echo '<option value="' . $row->id . '" data-store_name="' . $lData['store_name'] . '" ' . $selected . '>' . $row->location . ' </option>';
                        endforeach;
                        echo '</optgroup>';
                    endforeach;
                    ?>
                </select>
                <input type="hidden" id="item_id">
                <input type="hidden" id="item_name">
            </div>

            <div class="col-md-3 form-group otherReason">
                <label for="batch_no">Batch No</label>
                <input type="text" id="batch_no" class="form-control"> 
            </div>


            <!-- <div class="missedReason"> -->
            <div class="col-md-3 form-group missedReason">
                <label for="accepted_qty">Accepted Qty</label>
                <input type="text" class="form-control numericOnly" id="accepted_qty">
            </div>
            <div class="col-md-3 form-group missedReason">
                <label for="unaccepted_qty">Unaccepted Qty</label>
                <input type="text" class="form-control numericOnly" id="unaccepted_qty">
            </div>
            <!-- </div> -->

            <div class="col-md-10 form-group reason">
                <label for="reason">Reason</label>
                <input type="text" id="reason" class="form-control">
            </div>
            <div class="col-md-2">
                <label for="">&nbsp;</label>
                <button type="button" class="btn btn-block btn-info" id="addRow">Add</button>
            </div>

        </div>


        <hr style="width:100%;">
        <div class="row">
            <div class="error genral_error"></div>
            <div class="table-responsive">
                <table id="returnTbl" class="table  text-center jp-table">
                    <thead class="lightbg">
                        <tr>
                            <th>#</th>
                            <th>Inspecrtion Status</th>
                            <th>Item</th>
                            <th>Location</th>
                            <th>Batch</th>
                            <th>Qty</th>
                            <th>Reason</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="returnTbody">

                    </tbody>
                </table>
            </div>
        </div>

    </div>
</form>
<script>
    $(document).ready(function() {
        $('.model-select2').select2({
            dropdownParent: $('.model-select2').parent()
        });

        $('.missedReason').hide();

        $(document).on('change', '#ref_id', function() {
            var issue_no = $(this).val();
            var item_id = $('#ref_id :selected').data('item_id');
            var item_name = $('#ref_id :selected').data('item_name');
            $("#item_id").val(item_id);
            $("#req_item_id").val(item_id);
            $("#item_name").val(item_name);
            $.ajax({
                type: "POST",
                url: base_url + controller + '/getInspectionData',
                data: {
                    issue_no: issue_no
                },
                dataType: 'json',
            }).done(function(response) {
                console.log(response);
                $(".inspectData").html("");
                $(".inspectData").html(response.inspDataHtml);

            });
        });

        $(document).on('change', '#return_status', function() {
            if ($(this).val() == 6) {
                $('.missedReason').show();
                $('.otherReason').hide();
            } else {
                $('.missedReason').hide();
                $('.otherReason').show();
                if ($(this).val() == 5) {
                    let dataSet = {
                        item_type: 3
                    };
                    <?php if (!empty($dataRow->req_item_id)) { ?>
                        var jsonRow = '<?php echo htmlspecialchars(json_encode($dataRow), ENT_QUOTES, 'UTF-8'); ?>';
                        dataSet = {
                            id: '<?php echo $dataRow->req_item_id; ?>',
                            text: '<?php echo $dataRow->full_name; ?>',
                            row: jsonRow
                        };
                    <?php } ?>
                    getDynamicItemList(dataSet);
                    setPlaceHolder();
                    $(".otherItem").show();
                    $(".reason").attr("class", "col-md-7 form-group reason");
                } else {
                    $(".otherItem").hide();
                    $(".reason").attr("class", "col-md-10 form-group reason");
                }

                if ($(this).val() == 3) {
                    $("#location_id").val(<?= $this->SCRAP_STORE->id ?>);
                    $("#location_id").select2();
                    $("#location_id").attr('disabled', true);
                } else if ($(this).val() == 4) {
                    $("#location_id").val(<?= $this->REGRIND_STORE->id ?>);
                    $("#location_id").select2();
                    $("#location_id").attr('disabled', true);
                } else {
                    $("#location_id").val("");
                    $("#location_id").select2();
                    $("#location_id").attr('disabled', false);
                }
            }
        });

        $('#addRow').on('click', function() {
          
            $(".error").html("");
            var isValid = 1;

            if ($("#return_status").val() == "") {
                $(".return_status").html("Return Status is required.");
                isValid = 0;
            }
            if ($("#return_status").val() == 6) {
                if ($("#accepted_qty").val() == "" && $("#unaccepted_qty").val() == 0) {
                    $(".accepted_qty").html("Accepted Or Unaccepted Qty is required.");
                    isValid = 0;
                }
            } else {
                if ($("#qty").val() == "" || $("#qty").val() == 0) {
                    $(".qty").html("Qty is required.");
                    isValid = 0;
                }

                if ($("#location_id").val() == '') {
                    $(".location_id").html("Location is required.");
                    isValid = 0;
                }

            }
            if ($("#return_status").val() == 5) {
                if ($("#other_item").val() == "") {
                    $(".other_item").html("Item is required.");
                    isValid = 0;
                }
            }

            if (isValid) {
                var qty = $("#qty").val();
                var return_status = $("#return_status").val();
                var reason = $("#reason").val();
                var return_status_text = $("#return_status option:selected").text();

                var location_id = $("#location_id").val();
                var location_name = $("#location_id option:selected").text();

                var batch_no = $("#batch_no").val();

                var item_id = $("#item_id").val();
                var item_name = $("#item_name").val();
                if (return_status == 5) {
                    item_id = $("#other_item").val();
                    item_name = $("#other_item option:selected").text();
                    console.log($("#other_item").select2('data'));
                }

                var stock_type = 'USED';
                if (return_status == 2) {
                    stock_type = 'FRESH';
                }

                if (return_status == 6) {
                    if ($("#accepted_qty").val() > 0) {
                        var post = {
                            return_status: 6,
                            return_status_text: 'Accepted Missed',
                            item_name: item_name,
                            item_id: item_id,
                            location_name: '',
                            location_id: '',
                            batch_no: '',
                            stock_type: stock_type,
                            qty: $("#accepted_qty").val(),
                            reason: reason
                        };

                        AddRow(post);
                    }
                    if ($("#unaccepted_qty").val() > 0) {
                        var post = {
                            return_status: 7,
                            return_status_text: 'Unaccepted Missed',
                            item_name: item_name,
                            item_id: item_id,
                            location_name: '',
                            location_id: '',
                            batch_no: '',
                            stock_type: stock_type,
                            qty: $("#unaccepted_qty").val(),
                            reason: reason
                        };
                        console.log(post);
                        AddRow(post);
                    }

                } else {
                    var post = {
                        return_status: return_status,
                        return_status_text: return_status_text,
                        item_name: item_name,
                        item_id: item_id,
                        location_name: location_name,
                        location_id: location_id,
                        batch_no: batch_no,
                        stock_type: stock_type,
                        qty: qty,
                        reason: reason
                    };

                    AddRow(post);
                }
                console.log(post);
                $("#qty").val("");
                $("#accepted_qty").val("");
                $("#unaccepted_qty").val("");
                $("#return_status").val("");
                $("#return_status").comboSelect();
                $("#reason").val("");
                $("#return_status").trigger("change");

                $("#location_id").val("");
                $("#location_id").select2();
                $("#location_id").attr('disabled', false);
                $("#batch_no").val("");

            }
        });
    });

    function AddRow(data) {
        //Get the reference of the Table's TBODY element.
        $("#returnTbl").dataTable().fnDestroy();
        var tblName = "returnTbl";
        var tBody = $("#" + tblName + " > TBODY")[0];

        //Add Row.
        row = tBody.insertRow(-1);

        //Add index cell
        var countRow = $('#' + tblName + ' tbody tr:last').index() + 1;
        var cell = $(row.insertCell(-1));
        cell.html(countRow);



        cell = $(row.insertCell(-1));
        cell.html(data.return_status_text + '<input type="hidden" name="inspection_status[]" value="' + data.return_status + '">');

        cell = $(row.insertCell(-1));
        cell.html(data.item_name + '<input type="hidden" name="item_id[]" value="' + data.item_id + '">');

        cell = $(row.insertCell(-1));
        cell.html(data.location_name + '<input type="hidden" name="location_id[]" value="' + data.location_id + '"><input type="hidden" name="stock_type[]" value="' + data.stock_type + '">');

        cell = $(row.insertCell(-1));
        cell.html(data.batch_no + '<input type="hidden" name="batch_no[]" value="' + data.batch_no + '">');

        cell = $(row.insertCell(-1));
        cell.html(data.qty + '<input type="hidden" name="qty[]" value="' + data.qty + '"><input type="hidden" name="id[]" value="">');

        cell = $(row.insertCell(-1));
        cell.html(data.reason + '<input type="hidden" name="reason[]" value="' + data.reason + '">');

        //Add Button cell.
        cell = $(row.insertCell(-1));
        var btnRemove = $('<button><i class="ti-trash"></i></button>');
        btnRemove.attr("type", "button");
        btnRemove.attr("onclick", "Remove(this);");
        btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light btn-sm");
        cell.append(btnRemove);
        cell.attr("class", "text-center");
        $("#qty").val("");
        $("#return_status").val("");
        $("#return_status").comboSelect();
        $("#reason").val("");
        $("#return_status").trigger("change");

        $("#location_id").val("");
        $("#location_id").select2();
        $("#location_id").attr('disabled', false);
        $("#batch_no").val("");

    };

    function Remove(button) {
        console.log(button);
        // Determine the reference of the Row using the Button.
        $("#returnTbl").dataTable().fnDestroy();
        var row = $(button).closest("TR");
        var table = $("#returnTbl")[0];
        table.deleteRow(row[0].rowIndex);
        $('#returnTbl tbody tr td:nth-child(1)').each(function(idx, ele) {
            ele.textContent = idx + 1;
        });
    };
</script>