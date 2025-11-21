<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
	<div class="container-fluid bg-container">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<div class="row">
							<div class="col-md-12">
							    <?php if(!empty($mType) && $mType != '6~7'){ ?>
                                    <a href="<?=base_url($headData->controller."/index")?>" class="btn waves-effect waves-light btn-outline-primary permission-write"> Pending</a>
                                    <a href="<?=base_url($headData->controller."/index2")?>" class="btn waves-effect waves-light btn-outline-primary  permission-write"> Material Allocated</a>
                                    <a href="<?=base_url($headData->controller."/index3")?>" class="btn waves-effect waves-light btn-outline-primary  permission-write active"> Material Issued</a>
                                <?php }else{ ?>
                                    <a href="<?=base_url($headData->controller."/qcIndex")?>" class="btn waves-effect waves-light btn-outline-primary permission-write"> Pending</a>
                                    <a href="<?=base_url($headData->controller."/qcIndex2")?>" class="btn waves-effect waves-light btn-outline-primary  permission-write"> Material Allocated</a>
                                    <a href="<?=base_url($headData->controller."/qcIndex3")?>" class="btn waves-effect waves-light btn-outline-primary  permission-write active"> Material Issued</a>
                                <?php } ?>
                            </div>
						</div>
					</div>
					<div class="card-body">
						<div class="table-responsive">
							<table id='materialIssueTable' class="table table-bordered ssTable ssTable-cf" data-ninput='[0]' data-url='/getDTIssueRows/<?= $mType ?>'></table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script>
	$(document).ready(function() {

		$(document).on('keyup change', ".batchQty", function() {
			var batchQtyArr = $("input[name='batch_quantity[]']").map(function() {
				return $(this).val();
			}).get();
			var batchQtySum = 0;
			$.each(batchQtyArr, function() {
				batchQtySum += parseFloat(this) || 0;
			});
			$('#totalQty').html("");
			$('#totalQty').html(batchQtySum.toFixed(3));
			$("#booked_qty").val(batchQtySum.toFixed(3));

			var id = $(this).data('rowid');
			var cl_stock = $(this).data('cl_stock');
			var batchQty = $(this).val();
			$(".batch_qty" + id).html("");
			$(".qty").html();
			if (parseFloat(batchQty) > parseFloat(cl_stock)) {
				$(".batch_qty" + id).html("Stock not avalible.");
				$(this).val(0);
				$('#totalQty').html(batchQtySum - batchQty);
				$("#booked_qty").val(batchQtySum - batchQty);
			}
		});
	});

	function dispatch(data) {
		var button = "";
		$.ajax({
			type: "POST",
			url: base_url + controller + '/issueMaterial',
			data: {
				id: data.id,
				ref_id: data.ref_id
			}
		}).done(function(response) {
			$("#" + data.modal_id).modal();
			$("#" + data.modal_id + ' .modal-title').html(data.title);
			$("#" + data.modal_id + ' .modal-body').html(response);
			$("#" + data.modal_id + " .modal-body form").attr('id', data.form_id);
			$("#" + data.modal_id + " .modal-footer .btn-save").attr('onclick', "store('" + data.form_id + "');");
			if (button == "close") {
				$("#" + data.modal_id + " .modal-footer .btn-close").show();
				$("#" + data.modal_id + " .modal-footer .btn-save").hide();
			} else if (button == "save") {
				$("#" + data.modal_id + " .modal-footer .btn-close").hide();
				$("#" + data.modal_id + " .modal-footer .btn-save").show();
			} else {
				$("#" + data.modal_id + " .modal-footer .btn-close").show();
				$("#" + data.modal_id + " .modal-footer .btn-save").show();
			}
			$(".single-select").comboSelect();
			initMultiSelect();
			setPlaceHolder();
		});
	}

	function request(data) {
		var button = "";
		$.ajax({
			type: "POST",
			url: base_url + controller + '/generateIndent',
			data: {
				id: data.id,
				ref_id: data.ref_id
			}
		}).done(function(response) {
			$("#" + data.modal_id).modal();
			$("#" + data.modal_id + ' .modal-title').html(data.title);
			$("#" + data.modal_id + ' .modal-body').html(response);
			$("#" + data.modal_id + " .modal-body form").attr('id', data.form_id);
			$("#" + data.modal_id + " .modal-footer .btn-save").attr('onclick', "store('" + data.form_id + "','" + data.fnsave + "');");
			if (button == "close") {
				$("#" + data.modal_id + " .modal-footer .btn-close").show();
				$("#" + data.modal_id + " .modal-footer .btn-save").hide();
			} else if (button == "save") {
				$("#" + data.modal_id + " .modal-footer .btn-close").hide();
				$("#" + data.modal_id + " .modal-footer .btn-save").show();
			} else {
				$("#" + data.modal_id + " .modal-footer .btn-close").show();
				$("#" + data.modal_id + " .modal-footer .btn-save").show();
			}
			$(".single-select").comboSelect();
			initMultiSelect();
			setPlaceHolder();
		});
	}

	function statusTab(tableId, status) {
		$("#" + tableId).attr("data-url", '/getDTRows/' + status);
		ssTable.state.clear();
		initTable();
	}

	function issueMaterial(id) {
		var send_data = {
			id: id
		};
		$.confirm({
			title: 'Confirm!',
			content: 'Are you sure want to issue this material ?',
			type: 'red',
			buttons: {
				ok: {
					text: "ok!",
					btnClass: 'btn waves-effect waves-light btn-outline-success',
					keys: ['enter'],
					action: function() {
						$.ajax({
							url: base_url + controller + '/materialIssueFrmAllot',
							data: send_data,
							type: "POST",
							dataType: "json",
							success: function(data) {
								if (data.status == 0) {
									toastr.error(data.message, 'Sorry...!', {
										"showMethod": "slideDown",
										"hideMethod": "slideUp",
										"closeButton": true,
										positionClass: 'toastr toast-bottom-center',
										containerId: 'toast-bottom-center',
										"progressBar": true
									});
								} else {
									initTable();
									initMultiSelect();
									toastr.success(data.message, 'Success', {
										"showMethod": "slideDown",
										"hideMethod": "slideUp",
										"closeButton": true,
										positionClass: 'toastr toast-bottom-center',
										containerId: 'toast-bottom-center',
										"progressBar": true
									});
								}
							}
						});
					}
				},
				cancel: {
					btnClass: 'btn waves-effect waves-light btn-outline-secondary',
					action: function() {

					}
				}
			}
		});
	}
</script>