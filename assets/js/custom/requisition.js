$(document).ready(function() {
    $(document).on('click', ".approvePreq", function() {
        var id = $(this).data('id');
        var val = $(this).data('val');
        var msg = $(this).data('msg');
        $.confirm({
            title: 'Confirm!',
            content: 'Are you sure want to ' + msg + ' this Requisition?',
            type: 'green',
            buttons: {
                ok: {
                    text: "ok!",
                    btnClass: 'btn waves-effect waves-light btn-outline-success',
                    keys: ['enter'],
                    action: function() {
                        $.ajax({
                            url: base_url + controller + '/approvePreq',
                            data: {
                                id: id,
                                val: val,
                                msg: msg
                            },
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
                                    initTable();
                                    toastr.success(data.message, 'Success', {
                                        "showMethod": "slideDown",
                                        "hideMethod": "slideUp",
                                        "closeButton": true,
                                        positionClass: 'toastr toast-bottom-center',
                                        containerId: 'toast-bottom-center',
                                        "progressBar": true
                                    });
                                    //window.location.reload();
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
    });

    $(document).on('click', ".addNewRequisition", function() {
        var functionName = $(this).data("function");
        var modalId = $(this).data('modal_id');
        var button = $(this).data('button');
        var title = $(this).data('form_title');
        var formId = functionName.split('/')[0];
        var fnsave = $(this).data("fnsave");
        if (fnsave == "" || fnsave == null) {
            fnsave = "save";
        }
        $.ajax({
            type: "GET",
            url: base_url + controller + '/' + functionName,
            data: {}
        }).done(function(response) {
            $("#" + modalId).modal({
                show: true
            });
            $("#" + modalId + ' .modal-title').html(title);
            $("#" + modalId + ' .modal-body').html("");
            $("#" + modalId + ' .modal-body').html(response);
            $("#" + modalId + " .modal-body form").attr('id', formId);
            $("#" + modalId + " .modal-footer .btn-save").attr('onclick', "storeRequisition('" + formId + "','" + fnsave + "');");

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
            $('.model-select2').select2({
                dropdownParent: $('.model-select2').parent()
            });
            $("#processDiv").hide();
            $("#" + modalId + " .scrollable").perfectScrollbar({
                suppressScrollX: true
            });
            setTimeout(function() {
                initMultiSelect();
                setPlaceHolder();
            }, 5);
        });
    });

    //Created By Karmi @06/05/2022 For Reject Requisition
    $(document).on('click', ".rejectRequisition", function() {
        var id = $(this).data('id');
        var val = $(this).data('val');
        var msg = $(this).data('msg');
        $.confirm({
            title: 'Confirm!',
            content: 'Are you sure want to Reject this Requisition?',
            type: 'green',
            buttons: {
                ok: {
                    text: "ok!",
                    btnClass: 'btn waves-effect waves-light btn-outline-success',
                    keys: ['enter'],
                    action: function() {
                        $.ajax({
                            url: base_url + controller + '/rejectRequisition',
                            data: {
                                id: id,
                                val: val,
                                msg: msg
                            },
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
                                    $(".modal").modal('hide');
                                    initTable();
                                    toastr.success(data.message, 'Success', {
                                        "showMethod": "slideDown",
                                        "hideMethod": "slideUp",
                                        "closeButton": true,
                                        positionClass: 'toastr toast-bottom-center',
                                        containerId: 'toast-bottom-center',
                                        "progressBar": true
                                    });
                                    // window.location.reload();
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
    });

    $(document).on('click', ".btn-save", function() {
        setTimeout(function() {
            $(".modal-footer .rejectRequisition").remove();
        }, 200);
    });
    $(document).on('click', ".btn-save-close", function() {
        setTimeout(function() {
            $(".modal-footer .rejectRequisition").remove();
        }, 200);
    });
    $(document).on('click', ".btn-close", function() {
        setTimeout(function() {
            $(".modal-footer .rejectRequisition").remove();
        }, 200);
    });
    $(document).on('click', ".close", function() {
        setTimeout(function() {
            $(".modal-footer .rejectRequisition").remove();
        }, 200);
    });
    $(document).on('click', ".approveRequis", function() {
        // var id = $('#id').val();
        var id = $(this).data('id');
        // console.log(id+'----------');
        var rejectBtn = '<a id="rejectBtn" class="btn btn-outline-info rejectRequisition btn-edit permission-modify" data-id="' + id + '" data-val="2" data-msg="Rejected"  href="javascript:void(0)" datatip="Reject Requisition" flow="down"><i class="ti-close"></i> Reject</a>';
        setTimeout(function() {
            $(".modal-footer .rejectRequisition").remove();
            $(".modal-footer").append(rejectBtn);
        }, 200);
    });

});

function storeRequisition(formId, fnsave) {
    console.log($("#current_stock_val").val());
    if ($("#current_stock_val").val() == 0 || $("#current_stock_val").val() == '') {
        $.confirm({
            title: 'Confirm!',
            content: 'Your current stock is empty ,Are you sure want to send requisition ?',
            type: 'red',
            buttons: {
                ok: {
                    text: "ok!",
                    btnClass: 'btn waves-effect waves-light btn-outline-success',
                    keys: ['enter'],
                    action: function() {
                        store(formId, fnsave);
                    }
                },
                cancel: {
                    btnClass: 'btn waves-effect waves-light btn-outline-secondary',
                    action: function() {}
                }
            }
        });
    } else {
        store(formId, fnsave);
    }

}

function changeStatusTab(tableId, $mType, status) {
    $("#" + tableId).attr("data-url", '/getDTRows/' + $mType + '/' + status);
    ssTable.state.clear();
    initTable();
}


function returnMaterial(data){
    var button = data.button;if(button == "" || button == null){button="both";};
	var fnEdit = data.fnedit;if(fnEdit == "" || fnEdit == null){fnEdit="edit";}
	var fnsave = data.fnsave;if(fnsave == "" || fnsave == null){fnsave="save";}
	var savebtn_text = data.savebtn_text;if(savebtn_text == "" || savebtn_text == null){savebtn_text="Save";}
	var sendData = {id:data.id,batch_no:data.batch_no,pending_qty:data.pending_qty,size:data.size};
	if(data.approve_type){sendData = {id:data.id,approve_type:data.approve_type};}
	$.ajax({ 
		type: "POST",   
		url: base_url + controller + '/' + fnEdit,   
		data: sendData,
	}).done(function(response){
		$("#"+data.modal_id).modal();
		$("#"+data.modal_id+' .modal-body').html('');
		$("#"+data.modal_id+' .modal-title').html(data.title);
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
		$('.model-select2').select2({ dropdownParent: $('.model-select2').parent() });
		$("#"+data.modal_id+" .scrollable").perfectScrollbar({suppressScrollX: true});
		initMultiSelect();setPlaceHolder();
	});
}