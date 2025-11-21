<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-4">
                                <h4 class="card-title pageHeader"><?=$pageHeader?></h4>
                            </div>     
                            <div class="col-md-3">
                                <select name="dept_id" id="dept_id" class="form-control single-select">
                                    <option value="">Select Department</option>
                                    <?php   
										foreach($deptData as $row): 
											echo '<option value="'.$row->id.'">'.$row->name.'</option>';
										endforeach; 
                                    ?>
                                </select>
								<div class="error dept_id"></div>
                            </div>
                            <div class="col-md-2">   
                                <input type="date" name="from_date" id="from_date" class="form-control" max="<?=date('Y-m-d')?>" value="<?=date('Y-m-d')?>" />
                                <div class="error fromDate"></div>
                            </div>     
                            <div class="col-md-3">  
                                <div class="input-group">
                                    <input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-d')?>" />
                                    <div class="input-group-append ml-2">
                                        <button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" title="Load Data">
									        <i class="fas fa-sync-alt"></i> Load
								        </button>
                                    </div>
                                </div>
                                <div class="error toDate"></div>
                            </div>               
                        </div>                                         
                    </div>
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
									<tr>
                                        <th style="min-width:50px;">#</th>
										<th style="min-width:100px;">Date</th>
										<th style="min-width:80px;">Shift</th>
										<th style="min-width:80px;">M/C No.</th>
										<th style="min-width:100px;">Operator Name</th>
										<th style="min-width:100px;">Part No.</th>
										<th style="min-width:150px;">Setup</th>
										<th style="min-width:50px;">Cycle Time(m:s)</th>
										<th style="min-width:50px;">Load-Unload Time(m:s)</th>
										<th style="min-width:50px;">Setting Time(m:s)</th>
										<th style="min-width:50px;">Total Production</th>
										<th style="min-width:50px;">R/w. Qty.</th>
										<th style="min-width:50px;">Rej. Qty.</th>

										<th style="min-width:100px;">Breakdown<br /><small>(In Min.)</small></th>
										<th style="min-width:100px;">Misc. Times<br /><small>(In Min.)</small></th>
										<th style="min-width:100px;">Reason</th>
										<th style="min-width:100px;">Planned Pro.Time<br /><small>(In Min.)</small></th>
										<th style="min-width:100px;">Plan Qty.</th>
										<th style="min-width:100px;">Run Time<br /><small>(In Min.)</small></th>
										<th style="min-width:100px;">Ok Qty.</th>
										<th style="min-width:100px;">Load Unload Time <br /><small>(In Minutes per shift)</small></th>
										<th style="min-width:100px;">Availability <br /><small>(Run Time/Planned Prod. Time )</small> </th>
										<th style="min-width:100px;">Performance <br /><small>(Cycle Time x Total Prod./Run time)</small> </th>
										<th style="min-width:100px;">Overall Performance <br /><small>(Cycle Time X Total Prod./Planned Time)</small></th>
										<th style="min-width:100px;">Quality Rate <br /><small>( OK Qty./Total Prod.)</small></th>
										<th style="min-width:100px;">OEE <br /><small>(Availability x Performance x Quality Rate x 100)</small></th>
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
<?=$floatingMenu?>
<script>
$(document).ready(function(){
	reportTable();
    $(document).on('click','.loaddata',function(e){
		$(".error").html("");
		var valid = 1;
        var dept_id = $('#dept_id').val();
		var from_date = $('#from_date').val();
		var to_date = $('#to_date').val();
		if($("#dept_id").val() == ""){$(".dept_id").html("Department is required.");valid=0;}
		if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
		if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
		if(valid)
		{
            $.ajax({
                url: base_url + controller + '/getMachineWiseProduction',
                data: {dept_id:dept_id,from_date:from_date, to_date:to_date},
				type: "POST",
				dataType:'json',
				success:function(data){
                    $("#reportTable").dataTable().fnDestroy();
					$("#theadData").html(data.thead);
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
		buttons: [ 'pageLength', 'excel', {text: 'Refresh',action: function ( e, dt, node, config ) {loadAttendanceSheet();}}]
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