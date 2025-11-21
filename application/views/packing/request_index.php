<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <h4 class="card-title text-center">Packing Request</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='packingRequsetTable' class="table table-bordered ssTable" data-url='/getPackingRequestedRows'></table>
                        </div>
                    </div>
                </div>
            </div>
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
});
function loadDataTable(status){
    var party_id_filter = $('#party_id_filter').val();
    var item_id_filter = $('#item_id_filter').val();
    $("#packingRequsetTable").attr("data-url",'/getDTRows/'+status+'/'+party_id_filter+'/'+item_id_filter);
    ssTable.state.clear();initTable();
}
</script>