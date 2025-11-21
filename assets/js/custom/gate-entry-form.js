$(document).ready(function(){
    $(document).on('click','.addItem',function(){
        $("#geItemForm")[0].reset();
        $("#geItemForm #trans_id").val("");
        $("#geItemForm #row_index").val("");
        $(".btn-close").show();
		$(".btn-save").show();
		var today = new Date();
        $("#geItemForm #inv_date").val(formatDate(today));
        $("#geItemForm #doc_date").val(formatDate(today));	
        $("#itemModel").modal();
        // $("#item_id").comboSelect();
        $("#party_id").comboSelect();
        let dataSet = {};
        setTimeout(function(){
            getDynamicItemList(dataSet);
        },600);
    });

    $(document).on('click','.saveItem',function(){
        var btn = $(this).data('fn');
        var fd = $('#geItemForm').serializeArray();
        var valid = 1;
        var formData = {};
        $.each(fd,function(i, v) { formData[v.name] = v.value; });

        $("#girItemForm .error").html("");
        if(formData.party_id == ""){ $(".party_id").html("Party Name is required."); valid=0; }
        if(formData.item_id == ""){ $(".item_id").html("Item Name is required."); valid=0; }
        if(parseFloat(formData.qty) == 0 || formData.qty == ""){ $(".qty").html("Qty is required."); valid=0; }
        if(formData.inv_no == "" && formData.doc_no == ""){ $('.inv_no').html('Inv/CH No. is required.'); valid = 0; }
        if(formData.inv_no != "" && formData.inv_date == ""){ $('.inv_date').html('Inv. Date is required.'); valid = 0; }
        if(formData.doc_no != "" && formData.doc_date == ""){ $('.doc_date').html('Doc Date is required.'); valid = 0; }

        if(valid == 1){
          
            formData.party_name = $("#party_idc").val();
            AddRow(formData);
            $("#qty").val("");
            $("#row_index").val("");
            $("#trans_id").val("");
            $("#item_name").val("");
            $("#item_id").val("");
            // $("#geItemForm")[0].reset();
            $("#geItemForm #trans_id").val("");
            $("#geItemForm #row_index").val("");
            if(btn == "save"){
                $("#geItemForm #party_idc").focus();
                // $("#item_id").comboSelect();
                $("#party_id").comboSelect();
                
            }else if(btn == "save_close"){
                $("#geItemForm")[0].reset();
                $("#itemModel").modal('hide');
                // $("#item_id").comboSelect();
                $("#party_id").comboSelect();
            }
            let dataSet = {};
                setTimeout(function(){
                    getDynamicItemList(dataSet);
                },20);
        }
    });

    $(document).on("change","#item_id",function(){
		var itemId = $(this).val();
		$(".item_id").html("");
		if(itemId == ""){
			$(".item_id").html("Please Select Item.");
		}else{
            var itemData = $(this).find(":selected").data('row');
            if (!itemData) {
                itemData = JSON.parse($(this).select2('data')[0]['row']);
            }
			$("#item_name").val(itemData.full_name);		
		}
	});
});

function AddRow(data){
    $('table#geItems tr#noData').remove();
	//Get the reference of the Table's TBODY element.
	var tblName = "geItems";
	
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

    var partyIdInput = $("<input/>",{type:"hidden",name:"item["+countRow+"][party_id]",value:data.party_id});
    var transIdInput = $("<input/>",{type:"hidden",name:"item["+countRow+"][trans_id]",value:data.trans_id});
    cell = $(row.insertCell(-1));
    cell.html(data.party_name);
	cell.append(partyIdInput);
	cell.append(transIdInput);

    var itemIdInput = $("<input/>",{type:"hidden",name:"item["+countRow+"][item_id]",value:data.item_id});
    cell = $(row.insertCell(-1));
    cell.html(data.item_name);
	cell.append(itemIdInput);

    var invNoInput = $("<input/>",{type:"hidden",name:"item["+countRow+"][inv_no]",value:data.inv_no});
    cell = $(row.insertCell(-1));
    cell.html(data.inv_no);
	cell.append(invNoInput);

    var invDateInput = $("<input/>",{type:"hidden",name:"item["+countRow+"][inv_date]",value:data.inv_date});
    cell = $(row.insertCell(-1));
    cell.html(formatDate(data.inv_date));
	cell.append(invDateInput);

    var docNoInput = $("<input/>",{type:"hidden",name:"item["+countRow+"][doc_no]",value:data.doc_no});
    cell = $(row.insertCell(-1));
    cell.html(data.doc_no);
	cell.append(docNoInput);

    var docDateInput = $("<input/>",{type:"hidden",name:"item["+countRow+"][doc_date]",value:data.doc_date});
    cell = $(row.insertCell(-1));
    cell.html(formatDate(data.doc_date));
	cell.append(docDateInput);

    var qtyInput = $("<input/>",{type:"hidden",name:"item["+countRow+"][qty]",value:data.qty});
    cell = $(row.insertCell(-1));
    cell.html(data.qty);
	cell.append(qtyInput);

    var remarkInput = $("<input/>",{type:"hidden",name:"item["+countRow+"][item_remark]",value:data.item_remark});
    var lrInput = $("<input/>",{type:"hidden",name:"item["+countRow+"][lr]",value:data.lr});
    cell = $(row.insertCell(-1));
    cell.html(data.lr);
	cell.append(lrInput);
	cell.append(remarkInput);

    //Add Button cell.	
	var btnRemove = $('<button><i class="ti-trash"></i></button>');
	btnRemove.attr("type", "button");
	btnRemove.attr("onclick", "Remove(this);");
    btnRemove.attr("style","margin-left:4px;");
	btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");

    var btnEdit = $('<button><i class="ti-pencil-alt"></i></button>');
    btnEdit.attr("type", "button");
    btnEdit.attr("onclick", "Edit("+JSON.stringify(data)+",this);");
    btnEdit.attr("class", "btn btn-outline-warning waves-effect waves-light");

    cell = $(row.insertCell(-1));
    cell.append(btnEdit);
	cell.append(btnRemove);
	cell.attr("class","text-center");
	cell.attr("style","width:10%;");
}

function Edit(data,button){	
	var row_index = $(button).closest("tr").index();
    $("#itemModel").modal();
    $(".btn-close").show();
    $(".btn-save").hide();
    var fnm = "";
    $.each(data,function(key, value) {$("#"+key).val(value);});
    let dataSet = {};
	var iid = data.item_id;
	setTimeout(function(){
		if(iid){
			var jsonRow = JSON.stringify({full_name:data.item_name});
			dataSet = {id: iid, text: data.item_name,row: jsonRow};
		}
		getDynamicItemList(dataSet);
	},600);
    $("#party_id").comboSelect();
	$("#row_index").val(row_index);	
}

function Remove(button) {
    //Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#geItemForm")[0];
	table.deleteRow(row[0].rowIndex);
	$('#geItemForm tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
	var countTR = $('#geItemForm tbody tr:last').index() + 1;
	if(countTR == 0){
		$("#tempItem").html('<tr id="noData"><td colspan="10" class="text-center">No data available in table</td></tr>');	
	}
}

function save(formId){
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
            window.location = base_url + controller;
		}else{
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}				
	});
}