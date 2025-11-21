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
	
	$(document).on('change','#party_id',function(){
		$(".addNewStore").html("");
		if($(this).val() != ""){			
			var partyData = $("#party_id :selected").data('row');
			var grn_type = $("#type").val();
			if(grn_type == 2 && partyData.party_category == 1){				
				var countOption = $("#location_id optgroup[label='Customer'] option:contains("+partyData.party_code+")");				
				if(countOption.length > 0){
					$("#location_id optgroup[label='Customer'] option:contains("+partyData.party_code+")").removeAttr('disabled');
					$("#location_id optgroup[label='Customer'] option:contains("+partyData.party_code+")").attr('selected','selected');
					$("#location_id").select2();
				}else{
					$(".addNewStore").html('Customer Store not found. Click here to <a href="javascript:void(0)" id="createNewStore">add store</a>.');
				}
			}else{
				$("#location_id").val("");
				$("#location_id").select2();
			}
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
		$("#item_name").val("["+dataRow.item_code+"] "+dataRow.item_name);
		$("#unit_id").val("");$("#unit_id").val(dataRow.unit_id);
		$("#unit_name").val("");$("#unit_name").val(dataRow.unit_name);
		$("#price").val("");$("#price").val(dataRow.price);
		$("#po_trans_id").val("0");		
	});

    $(document).on('change keyup','#item_idc',function(){
        $("#item_name").val($('#item_idc').val());
    });
	
    $(document).on('click','.saveItem',function(){
        var fd = $('#grnItemForm').serializeArray();
        var formData = {};
        $.each(fd,function(i, v) { formData[v.name] = v.value; });
		
        $(".item_id").html("");$(".qty").html("");$(".location_id").html("");
        if(formData.item_id == ""){
			$(".item_id").html("Item Name is required..");
		}else{
			var itemIds = $("input[name='item_id[]']").map(function(){return $(this).val();}).get();
			// if ($.inArray(formData.item_id,itemIds) >= 0) {
				// $(".item_id").html("Item already added.");
			// }else {
				if(formData.qty == "" || formData.qty == "0" || formData.price == "" || formData.price == "0" || formData.location_id == "" || formData.location_id == "0"){
					if(formData.qty == "" || formData.qty == "0"){$(".qty").html("Qty is required.");}
					if(formData.location_id == "" || formData.location_id == "0"){$(".location_id").html("Location is required.");}
				}else{
					AddRow(formData);
                    $('#grnItemForm')[0].reset();
                    if($(this).data('fn') == "save"){
                        $("#item_id").focus();
                        $("#item_id").comboSelect();
						reInitMultiSelect();
						$("#location_id").select2();
                    }else if($(this).data('fn') == "save_close"){
                        $("#itemModel").modal('hide');
                        $("#item_id").comboSelect();
						reInitMultiSelect();
						$("#location_id").select2();
                    }   
				}
			// }
		}
    });   

	$(document).on('click','.add-item',function(){
		var party_id = $('#party_id').val();
		$(".party_id").html("");
		if(party_id)
		{
			$("#itemModel").modal();
			$(".btn-close").show();
			$(".btn-save").show();			
		}
		else{$(".party_id").html("Party name is required.");$(".modal").modal('hide');}
	});

    $(document).on('click','.btn-close',function(){
        $('#grnItemForm')[0].reset();
        $("#item_id").comboSelect();
		reInitMultiSelect();
		$("#location_id").select2();
		$('#grnItemForm .error').html('');				
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
	$('table#grnItems tr#noData').remove();
	//Get the reference of the Table's TBODY element.
	var tblName = "grnItems";
	
	var tBody = $("#"+tblName+" > TBODY")[0];
	
	//Add Row.
	row = tBody.insertRow(-1);
	
	//Add index cell
	var countRow = $('#'+tblName+' tbody tr:last').index() + 1;
	var cell = $(row.insertCell(-1));
	cell.html(countRow);
	cell.attr("style","width:5%;");	
	
	var itemIdInput = $("<input/>",{type:"hidden",name:"item_id[]",value:data.item_id});
	var transIdInput = $("<input/>",{type:"hidden",name:"trans_id[]",value:data.trans_id});
	var poTransIdInput = $("<input/>",{type:"hidden",name:"po_trans_id[]",value:data.po_trans_id});
	var colorCodeInput = $("<input/>",{type:"hidden",name:"color_code[]",value:data.color_code});
	cell = $(row.insertCell(-1));
	cell.html(data.item_name);
	cell.append(itemIdInput);
	cell.append(transIdInput);
	cell.append(poTransIdInput);
	cell.append(colorCodeInput);
	
	var qtyInput = $("<input/>",{type:"hidden",name:"qty[]",value:data.qty});
	var qtyKgInput = $("<input/>",{type:"hidden",name:"qty_kg[]",value:data.qty_kg});
	var fgItemIdInput = $("<input/>",{type:"hidden",name:"fgitem_id[]",value:data.fgitem_id});
	var fgItemNameInput = $("<input/>",{type:"hidden",name:"fgitem_name[]",value:data.fgitem_name});
	cell = $(row.insertCell(-1));
	cell.html(data.qty);
	cell.append(qtyInput);
	cell.append(qtyKgInput);
	cell.append(fgItemIdInput);
	cell.append(fgItemNameInput);
	
	var batchNoInput = $("<input/>",{type:"hidden",name:"batch_no[]",value:data.batch_no});
	var locationIdInput = $("<input/>",{type:"hidden",name:"location_id[]",value:data.location_id});
	var unitIdInput = $("<input/>",{type:"hidden",name:"unit_id[]",value:data.unit_id});
	cell = $(row.insertCell(-1));
	cell.html(data.batch_no);
	cell.append(batchNoInput);
	cell.append(locationIdInput);
	cell.append(unitIdInput);	

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
    $("#itemModel").modal();
    $(".btn-close").hide();
    $(".btn-save").hide();
    var fnm = "";
    $.each(data,function(key, value) {$("#"+key).val(value);}); 
    $("#item_id").comboSelect();	
	$("#color_code").comboSelect();
	$("#location_id").select2();
	
	$.ajax({
		url:base_url + controller + '/setFGItems',
		method:"POST",
		data:{fgitem_id:$("#fgitem_id").val()},
		dataType:"json",
		success:function(response){$("#fgSelect").html(response.fgOpt);reInitMultiSelect();}
	});	
    Remove(button);
}

function Remove(button) {
    //Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#grnItems")[0];
	table.deleteRow(row[0].rowIndex);
	$('#grnItems tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
	var countTR = $('#grnItems tbody tr:last').index() + 1;
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