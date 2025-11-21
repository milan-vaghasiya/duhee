$(document).ready(function(){
    //setTimeout(function(){ $("#entry_type").trigger('change'); }, 50);
     

    $(document).on('click','.add-item',function(){
		setPlaceHolder();
        $("#itemModel").modal();
        $(".btn-close").show();
        $(".btn-save-close").show();
        $(".btn-save").show();
        
        $('#packingItemForm #id').val("");
        $("#packingItemForm #row_index").val("");
        $('#packingItemForm #trans_child_id').val("0");
        $('#packingItemForm #item_code').val("");
        $('#packingItemForm #ref_id').val("");
        var packing_type = $("#packing_type").val();
        if(packing_type ==1){
            $(".disBatch").hide();
        }else{
            $(".disBatch").show();   
        }
        //$(".itemList").html('<option value="" data-trans_child_id="0">Selec Product</option>');
        $(".itemList").comboSelect();
        //$("#box_id").html('<option value="" data-qty_per_box="" data-ref_id="" data-wt_pcs="" data-size="">Packing Material</option>');
        //$("#box_id").comboSelect();
	});

    $(document).on('change',"#item_id",function(){	
        var id = $(this).val();
        var req_id=$('#item_id :selected').data('req_id');
        var item_code = $("#item_id:selected").data('item_code');
        $("#item_code").val(item_code);
        var packing_type = $("#packing_type").val();
        $.ajax({
			url: base_url + 'packing/batchWiseItemStock',
			data: {item_id:id,req_id:req_id,packing_type:packing_type},
			type: "POST",
			dataType:'json',
			success:function(data){
				$('#totalQty').html("0");
				$("#packing_qty").val(0);
				$("#batchData").html(data.batchData);
                $(".tentativePackDiv").html("");
                $(".tentativePackDiv").html(data.tentativePackData);
                if(packing_type ==1){
                    $(".disBatch").hide();
                }else{
                    $(".disBatch").show();   
                }
                    }
                });
    });

    $(document).on('keyup change','.totalQtyNos',function(){
        var qty_per_box = $("#qty_per_box").val();
        var total_box = $("#total_box").val();
        
        qty_per_box = (qty_per_box != 0 || qty_per_box != "")?qty_per_box:0;
        total_box = (total_box != 0 || total_box != "")?total_box:0;
        
        var total_qty = parseFloat((parseFloat(qty_per_box) * parseFloat(total_box))).toFixed(3);
        $("#total_qty").val(total_qty);
    });

    $(document).on('keyup change',".batchQty",function(){	
        
		var oldpqty = 0;//$("#oldpqty").val();	
		var batchQtyArr = $(".batchQty").map(function(){return $(this).val();}).get();
		var batchQtySum = 0;
		$.each(batchQtyArr,function(){batchQtySum += parseFloat(this) || 0;});
        batchQtySum += parseFloat(oldpqty);
        
		$('#totalQty').html("");
		$('#totalQty').html(batchQtySum.toFixed(3));
		$("#packing_qty").val(batchQtySum.toFixed(3));
		$("#total_qty").val(batchQtySum.toFixed(3));
		var id = $(this).data('rowid');
		var cl_stock = $(this).data('cl_stock');
		var batchQty = $(this).val();
		$(".batch_qty"+id).html("");
		$(".packing_qty").html();
		if(parseFloat(batchQty) > parseFloat(cl_stock)){
		
			$(".qty_per_box"+id).html("Stock not avalible.");
            var sum = parseFloat(batchQtySum) - parseFloat(batchQty) + parseFloat(oldpqty);
			$('#totalQty').html(sum);
		    $("#qty").val(sum);
		    $("#total_qty").val(sum);
			$(".bQty"+id).val(0);
		    $(this).val('0');
		}
	});

    $(document).on('click','.saveItem',function(){
        
        var fd = $('#packingItemForm').serializeArray();
        var formData = {};
        $.each(fd,function(i, v) {
            if(v.name != "batch_number[]" && v.name != "location[]" && v.name != "batch_quantity[]"){
                formData[v.name] = v.value;
            }   
        });
        var batch_quantity = $("#packingItemForm input[name='batch_quantity[]']").map(function(){return $(this).val();}).get();
		var batch_number = $("#packingItemForm input[name='batch_number[]']").map(function(){return $(this).val();}).get();
		var location = $("#packingItemForm input[name='location[]']").map(function(){return $(this).val();}).get();
		var qty_per_box = $("#packingItemForm input[name='qty_per_box[]']").map(function(){return $(this).val();}).get();
        $("#packingItemForm .error").html("");
        var valid = 1;
		if(formData.package_no == "" || formData.package_no == "Self Packing"){
			$("#package_no").val("");
			$(".package_no").html('Package No is required.'); valid = 0;
		}
		if(formData.wt_pcs == ""){
			$(".wt_pcs").html('Net Weight Per Pcs is required.'); valid = 0;
		}

		if(formData.packing_wt == ""){
			$(".packing_wt").html('Pcaking Weight is required.'); valid = 0;
		}
        if(formData.box_id == ""){
            $(".box_id").html('Packing Material is required.'); valid = 0;
        }
        if(formData.item_id == ""){
            $(".item_id").html('Product is required.'); valid = 0;
        }
        if(formData.qty_per_box == ""){
            $(".qty_per_box").html('Qty Per Box is required.'); valid = 0;
        }
        if(formData.total_box == ""){
            $(".total_box").html('Total Box is required.'); valid = 0;
        } 
        /*if((formData.batch_qty%formData.qty_per_box) !== 0)
		{
		     $(".qty_per_box"+i).html('Qty Per Box is Not Matched.');valid = 0;
		}*/
        
        
        if(valid){
            var net_wt = 0; var gross_wt = 0;
            formData.wooden_wt = (parseFloat(formData.wooden_wt) > 0)?(parseFloat(formData.wooden_wt).toFixed(3)):0;
            formData.wt_pcs = (parseFloat(formData.wt_pcs) > 0)?(parseFloat(formData.wt_pcs).toFixed(3)):0;
            formData.packing_wt = (parseFloat(formData.packing_wt) > 0)?(parseFloat(formData.packing_wt ).toFixed(3)):0;
            //net_wt = parseFloat(parseFloat(formData.total_qty) * parseFloat(formData.wt_pcs)).toFixed(3);            
            //gross_wt = parseFloat(parseFloat(net_wt) + parseFloat(formData.packing_wt) + parseFloat(formData.wooden_wt)).toFixed(3);
            formData.net_wt = net_wt;
            formData.gross_wt = gross_wt;
			formData.req_id=$('#item_id :selected').data('req_id');
			formData.so_id=$('#item_id :selected').data('so_id');
			formData.so_trans_id=$('#item_id :selected').data('so_trans_id');
			formData.item_code=$('#item_id :selected').data('item_code');
			formData.party_id=$('#item_id :selected').data('party_id');
			formData.packing_type=$('#packing_type').val();
            var batch_data = {};
			if(formData.packing_type == 2){
			    if(batch_quantity){
        			var i=0;
        			$.each(batch_quantity,function(key,bt_qty){
        				if(parseFloat(bt_qty) > 0){
        					formData.batch_qty = formData.total_qty = bt_qty;
        					formData.batch_no = batch_number[key];
        					formData.location_id = location[key];
        					formData.qty_per_box = qty_per_box[key];
        					formData.net_wt = parseFloat(parseFloat(formData.batch_qty) * parseFloat(formData.wt_pcs)).toFixed(3);            
                            formData.gross_wt = parseFloat(parseFloat(formData.net_wt) + parseFloat(formData.packing_wt) + parseFloat(formData.wooden_wt)).toFixed(3);
        					formData.total_box = parseFloat(bt_qty)/parseFloat(qty_per_box[key]);
        					AddRow(formData); 
        				}      
        				i++;
        			});
        		}
				
			}else{
				formData.batch_qty = $('#total_qty').val();
				AddRow(formData); 
			}
       
            $("#batchData").html('<tr><td class="text-center" colspan="5">No Data Found.</td></tr>');
            $("#packing_qty").val(0);
            $("#row_index").val('');
            $("#totalQty").html(0);
            if($(this).data('fn') == "save"){
                $("#packingItemForm .single-select").comboSelect();
                $("#box_id").val("");
                $("#total_box").val("");
                $("#total_qty").val("");
                $("#wt_pcs").val("");
                $("#wooden_wt").val("");
                $("#box_size").val("");
            }else if($(this).data('fn') == "save_close"){
                $('#packingItemForm #id').val("");
                $("#packingItemForm #row_index").val("");
                $('#packingItemForm #trans_child_id').val("0");
                $('#packingItemForm #item_code').val("");
                $('#packingItemForm #ref_id').val("");
                $('#packingItemForm #packing_type').val("1");
                
                $('#packingItemForm')[0].reset();
                $("#packingItemForm .single-select").comboSelect();
                $("#itemModel").modal('hide');
            } 
        }
    }); 

    $(document).on('click','.btn-close',function(){
        $('#packingItemForm #id').val("");
        $("#packingItemForm #row_index").val("");
        $('#packingItemForm #trans_child_id').val("0");
        $('#packingItemForm #item_code').val("");
        $('#packingItemForm #ref_id').val("");
        $('#packingItemForm #packing_type').val("1");
        
        $('#packingItemForm')[0].reset();
        $("#packingItemForm .single-select").comboSelect();
        $("#batchData").html('<tr><td class="text-center" colspan="5">No Data Found.</td></tr>');
        $("#packingItemForm .error").html("");
    });
});

function getNextNo(){
    if($("#version").val() == 3){
        $.ajax({
            url : base_url + controller + '/getNextPackingNo',
            type : 'post',
            data:{id:$("#pack_id").val(),trans_no:$("input[name=trans_no]").val(),entry_type:$("#entry_type").val()},
            dataType:'json',
            success:function(response){
                var data = response.data;
                $("input[name=trans_no]").val(data.trans_no);
                $("input[name=trans_prefix]").val(data.trans_prefix);
                $("input[name=trans_number]").val(data.trans_number);
                $("#trans_number").html(data.trans_number);
            }
        });
    }    
}

function AddRow(data) { //console.log(data);
	$('table#packingItems tr#noData').remove();
	//Get the reference of the Table's TBODY element.
	var tblName = "packingItems";
	
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
	
    var transIdInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][id]",value:data.id});	
    var boxSizeInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][box_size]",value:data.box_size});
    var itemCodeInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][item_code]",value:data.item_code});
    var packageNoInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][package_no]",value:data.package_no});
    var itemIdInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][item_id]",value:data.item_id});
	var qtyPerBoxInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][qty_per_box]",value:data.qty_per_box});
	var totalBoxInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][total_box]",value:data.total_box});
	var totalQtyInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][total_qty]",value:data.batch_qty});
	var wtPcsInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][wt_pcs]",value:data.wt_pcs});
	var packingWtInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][packing_wt]",value:data.packing_wt});
	var woodenWtInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][wooden_wt]",value:data.wooden_wt});
	var netWtInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][net_wt]",value:data.net_wt});
	var grossWtInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][gross_wt]",value:data.gross_wt});
	// var batchDataInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][batch_data]",value:data.batch_data});
    var packingReqIdInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][req_id]",value:data.req_id});
    var soIdInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][so_id]",value:data.so_id});
    var soTransIdInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][so_trans_id]",value:data.so_trans_id});
    var batchNoInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][batch_no]",value:data.batch_no});
    var locationInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][location_id]",value:data.location_id});
    var partyIdInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][party_id]",value:data.party_id});

	
    //----------------------
    cell = $(row.insertCell(-1));
	cell.html(data.package_no);
    cell.append(transIdInput);
	cell.append(packageNoInput);
	//cell.append(transMainIdInput);
	cell.append(packingReqIdInput);
	cell.append(soIdInput);
	cell.append(soTransIdInput);
	cell.append(partyIdInput);
	cell.append("<div class='error batch_data_"+countRow+"'></div>");

    cell = $(row.insertCell(-1));
	cell.html(data.box_size);
	cell.append(boxSizeInput);
    cell.append("<div class='error box_id_"+countRow+"'></div>");
    
    cell = $(row.insertCell(-1));
	cell.html(data.item_code);
	cell.append(itemCodeInput);
	cell.append(itemIdInput);	
	
	cell = $(row.insertCell(-1));
	cell.html(data.qty_per_box);
	cell.append(qtyPerBoxInput);
	
	
	cell = $(row.insertCell(-1));
	cell.html(data.total_box);
	cell.append(totalBoxInput);	
    cell.append("<div class='error total_box_"+countRow+"'></div>");

	cell = $(row.insertCell(-1));
	cell.html(data.batch_qty);
    cell.append(totalQtyInput);
    cell.append(batchNoInput);
    cell.append(locationInput);
    cell.append("<div class='error batch_qty_"+countRow+"'></div>");

    cell = $(row.insertCell(-1));
	cell.html(data.wt_pcs);
    cell.append(wtPcsInput);

    cell = $(row.insertCell(-1));
	cell.html(data.net_wt);
    cell.append(netWtInput);

    cell = $(row.insertCell(-1));
	cell.html(data.packing_wt);
    cell.append(packingWtInput);

    cell = $(row.insertCell(-1));
	cell.html(data.wooden_wt);
    cell.append(woodenWtInput);

    cell = $(row.insertCell(-1));
	cell.html(data.gross_wt);
    cell.append(grossWtInput);
	
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
	/* claculateColumn(); */
};

function Edit(data,button){   
    
    $('#packingItemForm #id').val("");
    $("#packingItemForm #row_index").val("");
    $('#packingItemForm #trans_child_id').val("0");
    $('#packingItemForm #item_code').val("");
    $('#packingItemForm #ref_id').val("");
    $('#packingItemForm #packing_type').val("1");
        
	var row_index = $(button).closest("tr").index();
	$("#itemModel").modal();
    var batchData = ''; var item_id = ''; var trans_main_id = "";var box_id = "";

	$.each(data,function(key, value) { 
        //console.log(key);
        if(key == "batch_data"){
            batchData=value;
        }else if(key == "item_id"){
            item_id = value;
        }else if(key == "trans_main_id"){
            trans_main_id = value;
        }else if(key == "box_id"){
            box_id = value;
        }else if(key == "qty_per_box[]"){
        }else{
            $("#"+key).val(value);
        } 
    }); 		   
	$("#packingItemForm #row_index").val(row_index);
    $(".btn-save").hide();
    
    $("#item_id").val(data.item_id);
    $("#item_id").comboSelect();
    $.ajax({
        url: base_url + 'packing/batchWiseItemStock',
        data: {item_id:data.item_id,req_id:data.req_id,packing_type:data.packing_type,batch_no:data.batch_no,location_id:data.location_id,batch_qty:data.total_qty,size:data.qty_per_box},
        type: "POST",
        dataType:'json',
        success:function(data){
            $('#totalQty').html("0");
            $("#packing_qty").val(0);
            $("#batchData").html(data.batchData);
            $(".tentativePackDiv").html("");
            $(".tentativePackDiv").html(data.tentativePackData);
            var packing_type = $("#packing_type").val();
            if(packing_type ==1){
                $(".disBatch").hide();
            }else{
                $(".disBatch").show();   
            }     
        }
    });
    
}

function Remove(button) {
	//Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#packingItems")[0];
	table.deleteRow(row[0].rowIndex);
	$('#packingItems tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
	var countTR = $('#packingItems tbody tr:last').index() + 1;
	if(countTR == 0){		
		$("#tempItem").html('<tr id="noData"><td colspan="13" align="center">No data available in table</td></tr>');
	}	
	/* claculateColumn(); */
};

function savePacking(formId){
	var fd = $('#'+formId)[0];
    var formData = new FormData(fd);
	$.ajax({
		url: base_url + controller + '/saveExportPacking',
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
            window.location = base_url + controller +'/dispatchExport';
		}else{
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}				
	});
}