$(document).ready(function(){	

	$(document).on('change','#type',function(){
		var grn_type = $(this).val();
		$(".addNewStore").html("");
		if(grn_type == 2){
			$("#location_id option").attr('disabled','disabled');
			$("#location_id option[value='']").removeAttr('disabled');
			$("#location_id").select2();
			$("#party_id").val("");
			$("#party_id").comboSelect();
		}else{
			$("#location_id option").removeAttr('disabled');
			$("#location_id").val("");
			$("#location_id").select2();
			$("#party_id").val("");
			$("#party_id").comboSelect();
		}
	});

	$(document).on('click','.createGIR',function(){
        var party_id = $("#savePurchaseInvoice #party_id :selected").val();
		var party_name = $("#savePurchaseInvoice #party_idc").val();

		$.ajax({
			url : base_url + 'purchaseOrder/getPartyOrders',
			type: 'post',
			data:{party_id:party_id},
			dataType:'json',
			success:function(data){
				$("#orderModal").modal();				
				$("#orderModal #partyName").html(party_name);
				$("#orderModal #party_name").val(party_name);
				$("#orderModal #party_id").val(party_id);
				$("#orderModal #orderData").html("");
				$("#orderModal #orderData").html(data.htmlData);
			}
		});
    });
	
	$(document).on('change','#party_id',function(){
		var party_id = $(this).val();
		var order_id = $("#order_id").val();
		if(party_id != ""){			
			$.ajax({
				url:base_url + controller + '/getPartyOrders',
				type:'post',
				data:{party_id:party_id,order_id:order_id},
				dataType:'json',
				success:function(data){
					$("#po_id").html("");
					$("#po_id").html(data.options);
					reInitMultiSelect();
					$("#order_id").val("");
					$("#girItemForm #item_id").html('<option value="">Select Item Name</option>');
					$("#girItemForm #item_id").comboSelect();
				}
			});
		}
	});

	$(document).on('click',"#createNewStore",function(){
		var partyData = $("#party_id :selected").data('row');
		var fd = {id:"",store_name:"Customer",storename:"Customer",location:partyData.party_code,remark:""};
		$.ajax({
			url: base_url + 'store/save',
			data:fd,
			type: "POST",
			dataType:"json",
		}).done(function(data){
			if(data.status===0){
				$(".error").html("");
				$.each( data.message, function( key, value ) {
					$("."+key).html(value);
				});
			}else if(data.status==1){
				$("#location_id optgroup[label='Customer']").append('<option value="'+data.insert_id+'" selected>'+partyData.party_code+'</option>');
				$("#location_id").select2();
				$(".addNewStore").html("");
				toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
				
			}else{
				toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
			}				
		});
	});

    $('.model-select2').select2({ dropdownParent: $('.model-select2').parent() });
	// $(document).on('change keyup','#fgitem_id',function(){$("#fgitem_name").val($('#fgitem_id :selected').text());});
    // $(document).on('keyup','#fgitem_idc',function(){ $("#fgitem_name").val($(this).val());});

    $(document).on("change","#item_id",function(){		
		var dataRow = $(this).find(":selected").data('row');
		$("#girItemForm #item_name").val("["+dataRow.item_code+"] "+dataRow.item_name);
		$("#girItemForm #unit_id").val("");$("#girItemForm #unit_id").val(dataRow.unit_id);
		$("#girItemForm #unit_name").val("");$("#girItemForm #unit_name").val(dataRow.unit_name);
		$("#girItemForm #price").val("");$("#girItemForm #price").val(dataRow.price);
		$("#girItemForm #po_trans_id").val("0");$("#girItemForm #po_trans_id").val(dataRow.po_trans_id);		
		$("#girItemForm #po_id").val("0");$("#girItemForm #po_id").val(dataRow.po_id);		
		$("#girItemForm #order_qty").val("0");$("#girItemForm #order_qty").val(dataRow.order_qty);		
		$("#girItemForm #pending_qty").html("0");$("#girItemForm #pending_qty").html(dataRow.order_qty);
		$("#girItemForm #item_code").val("");$("#girItemForm #item_code").val(dataRow.item_code);
		$("#girItemForm #item_type").val("");$("#girItemForm #item_type").val(dataRow.item_type);
		$("#girItemForm #batch_stock").val("");$("#girItemForm #batch_stock").val(dataRow.batch_stock);
		
		$("#girItemForm #location_id").val("");
		$("#girItemForm #location_id").val(dataRow.location);
		$("#girItemForm #location_id").select2();
		$("#girItemForm #batch_no").val("");$("#girItemForm #serial_no").val("");

		if(dataRow.item_type == 3 && dataRow.batch_stock == 1){ 
			$(".rmBatchDiv").show(); $(".batchDiv").hide(); 
		}else{ 
			$(".rmBatchDiv").hide(); $(".batchDiv").show(); 
		}

		if(dataRow.batch_stock == 2){			
			$.ajax({
				url : base_url + controller+ '/getBatchOrSerialNo',
				type: 'post',
				data:{item_id:$(this).val()},
				dataType:'json',
				success:function(data){
					$("#girItemForm #batch_no").val(dataRow.item_code+data.code);		
					$("#girItemForm #batch_no").attr('readOnly',true);					
					$("#girItemForm #serial_no").val(data.serial_no);
				}
			});
		}else{
			$("#girItemForm #batch_no").attr('readOnly',false);
		}
	});

    $(document).on('change keyup','#item_idc',function(){
        $("#item_name").val($('#item_idc').val());
    });
	
    $(document).on('click','.saveItem',function(){
		var btn = $(this).data('fn');
        var fd = $('#girItemForm').serializeArray();
        var formData = {};
        $.each(fd,function(i, v) { formData[v.name] = v.value; });
		
        $("#girItemForm .error").html("");
        if(formData.item_id == ""){
			$(".item_id").html("Item Name is required..");
		}else{
			var valid = 1;
			if(formData.qty == "" || formData.qty == "0"){
				$(".qty").html("Qty is required.");valid = 0;
			}
			if(formData.location_id == "" || formData.location_id == "0"){
				$(".location_id").html("Location is required.");valid = 0;
			}
			if(parseFloat(formData.order_qty) > 0 && parseFloat(formData.inward_qty) > parseFloat(formData.order_qty)){
				$(".inward_qty").html("Invalid Inv/CH qty.");valid = 0;
			}
			if(parseFloat(formData.inward_qty) > 0 && parseFloat(formData.qty) > parseFloat(formData.order_qty)){
				$(".qty").html("Invalid qty.");valid = 0;
			}			

			if(valid == 1){
				if(formData.batch_stock != 0 && formData.item_type == 3){
					$.ajax({
						url : base_url + controller+ '/getBatchOrSerialNo',
						type: 'post',
						data:{item_id:formData.item_id,item_type:formData.item_type,batch_stock:formData.batch_stock,heat_no:formData.heat_no,trans_id:formData.trans_id},
						dataType:'json',
						success:function(data){
							formData.batch_no = (formData.batch_stock == 1)?data.code:formData.item_code+data.code;
							formData.serial_no = data.serial_no;

							AddRow(formData);
							$('#girItemForm')[0].reset();
							$("#pending_qty").html("0");
							$("#row_index").val("");
							if(btn == "save"){
								$("#girItemForm #item_idc").focus();
								$("#item_id").comboSelect();
								$("#location_id").select2();
							}else if(btn == "save_close"){
								$("#itemModel").modal('hide');
								$("#item_id").comboSelect();
								$("#location_id").select2();
							}
						}
					});
				}else{
					AddRow(formData);
					$('#girItemForm')[0].reset();
					$("#pending_qty").html("0");
					$("#row_index").val("");
					if(btn == "save"){
						$("#girItemForm #item_idc").focus();
						$("#item_id").comboSelect();
						$("#location_id").select2();
					}else if(btn == "save_close"){
						$("#itemModel").modal('hide');
						$("#item_id").comboSelect();
						$("#location_id").select2();
					}
				}				   
			}
		}
    });   

	$(document).on('click','.add-item',function(){
		var party_id = $('#party_id').val();
		var order_id = $("#order_id").val();
		$(".error").html("");
		var valid = 1;
		if(party_id == ""){$(".party_id").html("Party name is required.");valid=0;}
		if(order_id == ""){$(".po_id").html("Order is required.");valid=0;}

		if(valid==1){			
			$("#itemModel").modal();
			$(".btn-close").show();
			$(".btn-save").show();	
			$("#pending_qty").html("0");
			$("#row_index").val("");
			$(".rmBatchDiv").hide();
			$('.batchDiv').show();
			//$("#row_index").val($('#girItems tbody').find('tr').length);

			$.ajax({
				url:base_url + controller + '/getOrderItems',
				type:'post',
				data:{po_id:order_id,edit_mode:0},
				dataType:'json',
				success:function(data){
					$("#girItemForm #item_id").html("");
					$("#girItemForm #item_id").html(data.options);
					$("#girItemForm #item_id").comboSelect();
				}
			});
		}	
	});

    $(document).on('click','.btn-close',function(){
        $('#girItemForm')[0].reset();
        $("#item_id").comboSelect();
		$('#girItemForm .error').html('');				
    });

	$('#color_code').typeahead({
		source: function(query, result)
		{
			$.ajax({
				url:base_url + controller + '/itemColorCode',
				method:"POST",
				global:false,
				data:{query:query},
				dataType:"json",
				success:function(data){result($.map(data, function(item){return item;}));}
			});
		}
	});
});


function AddRow(data) {
	$('table#girItems tr#noData').remove();
	//Get the reference of the Table's TBODY element.
	var tblName = "girItems";
	
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
	var countRow = (data.row_index == "")?($('#'+tblName+' tbody tr:last').index() + 1):(parseInt(data.row_index) + 1);
	var cell = $(row.insertCell(-1));
	cell.html(countRow);
	cell.attr("style","width:5%;");
	
	var itemIdInput = $("<input/>",{type:"hidden",name:"item_id[]",value:data.item_id});
	var transIdInput = $("<input/>",{type:"hidden",name:"trans_id[]",value:data.trans_id});
	var poTransIdInput = $("<input/>",{type:"hidden",name:"po_trans_id[]",value:data.po_trans_id});
	var poIdInput = $("<input/>",{type:"hidden",name:"po_id[]",value:data.po_id});
	var serialNoInput = $("<input/>",{type:"hidden",name:"serial_no[]",value:data.serial_no});
	var itemCodeInput = $("<input/>",{type:"hidden",name:"item_code[]",value:data.item_code});
	var itemTypeInput = $("<input/>",{type:"hidden",name:"item_type[]",value:data.item_type});
	var batchStockInput = $("<input/>",{type:"hidden",name:"batch_stock[]",value:data.batch_stock});
	cell = $(row.insertCell(-1));
	cell.html(data.item_name);
	cell.append(itemIdInput);
	cell.append(transIdInput);
	cell.append(poTransIdInput);
	cell.append(poIdInput);
	cell.append(serialNoInput);
	cell.append(itemCodeInput);
	cell.append(itemTypeInput);
	cell.append(batchStockInput);

	var inwardQtyInput = $("<input/>",{type:"hidden",name:"inward_qty[]",value:data.inward_qty});
	var orderQtyInput = $("<input/>",{type:"hidden",name:"order_qty[]",value:data.order_qty});
	cell = $(row.insertCell(-1));
	cell.html(data.order_qty);
	cell.append(inwardQtyInput);
	cell.append(orderQtyInput);
	
	var qtyInput = $("<input/>",{type:"hidden",name:"qty[]",value:data.qty});
	var qtyKgInput = $("<input/>",{type:"hidden",name:"qty_kg[]",value:data.qty_kg});
	cell = $(row.insertCell(-1));
	cell.html(data.qty);
	cell.append(qtyInput);
	cell.append(qtyKgInput);
	
	var batchNoInput = $("<input/>",{type:"hidden",name:"batch_no[]",value:data.batch_no});
	var heatNoInput = $("<input/>",{type:"hidden",name:"heat_no[]",value:data.heat_no});
	var forTracInput = $("<input/>",{type:"hidden",name:"forging_tracebility[]",value:data.forging_tracebility});
	var heatTracInput = $("<input/>",{type:"hidden",name:"heat_tracebility[]",value:data.heat_tracebility});
	var locationIdInput = $("<input/>",{type:"hidden",name:"location_id[]",value:data.location_id});
	var unitIdInput = $("<input/>",{type:"hidden",name:"unit_id[]",value:data.unit_id});
	cell = $(row.insertCell(-1));
	cell.html(data.batch_no);
	cell.append(batchNoInput);
	cell.append(locationIdInput);
	cell.append(unitIdInput);	
	cell.append(heatNoInput);	
	cell.append(forTracInput);	
	cell.append(heatTracInput);	

	var priceInput = $("<input/>",{type:"hidden",name:"price[]",value:data.price});
	cell = $(row.insertCell(-1));
	cell.html(data.price);
	cell.append(priceInput);

	//Add Button cell.
	cell = $(row.insertCell(-1));
	var btnRemove = $('<button><i class="ti-trash"></i></button>');
	btnRemove.attr("type", "button");
	btnRemove.attr("onclick", "Remove(this);");
    btnRemove.attr("style","margin-left:4px;");
	btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");

    var btnEdit = $('<button><i class="ti-pencil-alt"></i></button>');
    btnEdit.attr("type", "button");
    btnEdit.attr("onclick", "Edit("+JSON.stringify(data)+",this);");
    btnEdit.attr("class", "btn btn-outline-warning waves-effect waves-light");

    cell.append(btnEdit);
	cell.append(btnRemove);
	cell.attr("class","text-center");
	cell.attr("style","width:10%;");	
};

function Edit(data,button){	
	var row_index = $(button).closest("tr").index();
    $("#itemModel").modal();
    $(".btn-close").show();
    $(".btn-save").hide();
    var fnm = "";
    $.each(data,function(key, value) {$("#"+key).val(value);});

	$.ajax({
		url:base_url + controller + '/getOrderItems',
		type:'post',
		data:{po_id:$("#order_id").val(),edit_mode:1},
		dataType:'json',
		success:function(res){
			$("#girItemForm #item_id").html("");
			$("#girItemForm #item_id").html(res.options);
			$("#girItemForm #item_id").val(data.item_id);
			$("#girItemForm #item_id").comboSelect();
		}
	});
	$("#location_id").select2();
	$("#pending_qty").html("0");$("#pending_qty").html(data.order_qty);		
	if(data.item_type == 3 && data.batch_stock == 1){ 
		$(".rmBatchDiv").show(); $(".batchDiv").hide(); 
	}else{ 
		$(".rmBatchDiv").hide(); $(".batchDiv").show(); 
	}
	if(data.batch_stock == 2){$("#girItemForm #batch_no").attr('readOnly',true);}else{$("#girItemForm #batch_no").attr('readOnly',false);}
	$("#row_index").val(row_index);	
}

function Remove(button) {
    //Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#girItems")[0];
	table.deleteRow(row[0].rowIndex);
	$('#girItems tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
	var countTR = $('#girItems tbody tr:last').index() + 1;
	if(countTR == 0){
		$("#tempItem").html('<tr id="noData"><td colspan="6" align="center">No data available in table</td></tr>');	
	}
};

function saveInvoice(formId){
	var fd = $('#'+formId).serialize();
	$.ajax({
		url: base_url + controller + '/save',
		data:fd,
		type: "POST",
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {
				$("."+key).html(value);
			});
		}else if(data.status==1){
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
            window.location = data.url;
		}else{
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}				
	});
}