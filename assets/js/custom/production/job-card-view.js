$(document).ready(function () {
	initMultiSelect();
	$(document).on('click', '.addJobStage', function () {
		var jobid = $('#jobID').val();
		var process_id = $('#stage_id').val();
		$(".stage_id").html("");
		if (jobid != "" && process_id != "") {
			$.ajax({
				type: "POST",
				url: base_url + controller + '/addJobStage',
				data: { id: jobid, process_id: process_id },
				dataType: 'json',
				success: function (data) {
					$('#stageRows').html(""); $('#stageRows').html(data.stageRows);
					$('#stage_id').html(""); $('#stage_id').html(data.pOptions); $('#stage_id').comboSelect();
				}
			});
		} else {
			$(".stage_id").html("Stage is required.");
		}
	});

	$(document).on('click', '.removeJobStage', function () {
		var jobid = $('#jobID').val();
		var process_id = $(this).data('pid');
		$.confirm({
			title: 'Confirm!',
			content: 'Are you sure want to delete this Stage?',
			type: 'red',
			buttons: {
				ok: {
					text: "ok!",
					btnClass: 'btn waves-effect waves-light btn-outline-success',
					keys: ['enter'],
					action: function () {
						if (jobid != "" && process_id != "") {
							$.ajax({
								type: "POST",
								url: base_url + controller + '/removeJobStage',
								data: { id: jobid, process_id: process_id },
								dataType: 'json',
								success: function (data) {
									$('#stageRows').html(""); $('#stageRows').html(data.stageRows);
									$('#stage_id').html(""); $('#stage_id').html(data.pOptions); $('#stage_id').comboSelect();
								}
							});
						}
					}
				},
				cancel: {
					btnClass: 'btn waves-effect waves-light btn-outline-secondary',
					action: function () {

					}
				}
			}
		});
	});

	$("#jobStages tbody").sortable({
		items: 'tr',
		cursor: 'pointer',
		axis: 'y',
		dropOnEmpty: false,
		helper: fixWidthHelper,
		start: function (e, ui) { ui.item.addClass("selected"); },
		stop: function (e, ui) {
			ui.item.removeClass("selected");
			var seq = 1;
			$(this).find("tr").each(function () { $(this).find("td").eq(2).html(seq + 1); seq++; });
		},
		update: function () {
			var ids = '';
			$(this).find("tr").each(function (index) { ids += $(this).attr("id") + ","; });
			var lastChar = ids.slice(-1);
			if (lastChar == ',') { ids = ids.slice(0, -1); }
			var jobid = $('#jobID').val();
			var rnstages = $('#rnstages').val();

			$.ajax({
				url: base_url + controller + '/updateJobProcessSequance',
				type: 'post',
				data: { id: jobid, process_id: ids, rnstages: rnstages },
				dataType: 'json',
				global: false,
				success: function (data) { }
			});
		}
	});

	$(document).on('click', ".requiredMaterial", function () {
		var item_id = $(this).data('product_id');
		var productName = $(this).data('product');
		var orderQty = $(this).data('qty');
		var process_id = $(this).data('process_id');
		var process_name = $(this).data('process_name');

		$.ajax({
			url: base_url + controller + '/getProcessWiseRequiredMaterial',
			data: { process_id: process_id, item_id: item_id, qty: orderQty },
			type: "POST",
			dataType: "json",
			success: function (data) {
				if (data.status == 0) {
					swal("Sorry...!", data.message, "error");
				}
				else {
					$("#productName").html(productName);
					$("#processName").html(process_name);
					$("#orderQty").html(orderQty);
					$("#requiredMaterialModal").modal();
					$("#requiredItems").html("");
					$("#requiredItems").html(data.result);
				}
			}
		});
	});

	$(document).on("click", '#addJobBom', function () {
		var form = $('#job_bom_data')[0];
		var fd = new FormData(form);
		$.ajax({
			url: base_url + controller + '/saveJobBomItem',
			data: fd,
			type: "POST",
			processData: false,
			contentType: false,
			dataType: "json",
		}).done(function (data) {
			if (data.status === 0) {
				$(".error").html("");
				$.each(data.message, function (key, value) { $("." + key).html(value); });
			} else if (data.status == 1) {
				$("#bom_item_id").val(""); $("#bom_item_id").comboSelect();
				$("#bom_qty").val('');
				$("#requiredItems").html("");
				$("#requiredItems").html(data.result);
				toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
			} else {
				toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
			}

		});
	});

	$(document).on('click', ".productionTab", function () {
		location.reload();
	});
	$(document).on('click','.btn-close',function(){
		window.location.reload();
	});

	$(document).on("change", "#rejection_stage", function () {
        var process_id = $(this).val();
        var part_id = $("#product_id").val();
        if (process_id) {
            var job_card_id = $("#job_card_id").val();
            $.ajax({
                url: base_url +  'production/processMovement/getRejRWBy',
                type: 'post',
                data: {
                    process_id: process_id,
                    part_id: part_id,
                    job_card_id: job_card_id
                },
                dataType: 'json',
                success: function (data) {
                    $("#rej_from").html("");
                    $("#rej_from").html(data.rejOption);
                    $("#rej_from").comboSelect();
                }
            });
        } else {
            $("#rej_from").html("<option value=''>Select Rej. From</option>");
            $("#rej_from").comboSelect(); 
        }
    });

    $(document).on("change", "#rework_stage", function () {
        var process_id = $(this).val();
        var part_id = $("#product_id").val();
        if (process_id) {
            var job_card_id = $("#job_card_id").val();
            $.ajax({
                url: base_url + 'production/processMovement/getRejRWBy',
                type: 'post',
                data: {
                    process_id: process_id,
                    part_id: part_id,
                    job_card_id: job_card_id
                },
                dataType: 'json',
                success: function (data) {
                    $("#rw_from").html("");
                    $("#rw_from").html(data.rejOption);
                    $("#rw_from").comboSelect();
                }
            });
        } else {
            $("#rw_from").html("<option value=''>Select Rew. From</option>");
            $("#rw_from").comboSelect();
        }
    });

	
	 $(document).on('click', "#addReworkRow", function () {
        var rw_qty = $("#rw_qty").val();
        var rw_reason = $("#rw_reason :selected").val();
        var rw_from = $("#rw_from :selected").val();
        var rw_reason_code = $("#rw_reason :selected").data('code');
        var rework_reason = $("#rw_reason :selected").data('reason');
        var rw_party_name = $("#rw_from :selected").data('party_name');
        var rw_remark = $("#rw_remark").val();
        var rw_stage = $("#rework_stage").val();
        var rw_stage_name = $("#rework_stage :selected").data('process_name');
        var row_index = $('#reworkReason tbody').find('tr').length;

        var valid = 1;

        $(".rw_qty").html("");
        if (parseFloat(rw_qty) <= 0 || rw_qty == '') {
            $(".rw_qty").html("Rework Qty is required.");
            valid = 0;
        }

        $(".rw_reason").html("");
        if (rw_reason == "") {
            $(".rw_reason").html("Rework Reason is required.");
            valid = 0;
        }

        $(".rw_from").html("");
        if (rw_from == "") {
            $(".rw_from").html("Rework From is required.");
            valid = 0;
        }

        $(".rework_stage").html("");
        if (rw_stage == "") {
            $(".rework_stage").html("Rework Belongs to is required.");
            valid = 0;
        }

        if (valid == 1) {

            var postData = {
                rw_qty: rw_qty,
                rw_reason: rw_reason,
                rw_from: rw_from,
                rw_reason_code: rw_reason_code,
                rework_reason: rework_reason,
                rw_remark: rw_remark,
                rw_party_name: rw_party_name,
                rw_stage: rw_stage,
                rw_stage_name: rw_stage_name,
				row_index:row_index
            };


            AddRowRework(postData);
            $("#rw_qty").val("0");
            $("#rw_reason").val("");
            $("#rw_reason").comboSelect();
            $("#rw_from").val("");
            $("#rw_from").comboSelect();
            $("#rework_stage").val("");
            $("#rework_stage").comboSelect();
            $("#rw_remark").val("");
            $("#rw_qty").focus();

        }
    });
    $(document).on('click', "#addRejectionRow", function () {
        var rej_qty = $("#rej_qty").val();
        var rej_reason = $("#rej_reason :selected").val();
        var rej_from = $("#rej_from :selected").val();
        var rej_reason_code = $("#rej_reason :selected").data('code');
        var rejection_reason = $("#rej_reason :selected").data('reason');
        var rej_party_name = $("#rej_from :selected").data('party_name');
        var rej_remark = $("#rej_remark").val();
        var rej_stage = $("#rejection_stage").val();
        var rej_stage_name = $("#rejection_stage :selected").data('process_name');

        var valid = 1;

        $(".rej_qty").html("");
        if (parseFloat(rej_qty) <= 0 || rej_qty == '') {
            $(".rej_qty").html("Rejection Qty is required.");
            valid = 0;
        }

        $(".rej_reason").html("");
        if (rej_reason == "") {
            $(".rej_reason").html("Rejection Reason is required.");
            valid = 0;
        }

        $(".rej_from").html("");
        if (rej_from == "") {
            $(".rej_from").html("Rejection From is required.");
            valid = 0;
        }

        $(".rejection_stage").html("");
        if (rej_stage == "") {
            $(".rejection_stage").html("Rejection Belongs is required.");
            valid = 0;
        }

        if (valid == 1) {
            var postData = {
                rej_qty: rej_qty,
                rej_reason: rej_reason,
                rej_from: rej_from,
                rej_reason_code: rej_reason_code,
                rejection_reason: rejection_reason,
                rej_remark: rej_remark,
                rej_party_name: rej_party_name,
                rej_stage: rej_stage,
                rej_stage_name: rej_stage_name

            };
            AddRowRejection(postData);
            $("#rej_qty").val("0");
            $("#rej_reason").val("");
            $("#rej_reason").comboSelect();
            $("#rej_from").val("");
            $("#rej_from").comboSelect();
            $("#rejection_stage").val("");
            $("#rejection_stage").comboSelect();
            $("#rej_remark").val("");
            $("#rej_type").val(0);
            $("#rej_ref_id").val(0);
            $("#rej_qty").focus();

        }
    });

    $(document).on('click', ".openMaterialReturnModal", function () {
		var modalId = $(this).data('modal_id');
		var processName = $(this).data('process_name');
		var pendingQty = $(this).data('pending_qty');
		var item_name = $(this).data('item_name');
		var item_id = $(this).data('item_id');
		var job_card_id = $(this).data('job_card_id');
		var wp_qty=$(this).data('wp_qty');
        var dispatch_id = $(this).data('dispatch_id');
		$.ajax({
			type: "POST",
			url: base_url + controller + '/materialReturn',
			data: { job_card_id: job_card_id,item_id:item_id,processName:processName,pendingQty:pendingQty,item_name:item_name,wp_qty:wp_qty,dispatch_id:dispatch_id}
		}).done(function (response) {
			$("#" + modalId).modal({ show: true });
			$("#" + modalId + ' .modal-title').html('Return Material');
			$("#" + modalId + ' .modal-body').html("");
			$("#" + modalId + ' .modal-body').html(response);
			$("#" + modalId + " .modal-body form").attr('id','returnMaterialForm');
			$("#" + modalId + " .modal-footer .btn-close").show();
			$("#" + modalId + " .modal-footer .btn-save").hide();
			$(".single-select").comboSelect();

			$("#" + modalId + " .scrollable").perfectScrollbar({ suppressScrollX: true });
			setTimeout(function () { initMultiSelect(); setPlaceHolder(); }, 5);
		});
	});
    $(document).on('change','#ref_type',function(){
		var ref_type=$(this).val();
		var scrap_location=$("#scrap_store_id").val();
		if(ref_type == 10){
			$(".location").show();
			$(".batchNo").show();
			$("#location_id option").removeAttr("disabled");
			$("#location_id option[value='"+scrap_location+"']").attr("disabled","disabled");
			$("#location_id").comboSelect();
		}
		if(ref_type == 13){
			$(".location").show();
			// $('.batchNo').hide();
			$("#location_id").val(scrap_location);
			$("#location_id option").attr("disabled","disabled");
			$("#location_id option[value='"+scrap_location+"']").removeAttr("disabled");
			$("#location_id").comboSelect();
			
		}
		
		if(ref_type==21){
			$('.location').hide();
			// $('.batchNo').hide();
			$("#location_id").val("");
			$("#location_id").comboSelect();
			// $("#batch_no").val("");
			// $("#batch_no").comboSelect();
		}
	});
    
	$(document).on('change', '#send_to', function() {
		var used_at = $(this).val();
		var out_process_id=$("#out_process_id").val();
		$.ajax({
			type: "POST",
			url: base_url + 'production/jobcard/' + '/getHandoverData',
			data: {
				used_at: used_at,
				out_process_id:out_process_id
			},
			dataType: 'json',
		}).done(function(response) {
			$("#handover_to").html(response.handover);
			//$("#handover_to").comboSelect();
			$('#handover_to').select2({ dropdownParent: $('#handover_to').parent() });
		});
	});

	$(document).on('change', '.asignOperator', function() {
		var machine_id = $("#machine_id").val();
		var shift_id = $("#shift_id").val();
		if(machine_id!='' && shift_id!=''){
			$.ajax({
				type: "POST",
				url: base_url  + 'production/processMovement/getAsignOperator',
				data: {
					machine_id: machine_id,
					shift_id:shift_id
				},
				dataType: 'json',
			}).done(function(response) {
				$("#operator_id").val(response.operator_id);
				$("#operator_id").comboSelect();
			});
		}
	});

	$(document).on("keyup",".qtyCal", function(){
        var rej_qty = ($("#rej_qty").val() !='')?$("#rej_qty").val():0;
        var rw_qty = ($("#rw_qty").val() !='')?$("#rw_qty").val():0;
        var hold_qty = ($("#hold_qty").val() !='')?$("#hold_qty").val():0;
        
		var okQty=parseFloat($("#production_qty").val())-rej_qty-rw_qty-hold_qty;
      
		$("#out_qty").val(okQty);
    });
    
    $(document).on('click', "#addIdleRow", function () {
        var idle_time = $("#idle_time").val();
        var idle_reason = $("#idle_reason :selected").val();
		
		var idle_remark = $("#idle_remark").val();

        var valid = 1;

        $(".idle_time").html("");
        if (parseFloat(idle_time) <= 0 || idle_time == '') {
            $(".idle_time").html("Idle Time is required.");
            valid = 0;
        }

        $(".idle_reason").html("");
        if (idle_reason == "") {
            $(".idle_reason").html("Idle Reason is required.");
            valid = 0;
        }

        if (valid == 1) {
			var idle_reason_text = $("#idle_reason :selected").text();

            var postData = {
                idle_time:idle_time,
				idle_reason:idle_reason,
				idle_remark:idle_remark,
				idle_reason_text:idle_reason_text
            };
            AddRowIdle(postData);
            $("#idle_time").val("");
            $("#idle_reason").val("");
            $("#idle_reason").select2();
            $("#idle_remark").val("");
           
            $("#idle_time").focus();
        }
    });
	
	$(document).on("click",".idle_btn",function(){
		if($('section.d-none').length){
			$("#idleTime").fadeIn(500);
			$("#idleTime").removeClass("d-none");
			$("#idleTime").addClass("d-block");
		}else{
			$("#idleTime").fadeOut(1000);
			$("#idleTime").removeClass("d-block");
			$("#idleTime").addClass("d-none");
		}
	});
});

function fixWidthHelper(e, ui) {
	ui.children().each(function () {
		$(this).width($(this).width());
	});
	return ui;
}

function AddStageRow(data) {
	$('table#purchaseEnqItems tr#noData').remove();
	//Get the reference of the Table's TBODY element.
	var tblName = "purchaseEnqItems";

	var tBody = $("#" + tblName + " > TBODY")[0];

	//Add Row.
	row = tBody.insertRow(-1);

	//Add index cell
	var countRow = $('#' + tblName + ' tbody tr:last').index() + 1;
	var cell = $(row.insertCell(-1));
	cell.html(countRow);
	cell.attr("style", "width:5%;");

	cell = $(row.insertCell(-1));
	cell.html(data.item_name + '<input type="hidden" name="item_name[]" value="' + data.item_name + '"><input type="hidden" name="trans_id[]" value="' + data.trans_id + '" /><input type="hidden" name="item_remark[]" value="' + data.item_remark + '">');
}


function removeBomItem(id, job_card_id) {
	var send_data = { id: id, job_card_id: job_card_id };
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to Remove this Bom Item?',
		type: 'red',
		buttons: {
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function () {
					$.ajax({
						url: base_url + controller + '/deleteBomItem',
						data: send_data,
						type: "POST",
						dataType: "json",
						success: function (data) {
							if (data.status == 0) {
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
							else {
								$("#requiredItems").html("");
								$("#requiredItems").html(data.result);

								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
						}
					});
				}
			},
			cancel: {
				btnClass: 'btn waves-effect waves-light btn-outline-secondary',
				action: function () {

				}
			}
		}
	});
}

function processMovement(data){
	var button = data.button;if(button == "" || button == null){button="both";};
	var fnEdit = data.fnedit;if(fnEdit == "" || fnEdit == null){fnEdit="edit";}
	var fnsave = data.fnsave;if(fnsave == "" || fnsave == null){fnsave="save";}
	var savebtn_text = data.savebtn_text;if(savebtn_text == "" || savebtn_text == null){savebtn_text="Save";}
	var sendData = {id:data.id,ref_id:data.ref_id,p_qty : data.p_qty};
	if(data.approve_type){sendData = {id:data.id,approve_type:data.approve_type};}
	$.ajax({ 
		type: "POST",   
		url: base_url + 'production/processMovement/' + fnEdit,   
		data: sendData,
	}).done(function(response){
		$("#"+data.modal_id).modal();
		$("#"+data.modal_id+' .modal-title').html(data.title);
		$("#"+data.modal_id+' .modal-body').html(response);
		$("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);

		if(data.btnSave == "other"){
			$("#"+data.modal_id+" .btn-save-other").attr('onclick',"saveMovement('"+data.form_id+"','"+fnsave+"');");
		}else{
			$("#"+data.modal_id+" .modal-footer .btn-save").attr('onclick',"saveMovement('"+data.form_id+"','"+fnsave+"');");
		}
		
		$("#"+data.modal_id+" .modal-footer .btn-close").attr('data-modal_id',data.form_id);
		if(button == "close"){
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").hide();
		}else if(button == "save"){
			$("#"+data.modal_id+" .modal-footer .btn-close").hide();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
		}else{
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
		}
		initModalSelect();
		$(".single-select").comboSelect();
		$('.model-select2').select2({ dropdownParent: $('.model-select2').parent() });
		$("#"+data.modal_id+" .scrollable").perfectScrollbar({suppressScrollX: true});
		initMultiSelect();setPlaceHolder();
	});
}

function saveMovement(formId,fnsave){
	setPlaceHolder();
	if(fnsave == "" || fnsave == null){fnsave="save";}
	var form = $('#'+formId)[0];
	var fd = new FormData(form);
	$.ajax({
		url: base_url + 'production/processMovement/' + fnsave,
		data:fd,
		type: "POST",
		processData:false,
		contentType:false,
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {$("."+key).html(value);});
		}else if(data.status==1){			
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
			$("#movementTransData").html(data.transHtml);

			var pending_qty = $("#pend_qty").val();
			var qty = $("#qty").val();
			var newPendQty = parseFloat(parseFloat(pending_qty) - parseFloat(qty));
			$("#pending_qty").html(newPendQty);
			$("#pend_qty").val(newPendQty);
			
			$("#send_to").val("0");
			$("#send_to").trigger('change');
			$("#qty").val("");
			$("#remark").val("");			
		}else{			
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
			$("#movementTransData").html(data.transHtml);
		}				
	});
}

function trashMovement(id,qty){
	var send_data = { id:id };
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to delete this Record?',
		type: 'red',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + 'production/processMovement/deleteMovement',
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data)
						{
							if(data.status==0 || data.status==2){
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}else{
								$("#movementTransData").html(data.transHtml);

								var pending_qty = $("#pend_qty").val();
								var newPendQty = parseFloat(parseFloat(pending_qty) + parseFloat(qty));
								$("#pending_qty").html(newPendQty);
								$("#pend_qty").val(newPendQty);

								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
						}
					});
				}
			},
			cancel: {
                btnClass: 'btn waves-effect waves-light btn-outline-secondary',
                action: function(){

				}
            }
		}
	});
}

function receiveStoredMaterial(data){
	var button = data.button;if(button == "" || button == null){button="both";};
	var fnEdit = data.fnedit;if(fnEdit == "" || fnEdit == null){fnEdit="edit";}
	var fnsave = data.fnsave;if(fnsave == "" || fnsave == null){fnsave="save";}
	var savebtn_text = data.savebtn_text;if(savebtn_text == "" || savebtn_text == null){savebtn_text="Save";}
	var sendData = {job_card_id:data.job_card_id,job_approval_id:data.job_approval_id};
	if(data.approve_type){sendData = {id:data.id,approve_type:data.approve_type};}
	$.ajax({ 
		type: "POST",   
		url: base_url + 'production/processMovement/' + fnEdit,   
		data: sendData,
	}).done(function(response){
		$("#"+data.modal_id).modal();
		$("#"+data.modal_id+' .modal-title').html(data.title);
		$("#"+data.modal_id+' .modal-body').html(response);
		$("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);

		$("#"+data.modal_id+" .modal-footer .btn-save").attr('onclick',"saveReceiveStoredMaterial('"+data.form_id+"','"+fnsave+"');");

		$("#"+data.modal_id+" .modal-footer .btn-close").attr('data-modal_id',data.form_id);
		if(button == "close"){
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").hide();
		}else if(button == "save"){
			$("#"+data.modal_id+" .modal-footer .btn-close").hide();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
		}else{
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
		}
		initModalSelect();
		$(".single-select").comboSelect();
		$('.model-select2').select2({ dropdownParent: $('.model-select2').parent() });
		$("#"+data.modal_id+" .scrollable").perfectScrollbar({suppressScrollX: true});
		initMultiSelect();setPlaceHolder();
	});
}

function saveReceiveStoredMaterial(formId,fnsave){
	var form = $('#'+formId)[0];
	var fd = new FormData(form);
	$.ajax({
		url: base_url + 'production/processMovement/' + fnsave,
		data:fd,
		type: "POST",
		processData:false,
		contentType:false,
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {$("."+key).html(value);});
		}else if(data.status==1){	
			$(".modal").modal('hide');		
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
			window.location.reload();
		}else{			
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}				
	});
}

function acceptInward(data){
	$("#"+data.modal_id).modal();
	$("#"+data.modal_id+ " #job_approval_id").val(data.id);
	$("#"+data.modal_id+ " #pending_act_qty").html(data.pending_qty);
	setPlaceHolder();
}

function saveAcceptedQty(formId){
	var form = $('#'+formId)[0];
	var fd = new FormData(form);
	$.ajax({
		url: base_url + 'production/processMovement/saveAcceptedQty',
		data:fd,
		type: "POST",
		processData:false,
		contentType:false,
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {$("."+key).html(value);});
		}else if(data.status==1){			
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
			window.location.reload();
		}else{			
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}				
	});
}

function outward(data){
	var button = data.button;
	$.ajax({ 
		type: "POST",   
		url: base_url + 'production/processMovement/processApproved',   
		data: {id:data.id}
	}).done(function(response){
		$("#"+data.modal_id).modal();
		$("#"+data.modal_id+' .modal-title').html(data.title);
		$("#"+data.modal_id+' .modal-body').html(response);
		$("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);
		$("#"+data.modal_id+" .modal-footer .btn-save").attr('onclick',"store('"+data.form_id+"','"+data.fnsave+"');");
		if(data.button == "close"){
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").hide();
		}else if(data.button == "save"){
			$("#"+data.modal_id+" .modal-footer .btn-close").hide();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
		}else{
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
		}
		$(".single-select").comboSelect();
		setPlaceHolder();
		initMultiSelect();
	});
}

function saveOutward(formId){
    var fd = $('#'+formId).serialize();
    $.ajax({
		url: base_url + 'production/processMovement/save',
		data:fd,
		type: "POST",
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {$("."+key).html(value);});
		}else if(data.status==1){
			$("#pending_qty").html(data.pending_qty);

			$("#out_qty").val("");
			$("#remark").val("");
			$("#wp_qty").val("");
			$("#vendor_id").val("");
			$("#machine_id").val("");
			$("#shift_id").val("");
			$("#operator_id").val("");

            $("#vendor_id").comboSelect;
            $("#machine_id").comboSelect;
			$("#shift_id").comboSelect;
			$("#operator_id").comboSelect;
            $("#rejectionReasonData").html("");
			$("#outwardTransData").html(data.outwardTrans);

			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}else{
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}				
	});
}

function trashOutward(id,functionName,name='Record'){
	var send_data = { id:id };
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to delete this '+name+'?',
		type: 'red',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + 'production/processMovement/'+functionName,
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data)
						{
							if(data.status==0)
							{
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
							else
							{
								$("#pending_qty").html(data.pending_qty);
								$("#outwardTransData").html(data.outwardTrans);
								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
						}
					});
				}
			},
			cancel: {
                btnClass: 'btn waves-effect waves-light btn-outline-secondary',
                action: function(){

				}
            }
		}
	});
}

function storeLocation(data){
	var button = data.button;
	$.ajax({ 
		type: "POST",   
		url: base_url + 'production/processMovement/storeLocation',   
		data: {id:data.id,transid:data.transid,ref_batch:data.ref_batch,remark:data.remark}
	}).done(function(response){
		$("#"+data.modal_id).modal();
		$("#"+data.modal_id+' .modal-title').html(data.title);
		$("#"+data.modal_id+' .modal-body').html(response);
		$("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);
		$("#"+data.modal_id+" .modal-footer .btn-save").attr('onclick',"store('"+data.form_id+"');");
		if(data.button == "close"){
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").hide();
		}else if(data.button == "save"){
			$("#"+data.modal_id+" .modal-footer .btn-close").hide();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
		}else{
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
		}
		$(".single-select").comboSelect();
		//$(".select2").select2();
		setPlaceHolder();
		initMultiSelect();
	});
}

function saveStoreLocation(formId){
    var fd = $('#'+formId).serialize();
    $.ajax({
		url: base_url + 'production/processMovement/saveStoreLocation',
		data:fd,
		type: "POST",
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {$("."+key).html(value);});
		}else if(data.status==1){
			initTable(); $("#storeLocationData").html(data.htmlData);
			$("#unstoredQty").html(data.unstored_qty);
			// $('#'+formId)[0].reset();
			$("#qty").val();
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}else{
			initTable();
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}				
	});
}

// For Rework Data
function AddRowRework(data) {

    $('table#reworkReason tr#noData').remove();

    //Get the reference of the Table's TBODY element.
    var tblName = "reworkReason";

    var tBody = $("#" + tblName + " > TBODY")[0];


    row = tBody.insertRow(-1);
	var index = $('#' + tblName + ' tbody tr:last').index();
    var countRow = $('#' + tblName + ' tbody tr:last').index() + 1;
    var cell = $(row.insertCell(-1));
    cell.html(countRow);
    cell.attr("style", "width:5%;");

    var rework_qty_input = $("<input/>", {
        type: "hidden",
        name: "rework_reason[" + index + "][rw_qty]",
        value: data.rw_qty,
        class: "rw_sum"
    });
    cell = $(row.insertCell(-1));

    var rwQty = $('<a href="javascript:void(0)">' + data.rw_qty + '</a>');
    rwQty.attr("onclick", "convertToOKQty(" + JSON.stringify(data) + ",this);");
    cell.html(data.rw_qty);
    cell.append(rework_qty_input);
    cell.attr("style", "width:20%;");

    var rw_reason_input = $("<input/>", {
        type: "hidden",
        name: "rework_reason[" + index + "][rw_reason]",
        value: data.rw_reason
    });

    cell = $(row.insertCell(-1));
    cell.html(data.rework_reason);
    cell.append(rw_reason_input);
    cell.attr("style", "width:20%;");

    var rw_stage_input = $("<input/>", {
        type: "hidden",
        name: "rework_reason[" + index + "][rw_stage]",
        value: data.rw_stage
    });
    var rw_stage_name_input = $("<input/>", {
        type: "hidden",
        name: "rework_reason[" + index + "][rw_stage_name]",
        value: data.rw_stage_name
    });
    cell = $(row.insertCell(-1));
    cell.html(data.rw_stage_name);
    cell.append(rw_stage_input);
    cell.append(rw_stage_name_input);
    cell.attr("style", "width:20%;");

    var rw_from_input = $("<input/>", {
        type: "hidden",
        name: "rework_reason[" + index + "][rw_from]",
        value: data.rw_from
    });
    var rw_party_name_input = $("<input/>", {
        type: "hidden",
        name: "rework_reason[" + index + "][rw_party_name]",
        value: data.rw_party_name
    });
    cell = $(row.insertCell(-1));
    cell.html(data.rw_party_name);
    cell.append(rw_from_input);
    cell.append(rw_party_name_input);

    var rw_remark_input = $("<input/>", {
        type: "hidden",
        name: "rework_reason[" + index + "][rw_remark]",
        value: data.rw_remark
    });
    var rework_reason_input = $("<input/>", {
        type: "hidden",
        name: "rework_reason[" + index + "][rework_reason]",
        value: data.rework_reason
    });
    cell = $(row.insertCell(-1));
    cell.html(data.rw_remark);
    cell.append(rw_remark_input);
    cell.append(rework_reason_input);
    cell.attr("style", "width:20%;");

    //Add Button cell.
    cell = $(row.insertCell(-1));

    var btnRemove = $('<button><i class="ti-trash"></i></button>');
    btnRemove.attr("type", "button");
    btnRemove.attr("onclick", "RemoveRework(this);");
    btnRemove.attr("style", "margin-left:2px;");
    btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");

    cell.append(btnRemove);
    cell.attr("class", "text-center");
    cell.attr("style", "width:5%;");

}

function RemoveRework(button) {
    //Determine the reference of the Row using the Button.
    var row = $(button).closest("TR");
    var table = $("#reworkReason")[0];
    table.deleteRow(row[0].rowIndex);
    $('#idleReasons tbody tr td:nth-child(1)').each(function (idx, ele) {
        ele.textContent = idx + 1;
    });
    var countTR = $('#idleReasons tbody tr:last').index() + 1;
    if (countTR == 0) {
        $("#idleReasonData").html('<tr id="noData"><td colspan="6" class="text-center">No data available in table</td></tr>');
    }
    $(".qtyCal").trigger('keyup');
};

// For Rejection Data
function AddRowRejection(data) {
    //console.log(data.rej_qty);
    $('table#rejectionReason tr#noData').remove();

    //Get the reference of the Table's TBODY element.
    var tblName = "rejectionReason";

    var tBody = $("#" + tblName + " > TBODY")[0];
    row = tBody.insertRow(-1);

    var index = $('#' + tblName + ' tbody tr:last').index();
    var countRow = $('#' + tblName + ' tbody tr:last').index() + 1;
    var cell = $(row.insertCell(-1));
    cell.html(countRow);
    cell.attr("style", "width:5%;");

    var rejection_qty_input = $("<input/>", {
        type: "hidden",
        name: "rejection_reason[" + index + "][rej_qty]",
        value: data.rej_qty,
        class: "rej_sum"
    });

    var rejection_qty_input = $("<input/>", {
        type: "hidden",
        name: "rejection_reason[" + index + "][rej_qty]",
        value: data.rej_qty,
        class: "rej_sum"
    });
    cell = $(row.insertCell(-1));
    cell.html(data.rej_qty);
    cell.append(rejection_qty_input);
    cell.attr("style", "width:20%;");

    var rej_reason_input = $("<input/>", {
        type: "hidden",
        name: "rejection_reason[" + index + "][rej_reason]",
        value: data.rej_reason
    });
    
    cell = $(row.insertCell(-1));
    cell.html(data.rejection_reason);
    cell.append(rej_reason_input);
    cell.attr("style", "width:20%;");

    var rej_stage_input = $("<input/>", {
        type: "hidden",
        name: "rejection_reason[" + index + "][rej_stage]",
        value: data.rej_stage
    });
    var rej_stage_name_input = $("<input/>", {
        type: "hidden",
        name: "rejection_reason[" + index + "][rej_stage_name]",
        value: data.rej_stage_name
    });
    cell = $(row.insertCell(-1));
    cell.html(data.rej_stage_name);
    cell.append(rej_stage_input);
    cell.append(rej_stage_name_input);
    cell.attr("style", "width:20%;");

    var rej_from_input = $("<input/>", {
        type: "hidden",
        name: "rejection_reason[" + index + "][rej_from]",
        value: data.rej_from
    });
    var rej_party_name_input = $("<input/>", {
        type: "hidden",
        name: "rejection_reason[" + index + "][rej_party_name]",
        value: data.rej_party_name
    });
    cell = $(row.insertCell(-1));
    cell.html(data.rej_party_name);
    cell.append(rej_from_input);
    cell.append(rej_party_name_input);
    cell.attr("style", "width:20%;");

    var rej_remark_input = $("<input/>", {
        type: "hidden",
        name: "rejection_reason[" + index + "][rej_remark]",
        value: data.rej_remark
    });
    var rejection_reason_input = $("<input/>", {
        type: "hidden",
        name: "rejection_reason[" + index + "][rejection_reason]",
        value: data.rejection_reason
    });
    cell = $(row.insertCell(-1));
    cell.html(data.rej_remark);
    cell.append(rej_remark_input);
    cell.append(rejection_reason_input);
    cell.attr("style", "width:20%;");

    //Add Button cell.
    cell = $(row.insertCell(-1));


    var btnRemove = $('<button><i class="ti-trash"></i></button>');
    btnRemove.attr("type", "button");
    btnRemove.attr("onclick", "RemoveRejection(this);");
    btnRemove.attr("style", "margin-left:4px;");
    btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");


    cell.append(btnRemove);
    cell.attr("class", "text-center");
    cell.attr("style", "width:15%;");

    $(".qtyCal").trigger('keyup');
}

function RemoveRejection(button) {

    //Determine the reference of the Row using the Button.
    var row = $(button).closest("TR");
    var table = $("#rejectionReason")[0];
    table.deleteRow(row[0].rowIndex);
    $('#idleReasons tbody tr td:nth-child(1)').each(function (idx, ele) {
        ele.textContent = idx + 1;
    });
    var countTR = $('#idleReasons tbody tr:last').index() + 1;
    if (countTR == 0) {
        $("#idleReasonData").html('<tr id="noData"><td colspan="6" class="text-center">No data available in table</td></tr>');
    }
    $(".qtyCal").trigger('keyup');
};


function trashStockTrans(id,name='Record'){
	var send_data = { id:id };
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to delete this '+name+'?',
		type: 'red',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + 'production/processMovement/deleteStoreLocationTrans',
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data)
						{
							if(data.status==0)
							{
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
							else
							{
								initTable(); $("#storeLocationData").html(data.htmlData);
								$("#unstoredQty").html(data.unstored_qty);
								//getProcessWiseData();
								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
						}
					});
				}
			},
			cancel: {
                btnClass: 'btn waves-effect waves-light btn-outline-secondary',
                action: function(){

				}
            }
		}
	});
}

function saveMaterialReturn() {
	var fd = $('#returnMaterialForm').serialize();
	$.ajax({
		url: base_url + controller + '/saveMaterialReturn',
		data: fd,
		type: "POST",
		dataType: "json",
		success: function (data) {
			if (data.status === 0) {
				$(".error").html("");
				$.each(data.message, function (key, value) {
					$("." + key).html(value);
				});
			}
			else if (data.status == 1) {
				toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });

				// $('#returnMaterialForm')[0].reset();

				var obj = data.result;
				$("#qty_rs").val("");
				$("#qty").val("");
				$("#returnScrapData").html("");
				$("#returnScrapData").html(obj.resultHtml);
                $("#pendingQty").html(data.pending_qty);
			}
			else {
				toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
			}
		}
	});
};

function deleteMaterialReturn(id, qty, name = 'Record') {
	var job_card_id = $("#job_card_id").val();
	var page_process_id = $("#in_process_id").val();

	var send_data = { id: id, job_card_id: job_card_id, page_process_id: page_process_id };
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to delete this ' + name + '?',
		type: 'red',
		buttons: {
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function () {
					$.ajax({
						url: base_url +controller+'/deleteMaterialReturn',
						data: send_data,
						type: "POST",
						dataType: "json",
						success: function (data) {
							if (data.status == 0) {
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
							else {
								$("#pendingQty").html(data.pending_qty);
								var obj = data.result;
								$("#returnScrapData").html("");;
								$("#returnScrapData").html(obj.resultHtml);

								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
						}
					});
				}
			},
			cancel: {
				btnClass: 'btn waves-effect waves-light btn-outline-secondary',
				action: function () {

				}
			}
		}
	});
}


function saveRework(formId){
    var fd = $('#'+formId).serialize();
    $.ajax({
		url: base_url + 'production/processMovement/saveRework',
		data:fd,
		type: "POST",
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {$("."+key).html(value);});
		}else if(data.status==1){
			$("#pending_qty").html(data.pending_qty);

			$("#out_qty").val("");
			$("#remark").val("");
			$("#wp_qty").val("");
			$("#vendor_id").val("");
			$("#machine_id").val("");
			$("#shift_id").val("");
			$("#operator_id").val("");

            $("#vendor_id").comboSelect;
            $("#machine_id").comboSelect;
			$("#shift_id").comboSelect;
			$("#operator_id").comboSelect;
            $("#rejectionReasonData").html("");
			$("#outwardTransData").html(data.outwardTrans);

			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}else{
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}				
	});
}

function setupRequest(data){
	var button = data.button;
	$.ajax({ 
		type: "POST",   
		url: base_url + 'production/jobcard/setupRequest',   
		data: {id:data.id}
	}).done(function(response){
		$("#"+data.modal_id).modal();
		$("#"+data.modal_id+' .modal-title').html(data.title);
		$("#"+data.modal_id+' .modal-body').html(response);
		$("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);
		$("#"+data.modal_id+" .modal-footer .btn-save").attr('onclick',"saveSetupReq('"+data.form_id+"','"+data.fnsave+"');");
		if(data.button == "close"){
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").hide();
		}else if(data.button == "save"){
			$("#"+data.modal_id+" .modal-footer .btn-close").hide();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
		}else{
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
		}
		$(".single-select").comboSelect();
		setPlaceHolder();
		initMultiSelect();
	});

}
function saveSetupReq(formId,fnsave){
	setPlaceHolder();
	if(fnsave == "" || fnsave == null){fnsave="save";}
	var form = $('#'+formId)[0];
	var fd = new FormData(form);
	$.ajax({
		url: base_url +'production/jobcard/setupRequestSave',
		data:fd,
		type: "POST",
		processData:false,
		contentType:false,
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {$("."+key).html(value);});
		}else if(data.status==1){
			$("#setupReqTbody").html();
			$("#setupReqTbody").html(data.htmlData);
			$("#setupSaveBtn").attr("disabled",true);
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}else{
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}
				
	});
}
function trashSetupReq(id,job_approval_id){
	var send_data = { id:id,job_approval_id };
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to delete this request?',
		type: 'red',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + 'production/jobcard/trashSetupReq',
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data)
						{
							if(data.status==0)
							{
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
							else
							{
								$("#setupReqTbody").html();
								$("#setupReqTbody").html(data.htmlData);
								$("#setupSaveBtn").removeAttr("disabled");
								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
						}
					});
				}
			},
			cancel: {
                btnClass: 'btn waves-effect waves-light btn-outline-secondary',
                action: function(){

				}
            }
		}
	});
}

// For Idle Time Data
function AddRowIdle(data) {
	//Get the reference of the Table's TBODY element.
	var tblName = "idleTimeTable";

	$('table#'+ tblName +' tr#noData').remove();    

	var tBody = $("#" + tblName + " > tbody")[0];
	row = tBody.insertRow(-1);

	var index = $('#' + tblName + ' tbody tr:last').index();
	var countRow = $('#' + tblName + ' tbody tr:last').index() + 1;
	var cell = $(row.insertCell(-1));
	cell.html(countRow);
	cell.attr("style", "width:5%;");

	var idleTimeIp = $("<input/>", {
		type: "hidden",
		name: "idle_time[" + index + "][idle_time]",
		value: data.idle_time,
	});
	cell = $(row.insertCell(-1));
	cell.html(data.idle_time);
	cell.append(idleTimeIp);
	cell.attr("style", "width:20%;");

	var idleTimeReasonIp = $("<input/>", {
		type: "hidden",
		name: "idle_time[" + index + "][idle_reason]",
		value: data.idle_reason
	});    
	cell = $(row.insertCell(-1));
	cell.html(data.idle_reason_text);
	cell.append(idleTimeReasonIp);
	cell.attr("style", "width:20%;");

 

	var idleRemarkIp = $("<input/>", {
		type: "hidden",
		name: "idle_time[" + index + "][idle_remark]",
		value: data.idle_remark
	});
	
	cell = $(row.insertCell(-1));
	cell.html(data.idle_remark);
	cell.append(idleRemarkIp);
	cell.attr("style", "width:20%;");

	//Add Button cell.
	cell = $(row.insertCell(-1));


	var btnRemove = $('<button><i class="fas fa-trash"></i></button>');
	btnRemove.attr("type", "button");
	btnRemove.attr("onclick", "RemoveIdle(this);");
	btnRemove.attr("style", "margin-left:4px;");
	btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");
	cell.append(btnRemove);
	cell.attr("class", "text-center");
	cell.attr("style", "width:15%;");
}

function RemoveIdle(button) {
    //Determine the reference of the Row using the Button.
    var row = $(button).closest("TR");
    var table = $("#idleTimeTable")[0];
    table.deleteRow(row[0].rowIndex);
    $('#idleTimeTable tbody tr td:nth-child(1)').each(function (idx, ele) {
        ele.textContent = idx + 1;
    });
    var countTR = $('#idleTimeTable tbody tr:last').index() + 1;
    if (countTR == 0) {
        $("#idleTimeTable tbody").html('<tr id="noData"><td colspan="5" class="text-center">No data available in table</td></tr>');
    }
};
