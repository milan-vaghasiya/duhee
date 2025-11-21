<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            
                            <div class="col-md-6">
                            <h4><?php
								 echo '<a href="' . base_url("store/index/" . $store_ref_id) . '">' .$pageHeader . '</a>';	
                            ?></h4>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew permission-write" data-button="both" data-modal_id="modal-md" data-function="addStoreLocation" data-form_title="Add Store"><i class="fa fa-plus"></i> Add Store</button>
                            </div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                        <table id='commanTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
									<tr>
										<th>Action</th>
										<th>#</th>
										<th>Store Name</th>
										<th>Location</th>
										<th>Remark</th>
									</tr>
								</thead>
								<tbody>
									<?php $i=1;
                                        
										foreach($SubStoreData as $row):
											$deleteParam = $row->id.",'Store'";
    										$editParam = "{'id' : ".$row->id.", 'modal_id' : 'modal-md', 'form_id' : 'editStore', 'title' : 'Update Store'}";
    										$editButton=''; $deleteButton='';
											if(!empty($row->ref_id) && empty($row->store_type)):
												$editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
												$deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
											endif;
											if($row->final_location == 0)
											{
												$locationName = '<a href="' . base_url("store/index/" . $row->id) . '">' . $row->location . '</a>';
												
											}else{
												$locationName = $row->location;
											}
											echo '<tr>
												<td><div class="actionWrapper" style="position:relative;">
												<div class="actionButtons actionButtonsRight">
													<a class="mainButton btn-instagram" href="javascript:void(0)"><i class="fa fa-cog"></i></a>
													<div class="btnDiv" style="left:85%;">
														'.$editButton.$deleteButton.'
													</div>
												</div>
											</div></td>
												<td>'.$i++.'</td>
												<td>'.$row->store_name.'</td>
												<td>'.$locationName.'</td>
												<td>'.$row->remark.'</td>
											</tr>';
										endforeach;
									?>
								</tbody>
							</table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>

<script>
    $(document).ready(function () {
        initDataTable();
		
		$('.modal').on('hidden.bs.modal', function () {
			location.reload();
    	});
    });
</script>