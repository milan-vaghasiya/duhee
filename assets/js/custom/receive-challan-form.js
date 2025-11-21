function saveReceiveItem(frm){
    var fd = new FormData(frm);
	receiveTable();
    $.ajax({
		url: base_url + controller + '/saveReceiveItem',
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
			$("#in_challan_no").val("");
			$("#receive_qty").val("");
			$("#remark").val("");
            $("#receiveItemTable").dataTable().fnDestroy();
            $("#receiveItemTableData").html("");				
            $("#receiveItemTableData").html(data.resultHtml);
            receiveTable();
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}else{			
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}				
	});
}

function trashReceiveItem(send_data = "",name='Record'){
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
						url: base_url + controller + '/deleteReceiveItem',
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data)
						{
							if(data.status==0)
							{
								$(".error").html("");
								$.each( data.message, function( key, value ) {$("."+key).html(value);});
								// toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
							else
							{
								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });

								$("#receiveItemTable").dataTable().fnDestroy();
                                $("#receiveItemTableData").html("");
                                $("#receiveItemTableData").html(data.resultHtml);
								initTable();
                                receiveTable();
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

function receiveTable(){
	var receiveTable = $('#receiveItemTable').DataTable( {
		lengthChange: false,
		responsive: true,
		'stateSave':true,
		retrieve: true,
		buttons: [ 'pageLength','copy', 'excel']
	});
	receiveTable.buttons().container().appendTo( '#receiveItemTable_wrapper .col-md-6:eq(0)' );
	return receiveTable;
};