<div class="col-md-12">
	<div class="error general_error"></div>
    <div class="table-responsive">
        <table id="receiveItemTable" class="table table-bordered align-items-center">
            <thead class="thead-info">
                <tr>
                    <th style="width:5%;">#</th>
					<th>Location</th>
					<th>Batch</th>
                    <th>Qty.</th>
                    <th>Challan No.</th>
                    <th>Date</th>
                    <th class="text-center" style="width:10%;">Action</th>
                </tr>
            </thead>
            <tbody id="receiveItemTableData">
                <?=(!empty($resultHtml) ? $resultHtml : '')?>
            </tbody>
        </table>
    </div>
</div>