$(document).ready(function(){
	
	$(document).on('change keyup','#party_id',function(){
		$("#party_name").val($('#party_idc').val()); 
	});    
	
    $(document).on('click','.saveItem',function(){
        var fd = $('#challanItemForm').serializeArray();
        var formData = {};
        $.each(fd,function(i, v) {
            formData[v.name] = v.value;
        });
		
		var batch_qty = $("input[name='batch_quantity[]']").map(function(){return $(this).val();}).get();
		var batch_no = $("input[name='batch_number[]']").map(function(){return $(this).val();}).get();
		var location_id = $("input[name='location[]']").map(function(){return $(this).val();}).get();
		
		var i=0;formData.batch_qty = [];formData.batch_no = [];formData.location_id = [];
		$.each(batch_qty,function(key,value){
			if(parseFloat(value) > 0){
				formData.batch_qty[i] = value;
				formData.batch_no[i] = batch_no[key];
				formData.location_id[i] = location_id[key];
				i++;
			}
		});

        $(".error").html("");
		if(formData.qty == "0"){
			$(".qty").html("Qty is required.");
		}
		if(formData.price == "0" || formData.price == ""){
			$(".price").html("Price is required.");
		}
        if(formData.item_id == ""){
			$(".item_id").html("Item Name is required.");
		}else{
			var valid =1;
			if(formData.qty == "" || formData.qty == "0"){
				$(".qty").html("Qty is required.");
			}
		
			if(valid == 1){
				formData.item_name = $('#item_id option:selected').text();
				formData.process_name = $('#process_id option:selected').text();
				formData.gst_per_name = $('#gst_per option:selected').text();
				formData.qty = parseFloat(formData.qty).toFixed(2);
				AddRow(formData);
				$('#challanItemForm')[0].reset();
				if($(this).data('fn') == "save"){
					$("#item_name").focus();					
					              
				}else if($(this).data('fn') == "save_close"){
					$("#itemModel").modal('hide');
				}   
			}			
		}
    });  
    
	$(document).on('click','.add-item',function(){
		$(".btn-close").show();
    	$(".btn-save").show();
		
		$("#row_index").val("");
	});
    
    $(document).on('click','.btn-efclose',function(){
        $('#challanItemForm')[0].reset();
		$('#challanItemForm .error').html('');	
    });
	
	$(document).on('change','#item_id',function(){
		var item_id = $(this).val();
		var batchQtySum = 0;
		$("#qty").val(batchQtySum.toFixed(3));
		if(item_id == ""){
			$("#item_name").val("");
			$("#batchData").html('<tr><td class="text-center" colspan="5">No Data Found.</td></tr>');
		}else{
			var itemData = $('#item_id :selected').data('row');
			$("#item_name").val(itemData.item_name);

			$.ajax({
				url: base_url + 'store/batchWiseItemStock',
				data: {item_id:item_id,trans_id:"",batch_no:"",location_id:"",batch_qty:""},
				type: "POST",
				dataType:'json',
				success:function(data){
					$("#batchData").html(data.batchData);
				}
			});
		}	
	});
	
	$(document).on('keyup change',".batchQty",function(){
		var batchQtyArr = $("input[name='batch_quantity[]']").map(function(){return $(this).val();}).get();
		var batchQtySum = 0;
		$.each(batchQtyArr,function(){batchQtySum += parseFloat(this) || 0;});
		$('#totalQty').html("");
		$('#totalQty').html(batchQtySum.toFixed(3));
		$("#qty").val(batchQtySum.toFixed(3));

		var id = $(this).data('rowid');
		var cl_stock = $(this).data('cl_stock');
		var batchQty = $(this).val();
		$(".batch_qty"+id).html("");
		$(".qty").html();
		if(parseFloat(batchQty) > parseFloat(cl_stock)){
			$(".batch_qty"+id).html("Stock not avalible.");
		}
	});
});

function AddRow(data) {
	$('table#outChallanItems tr#noData').remove();
	//Get the reference of the Table's TBODY element.
	var tblName = "outChallanItems";
	
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
	cell.attr("class","text-center");
	
	var itemIdInput = $("<input/>",{type:"hidden",name:"item_id[]",value:data.item_id});
	var itemNameInput = $("<input/>",{type:"hidden",name:"item_name[]",value:data.item_name});
	var transIdInput = $("<input/>",{type:"hidden",name:"trans_id[]",value:data.trans_id});
	var stockEffInput = $("<input/>",{type:"hidden",name:"stock_eff[]",value:data.stock_eff});
	var batchQtyInput = $("<input/>",{type:"hidden",name:"batch_qty[]",value:data.batch_qty}); 
	var batchNoInput = $("<input/>",{type:"hidden",name:"batch_no[]",value:data.batch_no}); 
	var locationIdInput = $("<input/>",{type:"hidden",name:"location_id[]",value:data.location_id}); 
	var hsnCodeInput = $("<input/>",{type:"hidden",name:"hsn_code[]",value:data.hsn_code});
	var returnInput = $("<input/>",{type:"hidden",name:"is_returnable[]",value:data.is_returnable});
	cell = $(row.insertCell(-1));
	cell.html(data.item_name);
	cell.append(itemNameInput);
	cell.append(itemIdInput);
	cell.append(stockEffInput);
	cell.append(transIdInput);
	cell.append(batchQtyInput);
	cell.append(batchNoInput);
	cell.append(locationIdInput);
	cell.append(hsnCodeInput);
	cell.append(returnInput);
	cell.attr("class","text-center");
	
	var processIdInput = $("<input/>",{type:"hidden",name:"process_id[]",value:data.process_id});
	var processIdErrorDiv = $("<div></div>",{class:"error process_id"+countRow});
	cell = $(row.insertCell(-1));
	cell.html(data.process_name);
	cell.append(processIdInput);
	cell.append(processIdErrorDiv);
	cell.attr("class","text-center");
	
	var qtyInput = $("<input/>",{type:"hidden",name:"qty[]",value:data.qty});
	var qtyErrorDiv = $("<div></div>",{class:"error qty"+countRow});
	cell = $(row.insertCell(-1));
	cell.html(data.qty);
	cell.append(qtyInput);
	cell.append(qtyErrorDiv);
	cell.attr("class","text-center");
	
	var gstPerInput = $("<input/>",{type:"hidden",name:"gst_per[]",value:data.gst_per_name});
	var gstPerErrorDiv = $("<div></div>",{class:"error gst_per_name"+countRow});
	cell = $(row.insertCell(-1));
	cell.html(data.gst_per_name);
	cell.append(gstPerInput);
	cell.append(gstPerErrorDiv);
	cell.attr("class","text-center");
	
	var priceInput = $("<input/>",{type:"hidden",name:"price[]",value:data.price});
	var priceErrorDiv = $("<div></div>",{class:"error price"+countRow});
	cell = $(row.insertCell(-1));
	cell.html(data.price);
	cell.append(priceInput);
	cell.append(priceErrorDiv);
	cell.attr("class","text-center");
	
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
	console.log(data);
    $("#itemModel").modal();
    $(".btn-close").hide();
    $(".btn-save").hide();
	var batchNo = ""; var locationId = ""; var batchQty = "";
    $.each(data,function(key, value) {
		if(key=="batch_no"){ batchNo = value; }
		else if(key=="location_id"){ locationId = value; }
		else if(key=="batch_qty"){ batchQty = value; }
		else{$("#"+key).val(value);}
    });
	$("#item_id").comboSelect();
	$.ajax({
		url: base_url + 'store/batchWiseItemStock',
		data: {item_id:data.item_id,trans_id:data.trans_id,batch_no:data.batch_no,location_id:data.location_id,batch_qty:data.batch_qty},
		type: "POST",
		dataType:'json',
		success:function(data){
			$("#batchData").html(data.batchData);
		}
	});
    $("#row_index").val(row_index);		
}

function Remove(button) {
	//Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#outChallanItems")[0];
	table.deleteRow(row[0].rowIndex);
	$('#outChallanItems tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
	var countTR = $('#outChallanItems tbody tr:last').index() + 1;
	if(countTR == 0){
		$("#tempItem").html('<tr id="noData"><td colspan="7" align="center">No data available in table</td></tr>');
	}	
};

function saveOutChallan(formId){
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