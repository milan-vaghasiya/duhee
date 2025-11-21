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
								 echo '<a href="' . base_url("itemCategory/index/" . $category_ref_id) . '">' .$pageHeader . '</a>';	
                            ?></h4>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew permission-write" data-button="both" data-modal_id="modal-md" data-function="addItemCategory" data-form_title="Add Item Category"><i class="fa fa-plus"></i> Add Item Category</button>
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
										<th>Category Name</th>
										<th>Authority</th>
									</tr>
								</thead>
								<tbody>
									<?php $i=1;
										foreach($SubCategortData as $row):
											$deleteParam = $row->id.",'Item Category'";
    										$editParam = "{'id' : ".$row->id.", 'modal_id' : 'modal-md', 'form_id' : 'editItemCategory', 'title' : 'Update Item Category'}";
    										$editButton=''; $deleteButton='';
											if(!empty($row->ref_id)):
												$editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
												$deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
											endif;
											$cat_code ='';
											if($row->ref_id ==6 || $row->ref_id == 7){
												$cat_code =(!empty($row->tool_type))?'['.$row->tool_type.'] ':'';
											}
											if($row->final_category == 0)
											{
												$cName = $cat_code.'<a href="' . base_url("itemCategory/index/" . $row->id) . '">' . $row->category_name . '</a>';
												
											}else{
												$cName = $cat_code.$row->category_name;
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
												<td>'.$cName.'</td>
												<td>'.$row->ename.'</td>
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

    });
</script>