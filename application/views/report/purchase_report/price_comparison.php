<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="card-title pageHeader text-center"><?=$pageHeader?></h4>
                            </div>
                        </div>
                        <hr style="width:100%;">
                        <div class="row">
                            <div class="col-md-2 from-group">
								<select name="item_type" id="item_type" class="form-control single-select req">
									<option value="">Select All Category</option>
									<?php 
										foreach($itemTypeData as $row):
											echo '<option value="'.$row->id.'">'.$row->group_name.'</option>';
										endforeach;
									?>
								</select>
							</div>      
                            <div class="col-md-3 from-group">
								<select name="item_name" id="item_name" class="form-control single-select req">
									<option value="">Select Item</option>
									<?php 
										foreach($itemNameData as $row):
											echo '<option value="'.$row->id.'">'.$row->item_name.'</option>';
										endforeach;
									?>
								</select>
                                <div class="error item_name"></div>
							</div> 
                            <div class="col-md-2 from-group">   
                                <input type="date" name="from_date" id="from_date" class="form-control" max="<?=date('Y-m-d')?>" value="<?=date('Y-m-d')?>" />
                                <div class="error fromDate"></div>
                            </div>     
                            <div class="col-md-3 from-group">  
                                <div class="input-group">
                                    <input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-d')?>" />
                                    <div class="input-group-append">
                                        <button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" title="Load Data">
									        <i class="fas fa-sync-alt"></i> Load
								        </button>
                                    </div>
                                </div>
                                <div class="error toDate"></div>
                            </div>    
                            <div class="col-md-2 from-group">
								<select name="short_by" id="short_by" class="form-control single-select">
									<option value="">Short By</option>
									<option value="1">Short by Date</option>
									<option value="2">Short by Price</option>
								</select>
							</div>             
                        </div>                                         
                    </div>
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
                                    <tr class="text-center">
                                        <th colspan="5">Price Comparison Report</th>
                                    </tr>
									<tr class="text-center">
                                        <th>#</th>
                                        <th>PO NO.</th>
                                        <th>PO Date</th>
                                        <th>Supplier Name</th>
                                        <th>Price</th>
									</tr>
								</thead>
								<tbody id="tbodyData"> </tbody>
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
		var item_type = $('#item_type').val();
		var item_name = $('#item_name').val();
		var from_date = $('#from_date').val();
		var to_date = $('#to_date').val();
		if($("#item_namec").val() == ""){$("#item_name").html("Item Name is required.");valid=0;}
		if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
		if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
		if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}
		if(valid)
		{
            $.ajax({
                url: base_url + controller + '/getPriceComparison',
                data: {item_type:item_type,item_name:item_name,from_date:from_date, to_date:to_date},
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
    
    $(document).on('change', '#item_type', function () {
        var item_type = $(this).val();
        if (item_type) {
            $.ajax({
                url: base_url + controller + '/getItemListByCategory',
                data: { item_type: item_type },
                type: "POST",
                dataType: 'json',
                success: function (data) {
                    $("#item_name").html(data.htmlData);
                    $("#item_name").comboSelect();
                }
            });
        }
    });

	$(document).on('change', '#short_by', function () {
        var short_by = $(this).val();
		var from_date = $('#from_date').val();
		var to_date = $('#to_date').val();
        if (short_by) {
            $.ajax({
                url: base_url + controller + '/getPriceComparison',
                data: {short_by:short_by ,from_date:from_date, to_date:to_date},
                type: "POST",
                dataType: 'json',
                success: function (data) {
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
		scrollY: '55vh',
        scrollCollapse: true,
		"scrollX": true,
		"scrollCollapse":true,
		"autoWidth" : false,
		order:[],
		"columnDefs": 	[
							{ type: 'natural', targets: 0 },
							{ orderable: false, targets: "_all" }, 
							{ className: "text-left", targets: [0,2] }, 
							{ className: "text-center", "targets": "_all" } 
						],
		pageLength:25,
		language: { search: "" },
		lengthMenu: [
            [ 10, 25, 50, 100, -1 ],[ '10 rows', '25 rows', '50 rows', '100 rows', 'Show all' ]
        ],
		dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" +"<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
		buttons: [ 'pageLength', 'excel', {text: 'Refresh',action: function ( e, dt, node, config ) {loadAttendanceSheet();}}]
		// buttons: [ 'pageLength', 'excel', {text: 'Refresh',action: function () {$('.loadData').trigger('click');}}]
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