$(document).ready(function(){

    $(document).on("change","#item_id",function(){
        var fg_id = $(this).val();
        if(fg_id != ""){            
            getProductBatchDetails(fg_id);            
        }else{
            $("#batchData").html('<tr id="batchNoData"><td colspan="5" class="text-center">No data available in table</td></tr>');
            $('#total_qty').val(0);
        }

        $("#tempItem").html('<tr id="noData"><td colspan="9" class="text-center">No data available in table</td></tr>'); 
    });
	
	$(document).on("change","#box_item_id",function(){
        var qty_box = $('#box_item_id :selected').data('qty_box');
        if(qty_box){            
            $('#qty_box').val(qty_box);         
        }else{
            $('#qty_box').val('');
        }

        //$("#tempItem").html('<tr id="noData"><td colspan="9" class="text-center">No data available in table</td></tr>'); 
    });

    $(document).on('keyup change','.calculateBatchQty',function(){
        var row_id = $(this).data('srno');
        var batch_qty = $(this).val();
        var stock_qty = $("#batch_stock_"+row_id).val(); //console.log(stock_qty);        

        $(".batch_qty_"+row_id).html('');
        if(parseFloat(batch_qty) > parseFloat(stock_qty)){
            $(".batch_qty_"+row_id).html('Invalid Qty.');
            $(this).val("");
        }

        var batchQtyArr = $(".calculateBatchQty").map(function(){return $(this).val();}).get();;
        var batchQtySum = 0;
        $.each(batchQtyArr,function(){batchQtySum += parseFloat(this) || 0;});
        $('#total_qty').val(batchQtySum);
    });

    $(document).on('keyup change','.totalQtyNos',function(){
        var qty_box = $("#qty_box").val();
        var total_box = $("#total_box").val();
        
        qty_box = (qty_box != 0 || qty_box != "")?qty_box:0;
        total_box = (total_box != 0 || total_box != "")?total_box:0;
        
        var total_qty = parseFloat((parseFloat(qty_box) * parseFloat(total_box))).toFixed(3);
        $("#total_box_qty").val(total_qty);
    });

    $(document).on('click','.add-item',function(){
        var formData = {};
        formData.box_item_id = $("#box_item_id").val();
        formData.box_item_name = $("#box_item_idc").val();
        formData.qty_box = $("#qty_box").val();
        formData.total_box = $("#total_box").val();
        formData.total_box_qty = $("#total_box_qty").val();
        formData.remark = $("#remark").val();
        formData.so_trans_id = $("#so_trans_id").val();
        formData.dispatch_qty = 0;                              

        var batch_qty = $("input[name='batch_qty[]']").map(function(){return $(this).val();}).get();
		var batch_no = $("input[name='batch_no[]']").map(function(){return $(this).val();}).get();
		var location_id = $("input[name='location_id[]']").map(function(){return $(this).val();}).get();
		var batch_id = $("input[name='batch_id[]']").map(function(){return $(this).val();}).get();
		var location_name = $("input[name='location_name[]']").map(function(){return $(this).val();}).get();
		var so_no = $("input[name='so_no[]']").map(function(){return $(this).val();}).get();
        var total_batch_qty = $("#total_qty").val();        

        var valid = 1; $(".materialDetails .error").html("");
        // if(formData.so_trans_id == ""){ $(".so_trans_id").html("Sales Order is required."); valid = 0; }
        if(formData.box_item_id == ""){ $(".box_item_id").html("Packing Material is required."); valid = 0; }
        if(formData.qty_box == "" || parseFloat(formData.qty_box) == 0){ $(".box_qty").html("Qty Per Box is required."); valid = 0; }
        if(formData.total_box == "" || parseFloat(formData.total_box) == 0){ $(".total_box").html("Total Box is required."); valid = 0; }
        if(formData.total_box_qty == "" || parseFloat(formData.total_box_qty) == 0){ $(".total_box_qty").html("Total Qty is required."); valid = 0; }
        if(parseFloat(formData.total_box_qty) > 0 && parseFloat(formData.total_box_qty) != parseFloat(total_batch_qty)){
            $(".batchDetails").html("Packing Qty and Total Qty (Nos) is mismatch."); valid = 0;           
        }        

        if(valid == 1){
            var batch_detail = [];var i = 0;var index = 1;var cl_stock = 0;
            $.each(batch_qty,function(key, value){
                if(parseFloat(value) > 0){
                    batch_detail[i] = {'batch_id':batch_id[key],'batch_no':batch_no[key],'location_id':location_id[key],'location_name':location_name[key],'batch_qty':value,'so_no':so_no[key]};
                    cl_stock = 0;
                    cl_stock = parseFloat(parseFloat($("#batchData #batch_stock_"+index).val()) - value)
                    
                    $("#batchData #batch_qty_"+index).val("");
                    $("#batchData #batch_stock_"+index).val(cl_stock);
                    $("#batchData #closing_stock_"+index).html(cl_stock);
                    $("#total_qty").val("0");
                    i++;
                }index++;
            });
            formData.batch_detail = JSON.stringify(batch_detail);

            formData.trans_id = "";
            $.each(formData,function(key,value){ $("#"+key).val(""); });
            // $("#so_trans_id").val(0);
            // $("#so_trans_id").comboSelect();
            $("#box_item_id").comboSelect();
            initModalSelect();
            AddRow(formData);
        }
    });
});

function getProductBatchDetails(item_id){
    $.ajax({
        url: base_url + controller + '/getProductBatchDetails',
        type:'post',
        data:{item_id:item_id},
        dataType:'json',
        beforeSend: function(){
            $('#batchData').html('<tr><td colspan="5" class="text-center">Loading...</td></tr>');
        },
        success:function(data){
            $("#batchData").html("");
            $("#batchData").html(data.batchTbody);
            $("#box_item_id").html(data.boxOptions);
            $("#box_item_id").comboSelect();
            $('#total_qty').val(0);
        }
    });
}

function AddRow(data){
	var tblName = "packingItems";

    //Remove blank line
    $('table#'+tblName+' tr#noData').remove();
	
	//Get the reference of the Table's TBODY element.
	var tBody = $("#"+tblName+" > TBODY")[0];
	
	//Add Row.
	row = tBody.insertRow(-1);
	
	//Add index cell
	var countRow = $('#'+tblName+' tbody tr:last').index() + 1;
	var cell = $(row.insertCell(-1));
	cell.html(countRow);
	cell.attr("style","width:5%;");	

    var idInput = $("<input/>",{type:"hidden",name:"material_data["+countRow+"][id]",value:data.trans_id});
    var boxIdInput = $("<input/>",{type:"hidden",name:"material_data["+countRow+"][box_item_id]",value:data.box_item_id});
    var batchDataInput = $("<input/>",{type:"hidden",name:"material_data["+countRow+"][batch_detail]",value:data.batch_detail});
    var soIdInput = $("<input/>",{type:"hidden",name:"material_data["+countRow+"][so_trans_id]",value:data.so_trans_id});
    var cell = $(row.insertCell(-1));
	cell.html(data.box_item_name);
    cell.append(idInput);
    cell.append(boxIdInput);
    cell.append(batchDataInput);
    cell.append(soIdInput);

    var qtyBoxInput = $("<input/>",{type:"hidden",name:"material_data["+countRow+"][qty_box]",value:data.qty_box});
    var cell = $(row.insertCell(-1));
	cell.html(data.qty_box);
    cell.append(qtyBoxInput);

    var totalBoxInput = $("<input/>",{type:"hidden",name:"material_data["+countRow+"][total_box]",value:data.total_box});
    var totalBoxDiv = $("<div></div>",{class:'error total_box_'+countRow});
    var cell = $(row.insertCell(-1));
	cell.html(data.total_box);
    cell.append(totalBoxInput);
    cell.append(totalBoxDiv);

    var totalBoxQtyInput = $("<input/>",{type:"hidden",name:"material_data["+countRow+"][total_box_qty]",value:data.total_box_qty});
    var cell = $(row.insertCell(-1));
	cell.html(data.total_box_qty);
    cell.append(totalBoxQtyInput);

    var remarkInput = $("<input/>",{type:"hidden",name:"material_data["+countRow+"][remark]",value:data.remark});
    var cell = $(row.insertCell(-1));
	cell.html(data.remark);
    cell.append(remarkInput);

    //Add Button cell.
	cell = $(row.insertCell(-1));
	var btnRemove = $('<button><i class="ti-trash"></i></button>');
	btnRemove.attr("type", "button");
	btnRemove.attr("onclick", "Remove(this,"+data.batch_detail+");");
    btnRemove.attr("style","margin-left:4px;");
	btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");

    if(parseFloat(data.dispatch_qty) == 0){
        cell.append(btnRemove);
    }else{
        cell.html("");
    }    
	cell.attr("class","text-center");
	cell.attr("style","width:10%;");
}

function Remove(button,batch_detail){
    var tblName = "packingItems";
    
    //Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#"+tblName)[0];
	table.deleteRow(row[0].rowIndex);
	$('#'+tblName+' tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
	var countTR = $('#'+tblName+' tbody tr:last').index() + 1;
    if(countTR == 0){
        $("#tempItem").html('<tr id="noData"><td colspan="9" class="text-center">No data available in table</td></tr>');
    }    

    $.each(batch_detail,function(key,row){        
        var rowCount = $('#batchData #'+row.batch_id).length;
        if(rowCount > 0){            
            var cl_stock = $('#batchData #'+row.batch_id+' input[name="batch_stock[]"]').val();          
            cl_stock = parseFloat(parseFloat(cl_stock) + parseFloat(row.batch_qty));
            $('#batchData #'+row.batch_id+' input[name="batch_stock[]"]').val(cl_stock);
            $('#batchData #'+row.batch_id+' td:nth-child(4)').html(cl_stock);
        }else{            
            var tblName = "batchDetails";
            var tBody = $("#"+tblName+" > TBODY")[0];
            //Remove blank line
            $('table#'+tblName+' tr#batchNoData').remove();

            var rowId = row.batch_no.trim()+row.location_id;
	
            //Add Row.
            trRow = tBody.insertRow(-1);
            $(trRow).attr('id',rowId);
            
            //Add index cell
            var countRow = $('#'+tblName+' tbody tr:last').index() + 1;

            var cell = $(trRow.insertCell(-1));
            cell.html(countRow);
            cell.attr("style","width:5%;");	

            var cell = $(trRow.insertCell(-1));
            cell.html(row.location_name);

            var cell = $(trRow.insertCell(-1));
            cell.html(row.batch_no);

            var cell = $(trRow.insertCell(-1));
            var so_no = (typeof(row.so_no) != "undefined" && row.so_no !== null)?row.so_no:"";
            cell.html(so_no);

            var cell = $(trRow.insertCell(-1));
            cell.html(row.batch_qty);
            cell.attr("id","closing_stock_"+countRow);            
            
            var cell = $(trRow.insertCell(-1));
            var batchQtyInput = $("<input/>",{type:"text",name:"batch_qty[]",id:"batch_qty_"+countRow,class:"form-control floatOnly calculateBatchQty",'data-srno':countRow,value:""});
            var locationIdInput = $("<input/>",{type:"hidden",name:"location_id[]",id:"location_id_"+countRow,value:row.location_id});
            var batchNoInput = $("<input/>",{type:"hidden",name:"batch_no[]",id:"batch_no_"+countRow,value:row.batch_no});
            var batchIdInput = $("<input/>",{type:"hidden",name:"batch_id[]",id:"batch_id_"+countRow,value:row.batch_id});
            var locationNameInput = $("<input/>",{type:"hidden",name:"location_name[]",id:"location_name_"+countRow,value:row.location_name});
            var soNoInput = $("<input/>",{type:"hidden",name:"so_no[]",id:"so_no_"+countRow,value:so_no});
            var batchStockInput = $("<input/>",{type:"hidden",name:"batch_stock[]",id:"batch_stock_"+countRow,value:row.batch_qty});
            var errorDiv = $("<div></div>",{class:'error batch_qty_'+countRow});
            cell.append(batchQtyInput);
            cell.append(locationIdInput);
            cell.append(batchNoInput);
            cell.append(batchIdInput);
            cell.append(locationNameInput);
            cell.append(soNoInput);
            cell.append(batchStockInput);
            cell.append(errorDiv);
        }
    });
}

function savePacking(formId){
	var fd = $('#'+formId)[0];
    var formData = new FormData(fd);
	$.ajax({
		url: base_url + controller + '/save',
		data:formData,
        processData: false,
        contentType: false,
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