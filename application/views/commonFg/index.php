<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">Common FG</h4>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew" data-button="both" data-modal_id="modal-lg" data-function="addCommonFg" data-form_title="Add Common FG"><i class="fa fa-plus"></i> Add CommonFG</button>
                            </div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='commonFgTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>

<script>

    // Kit Table Functions
    function kitTable(){
        var kitTable = $('#productKit').DataTable( {
            lengthChange: false,
            "paging":false,
            responsive: true,
            'stateSave':true,
            retrieve: true,
            buttons: [ 'copy', 'excel', 'pdf' ]
        });
        kitTable.buttons().container().appendTo( '#productKit_wrapper .col-md-6:eq(0)' );
        return kitTable;
    }

    function AddKitRow() {
        var valid = 1;
        $(".error").html("");
        if($("#ref_item_id").val() == ""){$(".ref_item_id").html("Item is required.");valid = 0;}
        if($("#qty").val() == "" || $("#qty").val() == 0){$(".qty").html("Quantity is required.");valid = 0;}

        if(valid)
        {
            if($.inArray($("#ref_item_id").val()) >= 0){
                $(".ref_item_id").html("Item already added.");
            }else{
                $(".ref_item_id").html("");
                $(".qty").html("");

                //Get the reference of the Table's TBODY element.
                $("#productKit").dataTable().fnDestroy();
                var tblName = "productKit";                
                var tBody = $("#"+tblName+" > TBODY")[0];
                
                //Add Row.
                row = tBody.insertRow(-1);
                
                //Add index cell
                var countRow = $('#'+tblName+' tbody tr:last').index() + 1;
                var cell = $(row.insertCell(-1));
                cell.html(countRow);
                
                cell = $(row.insertCell(-1));
                cell.html($("#ref_item_idc").val() + '<input type="hidden" name="ref_item_id[]" value="'+$("#ref_item_id").val()+'"><input type="hidden" name="kit_id[]" value="">');

                cell = $(row.insertCell(-1));
                cell.html($("#qty").val() + '<input type="hidden" name="qty[]" value="'+$("#qty").val()+'">');
                    
                //Add Button cell.
                cell = $(row.insertCell(-1));
                var btnRemove = $('<button><i class="ti-trash"></i></button>');
                btnRemove.attr("type", "button");
                btnRemove.attr("onclick", "RemoveKit(this);");
                btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");
                cell.append(btnRemove);
                cell.attr("class","text-center");
                kitTable();

                $("#ref_item_idc").val("");
                $("#ref_item_id").val("");
                $("#qty").val("");
            }
        }
    };

    function RemoveKit(button) {
        //Determine the reference of the Row using the Button.
        $("#productKit").dataTable().fnDestroy();
        var row = $(button).closest("TR");
        var table = $("#productKit")[0];
        table.deleteRow(row[0].rowIndex);
        $('#productKit tbody tr td:nth-child(1)').each(function(idx, ele) {
            ele.textContent = idx + 1;
        });
        kitTable();
    };

    // Process Table Funcitons
    function processTable(){
        var processTable = $('#processKit').DataTable( {
            lengthChange: false,
            "paging":false,
            responsive: true,
            'stateSave':true,
            retrieve: true,
            buttons: [ 'copy', 'excel', 'pdf' ]
        });
        processTable.buttons().container().appendTo( '#processKit_wrapper .col-md-6:eq(0)' );
        return processTable;
    }

    function AddProcessRow() {
        var valid = 1;
        $(".error").html("");
        if($("#process_id").val() == ""){$(".process_id").html("Process is required.");valid = 0;}

        if(valid)
        {
            if($.inArray($("#process_id").val()) >= 0){
                $(".process_id").html("Process already added.");
            }else{
                $(".process_id").html("");
                $(".cycle_time").html("");

                //Get the reference of the Table's TBODY element.
                $("#processKit").dataTable().fnDestroy();
                var tblName = "processKit";                
                var tBody = $("#"+tblName+" > TBODY")[0];
                
                //Add Row.
                row = tBody.insertRow(-1);
                
                //Add index cell
                var countRow = $('#'+tblName+' tbody tr:last').index() + 1;
                var cell = $(row.insertCell(-1));
                cell.html(countRow);
                
                cell = $(row.insertCell(-1));
                cell.html($("#process_idc").val() + '<input type="hidden" name="process_id[]" value="'+$("#process_id").val()+'"><input type="hidden" name="kit_process_id[]" value="">');

                cell = $(row.insertCell(-1));
                cell.html($("#cycle_time").val() + '<input type="hidden" name="cycle_time[]" value="'+$("#cycle_time").val()+'">');
                    
                //Add Button cell.
                cell = $(row.insertCell(-1));
                var btnRemove = $('<button><i class="ti-trash"></i></button>');
                btnRemove.attr("type", "button");
                btnRemove.attr("onclick", "RemoveProcess(this);");
                btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");
                cell.append(btnRemove);
                cell.attr("class","text-center");
                processTable();

                $("#process_idc").val("");
                $("#process_id").val("");
                $("#cycle_time").val("");
            }
        }
    };

    function RemoveProcess(button) {
        //Determine the reference of the Row using the Button.
        $("#processKit").dataTable().fnDestroy();
        var row = $(button).closest("TR");
        var table = $("#processKit")[0];
        table.deleteRow(row[0].rowIndex);
        $('#processKit tbody tr td:nth-child(1)').each(function(idx, ele) {
            ele.textContent = idx + 1;
        });
        processTable();
    };

</script>