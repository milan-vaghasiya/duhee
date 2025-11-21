<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="" />
            <input type="hidden" name="ref_id" id="ref_id" value="<?= (!empty($ref_id)) ? $ref_id : "" ?>" />
            <input type="hidden" name="batch_no" id="batch_no" value="<?= (!empty($batch_no)) ? $batch_no : "" ?>" />
            <input type="hidden" name="pending_qty" id="pending_qty" value="<?= (!empty($pending_qty)) ? $pending_qty : "" ?>" />
            <input type="hidden" name="size" id="size" value="<?= (!empty($size)) ? $size : "" ?>" />
            <input type="hidden" name="trans_type" id="trans_type" value="0" />

            <div class="col-md-2 form-group">
                <label for="trans_date">Return Date</label>
                <input type="date" name="trans_date" class="form-control" id="trans_date" value="<?= date("Y-m-d") ?>">
            </div>

            <div class="col-md-2 form-group">
                <label for="used_qty">For Reuse Qty</label>
                <input type="text" class="form-control numericOnly" id="used_qty">
            </div> 
            <div class="col-md-2 form-group">
                <label for="fresh_qty">Fresh Qty</label>
                <input type="text" class="form-control numericOnly" id="fresh_qty">
            </div>
            <div class="col-md-2 form-group">
                <label for="missed_qty">Missed Qty</label>
                <input type="text" class="form-control numericOnly" id="missed_qty">
            </div>
            <div class="col-md-2 form-group">
                <label for="broken_qty">Broken Qty</label>
                <input type="text" class="form-control numericOnly" id="broken_qty">
            </div>
            <div class="col-md-2 form-group">
                <label for="scrap_qty">Scrap Qty</label>
                <input type="text" class="form-control numericOnly" id="scrap_qty">
            </div>
            <div class="col-md-2 form-group">
                <label for="regranding_qty">Regrinding Qty</label>
                <input type="text" class="form-control numericOnly" id="regranding_qty">
            </div>
            <!-- <div class="col-md-2 form-group">
                <label for="return_status">Return Status</label>
                <select id="return_status" class="form-control single-select">
                    <option value="">Select Status</option>
                    <option value="1">Used</option>
                    <option value="2">Fresh</option>
                    <option value="3">Missed</option>
                    <option value="4">Broken</option>
                </select>
            </div> -->
            <div class="col-md-8 form-group">
                <label for="reason">Remark</label>
                <input type="text" id="reason" class="form-control">
            </div>
            <div class="col-md-2">
                <label for="">&nbsp;</label>
                <button type="button" class="btn btn-block btn-info" onclick="AddRow()">Add</button>
            </div>
        </div>
        <hr style="width:100%;">
        <div class="row">
            <div class="error genral_error"></div>
            <div class="table-responsive">
                <table id="returnTbl" class="table table-bordered align-items-center text-center">
                    <thead class="thead-info">
                        <tr>
                            <th>#</th>
                            <th>Used Qty</th>
                            <th>Fresh Qty</th>
                            <th>Missed Qty</th>
                            <th>Broken Qty</th>
                            <th>Scrap Qty</th>
                            <th>Regrinding Qty</th>
                            <th>Remark</th>
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
    function AddRow() {
        $(".error").html("");
        var isValid = 1;
        if (($("#used_qty").val() == "" || $("#used_qty").val() == 0) && ($("#fresh_qty").val() == "" || $("#fresh_qty").val() == 0) && ($("#missed_qty").val() == "" || $("#missed_qty").val() == 0) && ($("#broken_qty").val() == "" || $("#broken_qty").val() == 0) && ($("#scrap_qty").val() == "" || $("#scrap_qty").val() == 0) && ($("#regranding_qty").val() == "" || $("#regranding_qty").val() == 0)) {
            $(".used_qty").html("Qty is required.");
            isValid = 0;
        }

        if (isValid) {
            var used_qty = $("#used_qty").val();
            var fresh_qty = $("#fresh_qty").val();
            var missed_qty = $("#missed_qty").val();
            var broken_qty = $("#broken_qty").val();
            var scrap_qty = $("#scrap_qty").val();
            var regranding_qty = $("#regranding_qty").val();
            var reason = $("#reason").val();

            if(used_qty == ''){ used_qty=0; }
            if(fresh_qty == ''){ fresh_qty=0; }
            if(missed_qty == ''){ missed_qty=0; }
            if(broken_qty == ''){ broken_qty=0; }
            if(scrap_qty == ''){ scrap_qty=0; }
            if(regranding_qty == ''){ regranding_qty=0; }

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
            cell.html(used_qty + '<input type="hidden" name="used_qty[]" value="' + used_qty + '"><input type="hidden" name="id[]" value="">');

            cell = $(row.insertCell(-1));
            cell.html(fresh_qty + '<input type="hidden" name="fresh_qty[]" value="' + fresh_qty + '">');

            cell = $(row.insertCell(-1));
            cell.html(missed_qty + '<input type="hidden" name="missed_qty[]" value="' + missed_qty + '">');

            cell = $(row.insertCell(-1));
            cell.html(broken_qty + '<input type="hidden" name="broken_qty[]" value="' + broken_qty + '">');

            cell = $(row.insertCell(-1));
            cell.html(scrap_qty + '<input type="hidden" name="scrap_qty[]" value="' + scrap_qty + '">');

            cell = $(row.insertCell(-1));
            cell.html(regranding_qty + '<input type="hidden" name="regranding_qty[]" value="' + regranding_qty + '">');

            cell = $(row.insertCell(-1));
            cell.html(reason + '<input type="hidden" name="reason[]" value="' + reason + '">');

            //Add Button cell.
            cell = $(row.insertCell(-1));
            var btnRemove = $('<button><i class="ti-trash"></i></button>');
            btnRemove.attr("type", "button");
            btnRemove.attr("onclick", "Remove(this);");
            btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light btn-sm");
            cell.append(btnRemove);
            cell.attr("class", "text-center");
            
            $("#used_qty").val("");
            $("#fresh_qty").val("");
            $("#missed_qty").val("");
            $("#broken_qty").val("");
            $("#scrap_qty").val("");
            $("#regranding_qty").val("");
            $("#reason").val("");
        }
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