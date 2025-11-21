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
                                 <select name="vendor_id" id="vendor_id" class="form-control single-select " style="width: 100%;">
										<option value="">Select Vendor</option>
										<?php   
											foreach($vendorList as $row): 
												echo '<option value="'.$row->id.'">'.$row->party_name.'</option>';
											endforeach; 
										?>
								</select>
								<div class="error vendor_id"></div>
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
										<th style="min-width:100px;">Challan No.</th>
										<th style="min-width:80px;">Challan Date</th>
										<th style="min-width:80px;">Vendor Name</th>
										<th style="min-width:100px;">Item</th>
										<th style="min-width:100px;">Process</th>
										<th style="min-width:100px;">Scrap (In Kgs)</th>
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
	loadData();
    $(document).on('click','.loaddata',function(){loadData();}); 
});

function loadData(pdf=""){
	$(".error").html("");
		var valid = 1;
        var vendor_id = $('#vendor_id').val();
		var from_date = $('#from_date').val();
		var to_date = $('#to_date').val();
		if($("#vendor_id").val() == ""){$(".vendor_id").html("Vendor is required.");valid=0;}
		if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
		if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
		var postData = {vendor_id:vendor_id,from_date:from_date, to_date:to_date,pdf:pdf};
		if(valid)
		{
			console.log("PDF 1");
			if(pdf == "")
			{
				$.ajax({
					url: base_url + controller + '/getVendorScrap',
					data: postData,
					type: "POST",
					dataType:'json',
					success:function(data){
						$("#reportTable").dataTable().fnDestroy();
						$("#tbodyData").html(data.tblData);
						reportTable();
					}
				});
       		}
			else
			{
				console.log("PDF");
				var url = base_url + controller + '/getVendorScrap/' + encodeURIComponent(window.btoa(JSON.stringify(postData)));
				console.log(url);
				window.open(url);
			}
		}
}

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
		buttons: [ 'pageLength', 'excel', {text: 'Pdf',action: function ( e, dt, node, config ) {loadData(1);}},{text: 'Refresh',action: function ( e, dt, node, config ) {loadAttendanceSheet();}}]
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