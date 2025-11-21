<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-3">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"> <button onclick="statusTab('dispatchRequsetTable',0);" data-status="0" class=" btn waves-effect waves-light btn-outline-info active" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending</button> </li>
                                    <li class="nav-item"> <button onclick="statusTab('dispatchRequsetTable',1);" data-status="1" class=" btn waves-effect waves-light btn-outline-success" style="outline:0px" data-toggle="tab" aria-expanded="false">Completed</button> </li>
                                </ul>
                            </div>
                            <div class="col-md-2">
                                <h4 class="card-title text-center">Dispatch Request</h4>
                            </div>
                            <div class="col-md-7">
                                <div class="input-group">
                                    <select class="form-control single-select" id="party_id_filter" name="party_id_filter" style="width: 35%;">
                                        <option value="">Select ALL Customer</option>
                                        <?php
                                        foreach ($partyData as $row) :
                                            echo '<option value="' . $row->id . '">[' . $row->party_code . '] ' . $row->party_name . '</option>';
                                        endforeach;
                                        ?>
                                    </select>
                                    <select name="item_id_filter" id="item_id_filter" class="form-control single-select" style="width: 35%;">
                                        <option value="">Select ALL Item</option>
                                        <?php
                                            foreach($itemData as $row):
                                                echo '<option value="'.$row->id.'">[' . $row->item_code . '] '.$row->item_name.'</option>';
                                            endforeach;
                                        ?>
                                    </select>
                                    <div class="input-group-append">
                                        <button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" title="Load Data">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                        <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew permission-write" data-button="both" data-modal_id="modal-lg" data-function="getPackingRequset" data-form_title="Send Request"><i class="fa fa-plus"></i> Send Request</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='dispatchRequsetTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="itemModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1">Item List</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="party_so" method="post" action="">
                <div class="modal-body">
                    <input type="hidden" name="req_ids" id="req_ids" value="">
                    <div class="col-md-12">
                        <div class="error general"></div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-info">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Item Name</th>
                                        <th class="text-center">Packing No.</th>
                                        <th class="text-center">Packing Qty</th>
                                        <th class="text-center">Linked Qty</th>
                                    </tr>
                                </thead>
                                <tbody id="itemData">
                                    <tr>
                                        <td class="text-center" colspan="4">No Data Found</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn waves-effect waves-light btn-outline-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
    $(".modal").on('hide.bs.modal', function(){
        $('#tempItem').html("");
    });
    $(document).on('click','.loaddata',function(){loadDataTable($('.nav-item .active').data('status'));});
    
    $(document).on('click','.createItemList',function(){		
        var id = $(this).data('id');
       
        $.ajax({
            url : base_url + controller + '/getItemData',
            type: 'post',
            data:{id:id},
            dataType:'json',
            success:function(data){
                $("#itemModal").modal();
                $("#req_ids").val(req_ids);
                $("#itemData").html("");
                $("#itemData").html(data.htmlData);
            }
        });
    });
});
function loadDataTable(status){
    var party_id_filter = $('#party_id_filter').val();
    var item_id_filter = $('#item_id_filter').val();
    $("#dispatchRequsetTable").attr("data-url",'/getDTRows/'+status+'/'+party_id_filter+'/'+item_id_filter);
    ssTable.state.clear();initTable();
}
</script>