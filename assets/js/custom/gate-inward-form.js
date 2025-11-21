$(document).ready(function(){
    $(".expDateDiv").hide();
    $(document).on('change','#item_id',function(){
        var item_id = $(this).val();
        if(item_id != ""){
            var item_type = $(this).find(':selected').data('item_type');
            $("#item_type").val(item_type); 
            $("#item_stock_type").val($(this).find(':selected').data('item_stock_type')); 
            $("#po_trans_id").val($(this).find(':selected').data('po_trans_id')); 

            /*if($("#item_stock_type").val() == 1){
                $(".batchDiv").show();
            }else{
                $(".batchDiv").hide(); 
            }*/
            if(item_type == 2){ $(".expDateDiv").show();  }else{ $(".expDateDiv").hide(); }
           
            var trans_no = $("#trans_no").val();
            var trans_prefix = $("#trans_prefix").val();  
            
            if(item_type == 3 || item_type == 1){
                $('.giNo').val(trans_prefix+'/'+trans_no);
            }
        }
    });
    
    $(document).on('change','#po_id',function(){
        var po_id = $(this).val();
        var po_trans_id = $("#po_trans_id").val();
        var grn_type = $("#grn_type").val();
        $.ajax({
            url : base_url + controller + '/getPoItemOptions',
            type: 'post',
            data:{po_id:po_id,po_trans_id:po_trans_id,grn_type:grn_type},
            dataType:'json',
            success:function(data){
                $("#item_id").html("");
                $("#item_id").html(data.options);
                $("#item_id").comboSelect();
            }
        });
    });
   
    $(document).on('click','.addBatch',function(){
        var item_stock_type = $("#item_stock_type").val();
        var item_id = $("#item_id").val();
        var item_type = $("#item_type").val();
        var formData = {};
        formData.mir_id = $("#mir_id").val();
        formData.mir_trans_id = $("#mir_trans_id").val();
        formData.location_id = $("#location_id").val();
        formData.location_name = $("#location_id :selected").text();
        formData.po_number = $("#po_id :selected").data('po_no');
        formData.item_name = $("#item_id :selected").data('item_name');
        formData.heat_no = $("#heat_no").val();
        formData.mill_heat_no = $("#mill_heat_no").val();
        formData.batch_qty = $("#qty").val();
        formData.po_trans_id = $("#po_trans_id").val();;
        formData.po_id = $("#po_id").val();
        formData.item_stock_type = item_stock_type;
        formData.item_id = item_id;
        formData.item_type = item_type;
        formData.batch_no = $("#batch_no").val();
        formData.row_index = $("#row_index").val();
        formData.expire_date = $("#expire_date").val();

        formData.trans_status = 0; 
               

        $(".error").html("");

        var valid = 1;
        if(formData.item_id == ""){ $('.item_id').html("Item is required."); valid=0; }
        if(formData.batch_qty == ""){ $('.qty').html("Qty is required."); valid=0; }
        if(formData.location_id == ""){ $('.location_id').html("Location is required."); valid=0; }
        if(formData.expire_date == "" && item_type == 2){ $('.expire_date').html("Expiry Date is required."); valid=0; }
        if(item_stock_type == 1 && formData.batch_no == ""){ $('.batch_no').html("Batch No. is required."); valid=0; }

        if(valid == 1){
           
            AddBatchRow(formData);

            $("#location_id").val("");$("#location_id").select2();
            $("#heat_no").val("");
            $("#mir_id").val("");
            $("#mir_trans_id").val("");
            $("#row_index").val("");
            $("#batch_no").val("");
            $("#mill_heat_no").val("");
            $("#qty").val("");
            $("#item_stock_type").val("");
            $("#item_type").val("");
            $("#item_id").val("");
            $("#item_id").comboSelect();
            $("#expire_date").val("");
            
        }
    });
    
    $(document).on('click',".createGI",function(){
        var id = $(this).data('id');
        var grn_type = $(this).data('grn_type');
        var modalId = $(this).data('modal_id');
        var button = $(this).data('button');
		var title = $(this).data('form_title');
		var formId = $(this).data('form_id');

        $.ajax({ 
            type: "POST",   
            url: base_url + 'gateInward/createGI',   
            data: {id:id,grn_type:grn_type}
        }).done(function(response){
            $("#"+modalId).modal();
			$("#"+modalId+' .modal-title').html(title);
            $("#"+modalId+' .modal-body').html(response);
            $("#"+modalId+" .modal-body form").attr('id',formId);
            $("#"+modalId+" .modal-footer .btn-save").attr('onclick',"store('"+formId+"');");
            if(button == "close"){
                $("#"+modalId+" .modal-footer .btn-close").show();
                $("#"+modalId+" .modal-footer .btn-save").hide();
            }else if(button == "save"){
                $("#"+modalId+" .modal-footer .btn-close").hide();
                $("#"+modalId+" .modal-footer .btn-save").show();
            }else{
                $("#"+modalId+" .modal-footer .btn-close").show();
                $("#"+modalId+" .modal-footer .btn-save").show();
            }
			$(".single-select").comboSelect();setPlaceHolder();
			$(".select2").select2();
			$('.model-select2').select2({ dropdownParent: $('.model-select2').parent() });
        });
    });
});

function AddBatchRow(data){
    $('table#batchTable tr#noData').remove();
    //Get the reference of the Table's TBODY element.
	var tblName = "batchTable";
	
	var tBody = $("#"+tblName+" > TBODY")[0];
	
	//Add Row.
	if(data.row_index != ""){
		var trRow = data.row_index;
		//$("tr").eq(trRow).remove();
		$("#"+tblName+" tbody tr:eq("+trRow+")").remove();
	}
	
	var ind = (data.row_index == "")?-1:data.row_index;
	row = tBody.insertRow(ind);

    //Add index cell
	var countRow = $('#'+tblName+' tbody tr:last').index() + 1;
	var cell = $(row.insertCell(-1));
	cell.html(countRow);
	cell.attr("style","width:5%;");	

    var poIdInput = $("<input/>",{type:"hidden",name:"po_id[]",value:data.po_id});
    var poTransIdInput = $("<input/>",{type:"hidden",name:"po_trans_id[]",value:data.po_trans_id});
    var itemIdInput = $("<input/>",{type:"hidden",name:"item_id[]",value:data.item_id});
    var itemTypeInput = $("<input/>",{type:"hidden",name:"item_type[]",value:data.item_type});
    var itemStockInput = $("<input/>",{type:"hidden",name:"item_stock_type[]",value:data.item_stock_type});
    var cell = $(row.insertCell(-1));
	cell.html(data.po_number);
	cell.attr("style","width:5%;");	
    cell.append(poIdInput);
	cell.append(poTransIdInput);
	cell.append(itemIdInput);
	cell.append(itemTypeInput);
	cell.append(itemStockInput);

    var cell = $(row.insertCell(-1));
	cell.html(data.item_name);

    var mirIdInput = $("<input/>",{type:"hidden",name:"mir_id[]",value:data.mir_id});
    var mirTransIdInput = $("<input/>",{type:"hidden",name:"mir_trans_id[]",value:data.mir_trans_id});
    var locationIdInput = $("<input/>",{type:"hidden",name:"location_id[]",value:data.location_id});
    cell = $(row.insertCell(-1));
	cell.html(data.location_name);
    cell.append(mirIdInput);
    cell.append(mirTransIdInput);
	cell.append(locationIdInput);

    var batchNoInput = $("<input/>",{type:"hidden",name:"batch_no[]",value:data.batch_no});
    cell = $(row.insertCell(-1));
	cell.html(data.batch_no);
    cell.append(batchNoInput);

    var heatNoInput = $("<input/>",{type:"hidden",name:"heat_no[]",value:data.heat_no});
    cell.append(heatNoInput);
    
    var millHeatNoInput = $("<input/>",{type:"hidden",name:"mill_heat_no[]",value:data.mill_heat_no});
    cell = $(row.insertCell(-1));
	cell.html(data.mill_heat_no);
    cell.append(millHeatNoInput);


    var batchQtyInput = $("<input/>",{type:"hidden",name:"batch_qty[]",value:data.batch_qty});
   
    cell = $(row.insertCell(-1));
	cell.html(data.batch_qty);
    cell.append(batchQtyInput);

    var expDateInput = $("<input/>",{type:"hidden",name:"expire_date[]",value:data.expire_date});
   
    cell = $(row.insertCell(-1));
	cell.html(data.expire_date);
    cell.append(expDateInput);

    //Add Button cell.	
	var btnRemove = $('<button><i class="ti-trash"></i></button>');
	btnRemove.attr("type", "button");
	btnRemove.attr("onclick", "batchRemove(this);");
    btnRemove.attr("style","margin-left:4px;");
	btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");

    var btnEdit = $('<button><i class="ti-pencil-alt"></i></button>');
    btnEdit.attr("type", "button");
    btnEdit.attr("onclick", "Edit("+JSON.stringify(data)+",this);");
    btnEdit.attr("class", "btn btn-outline-warning waves-effect waves-light");

   
    if(data.trans_status == 0)
    {
        cell = $(row.insertCell(-1));
    	cell.append(btnRemove);
        cell.append(btnEdit);
    	cell.attr("class","text-center");
    	cell.attr("style","width:10%;");
    }
    else{
        cell = $(row.insertCell(-1));
    	cell.append('');
    	cell.attr("class","text-center");
    	cell.attr("style","width:10%;");
    }
}

function batchRemove(button){
    
    var row = $(button).closest("TR");
	var table = $("#batchTable")[0];
	table.deleteRow(row[0].rowIndex);
	$('#batchTable tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
	var countTR = $('#batchTable tbody tr:last').index() + 1;
    if(countTR == 0){
        $("#batchTable tbody").html('<tr id="noData"><td colspan="9" align="center">No data available in table</td></tr>');
    }
    
}

function tcInspe(data){
	var button = data.button;if(button == "" || button == null){button="both";};
	var fnEdit = data.fnedit;if(fnEdit == "" || fnEdit == null){fnEdit="edit";}
	var fnsave = data.fnsave;if(fnsave == "" || fnsave == null){fnsave="save";}
	var savebtn_text = data.savebtn_text;if(savebtn_text == "" || savebtn_text == null){savebtn_text="Save";}
	var sendData = {mir_trans_id:data.mir_trans_id};
	if(data.approve_type){sendData = {mir_trans_id:data.mir_trans_id,approve_type:data.approve_type};}
	$.ajax({ 
		type: "POST",   
		url: base_url + controller + '/' + fnEdit,   
		data: sendData,
	}).done(function(response){
		$("#"+data.modal_id).modal();
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

function Edit(data,button){
    var row_index = $(button).closest("tr").index();	
 
    $.each(data,function(key, value) {
		
		$("#"+key).val(value);
	}); 
    var party_id =$("#party_id").val();
	$.ajax({
        url : base_url + controller + '/getPoOptions',
        type: 'post',
        data:{party_id:party_id,po_id:data.po_id},
        dataType:'json',
        success:function(data){
            $("#po_id").html("");
            $("#po_id").html(data.options);
            $("#po_id").comboSelect();

            $("#po_id").trigger('change');
        }
    });
    setTimeout(function(){ 
        if(data.po_id ==0){
            $("#item_id").val(data.item_id);
            $("#item_id").comboSelect();
        }
    }, 1000);
    $('.model-select2').select2({ dropdownParent: $('.model-select2').parent() });
    $("#row_index").val(row_index);	
}

function updateTestReport(data){
	var button = data.button;if(button == "" || button == null){button="both";}
	var fnEdit = data.fnEdit;if(fnEdit == "" || fnEdit == null){fnEdit="edit";}
	var fnsave = data.fnsave;if(fnsave == "" || fnsave == null){fnsave="save";}
	
	$.ajax({ 
		type: "POST",   
		url: base_url + controller + '/' + fnEdit,   
		data: {id:data.id}
	}).done(function(response){
		$("#"+data.modal_id).modal();
		$("#"+data.modal_id+' .modal-title').html(data.title);
		$("#"+data.modal_id+' .modal-body').html(response);
		$("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);
		if(button == "close"){
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").hide();
			$("#"+data.modalId+" .modal-footer .btn-save-close").hide();
		}
		$(".single-select").comboSelect();
		$("#"+data.modal_id+" .scrollable").perfectScrollbar({suppressScrollX: true});
		initMultiSelect();setPlaceHolder();
	});
}

function storeTestReport(formId,fnsave,srposition=1){
	setPlaceHolder();
	if(fnsave == "" || fnsave == null){fnsave="save";}
	var form = $('#'+formId)[0];
	var fd = new FormData(form);
	$.ajax({
		url: base_url + controller + '/' + fnsave,
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
			initTable(srposition);
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		
            $('#agency_id').val("");
            $('#name_of_agency').val("");
            $('#test_description').val("");
            $('#sample_qty').val("");
            $('#test_report_no').val("");
            $('#test_remark').val("");
            $('#test_result').val("");
            $('#inspector_name').val("");
            $('#tc_file').val("");
            $('#agency_id').comboSelect();

            $('#testReportBody').html("");
            $('#testReportBody').html(data.tcReportData);
        
        }else{
			initTable(srposition);
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}
				
	});
}

function trashTestReport(id,grn_trans_id,name='Record'){
	var send_data = { id:id,grn_trans_id:grn_trans_id };
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
						url: base_url + controller + '/deleteTestReport',
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data)
						{
							if(data.status==0){
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}else{
                                $('#testReportBody').html("");
                                $('#testReportBody').html(data.tcReportData);
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
