$(document).ready(function(){

	$(document).on('change','#item_id',function(){
		if($(this).val() == ""){
			$("#item_name").val("");
			$("#unit_name").val("");
			$("#unit_id").val("");
			$("#hsn_code").val("");
		}else{
			var unit_id = $('#item_id :selected').data('unit_id');
			var hsn_code = $('#item_id :selected').data('hsn_code');
			var converted_product = $('#item_id :selected').data('product');
			console.log(converted_product);
			$("#com_unit").val(unit_id);
			$("#hsn_code").val(hsn_code);
			$("#converted_product").val(converted_product);
			$("#com_unit").comboSelect();
			$("#hsn_code").comboSelect();
			$("#converted_product").comboSelect();
			$("#item_name").val($('#item_idc').val()); 
			$("#converted_item_name").val($('#converted_productc').val()); 
			$("#unit_name").val($('#com_unitc').val()); 
		}		
	});

	// $(document).on('change keyup','#item_id',function(){
	// 	$("#item_name").val($('#item_idc').val()); 
	// });
    $(document).on('change keyup','#com_unit',function(){
		$("#unit_name").val($('#com_unitc').val()); 
	});
    $(document).on('change keyup','#process_id',function(){
		$("#process_name").val($('#process_idc').val()); 
	});

	$(document).on('change keyup','#converted_product',function(){
		$("#converted_item_name").val($('#converted_productc').val()); 
	});

    $(document).on('click','.saveItem',function(){
        var fd = $('#challanItemForm').serializeArray();
        var formData = {};
        $.each(fd,function(i, v) {
            formData[v.name] = v.value;
        });
        $(".error").html("");
        if(formData.item_name == ""){
			$(".item_name").html("Item Name is required.");
		}else{
            var IsValid=1;
            if(formData.item_id == "" || formData.item_id == "0"){ $(".item_id").html("Item is required."); IsValid=0;}
            if(formData.process_id == "" || formData.process_id == "0"){ $(".process_id").html("Process is required."); IsValid=0;}
            if(formData.com_unit == "" || formData.com_unit == "0"){ $(".com_unit").html("Unit is required."); IsValid=0;}
            if(formData.process_charge == "" || formData.process_charge == "0"){ $(".process_charge").html("Price is required."); IsValid=0;}
            if(formData.wpp == "" || formData.wpp == "0"){ $(".wpp").html("Weight/Pcs is required."); IsValid=0;}
            if(formData.hsn_code == "" || formData.hsn_code == "0"){ $(".hsn_code").html("Hsn Code is required."); IsValid=0;}
            if(formData.value_rate == "" || formData.value_rate == "0"){ $(".value_rate").html("Valuation Rate is required."); IsValid=0;}
            if(IsValid){
                formData.process_charge = parseFloat(formData.process_charge).toFixed(2);
                AddRow(formData);
                $('#challanItemForm')[0].reset();
                if($(this).data('fn') == "save"){
                    $("#item_idc").focus();
                    $("#item_id").comboSelect();   
					$("#converted_product").comboSelect();
                    $("#com_unit").comboSelect();    
                    $("#hsn_code").comboSelect();    
                }else if($(this).data('fn') == "save_close"){
                    $("#itemModel").modal('hide');
                    $("#item_id").comboSelect();
					$("#converted_product").comboSelect();
                    $("#com_unit").comboSelect();
                    $("#hsn_code").comboSelect();
                }   
            }
		}
    });  
    
	$(document).on('click','.add-item',function(){
        var party_id = $('#vendor_id').val();	
		$(".vendor_id").html("");	
		$("#row_index").val("");
		if(party_id){
            $("#itemModel").modal();
            $(".btn-close").show();
            $(".btn-save").show();
		}else{$(".vendor_id").html("Vendor Name is required.");$(".modal").modal('hide');}
	});
    
    $(document).on('click','.btn-efclose',function(){
        $('#challanItemForm')[0].reset();
		$("#item_id").comboSelect();
        $("#process_id").comboSelect();
        $("#com_unit").comboSelect();
        $("#hsn_code").comboSelect();
        $("#converted_product").comboSelect();
		$('#challanItemForm .error').html('');	
    });		
});

function AddRow(data) {
	$('table#jobChallanItems tr#noData').remove();
	//Get the reference of the Table's TBODY element.
	var tblName = "jobChallanItems";
	
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
	cell = $(row.insertCell(-1));
	cell.html(data.item_name);
	cell.append(itemIdInput);
	cell.append(transIdInput);

	var converted_productInput = $("<input/>",{type:"hidden",name:"converted_product[]",value:data.converted_product});
	cell = $(row.insertCell(-1));
	cell.html(data.converted_item_name);
	cell.append(converted_productInput);
	
    var unitIdInput = $("<input/>",{type:"hidden",name:"com_unit[]",value:data.com_unit});
	cell = $(row.insertCell(-1));
	cell.html(data.unit_name);
	cell.append(unitIdInput);

	var processIdInput = $("<input/>",{type:"hidden",name:"process_id[]",value:data.process_id});
	cell = $(row.insertCell(-1));
	cell.html(data.process_name);
	cell.append(processIdInput);

    var priceInput = $("<input/>",{type:"hidden",name:"process_charge[]",value:data.process_charge});
	cell = $(row.insertCell(-1));
	cell.html(data.process_charge);
	cell.append(priceInput);

	var wppInput = $("<input/>",{type:"hidden",name:"wpp[]",value:data.wpp});	
	cell.append(wppInput);

	var hsnInput = $("<input/>",{type:"hidden",name:"hsn_code[]",value:data.hsn_code});	
	cell.append(hsnInput);

	var valueRateInput = $("<input/>",{type:"hidden",name:"value_rate[]",value:data.value_rate});	
	cell.append(valueRateInput);

	var variance = $("<input/>",{type:"hidden",name:"variance[]",value:data.variance});	
	cell.append(variance);

	var ScrapInput = $("<input/>",{type:"hidden",name:"scarp_per_pcs[]",value:data.scarp_per_pcs});	
	cell.append(ScrapInput);

	var ScrapRateInput = $("<input/>",{type:"hidden",name:"scarp_rate_pcs[]",value:data.scarp_rate_pcs});	
	cell.append(ScrapRateInput);
	
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
    $(".btn-close").hide();
    $(".btn-save").hide();
    $.each(data,function(key, value) {
		$("#"+key).val(value);
	}); 	
	$("#item_id").comboSelect();
	$("#converted_product").comboSelect();
	$("#converted_product").trigger('change');
	$("#process_id").comboSelect();
	$("#com_unit").comboSelect(); 
	$("#hsn_code").comboSelect(); 
    $("#row_index").val(row_index);	
   // Remove(button);
}


function Remove(button) {
	//Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#jobChallanItems")[0];
	table.deleteRow(row[0].rowIndex);
	$('#jobChallanItems tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
	var countTR = $('#jobChallanItems tbody tr:last').index() + 1;
	if(countTR == 0){
		$("#tempItem").html('<tr id="noData"><td colspan="7" align="center">No data available in table</td></tr>');
	}	
};

function saveChallan(formId){
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

// Created By Karmi @16/05/2022
function closeOrder(id,name='Order'){
	var send_data = { id:id };
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to De-Active this '+name+'?',
		type: 'red',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + controller + '/closeOrder',
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data)
						{
							if(data.status==0){
                                toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}else{
								initTable(); 
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