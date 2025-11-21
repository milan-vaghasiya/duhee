<table class="table table-bordered">
	<thead class="thead-info text-center">
		<tr>
			<th>#</th>
			<th>Serial No.</th>
			<th>Device No.</th>
			<th>Location</th>
			<th>Connection Time</th>
			<th>Status</th>
		</tr>
	</thead>
	<tbody>
	    <?php
	        $htmlData = '';$i=1;
	        if(!empty($deviceStatus))
	        {
	            foreach($deviceStatus as $row)
	            {
	                $bgCls =  ($row->con_status == 'Connect') ? 'bg-success' : 'bg-danger';
	                $htmlData .='<tr class="'.$bgCls.' text-white text-center">
                        			<td>'.$i++.'</td>
                        			<td>'.$row->SRNO.'</td>
                        			<td>'.$row->MachineNo.'</td>
                        			<td>'.$row->Location.'</td>
                        			<td>'.date('d-m-Y H:i:s',strtotime($row->con_date)).'</td>
                        			<td>'.$row->con_status.'</td>
                        		</tr>';
	            }
	        }
	        echo $htmlData;
	    ?>
	</tbody>
</table>
<h5 class="text-danger">NOTE : Status will be delayed by 5-10 Minutes</h5>