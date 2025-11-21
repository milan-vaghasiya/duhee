<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-3">
                                <h4 class="card-title">PFC</h4>
                            </div>
                            <div class="input-group float-left col-md-6">
                                <input type="hidden" id="item_id" value="<?=$item_id?>">
                                <input type="file" id="pfc_excel" name="pfc_excel" class="form-control-file  " style="width:50%" />
                                <a href="javascript:void(0);" class="btn  btn-success  ml-0" type="button"><i class="fa fa-upload"></i>&nbsp;<span class="btn-label" onclick="uploadExc();">Upload PFC &nbsp;<i class="fa fa-file-excel"></i></span></a>

                                <a href="<?= base_url($headData->controller . '/createExcelPFC/'. $item_id) ?>" class="btn  btn-info  mr-2" target="_blank"><i class="fa fa-download"></i> <span class="btn-label"> PFC Excel <i class="fa fa-file-excel"></i></span></a>
                            </div>
                            <div class="col-md-3">
                                <a href="<?= base_url($headData->controller . "/addPfc/" . $item_id) ?>" class="btn btn-outline-primary waves-effect waves-light float-right"><i class="fa fa-plus"></i> Add PFC</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='pfcTable' class="table table-bordered ssTable" data-url='/getPFCDTRows/<?= $item_id ?>'></table>
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
            var item_id = $(this).find(":selected").val();
            $("#pfcTable").attr("data-url", '/getPFCDTRows/' + item_id);
            ssTable.state.clear();
            initTable(0);
        });
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

    function trashPfc(id, name = 'Record') {
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
                            url: base_url + controller + '/deletePfc',
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

    function uploadExc() {
        setPlaceHolder();
        var fd = new FormData();
        fd.append("pfc_excel", $("#pfc_excel")[0].files[0]);
        fd.append("item_id", $('#item_id').val());
        $.ajax({
            url: base_url + controller + '/importExcelPFC',
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
                initTable(1);
                toastr.success(data.message, 'Success', {
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp",
                    "closeButton": true,
                    positionClass: 'toastr toast-bottom-center',
                    containerId: 'toast-bottom-center',
                    "progressBar": true
                });
            } else {
                initTable(1);
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
</script>