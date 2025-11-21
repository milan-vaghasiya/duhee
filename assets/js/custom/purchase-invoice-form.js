$(document).ready(function(){
    claculateColumn();
	$('.model-select2').select2({ dropdownParent: $('.model-select2').parent() });

    var gstType = $("#gst_type").val();
	if(gstType == 1){ 
		$(".cgstCol").show();$(".sgstCol").show();$(".igstCol").hide();
		$(".amountCol").hide();$(".netAmtCol").show();
	}else if(gstType == 2){
		$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").show();
		$(".amountCol").hide();$(".netAmtCol").show();
	}else{
		$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").hide();
		$(".amountCol").show();$(".netAmtCol").hide();
	}
	
	$(document).on("change","#gst_type",function(){
		var gstType = $(this).val();
		if(gstType == 1){ 
			$(".cgstCol").show();$(".sgstCol").show();$(".igstCol").hide();
			$(".amountCol").hide();$(".netAmtCol").show();
		}else if(gstType == 2){
			$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").show();
			$(".amountCol").hide();$(".netAmtCol").show();
		}else{
			$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").hide();
			$(".amountCol").show();$(".netAmtCol").hide();
		}
		claculateColumn();
	});

    $(document).on("change","#item_id",function(){
		
		var dataRow = $(this).find(":selected").data('data_row');
		$("#unit_id").val("");$("#unit_id").val(dataRow.unit_id);
		$("#unit_name").val("");$("#unit_name").val(dataRow.unit_name);
		$("#hsn_code").val("");$("#hsn_code").val(dataRow.hsn_code);
		$("#item_gst").val("");$("#item_gst").val(dataRow.gst_per);
		$("#price").val("");$("#price").val(dataRow.price);
		$("#po_trans_id").val("");$("#po_trans_id").val($(this).find(":selected").data('po_trans_id'));
		if($(this).find(":selected").data('po_trans_id'))
		{
			var pq = parseFloat(parseFloat(dataRow.qty) - parseFloat(dataRow.rec_qty)).toFixed(3);
			$('.pqty').html("Pending Qty : " + pq);
			$('.pono').html("PO. No. : PO/" + dataRow.po_no + "/" + $(this).find(":selected").data('year'));
			$('.pono').parent().css("padding","3px 5px");
		}else{$('.pqty').html("");$('.pono').html("");$('.pono').parent().css("padding","0px");}
	});

    $(document).on('change keyup','#item_id',function(){
        $("#item_name").val($('#item_idc').val());
    });
	
	var freightAmt = ($("#freight").val() == "")?"0.00":parseFloat($("#freight").val()).toFixed(2);
	$("#freight_amt").val(freightAmt);
	$(".freight_amt").html(freightAmt);

    $(document).on('keyup click',"#freight",function(){
		var freightAmt = ($(this).val() == "")?"0.00":parseFloat($(this).val()).toFixed(2);
		$("#freight_amt").val(freightAmt);
		$(".freight_amt").html(freightAmt);
		claculateColumn();
	});

    $(document).on('click','.saveItem',function(){
        var fd = $('#invoiceItemForm').serializeArray();
        var formData = {};
        $.each(fd,function(i, v) { formData[v.name] = v.value; });
		
        $(".item_id").html("");$(".qty").html("");$(".rate").html("");$(".location_id").html("");
        if(formData.item_id == ""){
			$(".item_id").html("Item Name is required..");
		}else{
			var itemIds = $("input[name='item_id[]']").map(function(){return $(this).val();}).get();
			// if ($.inArray(formData.item_id,itemIds) >= 0) {
				// $(".item_id").html("Item already added.");
			// }else {
				if(formData.qty == "" || formData.qty == "0" || formData.price == "" || formData.price == "0" || formData.location_id == ""){
					if(formData.qty == "" || formData.qty == "0"){$(".qty").html("Qty is required.");}
					if(formData.price == "" || formData.price == "0"){$(".rate").html("Price is required.");}
					if(formData.location_id == "" || formData.location_id == "0"){$(".location_id").html("Location is required.");}
				}else{
					var amount = 0;var total = 0;var disc_amt = 0;var igst_amt = 0;
					var cgst_amt = 0;var sgst_amt = 0;var net_amount = 0;var cgst_per = 0;var sgst_per = 0; var igst_per = 0;
					if(formData.disc_per == "" && formData.disc_per == "0"){
						amount = parseFloat(parseFloat(formData.qty) * parseFloat(formData.price)).toFixed(2);
					}else{
						total = parseFloat(parseFloat(formData.qty) * parseFloat(formData.price)).toFixed(2);
						disc_amt = parseFloat((total * parseFloat(formData.disc_per))/100).toFixed(2);
						amount = parseFloat(total - disc_amt).toFixed(2);
					}
					
					cgst_per = parseFloat(parseFloat(formData.item_gst)/2).toFixed(2);
					sgst_per = parseFloat(parseFloat(formData.item_gst)/2).toFixed(2);
					
					cgst_amt = parseFloat((cgst_per * amount )/100).toFixed(2);
					sgst_amt = parseFloat((sgst_per * amount )/100).toFixed(2);
					
					igst_per = parseFloat(formData.item_gst).toFixed(2);
					igst_amt = parseFloat((igst_per * amount )/100).toFixed(2);
					
					net_amount = parseFloat(parseFloat(amount) + parseFloat(igst_amt)).toFixed(2);

                    formData.gst_type = $('#gst_type').val();
					formData.qty = parseFloat(formData.qty).toFixed(2);
					formData.cgst_per = cgst_per;
                    formData.cgst_amt = cgst_amt;
                    formData.sgst_per = sgst_per;
                    formData.sgst_amt = sgst_amt;
                    formData.igst_per = igst_per;
                    formData.igst_amt = igst_amt;
                    formData.disc_amt = disc_amt;
                    formData.amount = amount;
                    formData.net_amount = net_amount;
					

					AddRow(formData);
                    $('#invoiceItemForm')[0].reset();
                    if($(this).data('fn') == "save"){
                        $("#item_id").focus();
                        //$("#unit_id").comboSelect();
                        $("#item_id").comboSelect();
                        // $("#location_id").comboSelect();
                    }else if($(this).data('fn') == "save_close"){
                        $("#itemModel").modal('hide');
                        //$("#unit_id").comboSelect();
                        $("#item_id").comboSelect();
                        // $("#location_id").comboSelect();
                    }   
				}
			// }
		}
    });   

	$(document).on('click','.add-item',function(){
		var party_id = $('#party_id').val();
		$("#item_id").html("");
		if(party_id)
		{
			$.ajax({
				url:base_url + controller + '/getItemsForGRN',
				type:'post',
				data:{party_id:party_id},
				dataType:'json',
				success:function(data){
					$("#itemModel").modal();
					$(".btn-close").show();
					$(".btn-save").show();
					
					$("#item_id").html(data.itemOptions);
					$("#item_id").comboSelect();
				}
			});
		}
		else{$(".party_id").html("Party name is required.");$(".modal").modal('hide');}
	});

    $(document).on('click','.btn-close',function(){
        $('#invoiceItemForm')[0].reset();
        //$("#unit_id").comboSelect();
        $("#item_id").comboSelect();
		$('#invoiceItemForm .error').html('');	
			
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
	/* Created By Mansee 27-11-2021  [ Note : set party name ]  */
	 $(document).on('change','.party_id',function(){
		var partyData = $(this).find(":selected").data('row');	
		$("#party_name").val(partyData.party_name);
    });
});

function AddRow(data) {
	$('table#purchaseItems tr#noData').remove();
	//Get the reference of the Table's TBODY element.
	var tblName = "purchaseItems";
	
	var tBody = $("#"+tblName+" > TBODY")[0];
	
	//Add Row.
	row = tBody.insertRow(-1);
	
	//Add index cell
	var countRow = $('#'+tblName+' tbody tr:last').index() + 1;
	var cell = $(row.insertCell(-1));
	cell.html(countRow);
	cell.attr("style","width:5%;");	
	
	cell = $(row.insertCell(-1));
	cell.html(data.item_name + '<input type="hidden" name="item_id[]" value="'+data.item_id+'"><input type="hidden" name="trans_id[]" value="'+data.trans_id+'" /><input type="hidden" name="po_trans_id[]" value="'+data.po_trans_id+'" /><input type="hidden" name="color_code[]" value="'+data.color_code+'" />');

	cell = $(row.insertCell(-1));
	cell.html(data.hsn_code + '<input type="hidden" name="hsn_code[]" value="'+data.hsn_code+'">');
	
	cell = $(row.insertCell(-1));
	cell.html(data.qty + '<input type="hidden" name="qty[]" value="'+data.qty+'">');
	
	cell = $(row.insertCell(-1));
	cell.html(data.batch_no + '<input type="hidden" name="batch_no[]" value="'+data.batch_no+'"><input type="hidden" name="unit_id[]" value="'+data.unit_id+'"><input type="hidden" name="location_id[]" value="'+data.location_id+'">');

	cell = $(row.insertCell(-1));
	cell.html(data.price + '<input type="hidden" name="price[]" value="'+data.price+'">');
	
	cell = $(row.insertCell(-1));
	cell.html(data.cgst_amt+ '(' + data.cgst_per + '%) <input type="hidden" name="cgst_amt[]" value="'+data.cgst_amt+'"><input type="hidden" name="cgst[]" value="'+data.cgst_per+'">');
	cell.attr("class","cgstCol");
	
	cell = $(row.insertCell(-1));
	cell.html(data.sgst_amt+ '(' + data.sgst_per + '%) <input type="hidden" name="sgst_amt[]" value="'+data.sgst_amt+'"><input type="hidden" name="sgst[]" value="'+data.sgst_per+'">');
	cell.attr("class","sgstCol");

	cell = $(row.insertCell(-1));
	cell.html(data.igst_amt + '(' + data.igst_per + '%) <input type="hidden" name="igst_amt[]" value="'+data.igst_amt+'"><input type="hidden" name="igst[]" value="'+data.igst_per+'">');
	cell.attr("class","igstCol");
	
	cell = $(row.insertCell(-1));
	cell.html(data.disc_amt + '(' + data.disc_per + '%) <input type="hidden" name="disc_per[]" value="'+data.disc_per+'"><input type="hidden" name="disc_amt[]" value="'+data.disc_amt+'">');
	
	cell = $(row.insertCell(-1));
	cell.html(data.amount + '<input type="hidden" name="amount[]" value="'+data.amount+'">');
	cell.attr("class","amountCol");
	
	cell = $(row.insertCell(-1));
	cell.html(data.net_amount + '<input type="hidden" name="net_amount[]" value="'+data.net_amount+'">');
	cell.attr("class","netAmtCol");
	
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
	
	if(data.gst_type == 1){ 
		$(".cgstCol").show();$(".sgstCol").show();$(".igstCol").hide();
		$(".amountCol").hide();$(".netAmtCol").show();
	}else if(data.gst_type == 2){
		$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").show();
		$(".amountCol").hide();$(".netAmtCol").show();
	}else{
		$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").hide();
		$(".amountCol").show();$(".netAmtCol").hide();
	}
	
	claculateColumn();
};

function Edit(data,button){
    $("#itemModel").modal();
    $(".btn-close").hide();
    $(".btn-save").hide();
    $.each(data,function(key, value) {
        $("#"+key).val(value);
    }); 
    //$("#unit_id").comboSelect();
    $("#item_id").comboSelect();	
	// $("#location_id").comboSelect();
    Remove(button);
}

function Remove(button) {
    //Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#purchaseItems")[0];
	table.deleteRow(row[0].rowIndex);
	$('#purchaseItems tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
	var countTR = $('#purchaseItems tbody tr:last').index() + 1;
	if(countTR == 0){
		if($("#gst_type").val() == 1){
			$("#tempItem").html('<tr id="noData"><td colspan="11" align="center">No data available in table</td></tr>');
		}else if($("#gst_type").val() == 2){
			$("#tempItem").html('<tr id="noData"><td colspan="10" align="center">No data available in table</td></tr>');
		}else{
			$("#tempItem").html('<tr id="noData"><td colspan="9" align="center">No data available in table</td></tr>');
		}
	}	
	claculateColumn();
};

function claculateColumn(){
	var amountArray = $("input[name='amount[]']").map(function(){return $(this).val();}).get();
    var amountSum = 0;
	$.each(amountArray,function(){amountSum += parseFloat(this) || 0;});
	
	var netAmtArray = $("input[name='net_amount[]']").map(function(){return $(this).val();}).get();
    var netAmtSum = 0;
	$.each(netAmtArray,function(){netAmtSum += parseFloat(this) || 0;});
			
	var igstAmtArr = $("input[name='igst_amt[]']").map(function(){return $(this).val();}).get();;
    var igstAmtSum = 0;
	$.each(igstAmtArr,function(){igstAmtSum += parseFloat(this) || 0;});
	$('#igst_amt_total').val("");
	$('#igst_amt_total').val(igstAmtSum.toFixed(2));
	
	var cgstAmtArr = $("input[name='cgst_amt[]']").map(function(){return $(this).val();}).get();;
    var cgstAmtSum = 0;
	$.each(cgstAmtArr,function(){cgstAmtSum += parseFloat(this) || 0;});
	$('#cgst_amt_total').val("");
	$('#cgst_amt_total').val(cgstAmtSum.toFixed(2));
	
	var sgstAmtArr = $("input[name='sgst_amt[]']").map(function(){return $(this).val();}).get();;
    var sgstAmtSum = 0;
	$.each(sgstAmtArr,function(){sgstAmtSum += parseFloat(this) || 0;});
	$('#sgst_amt_total').val("");
	$('#sgst_amt_total').val(sgstAmtSum.toFixed(2));
	
	var discAmtArr = $("input[name='disc_amt[]']").map(function(){return $(this).val();}).get();;
    var discAmtSum = 0;
	$.each(discAmtArr,function(){discAmtSum += parseFloat(this) || 0;});
	$('#disc_amt_total').val("");
	$('#disc_amt_total').val(discAmtSum.toFixed(2));
	
	if($("#gst_type").val() == 3 || $("#gst_type").val() == 4){
		var amount = parseFloat(amountSum + parseFloat($("#freight_amt").val())).toFixed(2);
		var decimal = amount.split('.')[1];
		var roundOff = 0;
		var netAmount = 0;
		var total = 0;
		if(decimal!==0)
		{
			if(decimal>=50){roundOff=(100-decimal)/100;netAmount = parseFloat(amount) + parseFloat(roundOff);}
			else{roundOff=(decimal-(decimal*2))/100;netAmount = parseFloat(amount) + parseFloat(roundOff);}
		}
		$(".subTotal").html("");
		$(".subTotal").html(amountSum.toFixed(2));
		$(".roundOff").html("");
		$(".roundOff").html(roundOff.toFixed(2));
		$(".netAmountTotal").html("");
		$(".netAmountTotal").html(netAmount.toFixed(2));
		
		$("#amount_total").val("");
		$("#amount_total").val(amountSum.toFixed(2));
		$("#round_off").val("");
		$("#round_off").val(roundOff.toFixed(2));
		$("#net_amount_total").val("");
		$("#net_amount_total").val(netAmount.toFixed(2));
	}else{
		var amount = parseFloat(netAmtSum + parseFloat($("#freight_amt").val())).toFixed(2);
		var decimal = amount.split('.')[1];
		var roundOff = 0;
		var netAmount = 0;
		var total = 0;
		if(decimal!==0)
		{
			if(decimal>=50){roundOff=(100-decimal)/100;netAmount = parseFloat(amount) + parseFloat(roundOff);}
			else{roundOff=(decimal-(decimal*2))/100;netAmount = parseFloat(amount) + parseFloat(roundOff);}
		}
		$(".subTotal").html("");
		$(".subTotal").html(netAmtSum.toFixed(2));
		$(".roundOff").html("");
		$(".roundOff").html(roundOff.toFixed(2));
		$(".netAmountTotal").html("");
		$(".netAmountTotal").html(netAmount.toFixed(2));
		
		$("#amount_total").val("");
		$("#amount_total").val(amountSum.toFixed(2));
		$("#round_off").val("");
		$("#round_off").val(roundOff.toFixed(2));
		$("#net_amount_total").val("");
		$("#net_amount_total").val(netAmount.toFixed(2));
	}
}

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