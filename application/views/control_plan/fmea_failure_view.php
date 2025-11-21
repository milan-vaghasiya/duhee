<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title"><?=$fmeaData->parameter.' [<small>'.$fmeaData->trans_number.'</small>]'?></h4>
                            </div>
                            <div class="col-md-6">
                                <a href="<?=base_url($headData->controller."/addFailureMode/".$trans_id)?>" class="btn btn-outline-primary waves-effect waves-light float-right" ><i class="fa fa-plus"></i> Add Failure Mode</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='fmeaTable' class="table table-bordered ssTable" data-url='/getFMEAFailDTRows/<?=$trans_id?>'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
