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
                                    <li class="nav-item"> <a href="<?= base_url($headData->controller . "/index/") ?>" class="btn waves-effect waves-light btn-outline-info   permission-write mr-1"> Inward</a> </li>
                                    <li class="nav-item"> <a href="<?= base_url($headData->controller . "/rqcIndex/0") ?>" class="btn waves-effect waves-light btn-outline-info  permission-write mr-1 <?=empty($status)?'active':''?>"> Inprocess </a> </li>
                                    <li class="nav-item"> <a href="<?= base_url($headData->controller . "/rqcIndex/1") ?>" class="btn waves-effect waves-light btn-outline-info  permission-write mr-1 <?=!empty($status)?'active':''?>"> Completed </a> </li>
                                </ul>
                            </div>     
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='firTable' class="table table-bordered ssTable" data-url='/getRQCDTRows/<?=$status?>'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>

<script src="<?php echo base_url(); ?>assets/js/custom/production/job-card-view.js?v=<?= time() ?>"></script>
<script>
    function acceptRQC(data){
        var button = data.button;if(button == "" || button == null){button="both";};
        var fnEdit = data.fnedit;if(fnEdit == "" || fnEdit == null){fnEdit="edit";}
        var fnsave = data.fnsave;if(fnsave == "" || fnsave == null){fnsave="save";}
        var savebtn_text = data.savebtn_text;if(savebtn_text == "" || savebtn_text == null){savebtn_text="Save";}
        var sendData = {id:data.id,job_card_id:data.job_card_id,job_approval_id:data.job_approval_id,product_id:data.product_id,in_process_id : data.in_process_id};
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

    function completeRQC(id){
        var send_data = { id:id };
        $.confirm({
            title: 'Confirm!',
            content: 'Are you sure want to Complete this Report?',
            type: 'red',
            buttons: {   
                ok: {
                    text: "ok!",
                    btnClass: 'btn waves-effect waves-light btn-outline-success',
                    keys: ['enter'],
                    action: function(){
                        $.ajax({
                            url: base_url + controller + '/completeRQC',
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

</script>