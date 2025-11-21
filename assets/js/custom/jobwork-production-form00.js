$(document).ready(function(){
	initMultiSelect();
    productionTable();
    OutwardTable();
    rejectTable();
	reworkTable();
    scrapeTable();
    returnTable();

	$("#operator_id").hide();$("#operator_id").comboSelect('destroy');
	$("#machine_id").hide();
	$(".ptime").hide();
	$("#shift_id").hide();$("#shift_id").comboSelect('destroy');

	$(document).on('change',"#rejection_type_id",function(){
		var type = $(this).val();
		if(type == "-1"){
			$("#rejection_reason").val("-1");
			$("#rejection_reason option").attr("disabled","disabled");			
			$("#rejection_reason option[value='-1']").removeAttr("disabled");
			$("#rejection_reason").comboSelect();
		}else{
			$("#rejection_reason option").removeAttr("disabled");
			$("#rejection_reason").val("");
			$("#rejection_reason").comboSelect();
		}
	});

    $(document).on('change',"#process_id",function(){
        var process_id = $(this).val();
        var job_id = $("#job_id").val();
        $.ajax({
            url:base_url + "productions/getProcessWiseProduction",
            type:'post',
            data:{process_id:process_id,job_id:job_id},
            dataType:'json',
            success:function(data){
                $("#productionData").html("");
                $("#productionTable").dataTable().fnDestroy();
                $("#productionData").html(data.html);
                productionTable();
            }
        });
    });

    $(document).on("click",".getForward",function(){
		$(".challanNoDiv").show();
		$(".remarkDiv").removeClass('col-md-10');$(".remarkDiv").addClass('col-md-6');
		var dataRow = $(this).data('row');
		var name = dataRow.product_name;
		var ProcessName = dataRow.process_name;
		var PendingQty = dataRow.pending_qty;
		$("#ProductItemName").html("");$("#ProductItemName").html(name);
		$("#ProductProcessName").html("");$("#ProductProcessName").html(ProcessName);
		$("#ProductPendingQty").html("");$("#PendingQty").val(parseFloat(PendingQty).toFixed(3));$("#ProductPendingQty").html(parseFloat(PendingQty).toFixed(3));
		
		var job_card_id = dataRow.job_card_id;
		var in_process_id = dataRow.in_process_id;
		var product_id = dataRow.product_id;		
		var ref_id = dataRow.ref_id;

		$("#job_card_id").val(job_card_id);
		$("#in_process_id").val(in_process_id);
		$("#product_id").val(product_id);		
		$("#ref_id").val(ref_id);
		$("#batch_no").val(dataRow.batch_no);
		$("#issue_material_qty").val(dataRow.issue_material_qty);
		$("#material_used_id").val(dataRow.material_used_id);
		$("#outEntryDate").attr('min',dataRow.minDate);
		$("#rejEntryDate").attr('min',dataRow.minDate);
		$("#rewEntryDate").attr('min',dataRow.minDate);
		$("#scrapeEntryDate").attr('min',dataRow.minDate);

		$("#operator_id").hide();
		$("#machine_id").hide();
		$(".ptime").hide();
		$("#shift_id").hide();$("#shift_id").comboSelect('destroy');
		
		/* $.ajax({
			url:base_url + 'productions/getOpretors',
			type:'post',
			data:{},
			dataType:'json',
			success:function(data){
				$(".operatorOptions").html("");
				$(".operatorOptions").html(data.options);
				$(".operatorOptions").comboSelect();
			}
		}); */

		$("#forward #OutwordTab").trigger('click');
		$(".inputmask-hhmm").inputmask("99:99");
	});

    $(document).on("click",".getOutWordQty",function(){
				
        var ref_id = $("#ref_id").val();
        var in_process_id = $("#in_process_id").val();		
        var job_card_id = $("#job_card_id").val();
        var page_process_id = $("#process_id").val();

        $.ajax({
            url:base_url + 'productions/getOutwordTrans',
            type:'post',
            data:{ref_id:ref_id,in_process_id:in_process_id,job_card_id:job_card_id,page_process_id:page_process_id},
            dataType:'json',
            success:function(data)
            {
                $("#outwardQtyData").html("");
                $("#outwardTable").dataTable().fnDestroy();
                $("#outwardQtyData").html(data.sendData);
                OutwardTable();
				$(".challanNoCol").show();

                initTable();
            }
        });
    });

    $(document).on('keyup change',".countWeightOut",function(){
		var qty = $("#outQty").val();
		var wQty = $("#outWpcs").val();
		var totalWeight = $("#outTotalWeight").val();
		var col = $(this).data('col');
		$('.outQty').html("");
		if(qty == "" || isNaN(qty)){
			$('.outQty').html("Qty. is required.");
			$("#outWpcs").val(0);
			$("#outTotalWeight").val(0);
		}else{
			var total = 0;
			if(col == "total_weight"){
				if(totalWeight == "" || isNaN(totalWeight)){
					$("#outWpcs").val(0);
				}else{
					total = parseFloat((parseFloat(totalWeight) / parseFloat(qty))).toFixed(3);
					$("#outWpcs").val(total);
				}
			}else if(col == "w_pcs"){
				if(wQty == "" || isNaN(wQty)){
					$("#outTotalWeight").val(0);
				}else{
					total = parseFloat((parseFloat(wQty) * parseFloat(qty))).toFixed(3);
					$("#outTotalWeight").val(total);
				}
			}
		}
	});

    $(document).on("click",".getRejectedQty",function(){	
        
        var ref_id = $("#ref_id").val();
        var in_process_id = $("#in_process_id").val();		
        var job_card_id = $("#job_card_id").val();
        var page_process_id = $("#process_id").val();

		$.ajax({
			url:base_url + 'productions/getRejectionTrans',
			type:'post',
			data:{ref_id:ref_id,in_process_id:in_process_id,job_card_id:job_card_id,page_process_id:page_process_id},
			dataType:'json',
			success:function(data)
			{
			    $("#rejEntryDate").html("");
				$("#rejEntryDate").html(data.dateOptions);
				$("#rejEntryDate").comboSelect();
			    
			    $("#rejection_type_id").html("");
				$("#rejection_type_id").html(data.processOptions);
				$("#rejection_type_id").comboSelect();
			    
			    $("#rejection_reason").html("");
				$("#rejection_reason").html(data.rrOptions);
				$("#rejection_reason").comboSelect();
			    
				$("#rejectQtyData").html("");
				$("#rejectTable").dataTable().fnDestroy();
				$("#rejectQtyData").html(data.sendData);
				rejectTable();

                initTable();
			}
		});
	});

    $(document).on('keyup change',".countWeightRej",function(){
		var qty = $("#rejQty").val();
		var wQty = $("#rejWpcs").val();
		var totalWeight = $("#rejTotalWeight").val();
		var col = $(this).data('col');
		$('.rejQty').html("");
		if(qty == "" || isNaN(qty)){
			$('.rejQty').html("Qty. is required.");
			$("#rejWpcs").val(0);
			$("#rejTotalWeight").val(0);
		}else{
			var total = 0;
			if(col == "total_weight"){
				if(totalWeight == "" || isNaN(totalWeight)){
					$("#rejWpcs").val(0);
				}else{
					total = parseFloat((parseFloat(totalWeight) / parseFloat(qty))).toFixed(3);
					$("#rejWpcs").val(total);
				}
			}else if(col == "w_pcs"){
				if(wQty == "" || isNaN(wQty)){
					$("#rejTotalWeight").val(0);
				}else{
					total = parseFloat((parseFloat(wQty) * parseFloat(qty))).toFixed(3);
					$("#rejTotalWeight").val(total);
				}
			}
		}
	});

	$(document).on("click",".getReworkQty",function(){	
        
        var ref_id = $("#ref_id").val();
        var in_process_id = $("#in_process_id").val();		
        var job_card_id = $("#job_card_id").val();
        var page_process_id = $("#process_id").val();

		$.ajax({
			url:base_url + 'productions/getReworkTrans',
			type:'post',
			data:{ref_id:ref_id,in_process_id:in_process_id,job_card_id:job_card_id,page_process_id:page_process_id},
			dataType:'json',
			success:function(data)
			{
				$("#rework_process").html("");
				$("#rework_process").html(data.process);
				reInitMultiSelect();

				$("#reworkQtyData").html("");
				$("#reworkTable").dataTable().fnDestroy();
				$("#reworkQtyData").html(data.sendData);
				reworkTable();

                initTable();
			}
		});
	});

	$(document).on('keyup change',".countWeightRew",function(){
		var qty = $("#rewQty").val();
		var wQty = $("#rewWpcs").val();
		var totalWeight = $("#rewTotalWeight").val();
		var col = $(this).data('col');
		$('.rewQty').html("");
		if(qty == "" || isNaN(qty)){
			$('.rewQty').html("Qty. is required.");
			$("#rewWpcs").val(0);
			$("#rewTotalWeight").val(0);
		}else{
			var total = 0;
			if(col == "total_weight"){
				if(totalWeight == "" || isNaN(totalWeight)){
					$("#rewWpcs").val(0);
				}else{
					total = parseFloat((parseFloat(totalWeight) / parseFloat(qty))).toFixed(3);
					$("#rewWpcs").val(total);
				}
			}else if(col == "w_pcs"){
				if(wQty == "" || isNaN(wQty)){
					$("#rewTotalWeight").val(0);
				}else{
					total = parseFloat((parseFloat(wQty) * parseFloat(qty))).toFixed(3);
					$("#rewTotalWeight").val(total);
				}
			}
		}
	});

	$(document).on("click",".getScrape",function(){
				
		var ref_id = $("#ref_id").val();
        var process_id = $("#in_process_id").val();		
        var job_card_id = $("#job_card_id").val();
        var page_process_id = $("#process_id").val();
		var type = $("#trans_type_s").val();

		$.ajax({
			url:base_url + 'productions/getReturnOrScrapeTrans',
			type:'post',
			data:{ref_id:ref_id,process_id:process_id,job_card_id:job_card_id,type:type,page_process_id:page_process_id},
			dataType:'json',
			success:function(data)
			{
				$("#scrapeData").html("");
				$("#scrapeTable").dataTable().fnDestroy();
				$("#scrapeData").html(data.resultHtml);
				scrapeTable();
				$("#scrape_item").html(data.itemOption);
				$("#scrape_item").comboSelect();

                initTable();
			}
		});
	});

    $(document).on('change',"#scrape_item",function(){
		var itemId = $(this).val();
		$(".item_id_s").html("");
		$("#unit_s").val("");
		if(itemId == ""){
			$(".item_id_s").html("Item Name is required.");
            $("#item_id_s").val("");
            $("#ptrans_id_s").val("");
		}else{
			var ptrans_id_s = $("#scrape_item :selected").data('ptrasn_id');

			$("#item_id_s").val(itemId);
			$("#ptrans_id_s").val(ptrans_id_s);

			$.ajax({
				url: base_url + 'productions/getItemData',
				type:'post',
				data:{id:itemId},
				dataType:'json',
				success:function(data){					
					$("#unit_s").val(data.unit_name);
				}
			});
		}		
	});

    $(document).on("click",".getReturnStock",function(){
				
		var ref_id = $("#ref_id").val();
        var process_id = $("#in_process_id").val();		
        var job_card_id = $("#job_card_id").val();
        var page_process_id = $("#process_id").val();
		var type = $("#trans_type_r").val();

		$.ajax({
			url:base_url + 'productions/getReturnOrScrapeTrans',
			type:'post',
			data:{ref_id:ref_id,process_id:process_id,job_card_id:job_card_id,type:type,page_process_id:page_process_id},
			dataType:'json',
			success:function(data)
			{
				$("#returnData").html("");
				$("#returnTable").dataTable().fnDestroy();
				$("#returnData").html(data.resultHtml);
				returnTable();
				$("#return_item").html(data.itemOption);
				$("#return_item").comboSelect();

                initTable();
			}
		});
	});

    $(document).on('change',"#return_item",function(){
		var itemId = $(this).val();
		$(".item_id_r").html("");
		$("#unit_r").val("");
		if(itemId == ""){
			$(".item_id_r").html("Item Name is required.");
		}else{
			var ptrans_id_r = $("#return_item :selected").data('ptrasn_id');

			$("#item_id_r").val(itemId);
			$("#ptrans_id_r").val(ptrans_id_r);

			$.ajax({
				url: base_url + 'productions/getItemData',
				type:'post',
				data:{id:itemId},
				dataType:'json',
				success:function(data){					
					$("#unit_r").val(data.unit_name);
				}
			});
		}		
	});

    $(document).on("keyup","#outQty",function(){
				
        var outQty = $(this).val();
        var product_id = $("#product_id").val();
        var process_id = $("#in_process_id").val();
        if(outQty > 0)
		{
			$.ajax({
				url:base_url + controller + '/getProductProcessRow',
				type:'post',
				data:{product_id:product_id,process_id:process_id,outQty:outQty},
				dataType:'json',
				global:false,
				success:function(data)
				{
					$('#cycle_time').val(data.ppData.cycle_time);
					$('.totalPT').html("As per Standard Time (" + data.ppData.cycle_time + " Per Piece), Production Time : " + data.ppData.ptLabel);
				}
			});
		}
    });

});

function productionTable(){
    var table = $('#productionTable').DataTable( {
		lengthChange: false,
		responsive: true,
		ordering: true,
		//'stateSave':true,
        'pageLength': 25,
		buttons: ['pageLength', 'copy', 'excel']
	});
	table.buttons().container().appendTo( '#productionTable_wrapper .col-md-6:eq(0)' );
}

function OutwardTable(){
	var OutQtyTable = $('#outwardTable').DataTable( {
		lengthChange: false,
		responsive: true,
		'stateSave':true,
		retrieve: true,
		buttons: [ 'pageLength','copy', 'excel']
	});
	OutQtyTable.buttons().container().appendTo( '#outwardTable_wrapper .col-md-6:eq(0)' );
	return OutQtyTable;
}

function rejectTable(){
	var rejectTable = $('#rejectTable').DataTable( {
		lengthChange: false,
		responsive: true,
		'stateSave':true,
		retrieve: true,
		buttons: [ 'pageLength','copy', 'excel']
	});
	rejectTable.buttons().container().appendTo( '#rejectTable_wrapper .col-md-6:eq(0)' );
	return rejectTable;
};

function reworkTable(){
	var reworkTable = $('#reworkTable').DataTable( {
		lengthChange: false,
		responsive: true,
		'stateSave':true,
		retrieve: true,
		buttons: [ 'pageLength','copy', 'excel']
	});
	reworkTable.buttons().container().appendTo( '#reworkTable_wrapper .col-md-6:eq(0)' );
	return reworkTable;
};

function scrapeTable(){
	var scrapeTable = $('#scrapeTable').DataTable( {
		lengthChange: false,
		responsive: true,
		'stateSave':true,
		retrieve: true,
		buttons: [ 'pageLength','copy', 'excel']
	});
	scrapeTable.buttons().container().appendTo( '#scrapeTable_wrapper .col-md-6:eq(0)' );
	return scrapeTable;
};

function returnTable(){
	var returnTable = $('#returnTable').DataTable( {
		lengthChange: false,
		responsive: true,
		'stateSave':true,
		retrieve: true,
		buttons: [ 'pageLength','copy', 'excel']
	});
	returnTable.buttons().container().appendTo( '#returnTable_wrapper .col-md-6:eq(0)' );
	return returnTable;
};

function acceptJob(id,name='Job Process'){
    var process_id = $("#process_id").val();
        var job_id = $("#job_id").val();
	var send_data = { id:id,process_id:process_id,job_id:job_id };
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to accept this '+name+'?',
		type: 'green',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + 'productions/acceptJob',
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data)
						{
							if(data.status==0)
							{
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
							else
							{
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

function saveOutQty() {
	
	var valid = 1;
	if($("#outQty").val() == "" || $("#outQty").val() == "" || $("#outQty").val() == 0){$(".outQty").html("Quantity is required.");valid=0;}
	if($("#production_time").val() != "")
	{
		var pt = $("#production_time").val();
		var ptime = pt.split(':');
		if(ptime[0] >= 10 && ptime[1] > 30){$(".ptime").html("Invalid Time");valid=0;}
	}
	if(valid)
	{		
		$(".outQty").html("");
		$(".outTotalWeight").html("");

		var ref_id = $("#ref_id").val();
		var product_id = $("#product_id").val();
		var in_process_id = $("#in_process_id").val();
		var job_card_id = $("#job_card_id").val();	
        var page_process_id = $("#process_id").val();

		var challan_no = $("#outChallanNo").val();
		var charge_no = $("#outChargeNo").val();
		var entry_date = $("#outEntryDate").val();
		var production_time = $("#production_time").val();
		var out_qty = $("#outQty").val();	
		var ud_qty = $("#udQty").val();			
		var w_pcs = $("#outWpcs").val();
		var total_weight = $("#outTotalWeight").val();
        var remark = $("#outRemark").val();
		var batch_no = $("#batch_no").val();
        var issue_material_qty = $("#issue_material_qty").val();
        var material_used_id = $("#material_used_id").val();
        var shift_id = $("#shift_id").val();
        var cycle_time = $("#cycle_time").val();

		var postData = {ref_id:ref_id,product_id:product_id,in_process_id:in_process_id,job_card_id:job_card_id,out_qty:out_qty,ud_qty:ud_qty,w_pcs:w_pcs,total_weight:total_weight,remark:remark,page_process_id:page_process_id,challan_no:challan_no,operator_id:"0",machine_id:"0",entry_date:entry_date,production_time:production_time,charge_no:charge_no,batch_no:batch_no,issue_material_qty:issue_material_qty,material_used_id:material_used_id,shift_id:shift_id,cycle_time:cycle_time};
		$.ajax({
			url:base_url + 'productions/saveOutTrans',
			type:'post',
			data:postData,
			dataType:'json',
			success:function(data)
			{
				if(data.status===0)
				{
					$(".error").html("");
					$.each( data.message, function( key, value ) {$("."+key).html(value);});
				}
				else
				{
					toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });

					var PendingQty = $("#PendingQty").val();
					var newPendingQty = parseFloat(parseFloat(PendingQty) - parseFloat(out_qty)).toFixed(3);
					$("#PendingQty").val(newPendingQty);
					$("#ProductPendingQty").html(newPendingQty);

					$("#production_time").val("");
					$("#outChargeNo").val("");
					$("#outQty").val(0);
					$("#udQty").val(0);		
					$("#outWpcs").val(0);
					$("#outTotalWeight").val(0);
					$("#outRemark").val("");
					$("#outChallanNo").val("");
					
					$("#outwardQtyData").html("");
					$("#outwardTable").dataTable().fnDestroy();
					$("#outwardQtyData").html(data.sendData);
					OutwardTable(); 
					$(".challanNoCol").show();                    
				}
                initTable();
			}
		});	
	}
}

function trashOutward(id,out_qty,name='Record'){
    var job_card_id = $("#job_card_id").val();	
    var page_process_id = $("#process_id").val();

	var send_data = { id:id,job_card_id:job_card_id,page_process_id:page_process_id };
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
						url: base_url + 'productions/deleteOutward',
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data)
						{
							if(data.status==0)
							{
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
							else
							{
								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });

								var PendingQty = $("#PendingQty").val();
								var newPendingQty = parseFloat(parseFloat(PendingQty) + parseFloat(out_qty)).toFixed(3);
								$("#PendingQty").val(newPendingQty);
								$("#ProductPendingQty").html(newPendingQty);

                                $("#outwardQtyData").html("");
                                $("#outwardTable").dataTable().fnDestroy();
                                $("#outwardQtyData").html(data.sendData);
                                OutwardTable(); 
								$(".challanNoCol").show();
							}
                            initTable();
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

function saveRejectQty() {
	if($("#rejQty").val() == "" || $("#rejQty").val() == 0 || $("#rejection_type_id").val() == "" ){
		if($("#rejQty").val() == "" || $("#rejQty").val() == 0){
			$(".rejQty").html("Quantity is required.");
		}
		if($("#rejection_type_id").val() == ""){
			$(".rejection_type_id").html("Rejection Type is required.");
		}
	}else{
		
		$(".rejQty").html("");
		$(".type").html("");
		$(".rejTotalWeight").html("");

		var ref_id = $("#ref_id").val();
		var product_id = $("#product_id").val();
		var in_process_id = $("#in_process_id").val();
		var job_card_id = $("#job_card_id").val();	
        var page_process_id = $("#process_id").val();

		var entry_date = $("#rejEntryDate").val();
		var rejection_type_id = $("#rejection_type_id").val();		
		var rejection_reason = $("#rejection_reason").val();		
		var qty = $("#rejQty").val();		
		var remark = $("#remark").val();
		var w_pcs = $("#rejWpcs").val();
		var total_weight = $("#rejTotalWeight").val();

		var postData = {ref_id:ref_id,product_id:product_id,in_process_id:in_process_id,job_card_id:job_card_id,page_process_id:page_process_id,rejection_type_id:rejection_type_id,rejection_reason:rejection_reason,qty:qty,remark:remark,w_pcs:w_pcs,total_weight:total_weight,operator_id:"0",machine_id:"0",entry_date:entry_date,shift_id:"0"};

		$.ajax({
			url:base_url + 'productions/saveRejection',
			type:'post',
			data:postData,
			dataType:'json',
			success:function(data)
			{
				if(data.status===0)
				{
					$(".error").html("");
					$.each( data.message, function( key, value ) {
						$("."+key).html(value);
					});
				}
				else
				{
                    toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
					
					var PendingQty = $("#PendingQty").val();
					var newPendingQty = parseFloat(parseFloat(PendingQty) - parseFloat(qty)).toFixed(3);
					$("#PendingQty").val(newPendingQty);
					$("#ProductPendingQty").html(newPendingQty);
					
					$("#rejQty").val(0);
					$("#rejWpcs").val(0);
					$("#rejTotalWeight").val(0);
					$("#remark").val("");
					$("#rejection_type_id").val("");
					$("#rejection_type_id").comboSelect();

                    $("#rejectTable").dataTable().fnDestroy();
					$("#rejectQtyData").html("");
					$("#rejectQtyData").html(data.sendData);
					rejectTable();
				}
                initTable();
			}
		});	
	}
};

function trashRejection(id,qty,name='Record'){
    var job_card_id = $("#job_card_id").val();	
    var page_process_id = $("#process_id").val();

	var send_data = { id:id,job_card_id:job_card_id,page_process_id:page_process_id };
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
						url: base_url + 'productions/deleteRejection',
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data)
						{
							if(data.status==0)
							{
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
							else
							{
								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });

								var PendingQty = $("#PendingQty").val();
								var newPendingQty = parseFloat(parseFloat(PendingQty) + parseFloat(qty)).toFixed(3);
								$("#PendingQty").val(newPendingQty);
								$("#ProductPendingQty").html(newPendingQty);

                                $("#rejectTable").dataTable().fnDestroy();
                                $("#rejectQtyData").html("");
                                $("#rejectQtyData").html(data.sendData);
                                rejectTable();
							}
                            initTable();
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

function saveReworkQty(){
	var valid = 1;
	if($("#rewQty").val() == "" || $("#rewQty").val() == 0){$(".rewQty").html("Quantity is required.");valid=0;}
	if($("#rework_process_id").val() == ""){$(".rework_process_id").html("Process is required.");valid=0;}
	//if($("#operator_id").val() == ""){$(".operator_id").html("Operator Name is required.");valid=0;}
	if($("#rewEntryDate").val() == ""){$(".rewEntryDate").html("Date is required.");valid=0;}
	//if($("#shift_id").val() == ""){$(".shift_id").html("Shift is required.");valid=0;}
	/* if($("#production_time").val() == ""){$(".production_time").html("Production Time is required.");valid=0;}
	else
	{
		var pt = $("#production_time").val();
		var ptime = pt.split(':');
		if(ptime[0] == ""){ptime[0]='00';}if(ptime[1] == ""){ptime[1]='00';}
		if(ptime[0] > 10){$(".production_time").html("Invalid Time");valid=0;}
		else{if(ptime[0] == 10 && ptime[1] > 30){$(".production_time").html("Invalid Time");valid=0;}}
	} */
	if(valid)
	{		
		$(".rewQty").html("");
		$(".rework_process_id").html("");
		$(".rewTotalWeight").html("");

		var ref_id = $("#ref_id").val();
		var product_id = $("#product_id").val();
		var in_process_id = $("#in_process_id").val();
		var job_card_id = $("#job_card_id").val();	
        var page_process_id = $("#process_id").val();

		var entry_date = $("#rewEntryDate").val();
		var rework_process_id = $("#rework_process_id").val();		
		var qty = $("#rewQty").val();		
		var remark = $("#rewRemark").val();
		var w_pcs = $("#rewWpcs").val();
		var total_weight = $("#rewTotalWeight").val();
        var remark = $("#outRemark").val();
        var batch_no = $("#batch_no").val();
        var issue_material_qty = $("#issue_material_qty").val();
        var material_used_id = $("#material_used_id").val();

		var postData = {ref_id:ref_id,product_id:product_id,in_process_id:in_process_id,job_card_id:job_card_id,page_process_id:page_process_id,rework_process_id:rework_process_id,qty:qty,remark:remark,w_pcs:w_pcs,total_weight:total_weight,operator_id:"0",machine_id:"0",entry_date:entry_date,batch_no:batch_no,issue_material_qty:issue_material_qty,material_used_id:material_used_id,shift_id:"0"};

		$.ajax({
			url:base_url + 'productions/saveRework',
			type:'post',
			data:postData,
			dataType:'json',
			success:function(data)
			{
				if(data.status===0)
				{
					$(".error").html("");
					$.each( data.message, function( key, value ) {
						$("."+key).html(value);
					});
				}
				else
				{
                    toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
					
					var PendingQty = $("#PendingQty").val();
					var newPendingQty = parseFloat(parseFloat(PendingQty) - parseFloat(qty)).toFixed(3);
					$("#PendingQty").val(newPendingQty);
					$("#ProductPendingQty").html(newPendingQty);
					
					$("#rewQty").val(0);
					$("#rewWpcs").val(0);
					$("#rewTotalWeight").val(0);
					$("#rewRemark").val("");

                    $("#reworkTable").dataTable().fnDestroy();
					$("#reworkQtyData").html("");
					$("#reworkQtyData").html(data.sendData);
					reworkTable();
				}
                initTable();
			}
		});	
	}
}

function trashRework(id,qty,name='Record'){
    var job_card_id = $("#job_card_id").val();	
    var page_process_id = $("#process_id").val();

	var send_data = { id:id,job_card_id:job_card_id,page_process_id:page_process_id };
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
						url: base_url + 'productions/deleteRework',
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data)
						{
							if(data.status==0)
							{
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
							else
							{
								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });

								var PendingQty = $("#PendingQty").val();
								var newPendingQty = parseFloat(parseFloat(PendingQty) + parseFloat(qty)).toFixed(3);
								$("#PendingQty").val(newPendingQty);
								$("#ProductPendingQty").html(newPendingQty);

                                $("#reworkTable").dataTable().fnDestroy();
								$("#reworkQtyData").html("");
								$("#reworkQtyData").html(data.sendData);
								reworkTable();
							}
                            initTable();
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

function scrapeSave(){

	var ref_id = $("#ref_id").val();
    var product_id = $("#product_id").val();
    var process_id = $("#in_process_id").val();
    var job_card_id = $("#job_card_id").val();	
    var page_process_id = $("#process_id").val();
	
	var entry_date = $("#scrapeEntryDate").val();
	var trans_type = $("#trans_type_s").val();
	var item_id = $("#item_id_s").val();
	var ptrans_id = $("#ptrans_id_s").val();
	var qty = $("#qty_s").val();
	var remark = $("#remark_s").val();

	$(".scrape_item").html('');
	$(".qty_s").html('');

	if(item_id == 0 || item_id == "" || isNaN(item_id) || qty == 0 || qty == "" || isNaN(qty)){
		if(item_id == 0 || item_id == "" || isNaN(item_id)){
			$(".scrape_item").html('Item Name is required.');
		}
		if(qty == 0 || qty == "" || isNaN(qty)){
			$(".qty_s").html('Qty. is required.');
		}
	}else{
		var postData = {id:"",ref_id:ref_id,product_id:product_id,process_id:process_id,job_card_id:job_card_id,page_process_id:page_process_id,trans_type:trans_type,item_id:item_id,ptrans_id:ptrans_id,qty:qty,remark:remark,operator_id:"0",machine_id:"0",entry_date:entry_date};

		$.ajax({
			url: base_url + 'productions/returnOrScrapeSave',
			data:postData,
			type: "POST",
			dataType:"json",
			success:function(data)
			{
				if(data.status===0)
				{
					$(".error").html("");
					$.each( data.message, function( key, value ) {
						$("."+key).html(value);
					});
				}
				else if(data.status==1)
				{
                    toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });

					var obj = data.result;
					$("#remark_s").val("");
					$("#item_id_s").val("");
					$("#ptrans_id_s").val("");
					$("#qty_s").val("");
					$("#unit_s").val("");
	
					$("#scrapeData").html("");
					$("#scrapeTable").dataTable().fnDestroy();
					$("#scrapeData").html(obj.resultHtml);
					scrapeTable();
					$("#scrape_item").html(obj.itemOption);
					$("#scrape_item").comboSelect();
				}
				else
				{
					toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
				}
                initTable();
			}
		});
	}	
};

function deleteScrape(id,name='Record'){
    var job_card_id = $("#job_card_id").val();	
    var page_process_id = $("#process_id").val();

	var send_data = { id:id,job_card_id:job_card_id,page_process_id:page_process_id };
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
						url: base_url + 'productions/deleteRetuenOrScrapeItem',
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data)
						{
							if(data.status==0)
							{
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
							else
							{
								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });

                                var obj = data.result;
                                $("#scrapeData").html("");
                                $("#scrapeTable").dataTable().fnDestroy();
                                $("#scrapeData").html(obj.resultHtml);
                                scrapeTable();

                                $("#scrape_item").html(obj.itemOption);
                                $("#scrape_item").comboSelect();
							}
                            initTable();
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

function returnSave(){

	var ref_id = $("#ref_id").val();
    var product_id = $("#product_id").val();
    var process_id = $("#in_process_id").val();
    var job_card_id = $("#job_card_id").val();	
    var page_process_id = $("#process_id").val();
	
	var trans_type = $("#trans_type_r").val();
	var item_id = $("#item_id_r").val();
	var ptrans_id = $("#ptrans_id_r").val();
	var qty = $("#qty_r").val();
	var remark = $("#remark_r").val();

	$(".return_item").html('');
	$(".qty_r").html('');

	if(item_id == 0 || item_id == "" || isNaN(item_id) || qty == 0 || qty == "" || isNaN(qty)){
		if(item_id == 0 || item_id == "" || isNaN(item_id)){
			$(".return_item").html('Item Name is required.');
		}
		if(qty == 0 || qty == "" || isNaN(qty)){
			$(".qty_r").html('Qty. is required.');
		}
	}else{
		var postData = {id:"",ref_id:ref_id,product_id:product_id,process_id:process_id,job_card_id:job_card_id,page_process_id:page_process_id,trans_type:trans_type,item_id:item_id,ptrans_id:ptrans_id,qty:qty,remark:remark,operator_id:"0",machine_id:"0"};

		$.ajax({
			url: base_url + 'productions/returnOrScrapeSave',
			data:postData,
			type: "POST",
			dataType:"json",
			success:function(data)
			{
				if(data.status===0)
				{
					$(".error").html("");
					$.each( data.message, function( key, value ) {
						$("."+key).html(value);
					});
				}
				else if(data.status==1)
				{
                    toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });

					var obj = data.result;
					$("#remark_r").val("");
					$("#item_id_r").val("");
					$("#ptrans_id_r").val("");
					$("#qty_r").val("");
					$("#unit_r").val("");
	
					$("#returnData").html("");
					$("#returnTable").dataTable().fnDestroy();
					$("#returnData").html(obj.resultHtml);
					returnTable();
					$("#return_item").html(obj.itemOption);
					$("#return_item").comboSelect();
				}
				else
				{
					toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
				}
                initTable();
			}
		});
	}	
};

function deleteReturn(id,name='Record'){
    var job_card_id = $("#job_card_id").val();	
    var page_process_id = $("#process_id").val();

	var send_data = { id:id,job_card_id:job_card_id,page_process_id:page_process_id };
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
						url: base_url + 'productions/deleteRetuenOrScrapeItem',
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data)
						{
							if(data.status==0)
							{
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
							else
							{
                                var obj = data.result;
                                $("#returnData").html("");
                                $("#returnTable").dataTable().fnDestroy();
                                $("#returnData").html(obj.resultHtml);
                                returnTable();
                                $("#return_item").html(obj.itemOption);
                                $("#return_item").comboSelect();

								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });                   
							}
                            initTable();
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