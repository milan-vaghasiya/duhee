<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-12">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"> <a href="<?= base_url($headData->controller . "/index/") ?>" class="btn waves-effect waves-light btn-outline-info  permission-write mr-1"> Inward</a> </li>
                                    <li class="nav-item"> <a href="<?= base_url($headData->controller . "/pendingPackingIndex/") ?>" class="btn waves-effect waves-light btn-outline-info  permission-write mr-1 "> Pending Packing</a> </li>
                                    <li class="nav-item"> <a href="<?= base_url($headData->controller . "/packingIndex/0") ?>" class="btn waves-effect waves-light btn-outline-info  permission-write mr-1 <?=empty($status)?'active':''?>"> Inprocess </a> </li>
                                    <li class="nav-item"> <a href="<?= base_url($headData->controller . "/packingIndex/1") ?>" class="btn waves-effect waves-light btn-outline-info  permission-write mr-1 <?=!empty($status)?'active':''?>"> Completed </a> </li>
                                    <!-- <li class="nav-item"> <a href="<?= base_url($headData->controller . "/firstBoxPacking") ?>" class="btn waves-effect waves-light btn-outline-info  permission-write mr-1"> First/Loose Box </a> </li> -->
                                    <li class="nav-item"> <a href="<?= base_url($headData->controller . "/materialshortage") ?>" class="btn waves-effect waves-light btn-outline-info  permission-write mr-1 "> Material Shortage </a> </li>

                                </ul>
                            </div>
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='firTable' class="table table-bordered ssTable" data-url='/getPackingDTRows/<?=$status?>'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script>
    
    function addBox(data){
        var button = data.button;if(button == "" || button == null){button="both";};
        var fnEdit = data.fnedit;if(fnEdit == "" || fnEdit == null){fnEdit="edit";}
        var fnsave = data.fnsave;if(fnsave == "" || fnsave == null){fnsave="save";}
        var savebtn_text = data.savebtn_text;if(savebtn_text == "" || savebtn_text == null){savebtn_text="Save";}
        var sendData = {id:data.id,packing_type:data.packing_type};
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

    function trashPacking(id,name='Record'){
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
                            url: base_url + controller + '/deletePacking',
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


    function completePacking(data){
        var button = data.button;if(button == "" || button == null){button="both";};
        var fnEdit = data.fnedit;if(fnEdit == "" || fnEdit == null){fnEdit="edit";}
        var fnsave = data.fnsave;if(fnsave == "" || fnsave == null){fnsave="save";}
        var savebtn_text = data.savebtn_text;if(savebtn_text == "" || savebtn_text == null){savebtn_text="Save";}
        var sendData = {id:data.id,box_listing:data.box_listing};
        if(data.approve_type){sendData = {id:data.id,approve_type:data.approve_type};}
        $.ajax({ 
            type: "POST",   
            url: base_url + controller + '/completePacking',   
            data: sendData,
        }).done(function(response){
            $("#"+data.modal_id).modal();
            $("#"+data.modal_id+' .modal-body').html('');
            $("#"+data.modal_id+' .modal-title').html(data.title);
            $("#"+data.modal_id+' .modal-body').html(response);
            $("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);
            //$("#"+data.modal_id+" .modal-footer .btn-save").html(savebtn_text);
            $("#"+data.modal_id+" .modal-footer .btn-save").attr('onclick',"storeCompletePacking('"+data.form_id+"','"+fnsave+"');");
            $("#"+data.modal_id+" .modal-footer .btn-save-close").attr('onclick',"store('"+data.form_id+"','"+fnsave+"','save_close');");
            $("#"+data.modal_id+" .modal-footer .btn-close").attr('data-modal_id',data.form_id);
        
            if(button == "close"){
                $("#"+data.modal_id+" .modal-footer .btn-close").show();
                $("#"+data.modal_id+" .modal-footer .btn-save").hide();
                $("#"+data.modalId+" .modal-footer .btn-save-close").hide();
            }else if(button == "save"){
                $("#"+data.modal_id+" .modal-footer .btn-close").hide();
                $("#"+data.modal_id+" .modal-footer .btn-save").text("Complete");
                $("#"+data.modalId+" .modal-footer .btn-save-close").show();
            }else{
                $("#"+data.modal_id+" .modal-footer .btn-close").show();
                $("#"+data.modal_id+" .modal-footer .btn-save").text("Complete");
                $("#"+data.modalId+" .modal-footer .btn-save-close").show();
            }
                
            
            initModalSelect();
            $(".single-select").comboSelect();
            $('.model-select2').select2({ dropdownParent: $('.model-select2').parent() });
            $("#"+data.modal_id+" .scrollable").perfectScrollbar({suppressScrollX: true});
            initMultiSelect();setPlaceHolder();
        });
    } 

    function storeCompletePacking(formId,fnsave){
        var send_data = { id:id };
        $.confirm({
            title: 'Confirm!',
            content: 'Are you sure want to Complete this Packing?',
            type: 'red',
            buttons: {   
                ok: {
                    text: "ok!",
                    btnClass: 'btn waves-effect waves-light btn-outline-success',
                    keys: ['enter'],
                    action: function(){
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
                },
                cancel: {
                    btnClass: 'btn waves-effect waves-light btn-outline-secondary',
                    action: function(){

                    }
                }
            }
        });
    }

    function trashPackBox(id,name='Record'){
        var send_data = { id:id };
        $.confirm({
            title: 'Confirm!',
            content: 'Are you sure want to delete this Box?',
            type: 'red',
            buttons: {   
                ok: {
                    text: "ok!",
                    btnClass: 'btn waves-effect waves-light btn-outline-success',
                    keys: ['enter'],
                    action: function(){
                        $.ajax({
                            url: base_url + controller + '/deletePackedBox',
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
                                    $('#boxTable').html(data.tbody);
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
</script>



