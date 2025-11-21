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
                                    <a href="<?=base_url($headData->controller."/pendingForChallan")?>" class="btn btn-outline-info active mr-1" style="border-radius:0px;">Pending Challan</a>
                                    <a href="<?=base_url($headData->controller."/index/0")?>" class="btn btn-outline-info mr-1" style="border-radius:0px;">Pending Return</a>
                                    <a href="<?=base_url($headData->controller."/index/1")?>" class="btn btn-outline-info mr-1" style="border-radius:0px;">Completed</a>
                                   
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='pendingOutsourceTable' class="table table-bordered ssTable" data-url='/getPendingChallanDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- <?php //$this->load->view('production/outsource/challan_modal'); ?> -->

<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url(); ?>assets/js/custom/production/job-card-view.js?v=<?= time() ?>"></script>
<script src="<?php echo base_url(); ?>assets/js/custom/production/outsource.js?v=<?= time() ?>"></script>

