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
                                    <li class="nav-item">  <a href="<?=base_url($headData->controller."/index/0")?>"  class="nav-link btn waves-effect waves-light btn-outline-info mr-1" >Pending Insp.</a> </li>

                                    <li class="nav-item">  <a href="<?=base_url($headData->controller."/index/1")?>" class="nav-link btn waves-effect waves-light btn-outline-info mr-1">Inspected</a> </li>

                                    <li class="nav-item">  <a href="<?=base_url($headData->controller."/index/2")?>" class="nav-link btn waves-effect waves-light btn-outline-info mr-1">Completed</a> </li>

                                    <li class="nav-item"> <a href="<?= base_url($headData->controller . "/pendingRegrindingIndex") ?>" class="nav-link btn waves-effect waves-light btn-outline-info mr-1">Pending Regrinding </a> </li>

                                    <li class="nav-item"> <a href="<?= base_url($headData->controller . "/regrindingChallan/0") ?>" class="nav-link btn waves-effect waves-light btn-outline-info mr-1  ">Regrinding Challan </a> </li>
                                    
                                    <li class="nav-item"> <a href="<?= base_url($headData->controller . "/regrindingInspection/") ?>" class="nav-link btn waves-effect waves-light btn-outline-info mr-1 active">Regrinding Inspection </a> </li>

                                    <li class="nav-item"> <a href="<?= base_url($headData->controller . "/reRegrindingItem") ?>" class="nav-link btn waves-effect waves-light btn-outline-info mr-1 ">Reregrinding </a> </li>
                                </ul>
                            </div>
                           
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='inspectionTable' class="table table-bordered ssTable" data-url='/getRegrindingInspectionDTRows/<?=$status?>'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
