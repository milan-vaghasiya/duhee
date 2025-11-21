<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-4">
                                <h4 class="card-title"><?= $itemData->full_name . '</small>' ?></h4>
                            </div>
                            <div class="input-group float-left col-md-6">
                                <input type="hidden" id="item_id" value="<?= $item_id ?>">
                                <input type="file" id="fmea_excel" name="fmea_excel" class="form-control-file  " style="width:50%" />
                                <a href="javascript:void(0);" class="btn  btn-success  ml-0" type="button" onclick="uploadExc();"><i class="fa fa-upload"></i>&nbsp;<span class="btn-label" >Upload FMEA &nbsp;<i class="fa fa-file-excel"></i></span></a>
                                <a href="<?= base_url($headData->controller . '/createExcelFmea/' . $item_id) ?>" class="btn  btn-info  mr-2" target="_blank"><i class="fa fa-download"></i> <span class="btn-label">  FMEA <i class="fa fa-file-excel"></i></span></a>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-outline-primary waves-effect waves-light float-right addNew" data-button="both" data-modal_id="modal-lg" data-function="addFmea/<?= $item_id ?>" data-form_title="Add New FMEA" data-fnsave="saveFmeaMaster"><i class="fa fa-plus"></i> Add New Fmea</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='fmeaTable' class="table table-bordered ssTable" data-url='/getFmeaDTRows/<?= $item_id ?>'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script>
    function uploadExc() {
        setPlaceHolder();
        var fd = new FormData();
        fd.append("fmea_excel", $("#fmea_excel")[0].files[0]);
        fd.append("item_id", $('#item_id').val());
        $.ajax({
            url: base_url + controller + '/importExcelCP',
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
                initTable(1);$("#fmea_excel").val("");
                toastr.success(data.message, 'Success', {
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp",
                    "closeButton": true,
                    positionClass: 'toastr toast-bottom-center',
                    containerId: 'toast-bottom-center',
                    "progressBar": true
                });
            } else {
                initTable(1);$("#fmea_excel").val("");
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

    function trashFMEA(id, name = 'Record') {
        var send_data = {
            id: id
        };
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
                        $.ajax({
                            url: base_url + controller + '/deleteFmea',
                            data: send_data,
                            type: "POST",
                            dataType: "json",
                            success: function(data) {
                                if (data.status == 0) {
                                    toastr.error(data.message, 'Sorry...!', {
                                        "showMethod": "slideDown",
                                        "hideMethod": "slideUp",
                                        "closeButton": true,
                                        positionClass: 'toastr toast-bottom-center',
                                        containerId: 'toast-bottom-center',
                                        "progressBar": true
                                    });
                                } else {
                                    initTable(0);
                                    toastr.success(data.message, 'Success', {
                                        "showMethod": "slideDown",
                                        "hideMethod": "slideUp",
                                        "closeButton": true,
                                        positionClass: 'toastr toast-bottom-center',
                                        containerId: 'toast-bottom-center',
                                        "progressBar": true
                                    });
                                    $("#inspectionBody").html(data.tbodyData);
                                }
                            }
                        });
                    }
                },
                cancel: {
                    btnClass: 'btn waves-effect waves-light btn-outline-secondary',
                    action: function() {

                    }
                }
            }
        });
    }
</script>