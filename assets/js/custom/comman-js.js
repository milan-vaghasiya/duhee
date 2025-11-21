$(document).ready(function(){
    
    /* document.addEventListener('contextmenu', function(e) {e.preventDefault();});
	document.onkeydown = function(e) {
		if(event.keyCode == 123) {return false;}
		if(e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)) {return false;}
		if(e.ctrlKey && e.shiftKey && e.keyCode == 'C'.charCodeAt(0)) {return false;}
		if(e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)) {return false;}
		if(e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)) {return false;}
	} */
    
	// initSpeechRecognitationMenu();
	$('[data-tooltip="tooltip"]').tooltip();
	ssTableInit();
	initMultiSelect();
	checkPermission();
	//$(window).scroll(toFixTableHeader);
	initModalSelect();
	$(".single-select").comboSelect();setPlaceHolder();
	$('.select2').select2({ width: null});
	
	getDynamicItemList();
	//(document).on("keypress",".numericOnly",function(event) {$(this).val($(this).val().replace(/^0+/,''));});
	/* $(document).on("keypress",".floatOnly",function(event) {$(this).val($(this).val().replace(/^0+/,''));}); */
	$(document).on("keypress",".numericOnly",function (e) {if (String.fromCharCode(e.keyCode).match(/[^0-9]/g)) return false;});	

	$(document).on("keypress",'.floatOnly',function(event) {
		if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {event.preventDefault();}
	});
	
	// Focus QR Code input
	$( "#qrCodeModal" ).on('shown.bs.modal', function(){$('.decodeData').html('');$('#decodeQr').val('');$('#decodeQr').focus();});	
	/**** Start row-column highlight of datatable (ssTable) ***/
	$('.ssTable tbody').on( 'mouseenter', 'td', function () {
		if(ssTable.cells().nodes().length > 0){
			var colIdx = ssTable.cell(this).index().column;
			$( ssTable.cells().nodes() ).removeClass( 'highlight' );
			$( ssTable.column( colIdx ).nodes() ).addClass( 'highlight' );
		}
	});
	/**** End row-column highlight of datatable (ssTable) ***/
	
	/*** Keep Selected Tab after page loading ***/
	$('.tabLinks a[data-toggle="tab"]').click(function (e) {e.preventDefault();$(this).tab('show');});
	$('.tabLinks a[data-toggle="tab"]').on("shown.bs.tab", function (e) {var id = $(e.target).attr("href");localStorage.setItem('selectedTab', id)});
	var selectedTab = localStorage.getItem('selectedTab');
	if (selectedTab != null) {$('.tabLinks a[data-toggle="tab"][href="' + selectedTab + '"]').tab('show');}
	
	$(document).on('click',".addNew",function(){
        var functionName = $(this).data("function");
        var modalId = $(this).data('modal_id');
        var button = $(this).data('button');
		var title = $(this).data('form_title');
		var formId = functionName.split('/')[0];
		var fnsave = $(this).data("fnsave");if(fnsave == "" || fnsave == null){fnsave="save";}
        $.ajax({ 
            type: "GET",   
            url: base_url + controller + '/' + functionName,   
            data: {}
        }).done(function(response){
            $("#"+modalId).modal({show:true});
			$("#"+modalId+' .modal-title').html(title);
			$("#"+modalId+' .modal-body').html("");
            $("#"+modalId+' .modal-body').html(response);
            $("#"+modalId+" .modal-body form").attr('id',formId);
			$("#"+modalId+" .modal-footer .btn-save").attr('onclick',"store('"+formId+"','"+fnsave+"');");
                
            if(button == "close"){
                $("#"+modalId+" .modal-footer .btn-close").show();
                $("#"+modalId+" .modal-footer .btn-save").hide();
            }else if(button == "save"){
                $("#"+modalId+" .modal-footer .btn-close").hide();
                $("#"+modalId+" .modal-footer .btn-save").show();
            }else{
                $("#"+modalId+" .modal-footer .btn-close").show();
                $("#"+modalId+" .modal-footer .btn-save").show();
            }
			initModalSelect();
			$(".single-select").comboSelect();
	        $('.model-select2').select2({ dropdownParent: $('.model-select2').parent() });
			$("#processDiv").hide();
			$("#"+modalId+" .scrollable").perfectScrollbar({suppressScrollX: true});
			setTimeout(function(){ initMultiSelect();setPlaceHolder(); }, 5);
        });
    });	
    
    // Normal Modal Without Save Button [ Used to show only Data ]
    $(document).on('click',".showBasicModal",function(){
        var functionName = $(this).data("function");
        var modalId = $(this).data('modal_id');
        var button = $(this).data('button');
    	var title = $(this).data('form_title');
        $.ajax({ 
            type: "GET",   
            url: base_url + controller + '/' + functionName,   
            data: {}
        }).done(function(response){
            $("#"+modalId).modal({show:true});
    		$("#"+modalId+' .modal-title').html(title);
    		$("#"+modalId+' .modal-body').html("");
            $("#"+modalId+' .modal-body').html(response);
                
            $("#"+modalId+" .modal-footer .btn-close").show();
            $("#"+modalId+" .modal-footer .btn-save").hide();
            $("#"+modalId+" .modal-footer .btn-close").hide();
        });
    });
    
	$(document).on('change','#country_id',function(){
		var id = $(this).val();
		if(id == ""){
			$("#state_id").html('<option value="">Select State</option>');
			$("#city_id").html('<option value="">Select City</option>');
			$(".single-select").comboSelect();
		}else{
			$.ajax({
				url: base_url + 'parties/getStates',
				type:'post',
				data:{id:id},
				dataType:'json',
				success:function(data){
					if(data.status==0)
					{
						swal("Sorry...!", data.message, "error");
					}
					else
					{
						$("#state_id").html(data.result);
						$(".single-select").comboSelect();
						$("#state_id").focus();
					}
				}
			});
		}
	});

	$(document).on('change',"#state_id",function(){
		var id = $(this).val();
		if(id == ""){
			$("#city_id").html('<option value="">Select City</option>');
			$(".single-select").comboSelect();
		}else{
			$.ajax({
				url: base_url + 'parties/getCities',
				type:'post',
				data:{id:id},
				dataType:'json',
				success:function(data){
					if(data.status==0)
					{
						swal("Sorry...!", data.message, "error");
					}
					else
					{
						$("#city_id").html(data.result);
						$(".single-select").comboSelect();
						$("#city_id").focus();
					}
				}
			});
		}
	});	

	$(document).on('click','.pswHideShow',function(){
		var type = $('.pswType').attr('type');
		if(type == "password"){
			$(".pswType").attr('type','text');
			$(this).html('<i class="fa fa-eye-slash"></i>');
		}else{
			$(".pswType").attr('type','password');
			$(this).html('<i class="fa fa-eye"></i>');
		}
	});

	$(document).on('mouseenter', '.mainButton', function(e){
		e.preventDefault();
		$(this).addClass('open');
		$(this).addClass('showAction');
		$(this).children('.fa').removeClass('fa-cog');
		$(this).children('.fa').addClass('fa-times');
		$(this).parent().children('.btnDiv').css('z-index','9');
	});

	$(document).on('mouseleave', '.actionButtons', function(e){
		e.preventDefault();
		$('.mainButton').removeClass('open');
		$('.mainButton').removeClass('showAction');
		$('.mainButton').children('.fa').removeClass('fa-times');
		$('.mainButton').children('.fa').addClass('fa-cog');
		$('.mainButton').parent().children('.btnDiv').css('z-index','-1');
	});
	
	$(document).ajaxStart(function(){
		$('.ajaxModal').show();$('.centerImg').show();$(".error").html("");
		$('.save-form').attr('disabled','disabled');
	});
	
	$(document).ajaxComplete(function(){
		$('.ajaxModal').hide();$('.centerImg').hide();
		$('.save-form').removeAttr('disabled');
		checkPermission();
	});
	
});

function setPlaceHolder(){
	var label="";
	$('input').each(function () {
		if(!$(this).hasClass('combo-input') && $(this).attr("type")!="hidden" )
		{
			label="";
			inputElement = $(this).parent();
			if($(this).parent().hasClass('input-group')){inputElement = $(this).parent().parent();}else{inputElement = $(this).parent();}
			label = inputElement.children("label").text();
			label = label.replace('*','');
			label = $.trim(label);
			if($(this).hasClass('req')){inputElement.children("label").html(label + ' <strong class="text-danger">*</strong>');}
			if(!$(this).attr("placeholder")){if(label){$(this).attr("placeholder", label);}}
			$(this).attr("autocomplete", 'off');
			var errorClass="";
			var nm = $(this).attr('name');
			if($(this).attr('id')){errorClass=$(this).attr('id');}else{errorClass=$(this).attr('name');if(errorClass){errorClass = errorClass.replace("[]", "");}}
			if(inputElement.find('.'+errorClass).length <= 0){inputElement.append('<div class="error '+ errorClass +'"></div>');}
		}
		else{$(this).attr("autocomplete", 'off');}
	});
	$('textarea').each(function () {
		label="";
		label = $(this).parent().children("label").text();
		label = label.replace('*','');
		label = $.trim(label);
		if($(this).hasClass('req')){$(this).parent().children("label").html(label + ' <strong class="text-danger">*</strong>');}
		if(label){$(this).attr("placeholder", label);}
		$(this).attr("autocomplete", 'off');
		var errorClass="";
		var nm = $(this).attr('name');
		if($(this).attr('name')){errorClass=$(this).attr('name');}else{errorClass=$(this).attr('id');}
		if($(this).parent().find('.'+errorClass).length <= 0){$(this).parent().append('<div class="error '+ errorClass +'"></div>');}
	});
	$('select').each(function () {
		label="";
		var selectElement = $(this).parent();
		if($(this).hasClass('single-select')){selectElement = $(this).parent().parent();}
		label = selectElement.children("label").text();
		label = label.replace('*','');
		label = $.trim(label);
		if($(this).hasClass('req')){selectElement.children("label").html(label + ' <strong class="text-danger">*</strong>');}
		var errorClass="";
		var nm = $(this).attr('name');
		if($(this).attr('name')){errorClass=$(this).attr('name');}else{errorClass=$(this).attr('id');}
		if(selectElement.find('.'+errorClass).length <= 0){selectElement.append('<div class="error '+ errorClass +'"></div>');}
	});
}

function initMultiSelect(){
    //$(".jp_multiselect option:selected").prependTo(".jp_multiselect");
	$('.jp_multiselect').multiselect({
		includeSelectAllOption:false,
		enableFiltering:true,
        enableCaseInsensitiveFiltering: true,
		buttonWidth: '100%',
		onChange: function() {
			var inputId = this.$select.data('input_id');
			var selected = this.$select.val();$('#' + inputId).val(selected);
			//$(".jp_multiselect option:selected").prependTo(".jp_multiselect");
		    //reInitMultiSelect();
		}
	});
	$('.form-check-input').addClass('filled-in');
	$('.multiselect-filter i').removeClass('fas');
	$('.multiselect-filter i').removeClass('fa-sm');
	$('.multiselect-filter i').addClass('fa');
	$('.multiselect-container.dropdown-menu').addClass('scrollable');
	$('.multiselect-container.dropdown-menu').css('max-height','200px');
	$('.scrollable').perfectScrollbar({wheelPropagation: !0});
}

function reInitMultiSelect(){
	$('.jp_multiselect').multiselect('rebuild');
	$('.form-check-input').addClass('filled-in');
	$('.multiselect-filter i').removeClass('fas');
	$('.multiselect-filter i').removeClass('fa-sm');
	$('.multiselect-filter i').addClass('fa');
	$('.multiselect-container.dropdown-menu').addClass('scrollable');
	$('.multiselect-container.dropdown-menu').css('height','200px');
	$('.scrollable').perfectScrollbar({wheelPropagation: !0});
}

function statusTab(tableId,status,srnoPosition=1){
    $("#"+tableId).attr("data-url",'/getDTRows/'+status);
    ssTable.state.clear();initTable(srnoPosition);
}

function ssTableInit(){
	var tableOptions = {pageLength: 25,'stateSave':false};
    ssDatatable($('.ssTable'),tableHeaders,tableOptions);
}

function initTable(srnoPosition=1, postData = {}){
	$('.ssTable').DataTable().clear().destroy();
	var tableOptions = {pageLength: 25,'stateSave':false};
	var tableHeaders = {'theads':'','textAlign':textAlign,'srnoPosition':srnoPosition,'reInit':'1'};
	var dataSet = postData;
	ssDatatable($('.ssTable'),tableHeaders,tableOptions,dataSet);
}

function initDataTable(){
	var table = $('#commanTable').DataTable( {
		lengthChange: false,
		responsive: true,
		'stateSave':true,
		retrieve: true,
		buttons: [ 'pageLength','copy', 'excel' ]
	});
	table.buttons().container().appendTo( '#commanTable_wrapper .col-md-6:eq(0)' );
	return table;
};

function store(formId,fnsave){
	// var fd = $('#'+formId).serialize();
	setPlaceHolder();
	if(fnsave == "" || fnsave == null){fnsave="save";}
	var form = $('#'+formId)[0];
	var fd = new FormData(form);
	$.ajax({
		url: base_url + controller + '/' + fnsave,
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
			initTable(); $('#'+formId)[0].reset();$(".modal").modal('hide');
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}else{
			initTable(); $('#'+formId)[0].reset();$(".modal").modal('hide');
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}
				
	});
}

function storeWithController(formId,fnsave,cnt){
	// var fd = $('#'+formId).serialize();
	setPlaceHolder();
	if(fnsave == "" || fnsave == null){fnsave="save";}
	var form = $('#'+formId)[0];
	var fd = new FormData(form);
	$.ajax({
		url: base_url + cnt + '/' + fnsave,
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
			initTable(); $('#'+formId)[0].reset();$(".modal").modal('hide');
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}else{
			initTable(); $('#'+formId)[0].reset();$(".modal").modal('hide');
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}
				
	});
}

function edit(data){
	var button = data.button;if(button == "" || button == null){button="both";};
	var fnEdit = data.fnedit;if(fnEdit == "" || fnEdit == null){fnEdit="edit";}
	var fnsave = data.fnsave;if(fnsave == "" || fnsave == null){fnsave="save";}
	var savebtn_text = data.savebtn_text;if(savebtn_text == "" || savebtn_text == null){savebtn_text="Save";}
	var sendData = {id:data.id};
	if(data.approve_type){sendData = {id:data.id,approve_type:data.approve_type};}
	$.ajax({ 
		type: "POST",   
		url: base_url + controller + '/' + fnEdit,   
		data: sendData,
	}).done(function(response){
		$("#"+data.modal_id).modal();
		$("#"+data.modal_id+' .modal-body').html('');
		$("#"+data.modal_id+' .modal-title').html(data.title);
		$("#"+data.modal_id+' .modal-body').html(response);
		$("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);
		//$("#"+data.modal_id+" .modal-footer .btn-save").html(savebtn_text);
		$("#"+data.modal_id+" .modal-footer .btn-save").attr('onclick',"store('"+data.form_id+"','"+fnsave+"');");
		$("#"+data.modal_id+" .modal-footer .btn-save-close").attr('onclick',"store('"+data.form_id+"','"+fnsave+"','save_close');");
		$("#"+data.modal_id+" .modal-footer .btn-close").attr('data-modal_id',data.form_id);
		if(button == "close"){
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").hide();
			$("#"+data.modalId+" .modal-footer .btn-save-close").hide();
		}else if(button == "save"){
			$("#"+data.modal_id+" .modal-footer .btn-close").hide();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
            $("#"+data.modalId+" .modal-footer .btn-save-close").show();
		}else{
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
            $("#"+data.modalId+" .modal-footer .btn-save-close").show();
		}
		initModalSelect();
		$(".single-select").comboSelect();
		$('.model-select2').select2({ dropdownParent: $('.model-select2').parent() });
		$("#"+data.modal_id+" .scrollable").perfectScrollbar({suppressScrollX: true});
		initMultiSelect();setPlaceHolder();
	});
}

function trash(id,name='Record',fnDelete="delete"){
	var send_data = { id:id };
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
						url: base_url + controller + '/' + fnDelete,
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
								initTable(); initMultiSelect();
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

function ajaxCall(data){
    console.log(data.dataset);
	var button = "close";
	var fname = data.fname;if(fname == "" || fname == null){fname="";}
	var sendData = data.dataset;if(sendData == "" || sendData == null){sendData={};}
	$.ajax({ 
		type: "POST",   
		url: base_url + fname,   
		data: sendData,
	}).done(function(response){
		$("#"+data.modal_id).modal();
		$("#"+data.modal_id+' .modal-title').html(data.title);
		$("#"+data.modal_id+' .modal-body').html(response);
		$("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);
		$("#"+data.modal_id+" .modal-footer .btn-close").attr('data-modal_id',data.form_id);
		if(button == "close"){
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").hide();
		}
		$("#"+data.modal_id+" .scrollable").perfectScrollbar({suppressScrollX: true});
	});
}

function changePsw(formId){
	var fd = $('#'+formId).serialize();
	$.ajax({
		url: base_url + 'hr/employees/changePassword',
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
			initTable(); $(".modal").modal('hide');
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}else{
			initTable(); $(".modal").modal('hide');
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}
				
	});
}

/***** Get Select2 Data *****/
function getDynamicItemList(dataSet = {},eleClass = "large-select2")
{   
	var eleID = $('.' + eleClass).attr('id');
	var url = base_url + $('.' + eleClass).data('url');
	var pholder = $('.' + eleClass).data('pholder');
	
	var base_element = $('.' + eleClass);
	
	$(base_element).select2({
		placeholder: pholder,
		closeOnSelect: true,
		ajax: {
			url: url,
			type: "post",
			dataType: 'json',
			//delay: 250,
			global:false,
			data: function (params) {var dataObj = {searchTerm: params.term,item_type:$(this).attr('data-item_type'),category_id:$(this).attr('data-category_id'),family_id:$(this).attr('data-family_id'),default_val:$(this).attr('data-default_val')};return $.extend(dataObj, dataSet);},
			processResults: function (response) {return {results: response};},
			templateSelection: function (item) { return item.name; },
			cache: true
		},
		dropdownParent: $(base_element).parent()
	});
	
	if(dataSet.id)
	{
    	setTimeout(function()
    	{
    	    if(dataSet.id != "" && dataSet.row != "" && dataSet.text != "")
    	    {
    		    var $option = "<option value='"+dataSet.id+"' data-row='"+dataSet.row+"' selected>"+dataSet.text+"</option>";
                $('.' + eleClass).append($option).trigger('change');
    	    }
    	}, 200);
    }
}

function isInteger(x) { return typeof x === "number" && isFinite(x) && Math.floor(x) === x; }
function isFloat(x) { return !!(x % 1); }

function checkPermission(){
	$('.permission-read').show();
	$('.permission-write').show();
	$('.permission-modify').show();
	$('.permission-remove').show();
	if(permissionRead == "1"){ $('.permission-read').show(); }else{ $('.permission-read').hide(); }
	if(permissionWrite == "1"){ $('.permission-write').show(); }else{ $('.permission-write').hide(); }
	if(permissionModify == "1"){ $('.permission-modify').show(); }else{ $('.permission-modify').hide(); }
	if(permissionRemove == "1"){ $('.permission-remove').show(); }else{ $('.permission-remove').hide(); }
}


function toFixTableHeader() {
    var scroll = $(window).scrollTop();
    $('.ssTable body').css("visibility", "hidden");

    if (scroll >= $('.table-responsive').offset().top) {$(".ssTable thead tr th").css({ top: scroll - $('.table-responsive').offset().top+10 });} else {$(".ssTable thead tr th").css({top: 0 });}
	$(".ssTable thead tr th").css('z-index','99');
    $('.ssTable body').css("visibility", "visible");
	checkPermission();
}
function initModalSelect(){$('.select2').select2({ width: null});}
function formatResult(node) {
    var level = "1";
    if(node.element !== undefined){
      level = (node.element.className);
      if(level.trim() !== ''){var l = level.split("_");level = l[1];}
    }
	
	var lArr = level.split(".");
	level = lArr.length-1;
    var $result = $('<span style="padding-left:' + (20 * level) + 'px;">' + node.text + '</span>');
    return $result;
};


function formatDate(date,format='Y-m-d') {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) 
        month = '0' + month;
    if (day.length < 2) 
        day = '0' + day;
        
    var convertedDate = date;
    if(format == "Y-m-d"){return [year, month, day].join('-');}
    if(format == "y-m-d"){year = year.toString().substr(-2); convertedDate = [year, month, day].join('-');}
    if(format == "d-m-Y"){return [day, month, year].join('-');}
    if(format == "d-m-y"){year = year.toString().substr(-2); convertedDate = [day, month, year].join('-');}
    
    return convertedDate;
}

function formatSymbol(selection) {
    var img_path = $(selection.element).data('img_path');
    
    if(!img_path){return selection.text;}
    else {
        var $selection = $('<img src="' + img_path + '" style="width:20px;"><span class="img-changer-text">' + $(selection.element).text() + '</span>');
        return $selection;
    }
}
function decodeQrCode(){
    var qrValue = $('#decodeQr').val();
	var sendData = {qrValue:qrValue};
	$.ajax({ 
		type: "POST",   
		url: base_url + 'dashboard/decodeQRCode',   
		data: sendData,
	}).done(function(response){
	    $('.decodeData').html(response.decodeData);
	});
}

$(document).on('keypress','#decodeQr',function(e){ 
	if(e.which == 13) {
		var qrValue = $(this).val();
		$.ajax({
			type: "POST",
			url: base_url + 'dashboard/decodeQRCode',
			data:{qrValue:qrValue},
			dataType:'json'
		}).done(function (response) {
	    $('.decodeData').html(response.decodeData);});
	}
});