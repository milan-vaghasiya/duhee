<?php 
class ProductionReportModel extends MasterModel
{
    private $jobCard = "job_card";
	private $jobOutward = "job_outward";    
	private $jobRejection = "job_rejection";
	private $jobTransaction = "jobwork_transaction";
	private $jobTrans = "job_transaction";
	private $jobApproval = "job_approval";

    public function getJobcardList(){
        $data['tableName'] = $this->jobCard;
        $data['select'] = 'job_card.id,job_card.job_no,job_card.job_prefix,job_card.job_number,job_card.job_date,item_master.item_code,item_master.item_name';
        $data['join']['item_master'] = 'item_master.id = job_card.product_id';
        return $this->rows($data); 
    }

	public function getJobWiseProduction($data)
	{
		$jobData = $this->jobcard->getJobcard($data['job_id']);

		$thead = '<tr><th colspan="10">Job Card : ' . $jobData->job_number . '</th></tr>
				    <tr>
				    	<th>#</th>
				    	<th>Date</th>
				    	<th>Process Name</th>
				    	<th>OK Qty.</th>
				    	<th>Reject Qty.</th>
				    	<th>Rework Qty.</th>
				    	<th>Operator</th>
				    	<th>Machine</th>
				    </tr>';
		$tbody = '';
		$i = 1;

		$queryData = array();
		$queryData['tableName'] = $this->jobTrans;
		$queryData['select'] = " job_transaction.*,item_master.item_code,employee_master.emp_name,process_master.process_name,rejection.qty as rej_qty,rework.qty as rw_qty";
		$queryData['leftJoin']['item_master'] = " job_transaction.machine_id = item_master.id";
		$queryData['join']['process_master'] = "process_master.id =  job_transaction.process_id";
		$queryData['leftJoin']['employee_master'] = "employee_master.id =  job_transaction.operator_id";
		$queryData['leftJoin']['rej_rw_management as rejection'] = "rejection.id =  job_transaction.rej_rw_manag_id AND rejection.operation_type = 1";
		$queryData['leftJoin']['rej_rw_management as rework'] = "rework.id =  job_transaction.rej_rw_manag_id AND rework.operation_type = 2";
		$queryData['where']['job_transaction.job_card_id'] = $data['job_id'];
		$queryData['where']['job_transaction.entry_type'] = 0;
		$result = $this->rows($queryData);

		if (!empty($result)) {
			foreach ($result as $row) {
				$tbody .= '<tr>';
				$tbody .= '<td class="text-center">' . $i++ . '</td>';
				$tbody .= '<td>' . formatDate($row->entry_date) . '</td>';
				$tbody .= '<td>' . $row->process_name . '</td>';
				$tbody .= '<td>' . floatVal($row->qty) . '</td>';
				$tbody .= '<td>' . floatval($row->rej_qty) . '</td>';
				$tbody .= '<td>' . floatval($row->rw_qty) . '</td>';
				$tbody .= '<td>' . $row->emp_name . '</td>';
				$tbody .= '<td>' . $row->item_code . '</td>';
				$tbody .= '</tr>';
			}
		}

		return ['status' => 1, 'thead' => $thead, 'tbody' => $tbody];
	}
	
	public function getJobworkRegister($data){
		$queryData['tableName'] = $this->jobTrans;
		$queryData['select'] = "job_transaction.*,outsource_challan.trans_number as challan_no,item_master.item_name,item_master.item_code,process_master.process_name,job_card.job_number,job_card.wo_no,party_master.party_name";
		$queryData['join']['item_master'] = "item_master.id = job_transaction.product_id";
		$queryData['leftJoin']['outsource_challan'] = "outsource_challan.id = job_transaction.challan_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = job_transaction.vendor_id";
		$queryData['leftJoin']['process_master'] = "process_master.id = job_transaction.process_id";
		$queryData['leftJoin']['job_card'] = "job_card.id = job_transaction.job_card_id";
		$queryData['where']['job_transaction.vendor_id'] = $data['vendor_id'];
		$queryData['where']['job_transaction.entry_type'] = 3;
		//$queryData['where']['job_transaction.entry_date >= '] = $this->startYearDate;
        //$queryData['where']['job_transaction.entry_date <= '] = $this->endYearDate;
		$result = $this->rows($queryData);
		return $result;
	}
	
	public function getJobOutwardData($ref_id)
	{
		$queryData['tableName'] = $this->jobTrans;
		$queryData['where']['job_transaction.ref_id'] = $ref_id;
		$queryData['where_in']['job_transaction.entry_type'] = 4;
		$queryData['order_by']['job_transaction.entry_date'] = 'ASC';
		$queryData['order_by']['job_transaction.id'] = 'ASC';
		$result = $this->rows($queryData);
		return $result;
	}

	//Created By Karmi @17/05/2022
	public function getVendorScrap($data)
	{
		$data['tableName'] = $this->jobTransaction;
        $data['select'] = "jobwork_transaction.*,party_master.party_name,party_master.party_address,party_master.gstin,jobwork.trans_number,process_master.process_name,item_master.item_name,item_master.full_name";
		$data['leftJoin']['process_master'] =  "process_master.id = jobwork_transaction.process_id";
        $data['leftJoin']['item_master'] =  "item_master.id = jobwork_transaction.item_id";
        $data['leftJoin']['jobwork'] = "jobwork_transaction.jobwork_id = jobwork.id";
        $data['leftJoin']['party_master'] = "party_master.id = jobwork.vendor_id";
        $data['where']['jobwork.vendor_id'] = $data['vendor_id'];
        $data['where']['jobwork_transaction.scrap_weight >'] = 0;
        $data['where']['jobwork_transaction.entry_type'] = 1;
        $resultData = $this->rows($data);
		return $resultData;
	}
	
	public function getVendorRejectionSum($ref_id)
	{
		$queryData['tableName'] = "job_rejection";
		$queryData['select'] = "SUM(qty) as rejectQty";
		$queryData['where']['job_inward_id'] = $ref_id;
		$result = $this->row($queryData);
	   	return $result;
	}

	public function getUsedMaterial($id){
		$data['tableName'] = 'job_used_material';
		$data['select'] = "job_used_material.*,item_master.item_name,item_master.item_code,item_master.item_type,unit_master.unit_name";
		$data['join']['item_master'] = "item_master.id = job_used_material.bom_item_id";
		$data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
		$data['where']['job_used_material.id'] = $id;
		return $this->row($data);
	}

	/* Get Production Analysis Data */
	public function getProductionAnalysis($data){
		$queryData = array();
		$queryData['tableName'] = $this->jobOutward;
		$queryData['select'] = 'job_outward.*,process_master.process_name,item_master.item_name,item_master.item_code,employee_master.emp_name,shift_master.shift_name,job_card.id as job_id';
		$queryData['join']['process_master'] = 'process_master.id = job_outward.in_process_id';
		$queryData['join']['job_card'] = 'job_card.id = job_outward.job_card_id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = job_card.product_id';
		//$queryData['leftJoin']['machine_master'] = 'machine_master.id = job_outward.machine_id';
		$queryData['leftJoin']['employee_master'] = 'employee_master.id = job_outward.operator_id';
		$queryData['leftJoin']['shift_master'] = 'shift_master.id = job_outward.shift_id';
		$queryData['customWhere'][] = "job_outward.entry_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['where']['job_outward.out_qty != '] = 0;
		$queryData['order_by']['job_outward.entry_date'] = 'ASC';
		$result = $this->rows($queryData);

		$i=1;$prev_date="";$pid=0;$tbody="";
		foreach($result as $row):
			$rjqty = 0;$rwqty = 0;$rjRatio=0;
			$machineData = $this->machine->getMachine($row->machine_id);
			$machineNo = (!empty($machineNo))?$machineNo->item_code:"";
			if($prev_date != $row->entry_date or $pid != $row->in_process_id)
			{
				$queryData = array();
				$queryData['select'] = "SUM(qty) as qty";
				$queryData['tableName'] = $this->jobRejection;
				$queryData['where']['job_card_id'] = $row->job_id;
				$queryData['where']['process_id'] = $row->in_process_id;
				$queryData['where']['entry_date'] = $row->entry_date;
				$queryData['where']['type'] = 0;
				$rejectQty = $this->row($queryData);
				$rjqty = (!empty($rejectQty)) ? $rejectQty->qty : 0;
				
				$queryData = array();
				$queryData['select'] = "SUM(qty) as qty";
				$queryData['tableName'] = $this->jobRejection;
				$queryData['where']['job_card_id'] = $row->job_id;
				$queryData['where']['process_id'] = $row->in_process_id;
				$queryData['where']['entry_date'] = $row->entry_date;
				$queryData['where']['type'] = 1;
				$reworkQty = $this->row($queryData);
				$rwqty = (!empty($reworkQty)) ? $reworkQty->qty : 0;

				if(!empty($row->out_qty) AND $row->out_qty > 0):
					$rjRatio = round((($rjqty * 100) / $row->out_qty),2);		
				endif;		
			}

			$tbody .= '<tr class="text-center">
						<td>'.$i++.'</td>
						<td>'.formatDate($row->entry_date).'</td>
						<td>'.$machineNo.'</td>
						<td>'.$row->shift_name.'</td>
						<td>'.$row->emp_name.'</td>
						<td>'.$row->item_code.'</td>
						<td>'.$row->production_time.'</td>
                        <td>'.$row->process_name.'</td>
                        <td>'.$row->cycle_time.'</td>
						<td>'.$row->out_qty.'</td>
						<td>'.$rwqty.'</td>
						<td>'.$rjqty.'</td>
						<td>'.$rjRatio.'%</td>
					</tr>';
			$prev_date = $row->entry_date; $pid = $row->in_process_id;
		endforeach;
		return ['status'=>1, 'tbody'=>$tbody];
	}
	
	/* Stage Wise Production */
    public function getProductList(){
        $queryData['tableName'] = $this->jobCard;
        $queryData['select'] = 'item_master.id,item_master.item_name, item_master.item_code';
		$queryData['join']['item_master'] = 'item_master.id= job_card.product_id';
        $queryData['where_in']['job_card.order_status'] = [0,1,2,3];
        $queryData['group_by'][] = 'job_card.product_id';
        $queryData['order_by']['item_master.item_code'] = 'ASC';
        return $this->rows($queryData);
    }
	
    public function getJobs($data){
        $queryData['tableName'] = $this->jobCard;
        $queryData['select'] = 'job_card.id,job_card.job_no,job_card.job_prefix,job_card.job_number,job_card.job_date,job_card.product_id,job_card.process, job_card.total_ok_qty,item_master.item_name, item_master.item_code';
		$queryData['join']['item_master'] = 'item_master.id= job_card.product_id';
		$queryData['customWhere'][] = "job_card.job_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        if(!empty($data['item_id'])){$queryData['where']['product_id'] = $data['item_id'];}
        $queryData['where_in']['job_card.order_status'] = [0,1,2,3];
        return $this->rows($queryData);
    }
	
	public function getStageWiseProduction($data){
		$jobData = $this->getJobs($data);
		$allProcess = Array();
		if(!empty($jobData)):
			foreach($jobData as $row):
				$allProcess = array_merge(explode(',',$row->process),$allProcess);
			endforeach;
		endif;
		
		$processList = array_unique($allProcess);		
		return ['jobData'=>$jobData,"processList"=>$processList];
	}
	
	public function getProductionQty($job_card_id,$process_id){
		$queryData['tableName'] = 'job_transaction';
		$queryData['select'] = "SUM(qty) as qty";
		$queryData['where']['entry_type'] = 0;
		$queryData['where']['job_card_id'] = $job_card_id;
		$queryData['where']['process_id'] = $process_id;
		$result = $this->row($queryData);
		return $result;
	}

	/* Job card Register */
	public function getJobcardRegister()
	{
		$queryData = array();
		$queryData['tableName'] = $this->jobCard;
		$queryData['select'] = 'job_card.*,party_master.party_name,party_master.party_code,item_master.item_code,item_master.item_name,employee_master.emp_name';
		$queryData['leftJoin']['party_master'] = 'party_master.id = job_card.party_id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = job_card.product_id';
		$queryData['leftJoin']['employee_master'] = 'employee_master.id = job_card.created_by';
        $data['where']['job_card.job_date >= '] = $this->startYearDate;
        $data['where']['job_card.job_date <= '] = $this->endYearDate;
		$queryData['group_by'][] = 'job_card.id';
		return $this->rows($queryData);
	}
		
	/* Machine Wise Production */
	public function getMachineWiseProduction($data,$dept_id){
		$queryData = array();
		$queryData['tableName'] = $this->jobOutward;
		$queryData['select'] = 'job_outward.*,process_master.process_name,item_master.item_name,item_master.item_code,employee_master.emp_name,shift_master.shift_name,job_card.id as job_id';
		$queryData['join']['process_master'] = 'process_master.id = job_outward.in_process_id';
		$queryData['join']['job_card'] = 'job_card.id = job_outward.job_card_id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = job_card.product_id';
		$queryData['leftJoin']['employee_master'] = 'employee_master.id = job_outward.operator_id';
		$queryData['leftJoin']['shift_master'] = 'shift_master.id = job_outward.shift_id';
		$queryData['customWhere'][] = "job_outward.entry_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['where']['job_outward.out_qty != '] = 0;
		$queryData['where']['process_master.dept_id'] = $dept_id;
		$queryData['order_by']['job_outward.entry_date'] = 'ASC';
		$result = $this->rows($queryData);

		$i=1; $prev_date=""; $pid=0; $tbody=""; $okqty=0; $runtime=""; $qualityRate=0;
		$availability=0; $performance=0; $overall=0; $oee=0; $cs=0; $cm=0; $tc=0; $ps=0; $pm=0; $tp=0;
		foreach($result as $row):
			$rjqty = 0;$rwqty = 0;$rjRatio=0;
			$machineData = $this->machine->getMachine($row->machine_id);
			$machineNo = (!empty($machineData))?$machineData->item_code:"";
			if($prev_date != $row->entry_date or $pid != $row->in_process_id)
			{
				$queryData = array();
				$queryData['select'] = "SUM(qty) as qty";
				$queryData['tableName'] = $this->jobRejection;
				$queryData['where']['job_card_id'] = $row->job_id;
				$queryData['where']['process_id'] = $row->in_process_id;
				$queryData['where']['entry_date'] = $row->entry_date;
				$queryData['where']['type'] = 0;
				$rejectQty = $this->row($queryData);
				$rjqty = (!empty($rejectQty)) ? $rejectQty->qty : 0;
				
				$queryData = array();
				$queryData['select'] = "SUM(qty) as qty";
				$queryData['tableName'] = $this->jobRejection;
				$queryData['where']['job_card_id'] = $row->job_id;
				$queryData['where']['process_id'] = $row->in_process_id;
				$queryData['where']['entry_date'] = $row->entry_date;
				$queryData['where']['type'] = 1;
				$reworkQty = $this->row($queryData);
				$rwqty = (!empty($reworkQty)) ? $reworkQty->qty : 0;

				if(!empty($row->out_qty) AND $row->out_qty > 0):
					$rjRatio = round((($rjqty * 100) / $row->out_qty),2);		
				endif;		
			}

			$okqty = ($row->out_qty - ($rwqty - $rjqty));
			/* $qualityRate = round(($row->out_qty * 100) / $okqty, 2);

			$ct = explode(':',$row->cycle_time); 
			$cm = intVal($ct[1]);
			$cs = intVal($ct[2]);
			$tc = ($cm * 3600) + ($cs * 60);

			$pt = explode(':',$row->production_time);
			$pm = intVal($pt[0]);
			$ps = intVal($pt[1]);
			$tp = ($pm * 3600) + ($ps * 60);

			$availability = round(($tc * 100) / $tp,2);
			$performance = round(($tc * $okqty) / $tp,2);
			$overall = round(($tc * $okqty) / $tp,2); 
			$oee =  round((($availability * ($performance * $qualityRate)) / 100), 2); */

			$tbody .= '<tr class="text-center">
						<td>'.$i++.'</td>
						<td>'.formatDate($row->entry_date).'</td>
						<td>'.$row->shift_name.'</td>
						<td>'.$machineNo.'</td>
						<td>'.$row->emp_name.'</td>
						<td>'.$row->item_code.'</td>
                        <td>'.$row->process_name.'</td>
                        <td>'.$row->cycle_time.'</td>
						<td>'.$row->production_time.'</td>
						<td></td>
						<td>'.$okqty.'</td>
						<td>'.$rwqty.'</td>
						<td>'.$rjqty.'</td>

						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td>'.$cm.'</td>
						<td>'.$row->out_qty.'</td>
						<td>'.$pm.'</td>
						<td>'.$availability.'%</td>
						<td>'.$performance.'%</td>
						<td>'.$overall.'%</td>
						<td>'.$qualityRate.'%</td>
						<td>'.$oee.'%</td>
					</tr>';
			$prev_date = $row->entry_date; $pid = $row->in_process_id;
		endforeach;
		return ['status'=>1, 'tbody'=>$tbody];
	}

	//Created By Avruti @08/08/2022
	public function getJobworkReport($data){
		$data['tableName'] = $this->jobTransaction;
        $data['select'] = "jobwork_transaction.*,process_master.process_name,item_master.item_name,item_master.full_name,jobwork.trans_number,jobwork.ewb_no,jobwork.vendor_id,party_master.party_name";
        $data['leftJoin']['process_master'] =  "process_master.id = jobwork_transaction.process_id";
        $data['leftJoin']['item_master'] =  "item_master.id = jobwork_transaction.item_id";
        $data['leftJoin']['jobwork'] =  "jobwork.id = jobwork_transaction.jobwork_id";
        $data['leftJoin']['party_master'] = "party_master.id = jobwork.vendor_id";
		$data['customWhere'][] = "jobwork_transaction.entry_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        $data['where']['jobwork.vendor_id'] = $data['vendor_id'];
        $data['where']['jobwork_transaction.entry_type'] = 2;
        $data['where']['jobwork_transaction.is_approve != '] = 0;
        $data['order_by']['jobwork_transaction.entry_date'] = "ASC";
		$result = $this->rows($data);
		return $result;
	}
	
	/* Created At: 03-12-2022 [ Milan Chauhan ] */
	public function getProductionMonitoringData($data){
		$queryData = array();
		$queryData['tableName'] = "job_transaction";
		$queryData['select'] = "job_transaction.entry_date, job_transaction.job_card_id,job_transaction.operator_id , employee_master.emp_name as operator_name, job_transaction.production_time, job_transaction.cycle_time, job_transaction.load_unload_time, job_transaction.machine_id, job_transaction.process_id, job_transaction.product_id, machine.item_code as machine_code, job_transaction.shift_id, shift_master.shift_name, process_master.process_name,
		SUM(job_transaction.load_unload_time) as total_load_unload_time,
		SUM(job_transaction.qty) as ok_qty,
		SUM(job_transaction.production_time) as shift_hour,
		AVG(job_transaction.cycle_time) as m_ct,
		SUM(ifnull(jtl.rej_qty,0)) as rej_qty,
		SUM(ifnull(jtl.rw_qty,0)) as rw_qty,
		SUM(ifnull(jtl.hold_qty,0)) as hold_qty,
		SUM(job_transaction.qty + ifnull(jtl.rej_qty,0) + ifnull(jtl.rw_qty,0) + ifnull(jtl.hold_qty,0)) as production_qty";

		$queryData['leftJoin']['job_card'] = "job_card.id = job_transaction.job_card_id";
		$queryData['leftJoin']['shift_master'] = "shift_master.id = job_transaction.shift_id";
		$queryData['leftJoin']['process_master'] = "process_master.id = job_transaction.process_id";
		$queryData['leftJoin']['item_master AS machine'] = "machine.id = job_transaction.machine_id";
		$queryData['leftJoin']['employee_master'] = "employee_master.id = job_transaction.operator_id";

		$queryData['leftJoin']['(
			SELECT SUM( CASE WHEN entry_type = 1 THEN qty ELSE 0 END ) as rej_qty,
			SUM( CASE WHEN entry_type = 2 THEN qty ELSE 0 END ) AS rw_qty,
			SUM( CASE WHEN entry_type = 5 THEN qty ELSE 0 END ) AS hold_qty,
			ref_id 
			FROM job_transaction 
			WHERE is_delete = 0 AND entry_type IN (1,2,5) AND job_card_id = '.$data['job_id'].' AND process_id = '.$data['process_id'].'
			GROUP BY ref_id
		) AS jtl'] = "jtl.ref_id = job_transaction.id";

		$queryData['where_in']['job_transaction.entry_type'] = [0,4];
		$queryData['where']['job_transaction.job_card_id'] = $data['job_id'];
		$queryData['where']['job_transaction.process_id'] = $data['process_id'];
		if(!empty( $data['machine_id']))
			$queryData['where']['job_transaction.machine_id'] = $data['machine_id'];

		$queryData['group_by'][] = "job_transaction.entry_date";
		$queryData['group_by'][] = "job_transaction.operator_id";
		$queryData['group_by'][] = "job_transaction.shift_id";
		$result = $this->rows($queryData);
		return $result;
	}
	
	/* Created At: 04-12-2022 [ Milan Chauhan ] */
	public function getDailyProductionLogSheet($logDate=""){
        $logDate = (!empty($logDate))?date("Y-m-d",strtotime($logDate)):date("Y-m-d");

        $queryData = array();
        $queryData['tableName'] = "job_transaction";
        $queryData['select'] = "employee_master.emp_name,machine_master.item_name as machine_name,shift_master.shift_name,item_master.item_name as product_name,item_master.item_code as product_code,job_card.job_number,job_card.wo_no,process_master.process_name,
		SUM(job_transaction.production_time) as total_production_time,
		SUM(job_transaction.cycle_time) as total_cycle_time,
		SUM(CASE WHEN job_transaction.entry_type = 0 THEN job_transaction.qty ELSE 0 END ) as total_ok_qty,job_approval.pre_finished_weight,job_approval.finished_weight,
		SUM(CASE WHEN job_transaction.entry_type = 1 THEN job_transaction.qty ELSE 0 END) as total_rej_qty,
		SUM(CASE WHEN job_transaction.entry_type = 2 THEN job_transaction.qty ELSE 0 END) as total_rw_qty,
		job_transaction.operator_id,job_transaction.machine_id,job_transaction.process_id,job_transaction.product_id,job_transaction.job_card_id,job_transaction.shift_id,trans_main.trans_prefix,trans_main.trans_no,job_card.sales_order_id";
		//SUM(production_log.total_idle_time) as total_idle_time,

        $queryData['leftJoin']['employee_master'] = "job_transaction.operator_id = employee_master.id";
        $queryData['leftJoin']['process_master'] = "job_transaction.process_id = process_master.id";
        $queryData['leftJoin']['shift_master'] = "job_transaction.shift_id = shift_master.id";
        $queryData['leftJoin']['job_card'] = "job_transaction.job_card_id = job_card.id";
        $queryData['leftJoin']['trans_main'] = "trans_main.id = job_card.sales_order_id";
        $queryData['leftJoin']['item_master'] = "job_transaction.product_id = item_master.id";
        $queryData['leftJoin']['item_master as machine_master'] = "job_transaction.machine_id = machine_master.id";
        $queryData['leftJoin']['job_approval'] = "job_transaction.job_approval_id = job_approval.id";

        $queryData['where']['job_transaction.entry_date'] = $logDate;
        $queryData['where']['job_transaction.vendor_id'] = 0;

        $queryData['group_by'][] = "job_transaction.operator_id";
        $queryData['group_by'][] = "job_transaction.machine_id";
        $queryData['group_by'][] = "job_transaction.process_id";
        $queryData['group_by'][] = "job_transaction.job_card_id";
        $queryData['group_by'][] = "job_transaction.shift_id";

        $result = $this->rows($queryData);

        $dataRows=array();$total_qty = 0;$estimated_qty = 0;
        if(!empty($result)):
            foreach($result as $row):
				$jobCardData = array();
				$jobCardData['id'] = $row->job_card_id;
				$jobCardData['product_id'] = $row->product_id;
				$jobCardData = (object)$jobCardData;
				$materialData = $this->jobcard->getMaterialIssueData($jobCardData);
				$row->rm_grade = (!empty($materialData['resultData']))?$materialData['resultData']['material_grade']:'';

				$row->total_idle_time = 0;

                //$productProcessData = $this->item->getProductProcessData($row->product_id,$row->process_id);
                
                $cycleTime = $row->total_cycle_time;
                
                //if(!empty($productProcessData)):
                //    $cycleTime = timeToSeconds($productProcessData->cycle_time);
                //endif;
                
                $total_qty = round($row->total_ok_qty +  $row->total_rej_qty);
                $estimated_qty = (!empty($row->total_production_time) && !empty($cycleTime))?(int) (($row->total_production_time * 60) / $cycleTime):0;

                $row->cycle_time = $cycleTime;
                $row->total_production_time = (!empty($row->total_production_time))?$row->total_production_time:0;
                $row->effecincy_per = (!empty($estimated_qty))?round(($total_qty*100)/$estimated_qty,2):0;
				$row->wo_no = $row->wo_no; //(!empty($row->sales_order_id))?getPrefixNumber($row->trans_prefix,$row->trans_no):"";
                
                $queryData = array();
				$queryData['tableName'] = "job_transaction";
                $queryData['select'] = "job_transaction.qty,rejection_comment.remark";
				$queryData['leftJoin']['rejection_comment'] = "rejection_comment.id = job_transaction.rr_reason";
                $queryData['where']['job_transaction.operator_id'] = $row->operator_id;
                $queryData['where']['job_transaction.machine_id'] = $row->machine_id;
                $queryData['where']['job_transaction.process_id'] = $row->process_id;
                $queryData['where']['job_transaction.job_card_id'] = $row->job_card_id;
                $queryData['where']['job_transaction.shift_id'] = $row->shift_id;
                $queryData['where']['job_transaction.entry_type'] = 1;
                $queryData['where']['job_transaction.entry_date'] = $logDate;
        		$queryData['where']['job_transaction.vendor_id'] = 0;
                $rejData = $this->rows($queryData);

				$queryData = array();
                $queryData['tableName'] = "job_transaction";
                $queryData['select'] = "job_transaction.qty,rejection_comment.remark";
				$queryData['leftJoin']['rejection_comment'] = "rejection_comment.id = job_transaction.rr_reason";
                $queryData['where']['job_transaction.operator_id'] = $row->operator_id;
                $queryData['where']['job_transaction.machine_id'] = $row->machine_id;
                $queryData['where']['job_transaction.process_id'] = $row->process_id;
                $queryData['where']['job_transaction.job_card_id'] = $row->job_card_id;
                $queryData['where']['job_transaction.shift_id'] = $row->shift_id;
                $queryData['where']['job_transaction.entry_type'] = 2;
                $queryData['where']['job_transaction.entry_date'] = $logDate;
        		$queryData['where']['job_transaction.vendor_id'] = 0;
                $rewData = $this->rows($queryData);

				$row->rej_reason = (!empty($rejData))?implode(", ",array_column($rejData,'remark')):"";
				$row->rw_reason = (!empty($rewData))?implode(", ",array_column($rejData,'remark')):"";
                $row->idle_reason = "";

                $dataRows[] = $row;
            endforeach;
        endif;
        return $dataRows;
    }
    
    public function getIdleTimeReasonForOee($data)
	{
	    $idleReasonList = $this->comment->getIdleReason();
	    $td = '';$totalIdleTime = 0;
	    foreach($idleReasonList as $row):
	    
    		$queryData = array();
    		$queryData['tableName'] = $this->jobTrans;
    		$queryData['select'] = 'SUM(job_transaction.production_time) as idle_time';
    		
    		if(!empty($data['entry_date'])): $queryData['where']['job_transaction.entry_date'] = $data['entry_date']; endif;
    		if(!empty($data['shift_id'])): $queryData['where']['job_transaction.shift_id'] = $data['shift_id']; endif;
    		if(!empty($data['machine_id'])): $queryData['where']['job_transaction.machine_id'] = $data['machine_id']; endif;
    		if(!empty($data['process_id'])): $queryData['where']['job_transaction.process_id'] = $data['process_id']; endif;
    		if(!empty($data['operator_id'])): $queryData['where']['job_transaction.operator_id'] = $data['operator_id']; endif;
    		if(!empty($data['product_id'])): $queryData['where']['job_transaction.product_id'] = $data['product_id']; endif;
    		if(!empty($data['job_card_id'])): $queryData['where']['job_transaction.job_card_id'] = $data['job_card_id']; endif;
    		
    		$queryData['where']['job_transaction.entry_type'] = 8;
    		$queryData['where']['job_transaction.rr_reason'] = $row->id;
    		$result = $this->row($queryData);
    		
    		if(!empty($result->idle_time)):
    		    $td .= '<td class="bg-light">' . $result->idle_time . '</td>';
    		else:
    		    $td .= '<td class="">0</td>'; 
    		endif;
    		$totalIdleTime = (!empty($result->idle_time))?$result->idle_time:0;
    	endforeach;
		
		return ['td' => $td, 'total_idle_time' => $totalIdleTime];
	}

    /* DEPARTMENT WISE PRODUCTION * Created By Meghavi @21/11/2023 */
	public function getDeptWiseProduction($data){
        $queryData['tableName'] = $this->jobApproval;
        $queryData['select'] = 'job_approval.*,(job_approval.in_qty - job_approval.total_prod_qty) as pend_prod_qty,item_master.item_name,item_master.item_code,job_card.job_number,process_master.process_name';
        $queryData['leftJoin']['item_master'] = "item_master.id = job_approval.product_id";
        $queryData['leftJoin']['process_master'] = "process_master.id = job_approval.in_process_id";
        $queryData['leftJoin']['job_card'] = "job_card.id = job_approval.job_card_id";
		$queryData['where']['process_master.dept_id'] = $data['dept_id'];
		$queryData['where_in']['job_card.order_status'] = [0,1,2];
		$queryData['customWhere'][] = '((job_approval.in_qty - job_approval.total_prod_qty) > 0 OR (job_approval.inward_qty - (job_approval.in_qty - job_approval.ch_qty)) > 0 OR (job_approval.ok_qty - job_approval.total_out_qty) > 0)';
		$result = $this->rows($queryData);
		return $result;
    }
    
    public function getRejectionMonitoring($data){
		$queryData = array();
        $queryData['tableName'] = "job_transaction";
        $queryData['select'] = "job_transaction.*,item_master.item_code,item_master.item_name,item_master.price,process_master.process_name,shift_master.shift_name,employee_master.emp_name,mc.item_name as machine_name,mc.item_code as machine_code,rejection_comment.remark as rejection_reason,rrStage.process_name as rejection_stage,rej_rw_management.remark as rej_remark,party_master.party_name as vendor_name";

        $queryData['leftJoin']['rej_rw_management'] = "rej_rw_management.id = job_transaction.rej_rw_manag_id";
        $queryData['leftJoin']['item_master'] = "item_master.id = job_transaction.product_id";
        $queryData['leftJoin']['rejection_comment'] = "rejection_comment.id = job_transaction.rr_reason";
        $queryData['leftJoin']['job_card'] = "job_card.id = job_transaction.job_card_id";
        $queryData['leftJoin']['trans_main'] = "trans_main.id = job_card.sales_order_id";
        $queryData['leftJoin']['job_transaction as okt'] = "job_transaction.ref_id = okt.id";
        $queryData['leftJoin']['process_master'] = "okt.process_id = process_master.id";
        $queryData['leftJoin']['shift_master'] = "okt.shift_id = shift_master.id";
        $queryData['leftJoin']['employee_master'] = "okt.operator_id = employee_master.id";
        $queryData['leftJoin']['item_master as mc'] = "mc.id = okt.machine_id";
        $queryData['leftJoin']['rejection_comment'] = "rejection_comment.id = job_transaction.rr_reason";
        $queryData['leftJoin']['process_master as rrStage'] = "job_transaction.rr_stage = rrStage.id";
        $queryData['leftJoin']['party_master'] = "party_master.id = rej_rw_management.rr_by";

        $queryData['where']['job_transaction.entry_type'] = 1;
        $queryData['where']['job_transaction.entry_date >= '] = $data['from_date'];
        $queryData['where']['job_transaction.entry_date <= '] = $data['to_date'];
			
		if (!empty($data['item_id'])) {
			$queryData['where_in']['job_card.product_id'] = $data['item_id'];
		}

		if(!empty($data['rejection_from'])){
			if($data['rejection_from'] == 1){
				$queryData['where']['rej_rw_management.rr_by'] = 0;
				
			}
			else if($data['rejection_from'] == 2){
				$queryData['where']['rej_rw_management.rr_by > '] = 0;
			}
		}
		
		return $this->rows($queryData);
	}
}
?>