<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-5">
                                <h4 class="card-title pageHeader">STOCK REGISTER</h4>
                            </div>       
                            <div class="col-md-3">   
                               
                            </div>     
                            <div class="col-md-4">  
                               
                            </div>                  
                        </div>                                         
                    </div>
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
									<tr>
										<th>#</th>
										<th>Received No</th>
										<th>Bill Date</th>
										<th>Code No</th>
										<th>Rev. No</th>
										<th>Product</th>
										<th>Received Qty</th>
									</tr>
								</thead>
								<tbody id="tbodyData">
                                    <?php
                                        $i=1;
                                        foreach($stockData as $row):
                                    ?>
                                    <tr>
                                        <td><?=$i++?></td>
                                        <td><?=$row->received_no?></td>
                                        <td><?=date("d-m-Y",strtotime($row->inv_date))?></td>
                                        <td><?=$row->item_code?></td>
                                        <td><?=$row->rev_no?></td>
                                        <td><?=$row->item_name?></td>
                                        <td><?=$row->qty?></td>
                                    </tr>
                                    <?php
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
$(document).ready(function(){
	reportTable();   
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
		buttons: [ 'pageLength', 'excel', 'pdf']
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