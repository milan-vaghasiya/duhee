<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card"> 
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-8">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"> <a href="<?= base_url($headData->controller . "/dispatchExport/") ?>" class="btn waves-effect waves-light btn-outline-info   mr-1 "> Pending</a> </li>
                                    <li class="nav-item"> <a href="<?= base_url($headData->controller . "/exportPackingIndex/1") ?>" class="btn waves-effect waves-light btn-outline-info   mr-1 <?=( $packing_type==1)?'active':''?>" > Tentative Packing</a> </li> 
                                    <li class="nav-item"> <a href="<?= base_url($headData->controller . "/exportPackingIndex/2") ?>" class="btn waves-effect waves-light btn-outline-info   mr-1 <?=($packing_type==2)?'active':''?>" > Final Packing</a> </li>
                                    <li class="nav-item"> <a href="<?= base_url($headData->controller . "/exportPackingIndex/3") ?>" class="btn waves-effect waves-light btn-outline-info   mr-1 <?=($packing_type==3)?'active':''?>" > Invoiced</a> </li> 
                                </ul>
                            </div>
                            
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='packingTable' class="table table-bordered ssTable" data-url='/getExportDTRows/<?=$packing_type?>'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<div class="modal fade" id="tagModel" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content animated zoomIn border-light">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark"><i class="fas fa-print" ></i> &nbsp;&nbsp; Packing Print</h5>
                <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
			<form id="printPackingTag" action="<?=base_url($headData->controller.'/printPackingTag')?>" method="POST"  target="_blank">
                <div class="modal-body">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label for="format_id">Print Format</label>
                                <select name="format_id" id="format_id" class="form-control single-select">
                                    <option value="">Select Format</option>
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="dispatch_date">Dispatch Date</label>
                                <input type="date" name="dispatch_date" id="dispatch_date" class="form-control" value="<?=date('Y-m-d')?>" />
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Item Name</label>
                                <select name="item_id" id="item_id" class="form-control single-select">
                                    <option value="">Select Item</option>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="inv_no">Invoice No</label>
                                <input type="text" name="inv_no" id="inv_no" class="form-control" value="" />
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="lr_no">L.R. No</label>
                                <input type="text" name="lr_no" id="lr_no" class="form-control" value="" />
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="lot_qty">Lot Qty.</label>
                                <input type="text" name="lot_qty" id="lot_qty" class="form-control floatOnly" value="" />
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="heat_no">Heat No.</label>
                                <input type="text" name="heat_no" id="heat_no" class="form-control" value="" />
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="trans_way">By</label>
                                <select name="trans_way" id="trans_way" class="form-control req">
                                    <option value="By Air">By Air</option>
                                    <option value="By Sea">By Sea</option>
                                    <option value="By Road">By Road</option>
                                </select>
                                <!-- <input type="text" name="trans_way" id="trans_way" class="form-control" value="" /> -->
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="print_qty">No. Of Print</label>
                                <input type="text" name="print_qty" id="print_qty" class="form-control floatOnly" value="" />
                            </div>
                            <input type="hidden" name="print_id" id="print_id">
                            <input type="hidden" name="trans_no" id="trans_no" value="0">
                            <input type="hidden" name="packing_type" id="packing_type" value="0">
                            <input type="hidden" name="order_id" id="order_id" value="0">
                        </div>
                        <div class="row">
                            <div class="col-sm-12 form-group">
                                <a href="javascript:void(0);" class="btn btn-labeled btn-success bg-success-dark printPackingTag float-right" type="button"><i class="fas fa-print" ></i>&nbsp;&nbsp;<span class="btn-label">Print &nbsp;&nbsp;</span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script>
   $(document).ready(function(){

        <?php if(!empty($printID)): ?>
            $("#printModel").attr('action',base_url + controller + '/packing_pdf');
            $("#printsid").val(<?=$printID?>);
            $("#print_dialog").modal();
        <?php endif; ?>

        $(document).on("click",".printPacking",function(){
            $("#printModel").attr('action',base_url + controller + '/packing_pdf');
            $("#printsid").val($(this).data('id'));
            $("#print_dialog").modal();
        });		

        $(document).on("click",".packingTag",function(){
            $('#printPackingTag')[0].reset();
            var trans_no = $(this).data('trans_no');
            var order_id = $(this).data('soid');
            var packing_type = $(this).data('packing_type');
            var packing_sticker = $(this).data('packing_sticker');
            $.ajax({
                url:base_url+'packing/getPackingItems',
                type:'post',
                data:{trans_no:trans_no,order_id:order_id,packing_type:packing_type},
                dataType:'json',
                success:function(data){
                    $("#item_id").html("");
                    $("#item_id").html(data.options);
                    $("#item_id").comboSelect();
                }
            });
            
            $.ajax({
                url:base_url+'packing/printFormatList',
                type:'post',
                data:{},
                dataType:'json',
                success:function(data){
                    $("#format_id").html("");
                    $("#format_id").html(data.options);
                    $("#format_id").comboSelect();
                }
            });
            
            $("#trans_no").val(trans_no); 
            $("#packing_type").val(packing_type); 
            $("#order_id").val(order_id); 
            $("#print_id").val(packing_sticker);
            $(".printTag1").data('id',$(this).data('id'));
            $("#tagModel").modal();
        });

        $(document).on("change","#item_id",function(){
            var id = $("#packingid").val();
            var item_id = $("#item_id").val();
            var order_id ='';
            // $.ajax({
            //     url:base_url+'packing/getSalesOrderNoListForPacking',
            //     type:'post',
            //     data:{id:id,order_id:order_id,item_id:item_id},
            //     dataType:'json',
            //     success:function(data){
                    
            //         $("#so_id_footer").html("");
            //         $("#so_id_footer").html(data.orderNoList);
            //         $("#so_id_footer").comboSelect();
            //     }
            // });
            $("#packingid").val(id); 
            $(".printTag1").data('id',$(this).data('id'));
            $("#tagModel").modal();
        });

        $(document).on('click','.createItemList',function(){		
            var id = $(this).data('id');
            $.ajax({
                url : base_url + controller + '/getItm',
                type: 'post',
                data:{id:id},
                dataType:'json',
                success:function(data){
                    $("#itemModal").modal();
                    $("#itemData").html("");
                    $("#itemData").html(data.htmlData);
                }
            });
        });

        $(document).on("click",".printPackingTag",function(){ 
            var IsValid = 1; $('.print_qty').html(''); $('.item_id').html('');
            if($('#item_id').val() == 0 || $('#item_id').val() == ''){ $('.item_id').html('Item Name is required.'); IsValid=0;}
            if($('#print_qty').val() == 0 || $('#print_qty').val() == ''){ $('.print_qty').html('Print Qty is required.'); IsValid=0;}
            if($("#format_id").val() == ""){ $('.format_id').html('Print Format is required.'); IsValid=0; }
            if(IsValid){
                $('#printPackingTag').submit(); 
            }
        });
    });
    function trashExportPacking(trans_no,packing_type,req_id,name='Record'){
        var send_data = { trans_no:trans_no,packing_type:packing_type,req_id:req_id};
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
                            url: base_url + controller + '/deleteExportPacking',
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