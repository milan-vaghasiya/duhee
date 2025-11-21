<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">Purchase Enquiry</h4>
                            </div>
                            <div class="col-md-6">
                                <a href="<?=base_url($headData->controller."/addEnquiry")?>" class="btn waves-effect waves-light btn-outline-primary float-right permission-write"><i class="fa fa-plus"></i> Add Enquiry</a>
                            </div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='purchaseEnquiryTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/purchase-enquiry-confirm.js?v=<?=time()?>"></script>