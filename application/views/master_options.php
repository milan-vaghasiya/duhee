<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">Master Options</h4>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save float-right save-form permission-write" onclick="store('addMasterOptions','save');"><i class="fa fa-check"></i> Save</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="addMasterOptions">
                            <div class="col-md-12">
                                <div class="row">
                                    <input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
                                    <!--<div class="col-md-6 form-group">-->
                                    <!--    <label for="material_grade">Material Grade</label>-->
                                    <!--    <input type="text" name="material_grade" id="material_grade" class="form-control req" value="<?= (!empty($dataRow->material_grade)) ? $dataRow->material_grade : "" ?>" data-role="tagsinput">-->
                                    <!--</div>-->
                                    <div class="col-md-12 form-group">
                                        <label for="color_code">Color Code</label>
                                        <input name="color_code" id="color_code" class="form-control req" value="<?= (!empty($dataRow->color_code)) ? $dataRow->color_code : "" ?>" data-role="tagsinput">
                                    </div>
                                    <div class="col-md-12 form-group">
                                        <label for="thread_types">Thread Types</label>
                                        <input name="thread_types" id="thread_types" class="form-control req" value="<?= (!empty($dataRow->thread_types)) ? $dataRow->thread_types : "" ?>" data-role="tagsinput">
                                    </div>
                                    <div class="col-md-12 form-group">
                                        <label for="machine_idle_reason">Machine Idle Reason</label>
                                        <input name="machine_idle_reason" id="machine_idle_reason" class="form-control req" value="<?= (!empty($dataRow->machine_idle_reason)) ? $dataRow->machine_idle_reason : "" ?>" data-role="tagsinput">
                                    </div>
                                    <div class="col-md-12 form-group">
                                        <label for="ppap_level">PPAP Level</label>
                                        <input name="ppap_level" id="ppap_level" class="form-control req" value="<?= (!empty($dataRow->ppap_level)) ? $dataRow->ppap_level : "" ?>" data-role="tagsinput">
                                    </div>
                                    <div class="col-md-12 form-group">
                                        <label for="vehicle_no">Vehicle No.</label>
                                        <input name="vehicle_no" id="vehicle_no" class="form-control req" value="<?= (!empty($dataRow->vehicle_no)) ? $dataRow->vehicle_no : "" ?>" data-role="tagsinput">
                                    </div>
                                    <div class="col-md-12 form-group">
                                        <label for="exp_responsibilities">Experience Responsibilities</label>
                                        <textarea name="exp_responsibilities" id="exp_responsibilities" class="form-control" rows="4"><?= (!empty($dataRow->exp_responsibilities)) ? $dataRow->exp_responsibilities : "" ?></textarea>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="doc_check_list">Doc. Check List</label>
                                        <input name="doc_check_list" id="doc_check_list" class="form-control req" value="<?= (!empty($dataRow->doc_check_list)) ? $dataRow->doc_check_list : "" ?>" data-role="tagsinput">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="op_mc_shift">Operator/Machine/Shift (Used In Production)</label>
                                        <select name="op_mc_shift" id="op_mc_shift" class="form-control">
                                            <option value="1" <?= (!empty($dataRow->op_mc_shift) && $dataRow->op_mc_shift==1) ? "selected" : "" ?>>Yes</option>
                                            <option value="2" <?= (!empty($dataRow->op_mc_shift) && $dataRow->op_mc_shift==2) ? "selected" : "" ?>>No</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>