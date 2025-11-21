<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                    <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title"><?=$fmeaData->trans_number.' <small>['.$fmeaData->process_no.'] '.$fmeaData->parameter.'</small>'?></h4>
                            </div>
                            <div class="col-md-6">
                                <a href="<?=base_url($headData->controller."/addDiamention/".$fmea_id)?>" class="btn btn-outline-primary waves-effect waves-light float-right" ><i class="fa fa-plus"></i> Add Dimension</a>
                            </div>
                        </div>                                 
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='fmeaTable' class="table table-bordered ssTable" data-url='/getDiamentionDTRows/<?=$fmea_id?>'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
    $(document).on('change','#item_id',function(){
        var item_id = $(this).find(":selected").val();
        $("#fmeaTable").attr("data-url",'/getFMEADTRows/'+item_id);
        ssTable.state.clear();initTable(0);
    });

    $(document).on('click', ".addFmea", function() {
        var id = $("#item_id").val();
        var productName = $("#item_idc").val();        

        var functionName = $(this).data("function");
        var modalId = $(this).data('modal_id');
        var button = $(this).data('button');
        var title = $(this).data('form_title');
        var formId = functionName;
        var fnsave = $(this).data("fnsave");if(fnsave == "" || fnsave == null){fnsave="save";}
        var srposition = 1;
        if ($(this).is('[data-srposition]')){srposition = $(this).data("srposition");}

        $.ajax({
                type: "POST",
                url: base_url + 'controlPlan/' + functionName,
                data: {id:id}
        }).done(function(response) {
            $("#" + modalId).modal();
            $("#" + modalId + ' .modal-title').html(title + " [ Product : "+productName+" ]");
            $("#" + modalId + ' .modal-body').html(response);
            $("#" + modalId + " .modal-body form").attr('id', formId);
            $("#" + modalId + " .modal-footer .btn-save").attr('onclick', "saveFmea('getFmea', 'saveFmea', '"+srposition+"');");
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
            $("#"+modalId+" .scrollable").perfectScrollbar({suppressScrollX: true});
            initMultiSelect();setPlaceHolder();
            $(".symbol-select").select2({templateResult: formatSymbol});
        });
    });
        
    $(document).on('change', "#pfc_id", function() {
        $('#process_no').val($('#pfc_id :selected').data('process_no'));
    });

    $(document).on('change', "#requirement", function() {
        var requirement = $(this).val();
        if(requirement == 1){
            $('.min_req').show();
            $('.max_req').show();
            $('.other_req').show();
        }else if(requirement == 2){
            $('.min_req').show();
            $('.max_req').hide();
            $('.other_req').show();
        }else if(requirement == 3){
            $('.min_req').hide();
            $('.max_req').show();
            $('.other_req').show();
        }else if(requirement == 4){
            $('.min_req').hide();
            $('.max_req').hide();
            $('.other_req').show();
        }
    });

    $(document).on('keyup', ".rpnCalc", function() {
        var sev = $('#sev').val();
        var occur = $('#occur').val();
        var detec = $('#detec').val();
        if(sev == '' || sev == 0){ sev=1; }
        if(occur == '' || occur == 0){ occur=1; }
        if(detec == '' || detec == 0){ detec=1; }
        
        var rpn = parseFloat(sev) * parseFloat(occur) * parseFloat(detec);
        $('#rpn').val(rpn);
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

function saveFmea(formId, fnsave) {
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
            initTable(0); $('#'+formId)[0].reset();$(".modal").modal('hide');
            toastr.success(data.message, 'Success', {
                "showMethod": "slideDown","hideMethod": "slideUp","closeButton": true,positionClass: 'toastr toast-bottom-center',containerId: 'toast-bottom-center',"progressBar": true
            });
        } else {
            initTable(0);
            $('#' + formId)[0].reset();
            $(".modal").modal('hide');
            toastr.error(data.message, 'Error', {
                "showMethod": "slideDown","hideMethod": "slideUp","closeButton": true,positionClass: 'toastr toast-bottom-center',containerId: 'toast-bottom-center',"progressBar": true
            });
        }

    });
}

function editFmea(data){
    var productName = $("#item_idc").val();   
	var button = data.button;if(button == "" || button == null){button="both";};
	var fnEdit = data.fnedit;if(fnEdit == "" || fnEdit == null){fnEdit="edit";}
	var fnsave = data.fnsave;if(fnsave == "" || fnsave == null){fnsave="save";}
	var savebtn_text = data.savebtn_text;if(savebtn_text == "" || savebtn_text == null){savebtn_text="Save";}
	var sendData = {id:data.id};
	if(data.approve_type){sendData = {id:data.id,approve_type:data.approve_type};}
	$.ajax({ 
		type: "POST",   
		url: base_url + controller + '/' + fnEdit,   
		data: sendData,
	}).done(function(response){
		$("#"+data.modal_id).modal();
        $("#" + data.modalId + ' .modal-title').html(data.title + " [ Product : "+productName+" ]");
		$("#"+data.modal_id+' .modal-body').html(response);
		$("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);
		//$("#"+data.modal_id+" .modal-footer .btn-save").html(savebtn_text);
		$("#"+data.modal_id+" .modal-footer .btn-save").attr('onclick',"store('"+data.form_id+"','"+fnsave+"');");
		$("#"+data.modal_id+" .modal-footer .btn-save-close").attr('onclick',"store('"+data.form_id+"','"+fnsave+"','save_close');");
		$("#"+data.modal_id+" .modal-footer .btn-close").attr('data-modal_id',data.form_id);
		if(button == "close"){
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").hide();
			$("#"+data.modalId+" .modal-footer .btn-save-close").hide();
		}else if(button == "save"){
			$("#"+data.modal_id+" .modal-footer .btn-close").hide();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
            $("#"+data.modalId+" .modal-footer .btn-save-close").show();
		}else{
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
            $("#"+data.modalId+" .modal-footer .btn-save-close").show();
		}
		initModalSelect();
		$(".single-select").comboSelect();
		$(".symbol-select").select2({templateResult: formatSymbol});
		$("#"+data.modal_id+" .scrollable").perfectScrollbar({suppressScrollX: true});
		initMultiSelect();setPlaceHolder();
	});
}

function trashFmea(id, name = 'Record') {
    var send_data = {
        id: id
    };
    $.confirm({
        title: 'Confirm!',
        content: 'Are you sure want to delete this ' + name + '?',
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

function uploadExc(formId, fnsave) {
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