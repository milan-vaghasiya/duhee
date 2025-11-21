<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-4">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"> 
                                        <a  href="<?=base_url('deliveryChallan/dispatchRequest')?>" class="btn btn-sm waves-effect waves-light btn-outline-info active" style="outline:0px" >Dispatch Request</a>
                                    </li>
                                    <li class="nav-item"> 
										<a  href="<?=base_url('deliveryChallan/index/0')?>" class="btn btn-sm waves-effect waves-light btn-outline-success" style="outline:0px">Challan</a>
									</li>
                                    <li class="nav-item"> 
										<a  href="<?=base_url('deliveryChallan/index/1')?>" class="btn btn-sm waves-effect waves-light btn-outline-info" style="outline:0px">Completed</a>
									</li>
                                    <li class="nav-item"> 
										<a  href="<?=base_url('deliveryChallan/index/1')?>" class="btn btn-sm waves-effect waves-light btn-outline-success" style="outline:0px">Invoiced</a>
									</li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <h4 class="card-title text-center">Dispatch Request</h4>
                            </div>
							<div class="col-md-4"> </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
						<table id='dispatchRequsetTable' class="table table-bordered ssTable" data-url='/getRequestDTRows'></table>
							<!--<table id='dispatchRequsetTable' class="table table-bordered" style="width:100%;">
								<thead class="thead-info" id="theadData">
									<tr>
										<th>#</th>
										<th>So. No.</th>
										<th>So. Date</th>
										<th>Customer</th>
										<th>Item Description</th>
										<th>Req. Qty.</th>
									</tr>
								</thead>
								<tbody>
								<?php
									/*$i=1;
									if(!empty($reqData))
									{
										foreach($reqData as $row)
										{
											echo '<tr>';
												echo '<td>'.$i++.'</td>';
												echo '<td>'.getPrefixNumber($row->so_prefix,$row->so_no).'</td>';
												echo '<td>'.date('d-m-Y',strtotime($row->so_date)).'</td>';
												echo '<td>['.$row->party_code.'] '.$row->party_name.'</td>';
												echo '<td>['.$row->item_code.'] '.$row->item_name.'</td>';
												echo '<td>'.floatVal($row->req_qty).'</td>';
											echo '</tr>';
										}
									}*/
								?>
								</tbody>
							</table>-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="challanModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1">Create Challan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form method="post" action="<?=base_url('deliveryChallan/createChallanFromRequest')?>">
                <div class="modal-body">
                    <div class="col-md-12"><b>Party Name : <span id="partyName"></span></b></div>
                    <input type="hidden" name="party_id" id="party_id" value="">
                    <input type="hidden" name="party_name" id="party_name" value="">
                    <input type="hidden" name="from_entry_type" id="from_entry_type" value="4">
                    <div class="col-md-12">
                        <div class="error general"></div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-info">
                                    <tr>
                                        <th class="text-center" style="min-width:5%;">#</th>
                                        <th class="text-center" style="min-width:25%;">Req. No.</th>
                                        <th class="text-center" style="min-width:20%;">Req. Date</th>
                                        <th class="text-center" style="min-width:40%;">Product</th>
                                        <th class="text-center" style="min-width:10%;">Req. Qty.</th>
                                    </tr>
                                </thead>
                                <tbody id="requestData">
                                    <tr>
                                        <td class="text-center" colspan="4">No Data Found</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn waves-effect waves-light btn-outline-secondary save-form" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                    <button type="submit" class="btn waves-effect waves-light btn-outline-success save-form"><i class="fa fa-check"></i> Create Challan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
	//jpReportTable('dispatchRequsetTable');
    $(document).on('click','.createChallan',function(){
        var party_id = $(this).data('id');
		var party_name = $(this).data('party_name');

		$.ajax({
			url : base_url + controller + '/getPartyRequest',
			type: 'post',
			data:{party_id:party_id},
			dataType:'json',
			success:function(data){
				$("#challanModal").modal();
				$("#partyName").html(party_name);
				$("#party_name").val(party_name);
				$("#party_id").val(party_id);
				$("#requestData").html("");
				$("#requestData").html(data.htmlData);
			}
		});
    });
});

function jpReportTable(tableId) {
	var jpReportTable = $('#'+tableId).DataTable({
		responsive: true,
		"scrollY": '52vh',
		"scrollX": true,
		deferRender: true,
		scroller: true,
		destroy: true,
		// 'stateSave':false,
		"autoWidth" : false,
		order: [],
		"columnDefs": [
		    {type: 'natural',targets: 0},
			{orderable: false,targets: "_all"},
			{className: "text-center",targets: [0, 1]},
			{className: "text-center","targets": "_all"}
		],
		pageLength: 25,
		language: {search: ""},
		lengthMenu: [
			[ 10, 20, 25, 50, 75, 100, 250,500 ],
			[ '10 rows', '20 rows', '25 rows', '50 rows', '75 rows', '100 rows','250 rows','500 rows' ]
		],
		dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" + "<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
		buttons: ['pageLength', 'excel',{text: 'Pdf',action: function ( e, dt, node, config ) {loadData('pdf');}}],
		"fnInitComplete":function(){$('.dataTables_scrollBody').perfectScrollbar();},
	    "fnDrawCallback": function( oSettings ) {$('.dataTables_scrollBody').perfectScrollbar('destroy').perfectScrollbar();}
	});
	jpReportTable.buttons().container().appendTo('#'+tableId+'_wrapper toolbar');
	$('.dataTables_filter .form-control-sm').css("width", "97%");
	$('.dataTables_filter .form-control-sm').attr("placeholder", "Search.....");
	$('.dataTables_filter').css("text-align", "left");
	$('.dataTables_filter label').css("display", "block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius", "0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius", "0");
	setTimeout(function(){ jpReportTable.columns.adjust().draw();}, 10);
	$('.page-wrapper').resizer(function() {jpReportTable.columns.adjust().draw(); });
	return jpReportTable;
}
</script>