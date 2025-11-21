<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-3">
                                <h4 class="card-title">Asign OPR/INQ</h4>
                            </div>
                            <div class="col-md-9 row">
                                <div class="col-md-3 form-group">
                                    <select name="emp_type" id="emp_type" class="form-control">
                                        <option value="">Select Employee Type</option>
                                        <option value="OPR">Operator</option>
                                        <option value="INQ">Line Inspector</option>
                                    </select>
                                    <div class="error emp_type"></div>
                                </div>
                                <div class="col-md-2 form-group">
                                    <select name="shift_id" id="shift_id" class="form-control">
                                        <option value="1">Day</option>
                                        <option value="2">Night</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="datetime-local" name="from_date" id="from_date" class="form-control" max="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d\TH:i:s') ?>" />
                                    <div class="error fromDate"></div>

                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="datetime-local" name="to_date" id="to_date" class="form-control" value="<?= date('Y-m-d\TH:i:s') ?>" />
                                        <div class="input-group-append">
                                            <button type="button" class="btn waves-effect waves-light btn-success float-right " onclick="printData()" title="Print">
                                                <i class="fas fa-print"></i> Print
                                            </button>
                                        </div>
                                    </div>
                                    <div class="error toDate"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="col-md-12">
                            <div class="row">
                                <table id="itemProcess" class="table excel_table table-bordered">
                                    <thead class="thead-info">
                                        <tr>
                                            <th colspan="2"></th>
                                            <th colspan="2" class="text-center">Day</th>
                                            <th colspan="2" class="text-center">Night</th>

                                        </tr>
                                        <tr>
                                            <th style="width:10%;text-align:center;">#</th>
                                            <th class="text-center">Machine</th>
                                            <th class="text-center">Operator</th>
                                            <th class="text-center">Line Inspector</th>
                                            <th class="text-center">Operator</th>
                                            <th class="text-center">Line Inspector</th>

                                        </tr>
                                    </thead>
                                    <tbody id="itemProcessData">
                                        <?php
                                        if (!empty($assignData)) :
                                            $i = 1;
                                            $html = "";
                                            foreach ($assignData as $row) :
                                        ?>
                                                <tr id="<?= $row->id ?>">
                                                    <td class="text-center"><?= $i++ ?></td>
                                                    <td><?= '[' . $row->item_code . '] ' . $row->item_name ?></td>
                                                    <td>
                                                        <select name="d_opr" id="d_opr_<?= $row->id ?>" class="form-control single-select req machineConfig">

                                                            <?php
                                                            echo '<option value="0" data-shift_id="1" data-machine_id="' . $row->id . '" data-emp_type = "OPR">Select</option>';
                                                            foreach ($oprList as $opr) :
                                                                $selected = (!empty($row->dopr_id) && $row->dopr_id == $opr->id) ? "selected" : "";
                                                                echo '<option value="' . $opr->id . '" ' . $selected . ' data-shift_id="1" data-machine_id="' . $row->id . '" data-emp_type = "OPR">' . $opr->emp_name . '</option>';
                                                            endforeach;
                                                            ?>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="d_inq" id="d_inq_<?= $row->id ?>" class="form-control single-select req machineConfig">
                                                            <?php
                                                            echo '<option value="0" data-shift_id="1" data-machine_id="' . $row->id . '" data-emp_type = "INQ">Select</option>';
                                                            foreach ($inqList as $inq) :
                                                                $selected = (!empty($row->dinq_id) && $row->dinq_id == $inq->id) ? "selected" : "";
                                                                echo '<option value="' . $inq->id . '" ' . $selected . '  data-shift_id="1"  data-machine_id="' . $row->id . '" data-emp_type = "INQ">' . $inq->emp_name . '</option>';
                                                            endforeach;
                                                            ?>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="n_opr" id="n_opr_<?= $row->id ?>" class="form-control single-select req machineConfig">
                                                            <?php
                                                            echo '<option value="0" data-shift_id="2" data-machine_id="' . $row->id . '" data-emp_type = "OPR">Select</option>';
                                                            foreach ($oprList as $opr) :
                                                                $selected = (!empty($row->nopr_id) && $row->nopr_id == $opr->id) ? "selected" : "";
                                                                echo '<option value="' . $opr->id . '" ' . $selected . ' data-mc_config_id="' . $row->config_id . '" data-shift_id="2" data-emp_type = "OPR"  data-machine_id="' . $row->id . '">' . $opr->emp_name . '</option>';
                                                            endforeach;
                                                            ?>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="n_inq" id="n_inq_<?= $row->id ?>" class="form-control single-select req machineConfig">

                                                            <?php
                                                            echo '<option value="0" data-shift_id="2" data-machine_id="' . $row->id . '" data-emp_type = "INQ">Select</option>';
                                                            foreach ($inqList as $inq) :
                                                                $selected = (!empty($row->ninq_id) && $row->ninq_id == $inq->id) ? "selected" : "";
                                                                echo '<option value="' . $inq->id . '" ' . $selected . ' data-mc_config_id="' . $row->config_id . '" data-shift_id="2" data-machine_id="' . $row->id . '" data-emp_type = "INQ">' . $inq->emp_name . '</option>';
                                                            endforeach;
                                                            ?>
                                                        </select>
                                                    </td>
                                                </tr>
                                        <?php
                                            endforeach;
                                        else :
                                            echo '<tr><td colspan="6" class="text-center">No Data Found.</td></tr>';
                                        endif;
                                        ?>
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