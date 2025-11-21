
<div class="row">
	<div class="col-12">
		<table class="table top-table-border" style="margin-top:2px;">
			<tr>
				<td style="width:60%;vertical-align:top;">
					<b>Suplier Name : <?=(!empty($inInspectData->party_name)) ? $inInspectData->party_name:""?></b> <br><br>
					<b>Part Name :</b> <?=(!empty($inInspectData->full_name)) ? $inInspectData->full_name:""?> <br><br>
					<b>Part No.:</b> <?=(!empty($inInspectData->fgCode)) ?$inInspectData->fgCode:""?><?php (!empty($inInspectData->charge_no)) ?'/'.$inInspectData->charge_no:""?> <br><br>
					<b>Material Grade :</b> <?=(!empty($inInspectData->material_grade)) ? $inInspectData->material_grade:""?><br>
				</td>
				<td style="width:40%;vertical-align:top;">
					<b>Receive Date :</b> <?=(!empty($inInspectData->trans_date)) ? formatDate($inInspectData->trans_date) : ""?> <br><br>
					<b>Lot Qty.:</b> <?=(!empty($inInspectData->qty)) ? $inInspectData->qty:""?> <br><br>
					<b>Batch No.:</b> <?=(!empty($inInspectData->batch_no)) ? $inInspectData->batch_no:""?> <br><br>
					<b>Color Code:</b> <?=(!empty($inInspectData->color_code)) ? $inInspectData->color_code:""?><br>
				</td>
			</tr>
		</table>
		<?php
				$pramIds = explode(',',$inInspectData->parameter_ids); 
				$prms = '';$spcf = '';$tolerance = '';$instr = '';$i=1;$sample = Array();$param_sample='';$charCount = 'A';$thead='';
				foreach($paramData as $param):
					if(in_array($param->id, $pramIds)):
						$prms .= '<td>'.$param->parameter.'</td>';
						$spcf .= '<td>'.$param->specification.'</td>';
						$tolerance .= '<td>'.$param->lower_limit.'</td>';
						$instr .= '<td>'.$param->measure_tech.'</td>';
						if(!empty($inInspectData->parameter_ids)):
							$os = json_decode($inInspectData->observation_sample); 
							$sample[$i-1] = $os->{$param->id};
						else:
							$sample[$i-1] = ['','','','','','','','','','',''];
						endif; 
						$thead .= '<th style="width: 10%;">'.$charCount++.'</th>';
						$i++;
					endif;
				endforeach;
				$i = $i-1;
				for($x=$i;$x<$i;$x++){
					$prms .= '<td>&nbsp;</td>';
					$spcf .= '<td>&nbsp;</td>';
					$tolerance .= '<td>&nbsp;</td>';
					$instr .= '<td>&nbsp;</td>';
					$sample[$x] = ['','','','','','','','','','',''];
					$thead .= '<th style="width: 10%;">'.$charCount++.'</th>';
				}
				for($r=0;$r<11;$r++){
					if($r <= 9){$param_sample .= '<tr class="text-center"><th>'.($r+1).'</th>';}
					else{$param_sample .= '<tr class="text-center"><th>Result</th>';}
					$c=0;
					foreach($sample as $smpl){
						$pl = count($sample[$c]);$count=1;
						$param_sample .= '<td>'.$sample[$c++][$r].'</td>';
					}
					$param_sample .= '</tr>';
				}
			?>
		<table class="table item-list-bb" style="margin-top:10px;">
			<tr style="text-align:center;" class="text-center">
				<th style="width: 10%!important;"></th>
				<?=$thead?>
			</tr>
			<tr class="text-center">
				<th>Parameter</th>
				<?=$prms?>
			</tr>
			<tr class="text-center">
				<th>Specification</th>
				<?=$spcf?>
			</tr>
			<tr class="text-center">
				<th>Tolerance</th>
				<?=$tolerance?>
			</tr>
			<tr class="text-center">
				<th>Instruments Use</th>
				<?=$instr?>
			</tr>
			<tr class="text-center">
				<th colspan="<?=($i+1)?>" style="font-size:14px;">Observation on Samples</th>
			</tr>
			<?=$param_sample?>
		</table>
		
		<table class="table top-table" style="margin-top: 50px;">
			<tr>
				<th style="width:8%">Note :</th>
				<td style="text-align:left">
				<?=(!empty($inInspectData->approval_remarks)) ? $inInspectData->approval_remarks:""?>
				</td>
			</tr>
		</table>
	</div>
</div>