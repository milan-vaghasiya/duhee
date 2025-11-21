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
                                <li class="nav-item"><a href="<?=base_url($headData->controller."/pendingForChallan")?>" class="btn btn-outline-info mr-1" style="border-radius:0px;">Pending Challan</a></li>
                                    <li class="nav-item"> <button onclick="statusTab('outsourceTable',0);" class=" btn waves-effect waves-light btn-outline-info <?=(empty($status)?'active':'')?> mr-1" style="outline:0px" data-toggle="tab" aria-expanded="false ">Pending</button> </li>
                                    <li class="nav-item"> <button onclick="statusTab('outsourceTable',1);" class=" btn waves-effect waves-light btn-outline-info <?=(!empty($status)?'active':'')?>" style="outline:0px" data-toggle="tab" aria-expanded="false">Completed</button> </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='outsourceTable' class="table table-bordered ssTable" data-url='/getDTRows/<?=$status?>'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- <div class="modal fade" id="vendorChallanModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content animated slideDown">
            <form id="vendorChallanForm">
                <input type="hidden" name="vendor_id" id="vendor_id" value="0" />
                <input type="hidden" name="challan_id" id="challan_id" value="0" />

                <div class="modal-header">
                    <div class="col-md-8">
                        <h4 class="modal-title">Create Challan For : <span id="vendorName"></span></h4>
                    </div>
                    <div class="col-md-4">
                        <input type="date" name="trans_date" id="trans_date" class="form-control float-right req" value="<?= date('Y-m-d') ?>">
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label for="process_id">Select Process</label>
                            <select name="process_id" id="process_id" class="form-control single-select req">
                                <option value="">All Process</option>
                                <?php
                                foreach ($processList as $row) :
                                    echo '<option value="' . $row->id . '">' . $row->process_name . '</option>';
                                endforeach;
                                ?>
                            </select>
                        </div>
                        <div class="col-md-8 form-group">
                            <label for="remark">Remark</label>
                            <input type="text" name="remark" id="remark" class="form-control" value="">
                        </div>
                        <div class="table-responsive">
                            <div class="error orderError"></div><br>
                            <table id='outsourceTransTable' class="table table-bordered jpDataTable colSearch">
                                <thead class="thead-info">
                                    <tr class="text-center">
                                        <th class="text-center" style="width:5%;">#</th>
                                        <th class="text-center" style="width:12%;">Job No.</th>
                                        <th class="text-center" style="width:10%;">Job Date</th>
                                        <th class="text-center">Product</th>
                                        <th class="text-center" style="width:8%;">Ok Qty.</th>
                                        <th class="text-center" style="width:10%;">Pending Qty.</th>
                                        <th>Challan Qty.</th>
                                    </tr>
                                </thead>
                                <tbody id="outsourceTransData">
                                    <tr><td colspan="7" class="text-center">No data available in table</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn waves-effect waves-light btn-outline-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                    <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form" onclick="store('vendorChallanForm','save');"><i class="fa fa-check"></i> Create Challan</button>
                </div>
            </form>
        </div>
    </div>
</div> -->

<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url(); ?>assets/js/custom/production/job-card-view.js?v=<?= time() ?>"></script>
<script src="<?php echo base_url(); ?>assets/js/custom/production/outsource.js?v=<?= time() ?>"></script>

<script>
   
</script>