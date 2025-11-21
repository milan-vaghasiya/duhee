<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-4">
                                <h4 class="card-title"><?=$itemData->full_name?></h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='fmeaTable' class="table table-bordered ssTable" data-url='/getPFCOperationRows/<?= $item_id ?>'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="uploadModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content animated slideDown">
            <form id="uploadExcel">
                <div class="modal-header">
                    <div class="col-md-8">
                        <h4 class="modal-title">Upload/Download Excel</h4>
                    </div>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="item_id" id="itemId" value="" />
                    <input type="hidden" name="item_code" id="item_code" value="" />
                    <input type="hidden" name="app_rev_no" id="app_rev_no" value="" />
                    <input type="hidden" name="rev_no" id="rev_no" value="" />
                    <div class="row">
                        <div class="input-group float-left col-md-12">
                            <input type="file" id="fmea_excel" name="fmea_excel" class="form-control-file  " style="width:50%" />
                            <a href="javascript:void(0);" class="btn  btn-success  ml-0" type="button"><i class="fa fa-upload"></i>&nbsp;<span class="btn-label" onclick="uploadExc('uploadExcel','importExcelFMEA');">Upload FMEA &nbsp;</span><i class="fa fa-file-excel"></i></a>
                            <a href="<?= base_url($headData->controller . '/createExcelFmea') ?>" class="btn  btn-info  mr-2" target="_blank"><i class="fa fa-download"></i>&nbsp;<span class="btn-label">FMEA Excel &nbsp;</span><i class="fa fa-file-excel"></i></a>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn waves-effect waves-light btn-outline-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script>
    $(document).ready(function() {
        $(document).on('change', '#item_id', function() {
            var item_id = $(this).find(":selected").val();
            $("#fmeaTable").attr("data-url", '/getControlPlanDTRows/' + item_id);
            ssTable.state.clear();
            initTable(0);
        });

        $(document).on('click', ".addControlPlan", function() {
            var id = $("#item_id").val();
            var productName = $("#item_idc").val();

            var functionName = $(this).data("function");
            var modalId = $(this).data('modal_id');
            var button = $(this).data('button');
            var title = $(this).data('form_title');
            var formId = functionName;
            var fnsave = $(this).data("fnsave");
            if (fnsave == "" || fnsave == null) {
                fnsave = "save";
            }
            var srposition = 1;
            if ($(this).is('[data-srposition]')) {
                srposition = $(this).data("srposition");
            }

            $.ajax({
                type: "POST",
                url: base_url + 'controlPlan/' + functionName,
                data: {
                    id: id
                }
            }).done(function(response) {
                $("#" + modalId).modal();
                $("#" + modalId + ' .modal-title').html(title + " [ Product : " + productName + " ]");
                $("#" + modalId + ' .modal-body').html(response);
                $("#" + modalId + " .modal-body form").attr('id', formId);
                $("#" + modalId + " .modal-footer .btn-save").attr('onclick', "saveControlPlan('"+formId+"', 'saveControlPlan', '" + srposition + "');");
                if (button == "close") {
                    $("#" + modalId + " .modal-footer .btn-close").show();
                    $("#" + modalId + " .modal-footer .btn-save").hide();
                } else if (button == "save") {
                    $("#" + modalId + " .modal-footer .btn-close").hide();
                    $("#" + modalId + " .modal-footer .btn-save").show();
                } else {
                    $("#" + modalId + " .modal-footer .btn-close").show();
                    $("#" + modalId + " .modal-footer .btn-save").show();
                }
                initModalSelect();
                $(".single-select").comboSelect();
                $("#" + modalId + " .scrollable").perfectScrollbar({
                    suppressScrollX: true
                });
                initMultiSelect();
                setPlaceHolder();
                $(".symbol-select").select2({
                    templateResult: formatSymbol
                });
            });
        });

        $(document).on('change', "#pfc_id", function() {
            var process_no = $('#pfc_id :selected').data('process_no');
            $('#process_no').val(process_no);

            var prefix = $("#cp_prefix").val();
            $("#cp_number").val(prefix+process_no);
        });

        $(document).on('change', "#tolerance_type", function() {
            var tolerance_type = $(this).val();
            if (tolerance_type == 1) {
                $('.min_req').show();
                $('.max_req').show();
                $('.other_req').show();
            } else if (tolerance_type == 2) {
                $('.min_req').show();
                $('.max_req').hide();
                $('.other_req').show();
            } else if (tolerance_type == 3) {
                $('.min_req').hide();
                $('.max_req').show();
                $('.other_req').show();
            } else if (tolerance_type == 4) {
                $('.min_req').hide();
                $('.max_req').hide();
                $('.other_req').show();
            }
        });



        $(document).ready(function() {
            $(document).on('click', '.uploadExcel', function(e) {
                $(".error").html("");
                var valid = 1;
                var item_id = $('#item_id :selected').val();
                var item_code = $('#item_id :selected').data('product_code');
                var app_rev_no = $('#item_id :selected').data('app_rev_no');
                var rev_no = $('#item_id :selected').data('rev_no');
                $("#uploadModel").modal();
                $("#exampleModalLabel1").html('Upload/Download Excel');
                $("#itemId").val("");
                $("#item_code").val("");
                $("#app_rev_no").val("");
                $("#rev_no").val("");

                $("#itemId").val(item_id);
                $("#item_code").val(item_code);
                $("#app_rev_no").val(app_rev_no);
                $("#rev_no").val(rev_no);

            });
        });
    });

    function saveControlPlan(formId, fnsave) {
        setPlaceHolder();
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
                initTable(0);
                $('#' + formId)[0].reset();
                $(".modal").modal('hide');
                toastr.success(data.message, 'Success', {
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp",
                    "closeButton": true,
                    positionClass: 'toastr toast-bottom-center',
                    containerId: 'toast-bottom-center',
                    "progressBar": true
                });
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

    function editControlPlan(data) {
        var productName = $("#item_idc").val();
        var button = data.button;
        if (button == "" || button == null) {
            button = "both";
        };
        var fnEdit = data.fnedit;
        if (fnEdit == "" || fnEdit == null) {
            fnEdit = "edit";
        }
        var fnsave = data.fnsave;
        if (fnsave == "" || fnsave == null) {
            fnsave = "save";
        }
        var savebtn_text = data.savebtn_text;
        if (savebtn_text == "" || savebtn_text == null) {
            savebtn_text = "Save";
        }
        var sendData = {
            id: data.id
        };
        if (data.approve_type) {
            sendData = {
                id: data.id,
                approve_type: data.approve_type
            };
        }
        $.ajax({
            type: "POST",
            url: base_url + controller + '/' + fnEdit,
            data: sendData,
        }).done(function(response) {
            $("#" + data.modal_id).modal();
            $("#" + data.modalId + ' .modal-title').html(data.title + " [ Product : " + productName + " ]");
            $("#" + data.modal_id + ' .modal-body').html(response);
            $("#" + data.modal_id + " .modal-body form").attr('id', data.form_id);
            //$("#"+data.modal_id+" .modal-footer .btn-save").html(savebtn_text);
            $("#" + data.modal_id + " .modal-footer .btn-save").attr('onclick', "store('" + data.form_id + "','" + fnsave + "');");
            $("#" + data.modal_id + " .modal-footer .btn-save-close").attr('onclick', "store('" + data.form_id + "','" + fnsave + "','save_close');");
            $("#" + data.modal_id + " .modal-footer .btn-close").attr('data-modal_id', data.form_id);
            if (button == "close") {
                $("#" + data.modal_id + " .modal-footer .btn-close").show();
                $("#" + data.modal_id + " .modal-footer .btn-save").hide();
                $("#" + data.modalId + " .modal-footer .btn-save-close").hide();
            } else if (button == "save") {
                $("#" + data.modal_id + " .modal-footer .btn-close").hide();
                $("#" + data.modal_id + " .modal-footer .btn-save").show();
                $("#" + data.modalId + " .modal-footer .btn-save-close").show();
            } else {
                $("#" + data.modal_id + " .modal-footer .btn-close").show();
                $("#" + data.modal_id + " .modal-footer .btn-save").show();
                $("#" + data.modalId + " .modal-footer .btn-save-close").show();
            }
            initModalSelect();
            $(".single-select").comboSelect();
            $(".symbol-select").select2({
                templateResult: formatSymbol
            });
            $("#" + data.modal_id + " .scrollable").perfectScrollbar({
                suppressScrollX: true
            });
            initMultiSelect();
            setPlaceHolder();
        });
    }


</script>