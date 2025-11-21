<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : "" ?>" />
            <input type="hidden" name="log_no" id="log_no" value="<?= (!empty($dataRow->log_no)) ? $dataRow->log_no : $reqNo ?>" />
            <input type="hidden" name="auth_detail" id="auth_detail" value="<?= (!empty($dataRow->auth_detail)) ? $dataRow->auth_detail : "" ?>" />
            <input type="hidden" name="req_date" id="req_date" value="<?= (!empty($dataRow->req_date)) ? $dataRow->req_date : date("Y-m-d H:i:s") ?>" />
            <input type="hidden" name="approve_type" id="approve_type" value="<?= (!empty($approve_type)) ? $approve_type : '' ?>" />
            <input type="hidden"  id="iid" value="<?= (!empty($dataRow->req_item_id)) ? $dataRow->req_item_id : '' ?>" />
            <input type="hidden"  id="inm" value="<?= (!empty($dataRow->full_name)) ? $dataRow->full_name : '' ?>" />
            <input type="hidden"  id="reqn_type" name="reqn_type" value="<?= (!empty($dataRow->reqn_type)) ? $dataRow->reqn_type : 1 ?>" />
            <input type="hidden"  id="req_from" name="req_from" value="<?= (!empty($dataRow->req_from)) ? $dataRow->req_from : 0 ?>" />
            
            <div class="col-md-3 form-group">
                <label for="item_type">Item Group</label>
                <select id="item_type" class="form-control single-select">
                    <option value="">Select ALL</option>
                    <?php
                        foreach ($itemTypeList as $row) :
                            echo '<option value="' . $row->category_type . '">' . $row->category_name . '</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="category_id">Category</label>
                <select id="category_id" class="form-control single-select">
                    <option value="">Select ALL</option>
                    <?php
                        foreach ($categoryList as $row) :
                            echo '<option value="' . $row->id . '">' . $row->category_name . '</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-2 form-group">
                <label for="family_id">Family Group</label>
                <select id="family_id" class="form-control single-select">
                    <option value="">Select ALL</option>
                    <?php
                        foreach ($familyGroup as $row) :
                            echo '<option value="' . $row->id . '">' . $row->family_name . '</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-4 form-group req">
                <label for="req_item_id">Full Name</label>
                <div class="float-right">
                    <a class="text-primary font-bold waves-effect waves-dark" href="javascript:void(0)" aria-haspopup="true" aria-expanded="false" datatip="Return Stock" flow="down" id="returnAbleLink">Return Stock</a>
                </div>
                <select name="req_item_id" id="req_item_id" class="form-control large-select2 req" data-item_type="" data-category_id="" data-family_id="" autocomplete="off" data-default_id="<?= (!empty($dataRow->req_item_id)) ? $dataRow->req_item_id : "" ?>" data-default_text="<?= (!empty($dataRow->full_name)) ? $dataRow->full_name : "" ?>" data-url="items/getDynamicItemList">
                    <option value="">Select Item</option>
                </select>
            </div>
        </div>
        <div class="row">
           
            <input type="hidden" id="unit_id" value="" value="<?= (!empty($dataRow->unit_id)) ? $dataRow->unit_id : "" ?>" />

            <!-- <div class="col-md-3 form-group">
                <label for="unit_name">Unit</label>
                <input type="text" id="unit_name" class="form-control" readonly>            </div>
            <div class="col-md-3 form-group req">
                <label for="min_qty">Min Qty</label>
                <input type="text" id="min_qty" class="form-control" readonly value="<?= (!empty($dataRow->min_qty)) ? $dataRow->min_qty : "" ?>">
            </div>
            <div class="col-md-3 form-group req">
                <label for="max_qty">Max Qty</label>
                <input type="text" id="max_qty" class="form-control" readonly value="<?= (!empty($dataRow->max_qty)) ? $dataRow->max_qty : "" ?>">
            </div> -->
            <!-- <div class="col-md-3 form-group req leadTimeDiv">
                <label for="lead_time">Lead Time (In Days)</label>
                <input type="text" id="lead_time" class="form-control" readonly value="<?= (!empty($dataRow->lead_time)) ? $dataRow->lead_time : "" ?>">
            </div> -->
            
            <div class="col-md-3 form-group req">
                <label for="req_type">Required Type</label>
                <select name="req_type" id="req_type" class="form-control single-select ">
                    <option value="1" <?= (!empty($dataRow->req_type) && $dataRow->req_type == 1) ? 'selected' : ''; ?>>Fresh</option>
                    <option value="2" <?= (!empty($dataRow->req_type) && $dataRow->req_type == 2) ? 'selected' : ''; ?>>Used</option>
                </select>
            </div>
            <div class="col-md-6 form-group req sizeDiv" >
                <div class="input-group">
                    <label for="diameter" style="width:35%">Dia.<small>(mm)</small></label>
                    <label for="length" style="width:30%">Length<small>(mm)</small></label>
                    <label for="flute_length">Flute Length<small>(mm)</small></label>
                </div>
                <div class="input-group">
                    <?php
                    $diameter ='';$length ='';$flute_length ='';
                    if(!empty($dataRow->size)){
                        $size = explode("X",$dataRow->size);
                        $diameter =!empty($size[0])?$size[0]:'';$length =!empty($size[1])?$size[1]:'';$flute_length =!empty($size[2])?$size[2]:'';
                    }
                    ?>
                    <input type="text" id="diameter" name="diameter" class="form-control floatOnly" value="<?=$diameter?>">
                    <input type="text" id="length" name="length" class="form-control floatOnly"  value="<?=$length?>">
                    <input type="text" id="flute_length" name="flute_length" class="form-control floatOnly"  value="<?=$flute_length?>">
                </div>            
            </div>
      
            <div class="col-md-3 form-group">
                <label for="req_qty">Request Qty.</label>
                <input type="text" name="req_qty" id="req_qty" class="form-control floatOnly req" min="0" value="<?= (!empty($dataRow->req_qty)) ? (($dataRow->req_qty != "0.000") ? $dataRow->req_qty : $dataRow->req_qty) : "" ?>">
            </div>
            <div class="col-md-3 form-group ">
                <label for="urgency">Urgency</label>
                <select name="urgency" id="urgency" class="form-control single-select ">
                    <option value="0" <?= (!empty($dataRow->urgency) && $dataRow->urgency == 0) ? 'selected' : ''; ?>>Low</option>
                    <option value="1" <?= (!empty($dataRow->urgency) && $dataRow->urgency == 1) ? 'selected' : ''; ?>>Medium</option>
                    <option value="2" <?= (!empty($dataRow->urgency) && $dataRow->urgency == 2) ? 'selected' : ''; ?>>High</option>
                </select>
            </div>
            <div class="col-md-3 form-group req">
                <label for="delivery_date">Required Date</label>
                <input type="date" name="delivery_date" id="delivery_date" class="form-control" value="<?= (!empty($dataRow->delivery_date)) ? $dataRow->delivery_date : date("Y-m-d") ?>">
            </div>
       
            <div class="col-md-3 form-group">
                <label for="machine_id">Machine</label>
                <select name="machine_id" id="machine_id" class="form-control single-select ">
                    <option value="">Select Machine</option>
                    <?php
                    foreach ($fgNMcData as $row) :
                        if ($row->item_type == 5) {
                            $selected = (!empty($dataRow->machine_id) && $dataRow->machine_id == $row->id) ? 'selected' : '';
                            echo "<option value='" . $row->id . "' " . $selected . " data-item_type='" . $row->item_type . "' data-row='" . json_encode($row) . "'>[" . $row->item_code . "] " . $row->item_name . " </option>";
                        }
                    endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="fg_item_id">Where to Use</label>
                <select name="fg_item_id" id="fg_item_id" class="form-control single-select ">
                    <option value="">Select Finished Item</option>
                    <?php
                    foreach ($fgNMcData as $row) :
                        if ($row->item_type == 1) {
                            $selected = (!empty($dataRow->fg_item_id) && $dataRow->fg_item_id == $row->id) ? 'selected' : '';
                            echo "<option value='" . $row->id . "' " . $selected . " data-item_type='" . $row->item_type . "' data-row='" . json_encode($row) . "'>[" . $row->item_code . "] " . $row->item_name . " </option>";
                        }
                    endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="used_at">Used at</label>
                <select name="used_at" id="used_at" class="form-control single-select">
                    <option value="0" <?= (!empty($dataRow->used_at) && $dataRow->used_at == 0) ? 'selected' : '' ?>>In House</option>
                    <option value="1" <?= (!empty($dataRow->used_at) && $dataRow->used_at == 1) ? 'selected' : '' ?>>Vendor</option>
                </select>
            </div>
            <?php $returnOpt = ['No', 'Yes']; ?>
            <div class="col-md-3 form-group">
                <label for="is_returnable">Returnable</label>
                <input type="text" name="is_returnable1" id="is_returnable1" class="form-control" value="<?= (!empty($dataRow->is_returnable)) ? $returnOpt[$dataRow->is_returnable] : "" ?>" readonly>
                <input type="hidden" name="is_returnable" id="is_returnable" class="form-control" value="<?= (!empty($dataRow->is_returnable)) ? $dataRow->is_returnable : "" ?>">
            </div>
        </div>
        <div class="row">   
            <div class="col-md-12 form-group" id="authDetail"></div>
            <div class="col-md-10 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="<?= (!empty($dataRow->remark)) ? $dataRow->remark : "" ?>">
            </div>
            <div class="col-md-2 form-group req">
                <label for="item_image">&nbsp;</label>
                <a href="javascript:void(0)" id="item_image" class="btn btn-info btn-block" target="_blank">Item Image</a>
                <!--<img src="" id="item_image" class="img-responsive" style="  width: 100%; height: 100%x;object-fit: cover;">-->
            </div>
        </div>

        <hr style="width:100%;">
        <div class="row">
            <h5>Item Detail</h5>
            <table class="table jp-table align-items-center text-left">
                <tr>
                    <th>Unit</th> <td id="unit_name"> <?= (!empty($dataRow->unit_id)) ? $dataRow->unit_id : "" ?></td>
                    <th>Min Qty</th> <td id="min_qty"><?= (!empty($dataRow->min_qty)) ? $dataRow->min_qty : "" ?></td>
                    <th>Max Qty</th> <td id="max_qty"><?= (!empty($dataRow->max_qty)) ? $dataRow->max_qty : "" ?></td>
                    <th>Lead Time</th> <td id="lead_time"><?= (!empty($dataRow->lead_time)) ? $dataRow->lead_time : "" ?></td>
                </tr>
                
            </table>
        </div>
        <hr style="width:100%;">
        <div class="row">
            <div class="table-responsive">
                <table id="stockTbl" class="table jp-table align-items-center text-center" <?= empty($approve_type) ? 'hidden' : '' ?>>
                    <thead class="lightbg">
                        <tr>
                            <th>Current Stock</th>
                            <th>WIP Stock</th>
                            <th>Allocated Stock</th>
                            <th>Pending Purchase<br>Order Stock</th>
                            <th>Pending Requisition</th>
                            <th>Pending Indent Stock</th>
                            <th>Pending Indent for<br>Approval Stock</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody id="stockBody">
                        
                    </tbody>
                </table>
            </div>
        </div>
        <hr style="width:100%;">
    </div>
</form>
<div class="modal fade" id="returnStockModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table jp-table text-center">
                            <thead class="lightbg">
                                <tr>
                                    <th>Issue Date</th>
                                    <th>Issue NO</th>
                                    <th>Return Qty</th>
                                </tr>
                            </thead>
                            <tbody id="returnTbody">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn waves-effect waves-light btn-outline-secondary closeModal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
    </div>
</div>
<?php if(!empty($dataRow->is_return)){$dataRow->is_return=$dataRow->is_returnable; }?>
<script>
    $(document).on('shown.bs.modal', function () {
        let dataSet = {};
        var iid = $('#iid').val();//alert(iid);
        setTimeout(function(){
            if(iid)
            {
                <?php if (!empty($dataRow)) {$dataRow->is_return=$dataRow->is_returnable; ?>
                    var jsonRow = '<?php echo htmlspecialchars(json_encode($dataRow), ENT_QUOTES, 'UTF-8'); ?>';
                    dataSet = {id: iid, text: $('#inm').val(),row: jsonRow}; 
                <?php } else { ?> dataSet = {};  <?php } ?>
            }
            getDynamicItemList(dataSet);
        },600);
    });

    $(document).ready(function() {
        $("#returnAbleLink").hide();
      
        $(".sizeDiv").hide();
        setPlaceHolder();

        $(document).on('change', '#req_item_id', function() {
            var item_id = $(this).val();
            if (item_id) {
                <?php if (!empty($dataRow->req_item_id)) { ?>
                    var itemData = $(this).find(":selected").data('row');
                    if (!itemData) {
                        itemData = JSON.parse($(this).select2('data')[0]['row']);
                    }
                <?php } else { ?>
                    var itemData = JSON.parse($(this).select2('data')[0]['row']);
                <?php } ?>

                $("#unit_id").val("");
                $("#unit_id").val(itemData.unit_id);
                $("#unit_name").html("");
                $("#unit_name").html(itemData.unit_name);

                $("#drawing_no").val("");
                $("#drawing_no").val(itemData.drawing_no);

                $("#lead_time").html("");
                $("#lead_time").html(itemData.lead_time);

                $("#min_qty").html("");
                $("#min_qty").html(itemData.min_qty);

                $("#max_qty").html("");
                $("#max_qty").html(itemData.max_qty);

                $("#auth_detail").val("");
                $("#auth_detail").val(itemData.auth_detail);

                $("#item_type").val(itemData.item_type);
                $("#item_type").comboSelect();
                $("#category_id").val(itemData.category_id);
                $("#category_id").comboSelect();
                $("#family_id").val(itemData.family_id);
                $("#family_id").comboSelect();

                var returnOpt = ['No', 'Yes'];
                $("#is_returnable1").val(returnOpt[itemData.is_return]);
                $("#is_returnable").val(itemData.is_return);

                if (!itemData.item_image) {
                    itemData.item_image = 'no-photo.png';
                }
                $("#item_image").attr("href", base_url + '/assets/uploads/items/' + itemData.item_image);
                $.ajax({
                    type: "POST",
                    url: base_url + '/sendPR/getItemStockData',
                    data: {
                        item_id: item_id
                    },
                    dataType: 'json',
                }).done(function(response) {
                    console.log(response);
                    $("#stockBody").html("");
                    $("#stockBody").html(response.html);
                    $("#authDetail").html(response.authDetail);
                });

                
                if (itemData.is_return == 1) {$("#returnAbleLink").show();} else {$("#returnAbleLink").hide();}
                if (itemData.item_type == 2 && itemData.size != null && itemData.size != '') {
                    var size  = itemData.size.split('X');
                    $("#diameter").val(size[0]);
                    $("#length").val(size[1]);
                    $("#flute_length").val(size[2]);
                    $(".sizeDiv").show(); 
                } else {$(".sizeDiv").hide();}
            }
        });

        $(document).on('change', '#item_type', function() {
            var item_type = $(this).val();
            $("#req_item_id").attr('data-item_type', item_type);
            $.ajax({
                type: "POST",
                url: base_url + controller + '/getCategoryData',
                data: { item_type: item_type},
                dataType: 'json',
            }).done(function(response) {
                $("#category_id").html("");
                $("#category_id").html(response.options);
                $("#category_id").comboSelect();
            });
        });

        $(document).on('change', '#category_id', function() {
            $("#req_item_id").attr('data-category_id', $(this).val());
        });

        $(document).on('change', '#family_id', function() {
            $("#req_item_id").attr('data-family_id', $(this).val());
        });

        $(document).on('change', '#used_at', function() {
            var used_at = $(this).val();
            /*if (used_at == 0) {
                $("#is_returnable").val('0');
                $("#is_returnable1").val('No');
            }*/
            if (used_at == 1) {
                $("#is_returnable").val('1');
                $("#is_returnable1").val('Yes');
            }
            else
            {
                var item_id = $("#req_item_id").val();
                if (item_id) {
                    <?php if (!empty($dataRow->req_item_id)) { ?>
                        var itemData = $("#req_item_id").find(":selected").data('row');
                        if (!itemData) {
                            itemData = JSON.parse($("#req_item_id").select2('data')[0]['row']);
                        }
                    <?php } else { ?>
                        var itemData = JSON.parse($("#req_item_id").select2('data')[0]['row']);
                    <?php } ?>
                    
                    var returnOpt = ['No', 'Yes'];
                    $("#is_returnable1").val(returnOpt[itemData.is_return]);
                    $("#is_returnable").val(itemData.is_return);
                }
                else{
                    $("#is_returnable").val('0');
                    $("#is_returnable1").val('No');
                }
            }
            $.ajax({
                type: "POST",
                url: base_url + controller + '/getHandoverData',
                data: {
                    used_at: used_at
                },
                dataType: 'json',
            }).done(function(response) {
                $("#handover_to").html(response.handover);
                $("#handover_to").comboSelect();
            });
        });

        $(document).on('click', "#returnAbleLink", function() {
            var item_id = $("#req_item_id").val();
            <?php if (!empty($dataRow->req_item_id)) { ?>
                var itemData = $("#req_item_id").find(":selected").data('row');

                if (!itemData) {
                    itemData = JSON.parse($("#req_item_id").select2('data')[0]['row']);
                }
            <?php } else { ?>
                var itemData = JSON.parse($("#req_item_id").select2('data')[0]['row']);
            <?php } ?>
            $.ajax({
                type: "POST",
                url: base_url + '/sendPR/getReturnStock',
                data: {
                    item_id: item_id
                },
                dataType: 'json',
            }).done(function(response) {
                $("#returnTbody").html("");
                $("#returnTbody").html(response.tbodyHtml);
            });
            $('#returnStockModal').modal('show');
            $('#returnStockModal .modal-header .modal-title').html(itemData.full_name);
        });

        $(document).on('click', ".closeModal", function() {
            $('#returnStockModal').modal('hide');
        });

        $(document).on('change', '#req_type', function() {
            if($(this).val() == 1){
                $(".sizeDiv").hide();
            }else{
                $(".sizeDiv").show();
            }
        });

    });

    function AddRow() {
        $(".error").html("");
        var isValid = 1;
        if ($("#req_item_id").val() == "") {
            $(".req_item_id").html("Item Name is required.");
            isValid = 0;
        }
        if ($("#req_qty").val() == "") {
            $(".req_qty").html("Request Qty. is required.");
            isValid = 0;
        }

        if (isValid) {

            //Get the reference of the Table's TBODY element.
            $("#requesttbl").dataTable().fnDestroy();
            var tblName = "requesttbl";
            var tBody = $("#" + tblName + " > TBODY")[0];

            //Add Row.
            row = tBody.insertRow(-1);

            //Add index cell
            var countRow = $('#' + tblName + ' tbody tr:last').index() + 1;
            var cell = $(row.insertCell(-1));
            cell.html(countRow);

            cell = $(row.insertCell(-1));
            cell.html($("#req_item_idc").val() + '<input type="hidden" name="req_item_id[]" value="' + $("#req_item_id").val() + '"><input type="hidden" name="req_item_name[]" value="' + $("#req_item_idc").val() + '">');

            cell = $(row.insertCell(-1));
            cell.html($("#req_qty").val() + '<input type="hidden" name="req_qty[]" value="' + $("#req_qty").val() + '">');
            cell.append('<input type="hidden" name="planning_type[]" value="' + $("#planning_type").val() + '">');
            cell.append('<input type="hidden" name="delivery_date[]" value="' + $("#delivery_date").val() + '">');
            cell.append('<input type="hidden" name="description[]" value="' + $("#description").val() + '">');
            cell.append('<input type="hidden" name="item_dtl_description[]" value="' + $("#item_dtl_description").val() + '">');
            cell.append('<input type="hidden" name="current_stock[]" value="' + $("#current_stock").html() + '">');
            cell.append('<input type="hidden" name="wip_stock[]" value="' + $("#wip_stock").html() + '">');
            cell.append('<input type="hidden" name="pending_po_stock[]" value="' + $("#pending_po_stock").html() + '">');
            cell.append('<input type="hidden" name="pending_indent_stock[]" value="' + $("#pending_indent_stock").html() + '">');
            cell.append('<input type="hidden" name="pending_indent_apr_stk[]" value="' + $("#pending_indent_apr_stk").html() + '">');
            cell.append('<input type="hidden" name="remark[]" value="' + $("#remark").val() + '">');
            //Add Button cell.
            cell = $(row.insertCell(-1));
            var btnRemove = $('<button><i class="ti-trash"></i></button>');
            btnRemove.attr("type", "button");
            btnRemove.attr("onclick", "Remove(this);");
            btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light btn-sm");
            cell.append(btnRemove);
            cell.attr("class", "text-center");
            $("#req_item_id").val('');
            $("#req_item_idc").val('');
            $("#req_qty").val('');
        }
    };

    function Remove(button) {
        //Determine the reference of the Row using the Button.
        $("#requesttbl").dataTable().fnDestroy();
        var row = $(button).closest("TR");
        var table = $("#requesttbl")[0];
        table.deleteRow(row[0].rowIndex);
        $('#requesttbl tbody tr td:nth-child(1)').each(function(idx, ele) {
            ele.textContent = idx + 1;
        });
    };
</script>