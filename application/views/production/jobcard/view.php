<link href="<?= base_url(); ?>assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">
<link href="<?= base_url(); ?>assets/css/style.css?v=<?= time() ?>" rel="stylesheet">
<div class="row">
	<div class="col-12">
		<table class="table" style="border-bottom:1px solid #000000;">
			<tr>
				<td class="text-uppercase text-center" style="font-size:1.3rem;font-weight:bold;width:40%;border-top:1px solid #000000;">PROCESS ROUTE CARD</td>
			</tr>
		</table>
		<table class="table top-table">
			<tr>
				<th style="width:12%">Job Card No.</th>
				<td>: <?= $jobData->job_number ?></td>
				<th style="width:12%">Job Quantity</th>
				<td>: <?= floatval($jobData->qty) ?></td>
				<th style="width:12%">Job Date</th>
				<td>: <?= formatDate($jobData->job_date) ?></td>
			</tr>
			<tr>
				<th>Product </th>
				<td colspan="3">: <?= $jobData->full_name ?></td>
				<!--<th>Drg. No.</th><td> : <?= $jobData->drawing_no ?>, <?= $jobData->rev_no ?></td>-->
				<th>Created By</th>
				<td>: <?= $userDetail->emp_name ?></td>
			</tr>
		</table>
		<h4 class="row-title">Material Detail:</h4>
		<table class="table itemList DD tbl-fs-11">
			<tr class="thead-gray">
				<th>Item Description</th>
				<th class="text-center">Batch No</th>
				<th class="text-center">Heat No</th>
				<th class="text-center" style="width:15%;">Issued Qty</th>
			</tr>
			<?php
			if (!empty($materialDetail)) :
				$i=0;
				foreach ($materialDetail as $row) :
					// foreach($material as $row):
						echo '<tr>';
						echo '<td>' . $row->item_full_name . '</td>';
						echo '<td class="text-center">' . $row->ref_batch . '</td>';
						echo '<td class="text-center">' . $row->heat_no . '</td>';
						echo '<td class="text-center">' . floatVal($row->issue_qty) . '</td>';
						echo '</tr>';
						$i++;
					// endforeach;
				endforeach;
			else :
				echo '<tr><th class="text-center" colspan="3">Record Not Found !</th></tr>';
			endif;
			?>
		</table>
		<h4 class="row-title">Process Detail:</h4>
		<table class="table itemList pad5 tbl-fs-11">
			<tr class="text-center thead-gray">
				<th style="width:5%;">No.</th>
				<th class="text-left">Process Detail</th>
				<?php if($this->CONTROL_PLAN == 1){ ?><th class="text-left">Operation</th> <?php } ?>
				<th style="width:10%;">Issued Qty</th>
				<th style="width:10%;">OK Qty</th>
				<th style="width:10%;">Rej. Qty</th>
				<th style="width:10%;">Pending Qty</th>
			</tr>
			<?php
			if (!empty($processDetail)) :
				$i = 1;
				foreach ($processDetail as $row) :
					echo '<tr>';
					echo '<td class="text-center">' . $i++ . '</td>';
					echo '<td class="text-left">' . $row->process_name . '</td>';
					 if($this->CONTROL_PLAN == 1){ echo '<td class="text-left">' . $row->operation . '</td>'; }
					echo '<td class="text-center">' . floatVal($row->in_qty) . '</td>';
					echo '<td class="text-center">' . floatVal($row->ok_qty) . '</td>';
					echo '<td class="text-center">' . floatVal($row->total_rejection_qty) . '</td>';
					echo '<td class="text-center">' . floatVal($row->pending_prod_qty) . '</td>';
					echo '</tr>';
				endforeach;
			else :
				echo '<tr><th class="text-center" colspan="6">Record Not Found !</th></tr>';
			endif;
			?>

		</table>

		<!-- Inhouse Production Data -->
		<?php if (!empty($inhouseProduction)) { ?>
			<h4 class="row-title">Inhouse Production Detail :</h4>
			<table class="table itemList pad5 tbl-fs-11">
				<tr class="text-center thead-gray">
					<th style="width:5%;">#</th>
					<th>Date</th>
					<th>Process</th>
					<th>Machine</th>
					<th>Operator</th>
					<th>Shift</th>
					<th>Prod. Time</th>
					<th>Out Qty.</th>
					<th>Rej. Qty.</th>
					<th>RW Qty.</th>
					<th>Hold Qty.</th>
					<th>Remark</th>
				</tr>
				<?php
				$i = 1;
				foreach ($inhouseProduction as $row) { if(!empty($row->process_name)):?>
					<tr class="text-center">
						<td style="widtd:5%;"><?=$i++?></td>
						<td><?=formatDate($row->entry_date)?></td>
						<td><?=$row->process_name?></td>
						<td><?=(!empty($row->machine_code) ? '[' . $row->machine_code . '] ' : "") . $row->machine_name?></td>
						<td><?=$row->emp_name?></td>
						<td><?=$row->shift_name?></td>
						<td><?=number_format($row->production_time,3)?></td>
						<td><?=floatVal($row->qty)?></td>
						<td><?=floatVal($row->rej_qty)?></td>
						<td><?=floatVal($row->rw_qty)?></td>
						<td><?=floatVal($row->hold_qty)?></td>
						<td><?=$row->remark?></td>
					</tr>
				<?php endif; } ?>
			</table>
		<?php } ?>

		<!-- Vendor Production Data -->
		<?php if (!empty($vendorProduction)) { ?>
			<h4 class="row-title">Vendor Production Detail :</h4>
			<table class="table itemList pad5 tbl-fs-11">
				<tr class="text-center thead-gray">
					<th style="width:5%;">#</th>
					<th>Date</th>
					<th>Process</th>
					<th>Vendor</th>
					<th>Prod. Time</th>
					<th>Out Qty.</th>
					<th>Rej. Qty.</th>
					<th>RW Qty.</th>
					<th>Hold Qty.</th>
					<th>Remark</th>
				</tr>
				<?php
				if (!empty($vendorProduction)) {
					foreach ($vendorProduction as $row) { ?>
						<tr class="text-center">
							<td style="widtd:5%;"><?=$i++?></td>
							<td><?=formatDate($row->entry_date)?></td>
							<td><?=$row->process_name?></td>
							<td><?=$row->party_name?></td>
							<td><?=number_format($row->production_time,3)?></td>
							<td><?=floatVal($row->qty)?></td>
							<td><?=floatVal($row->rej_qty)?></td>
							<td><?=floatVal($row->rw_qty)?></td>
							<td><?=floatVal($row->hold_qty)?></td>
							<td><?=$row->remark?></td>
						</tr>
					<?php } }?>
			
			</table>
		<?php } ?>
	</div>
</div>