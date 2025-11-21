$(document).ready(function(){
	
	$(document).on("change", "#transport_name", function() {
		var transVal = $(this).val();
		var transId = $(this).children('option:selected').data('val');
		$("#transport_id").val(transId);
	});

	$("#vehicle_no").attr("autocomplete", "off");
	$('#vehicle_no').typeahead({
		source: function(query, result) {
			$.ajax({
				url: base_url + 'ewayBill/vehicleSearch',
				method: "POST",
				global: false,
				data: {
					query: query
				},
				dataType: "json",
				success: function(data) {
					result($.map(data, function(item) {
						return item;
					}));
				}
			});
		}
	});	
	
	
	$(document).on('change',"#transaction_type", function() {
		$("#from_address").val("");
		$("#from_pincode").val("");
		$("#ship_pincode").val("");
		$("#ship_address").val("");
		$("#from_city").val("");
		$("#from_city").comboSelect();
		$("#from_state").val("");
		$("#from_state").comboSelect();
		$("#ship_city").val("");
		$("#ship_city").comboSelect();
		$("#ship_state").val("");
		$("#ship_state").comboSelect();
		
		var party_id = $("#party_id").val();
		var transaction_type = $("#transaction_type").val();
		$(".transaction_type").html('');
		$(".party_id").html('');
		if (transaction_type == "") {
			$(".transaction_type").html('Transaction Type is Required');
		} else {
			if (party_id == "") {
				$("#transaction_type").val("");
				$(".party_id").html('Party is Required');
				$("#transaction_type").comboSelect();
			} else {
				$.ajax({
					url: base_url + 'ewayBill/getEwbAddress',
					type: 'post',
					data: {
						party_id: party_id,transaction_type:transaction_type
					},
					dataType: 'json',
					success: function(data) {
						$("#from_address").val(data.from_address);
						$("#from_pincode").val(data.from_pincode);
						$("#ship_pincode").val(data.ship_pincode);
						$("#ship_address").val(data.ship_address);
						$("#from_city").html("");
						$("#from_city").html(data.from_city);
						$("#from_city").comboSelect();
						$("#from_state").val(data.from_state);
						$("#from_state").comboSelect();
						$("#ship_city").html("");
						$("#ship_city").html(data.ship_city);
						$("#ship_city").comboSelect();
						$("#ship_state").val(data.ship_state);
						$("#ship_state").comboSelect();
					}
				});
			}
		}
	});
	$(document).on('change',"#from_state",function(){
		var id=$("#from_state").val();
		$.ajax({
			url: base_url + 'parties/getCities',
			type: 'post',
			data: {
				id: id
			},
			dataType: 'json',
			success: function(data) {
				if (data.status == 0) {
					//swal("Sorry...!", data.message, "error");
				} else {
					$("#from_city").html(data.result);					
					$(".single-select").comboSelect();
				}
			}
		});
	});

	$(document).on('change',"#ship_state",function(){
		var id=$("#ship_state").val();
		$.ajax({
			url: base_url + 'parties/getCities',
			type: 'post',
			data: {
				id: id
			},
			dataType: 'json',
			success: function(data) {
				if (data.status == 0) {
					//swal("Sorry...!", data.message, "error");
				} else {
					$("#ship_city").html(data.result);					
					$(".single-select").comboSelect();
				}
			}
		});
	});

	$('#from_address').attr({ maxLength : 120 });
	$('#ship_address').attr({ maxLength : 120 });
});

function ewayBill(data){
	var button = data.button;if(button == "" || button == null){button="both";};
	var fnsave = data.fnsave;if(fnsave == "" || fnsave == null){fnsave="generateEwb";}
	var savebtn_text = '<i class="fa fa-file-text"></i> Generate';
	var sendData = {id:data.id,party_id:data.party_id};
	$.ajax({ 
		type: "POST",   
		url: base_url + '/ewayBill/addEwayBill',   
		data: sendData,
	}).done(function(response){
		$("#"+data.modal_id).modal();
		$("#"+data.modal_id+' .modal-title').html(data.title);
		$("#"+data.modal_id+' .modal-body').html(response);
		$("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);
		$("#"+data.modal_id+" .modal-footer .btn-save").html(savebtn_text);
		$("#"+data.modal_id+" .modal-footer .btn-save").attr('onclick',"generateEwb('"+data.form_id+"','"+fnsave+"');");
		$("#"+data.modal_id+" .modal-footer .btn-save-close").attr('onclick',"generateEwb('"+data.form_id+"','"+fnsave+"','save_close');");
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
		$("#"+data.modal_id+" .scrollable").perfectScrollbar({suppressScrollX: true});
		initMultiSelect();setPlaceHolder();

		$("#transaction_type").trigger('change');
	});
}

function generateEwb(formId,fnsave){
	setPlaceHolder();
	if(fnsave == "" || fnsave == null){fnsave="generateEwb";}
	var form = $('#'+formId)[0];
	var fd = new FormData(form);
	$.ajax({
		url: base_url + 'ewayBill/' + fnsave,
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
			initTable(); $('#'+formId)[0].reset();$(".modal").modal('hide');
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}else{
			initTable(); $('#'+formId)[0].reset();$(".modal").modal('hide');
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}				
	});
}