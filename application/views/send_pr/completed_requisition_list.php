<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
	<div class="container-fluid bg-container">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<div class="row">
							<div class="col-md-9">
								<a href="<?= base_url($headData->controller. "/" . $index) ?>" class="btn waves-effect waves-light btn-outline-primary ">Pending</a>

								<a href="<?= base_url($headData->controller . "/approvedPR/".$mType. "/" . $index) ?>" class="btn waves-effect waves-light btn-outline-primary ">Approved</a>

								<a href="<?= base_url($headData->controller . "/rejectedPR/".$mType. "/" . $index) ?>" class="btn waves-effect waves-light btn-outline-primary ">Rejected</a>

								<a href="<?= base_url($headData->controller . "/allotedMatrialList/".$mType. "/" . $index) ?>" class=" btn waves-effect waves-light btn-outline-primary">Alloted</a>

								<a href="<?= base_url($headData->controller . "/completedPR/".$mType. "/" . $index) ?>" class="btn waves-effect waves-light btn-outline-primary active">Completed</a>

								<a href="<?= base_url($headData->controller . "/returnMaterial/".$mType. "/" . $index) ?>" class=" btn waves-effect waves-light btn-outline-primary " style="outline:0px">Material Return</a>
							</div>
							<div class="col-md-3">
								<button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNewRequisition permission-write" data-button="both" data-modal_id="modal-xl" data-function="addPurchaseRequest/" data-form_title="Requisition at <?= date("d-m-Y H:i:s") ?>" data-fnsave="savePurchaseRequest"><i class="fa fa-plus"></i> Requisition</button>
							</div>



						</div>
					</div>
					<div class="card-body">
						<div class="table-responsive">
							<table id='issueMaterialTable' class="table table-bordered ssTable ssTable-cf" data-ninput='[0,6,7]' data-url='/getCompletedDTRows/<?= $mType ?>'></table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url(); ?>assets/js/custom/requisition.js?v=<?= time() ?>"></script>