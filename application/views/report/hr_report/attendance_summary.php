<?php 
	$this->load->view('includes/header'); 	
	$today = new DateTime();
	$today->modify('first day of this month');$first_day = date('Y-m-d');
	$today->modify('last day of this month');$last_day = date("t",strtotime($today->format('Y-m-d')));
	$monthArr = ['April'=>'04','May'=>'05','June'=>'06','July'=>'07','August'=>'08','September'=>'09','October'=>'10','November'=>'11','December'=>'12','January'=>'01','February'=>'02','March'=>'03'];
	
	$printString = '';
	for($r=1;$r<=5;$r++)
	{
		for($c=1;$c<=$r;$c++){$printString .= ($c + (($r + ($r-1)) * $c) - 1)." ";}
		$printString .= '<br>';
	}
?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-3"> <h4 class="card-title">Attendance Log Summary</h4></div>
							<div class="col-md-9">
                                <div class="input-group">
									<select name="biomatric_id" id="biomatric_id" class="form-control single-select req" style="width:250px;">
										<?php
										    if(in_array($this->userRole,[-1,1,7])){ echo '<option value="ALL">ALL</option>'; }
											foreach($empList as $row):
												if(!empty($row->biomatric_id)):
												    $selected='';
												    if(!in_array($this->userRole,[-1,1,7])){ $selected = ($this->loginId == $row->id)?'selected':'disabled'; }
													echo '<option value="'.$row->emp_code.'" '.$selected.'>['.$row->emp_code.'] '.$row->emp_name.'</option>';
												endif;
											endforeach;
										?>
									</select>
									<input type="date" name="from_date" id="from_date" class="form-control" max="<?=date('Y-m-d')?>" value="<?=date('Y-m-d')?>" />
									<div class="error fromDate"></div>
									<input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-d')?>" />
									<button type="button" class="btn waves-effect waves-light btn-warning float-right" title="Load Data" style="padding: 0.3rem 0px;border-radius:0px;width:12%;" onclick="printaLogSummary('excel');"><i class="fa fa-file-excel"></i> Excel</button>
									<button type="button" class="btn waves-effect waves-light btn-primary float-right" title="Load Data" style="padding: 0.3rem 0px;width:12%;border-top-left-radius:0px;border-bottom-left-radius:0px;" onclick="printaLogSummary('pdf');"><i class="fa fa-file-pdf"></i> PDF</a>
								</div>
                            </div>                        
                        </div>                                         
                    </div>
                    <div class="card-body" style="min-height:50vh;">
                        <div class="table-responsive">
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>

<script src="<?php echo base_url();?>assets/js/custom/month-attendance.js?v=<?=time()?>"></script>
<script>
    $(document).ready(function(){
        
	attendanceSummaryTable();
	$('.jdt thead .clonTR').clone(true).insertAfter( '.jdt thead .clonTR' );
    $('.jdt thead tr:eq(1) th').each( function (i) {
        var title = $(this).text(); //placeholder="'+title+'"
		$(this).html( '<input type="text" style="width:100%;"/>' );
	});
});
function attendanceSummaryTable()
{
	var attendanceSummaryTable = $('#attendanceSummaryTable').DataTable( 
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
	attendanceSummaryTable.buttons().container().appendTo( '#attendanceSummaryTable_wrapper toolbar' );
	$('#attendanceSummaryTable_filter .form-control-sm').css("width","97%");
	$('#attendanceSummaryTable_filter .form-control-sm').attr("placeholder","Search.....");
	$('.dataTables_filter').css("text-align","left");
	$('#attendanceSummaryTable_filter label').css("display","block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius","0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius","0");

	//Datatable Column Filter
    $('.jdt thead tr:eq(1) th').each( function (i) {
		$( 'input', this ).on( 'keyup change', function () {
			if ( attendanceSummaryTable.column(i).search() !== this.value ) {attendanceSummaryTable.column(i).search( this.value ).draw();}
		});
	} );
	return attendanceSummaryTable;
}
</script>