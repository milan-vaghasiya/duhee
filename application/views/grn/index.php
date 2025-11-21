<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                        <div class="col-md-4">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"> <button onclick="tabStatus('grnTable',1);" class=" btn waves-effect waves-light btn-outline-info active" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending</button> </li>
                                    <li class="nav-item"> <button onclick="tabStatus('grnTable',2);" class=" btn waves-effect waves-light btn-outline-success" style="outline:0px" data-toggle="tab" aria-expanded="false">Completed</button> </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h4 class="card-title">Goods Receipt Note</h4>
                            </div>
                            <div class="col-md-6">
                                <!-- <a href="<?=base_url($headData->controller."/addGRN")?>" class="btn waves-effect waves-light btn-outline-primary permission-write float-right"><i class="fa fa-plus"></i> Add GRN</a> -->
                            </div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='grnTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script>
    function tabStatus(tableId,status){
    $("#"+tableId).attr("data-url",'/getDTRows/'+status);
    ssTable.state.clear();initTable();

}
</script>