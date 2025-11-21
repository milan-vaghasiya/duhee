<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                        <ul class="nav nav-pills">
                                <li class="nav-item"> <a href="<?= base_url($headData->controller . "/index/") ?>" class="btn waves-effect waves-light btn-outline-info  permission-write mr-1 "> Inward</a> </li>
                                <li class="nav-item"> <a href="<?= base_url($headData->controller . "/pendingPackingIndex/") ?>" class="btn waves-effect waves-light btn-outline-info  permission-write mr-1 "> Pending Packing</a> </li>
                                <li class="nav-item"> <a href="<?= base_url($headData->controller . "/packingIndex/0") ?>" class="btn waves-effect waves-light btn-outline-info  permission-write mr-1"> Inprocess </a> </li>
                                <li class="nav-item"> <a href="<?= base_url($headData->controller . "/packingIndex/1") ?>" class="btn waves-effect waves-light btn-outline-info  permission-write mr-1"> Completed </a> </li>
                                <li class="nav-item"> <a href="<?= base_url($headData->controller . "/firstBoxPacking") ?>" class="btn waves-effect waves-light btn-outline-info  permission-write mr-1"> First/Loose Box </a> </li>
                                <li class="nav-item"> <a href="<?= base_url($headData->controller . "/materialshortage") ?>" class="btn waves-effect waves-light btn-outline-info  permission-write mr-1 active"> Material Shortage </a> </li>
                            </ul>
                         
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="col-md-12">
                            <div class="row">
                                <table id="itemProcess" class="table excel_table table-bordered">
                                    <thead class="thead-info">
                                       <tr>
                                            <th>#</th>
                                            <th>Item</th>
                                            <th>Material</th>
                                            <th>Qty</th>
                                       </tr>
                                    </thead>
                                    <tbody id="itemProcessData">
                                        <?=$tbodyData?>
                                        
                                    </tbody>
                                </table>
                            </div>
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
        reportTable();
        $(document).on('change', '.machineConfig', function() {
            var machine_id = $(this).find(":selected").data("machine_id");
            var shift_id = $(this).find(":selected").data("shift_id");
            var emp_type = $(this).find(":selected").data("emp_type");

            var opr_id = '';
            var inq_id = '';
            if (shift_id == 1 && emp_type == 'OPR') {
                opr_id = $("#d_opr_" + machine_id).val();
                inq_id = '';
            } else if (shift_id == 1 && emp_type == 'INQ') {
                opr_id = '';
                inq_id = $("#d_inq_" + machine_id).val();
            } else if (shift_id == 2 && emp_type == 'OPR') {
                opr_id = $("#n_opr_" + machine_id).val();
                inq_id = "";

            } else if (shift_id == 2 && emp_type == 'OPR') {
                opr_id = '';
                inq_id = $("#n_inq_" + machine_id).val();
            }


            if (machine_id) {
                $.ajax({
                    type: "post",
                    url: base_url + "machines/saveOprInqData",
                    data: {
                        machine_id: machine_id,
                        shift_id: shift_id,
                        opr_id: opr_id,
                        inq_id: inq_id,
                        emp_type: emp_type
                    },
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == 0) {
                            swal("Sorry...!", data.message, "error");
                        } else {
                            window.location.reload();
                        }
                    }
                });
            }
        });
    });

    function reportTable() {
        var reportTable = $('#itemProcess').DataTable({
            responsive: true,
            //'stateSave':true,
            "autoWidth": false,
            order: [],
            "columnDefs": [{
                    type: 'natural',
                    targets: 0
                },
                {
                    orderable: false,
                    targets: "_all"
                },
                {
                    className: "text-left",
                    targets: [0, 1]
                },
                {
                    className: "text-center",
                    "targets": "_all"
                }
            ],
            pageLength: 25,
            language: {
                search: ""
            },
            lengthMenu: [
                [10, 25, 50, 100, -1],
                ['10 rows', '25 rows', '50 rows', '100 rows', 'Show all']
            ],
            dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" + "<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: ['pageLength', 'excel', {
                text: 'Refresh',
                action: function(e, dt, node, config) {
                    loadAttendanceSheet();
                }
            }]
        });
        reportTable.buttons().container().appendTo('#itemProcess_wrapper toolbar');
        $('.dataTables_filter .form-control-sm').css("width", "97%");
        $('.dataTables_filter .form-control-sm').attr("placeholder", "Search.....");
        $('.dataTables_filter').css("text-align", "left");
        $('.dataTables_filter label').css("display", "block");
        $('.btn-group>.btn:first-child').css("border-top-right-radius", "0");
        $('.btn-group>.btn:first-child').css("border-bottom-right-radius", "0");
        return reportTable;
    }

    function printData() {
        var emp_type = $("#emp_type").val();
        var from_date = $("#from_date").val();
        var to_date = $("#to_date").val();
        var shift_id = $('#shift_id').val();
        var valid = 1;
        if(emp_type == ''){
            valid = 0;
            $(".emp_type").html("Employee is required");
        }
        if(from_date == ''){
            valid = 0;
            $(".from_date").html("From Date is required");
        }
        if(valid){
            window.open(base_url + controller + '/printMcAsignData/' + from_date + '~' + to_date + '/' + shift_id + '/' + emp_type, '_blank').focus();
        }
    }
</script>