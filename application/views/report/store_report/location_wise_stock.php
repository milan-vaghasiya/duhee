<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-5">
                                <h4 class="card-title pageHeader"><?=$pageHeader?></h4>
                            </div>       
							<div class="col-md-3">
								<select name="item_type" id="item_type" class="form-control single-select">
                                    <option value="">Select Item Type</option>
                                    <?php
                                    if(!empty($itemGroup)):
										foreach($itemGroup as $row):
											echo '<option value="'.$row->id.'">'.$row->group_name.'</option>';
										endforeach;  
                                    endif;
                                    ?>
                                </select>
							</div>   
                            <div class="col-md-4">  
                                <div class="input-group">
                                    <select name="location_id" id="location_id" class="form-control single-select" style="width:80%">
                                        <option value="">Select Location</option>
                                        <?php
                                        if(!empty($locationList)):
                                            foreach($locationList as $lData):
                                                echo '<optgroup label="'.$lData['store_name'].'">';
                                                foreach($lData['location'] as $row):
                                                    echo '<option value="'.$row->id.'">'.$row->location.' </option>';
                                                endforeach;
                                                echo '</optgroup>';
                                            endforeach;
                                        endif;
                                        ?>
                                    </select>
                                    <div class="input-group-append">                                        
                                        <button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" title="Load Data">
									        <i class="fas fa-sync-alt"></i> Load
								        </button>
                                    </div>
                                </div>
                            </div>                  
                        </div>                                         
                    </div>
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
                                    <tr class="text-center">
                                        <th colspan="5">Location Wise Stock Report</th>
                                    </tr>
									<tr>
										<th>#</th>
										<th>Part No.</th>
										<th>Part Name</th>
										<th>Batch No.</th>
										<th>Stock Qty.</th>
									</tr>
								</thead>
								<tbody id="tbodyData"></tbody>
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
$(document).ready(function(){
	reportTable();
    $(document).on('click','.loaddata',function(e){
		$(".error").html("");
		var valid = 1;
		var location_id = $('#location_id').val();
		var item_type = $('#item_type').val();
		if($("#location_id").val() == ""){$(".location_id").html("location is required.");valid=0;}
		if($("#item_type").val() == ""){$(".item_type").html("Item Type is required.");valid=0;}
		
		if(valid)
		{
            $.ajax({
                url: base_url + controller + '/getLocationWiseStockReport',
                data: {location_id:location_id, item_type:item_type},
				type: "POST",
				dataType:'json',
				success:function(data){
                    $("#reportTable").dataTable().fnDestroy();
					$("#tbodyData").html(data.tbody);
					reportTable();
                }
            });
        }
    });    
});

function reportTable()
{
	var reportTable = $('#reportTable').DataTable( 
	{
		responsive: true,
		//'stateSave':true,
		"autoWidth" : false,
		order:[],
		"columnDefs": 	[
							{ type: 'natural', targets: 0 },
							{ orderable: false, targets: "_all" }, 
							{ className: "text-left", targets: [0,1] }, 
							{ className: "text-center", "targets": "_all" } 
						],
		pageLength:25,
		language: { search: "" },
		lengthMenu: [
            [ 10, 25, 50, 100, -1 ],[ '10 rows', '25 rows', '50 rows', '100 rows', 'Show all' ]
        ],
		dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" +"<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
		buttons: [ 'pageLength', 'excel', {text: 'Refresh',action: function ( e, dt, node, config ) { $('.loaddata').trigger('click'); }}]
	});
	reportTable.buttons().container().appendTo( '#reportTable_wrapper toolbar' );
	$('.dataTables_filter .form-control-sm').css("width","97%");
	$('.dataTables_filter .form-control-sm').attr("placeholder","Search.....");
	$('.dataTables_filter').css("text-align","left");
	$('.dataTables_filter label').css("display","block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius","0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius","0");
	return reportTable;
}
</script>