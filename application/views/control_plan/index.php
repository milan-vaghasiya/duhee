<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">Control Plan</h4>
                            </div>                            
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='inspectionParamTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
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
                        <h4 class="modal-title">Upload/Download Excel></h4>
                    </div>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="item_id" id="item_id" value="" />
                    <input type="hidden" name="item_code" id="item_code" value="" />
                    <input type="hidden" name="app_rev_no" id="app_rev_no" value="" />
                    <input type="hidden" name="rev_no" id="rev_no" value="" />

                    <div class="row">
                        <div class="input-group float-left col-md-12">

                            <input type="file" id="pfc_excel" name="pfc_excel" class="form-control-file  " style="width:50%" />
                            <a href="javascript:void(0);" class="btn  btn-success  ml-0" type="button"><i class="fa fa-upload"></i>&nbsp;<span class="btn-label" onclick="uploadExc('uploadExcel','importExcelPFC');">Upload PFC &nbsp;<i class="fa fa-file-excel"></i></span></a>

                            <a href="<?= base_url($headData->controller . '/createExcelPFC') ?>" class="btn  btn-info  mr-2" target="_blank"><i class="fa fa-download"></i><span class="btn-label">PFC Excel <i class="fa fa-file-excel"></i></span></a>
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
<script src="<?php echo base_url();?>assets/js/custom/control-plan.js?v=<?=time()?>"></script>
<script>
    $(document).ready(function() {
        $(document).on('click', '.uploadExcel', function(e) {
            $(".error").html("");
            var valid = 1;
            var item_id = $(this).data('item_id');
            var item_code = $(this).data('product_code');
            var app_rev_no = $(this).data('app_rev_no');
            var rev_no = $(this).data('rev_no');
            $("#uploadModel").modal();
            $("#exampleModalLabel1").html('Upload/Download Excel');
            $("#item_id").val("");
            $("#item_code").val("");
            $("#app_rev_no").val("");
            $("#rev_no").val("");

            $("#item_id").val(item_id);
            $("#item_code").val(item_code);
            $("#app_rev_no").val(app_rev_no);
            $("#rev_no").val(rev_no);

        });
    });

    function uploadExc(formId, fnsave) {
        // var fd = $('#'+formId).serialize();
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
</script>