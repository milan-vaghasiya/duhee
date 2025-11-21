<?php
class MachineTicketModel extends MasterModel{
    private $machineMaintenance = "machine_maintenance";
    private $itemMaster = "item_master";
	private $deptMaster = "department_master";

    public function getDTRows($data){
        $data['tableName'] = $this->machineMaintenance;
        $data['select'] = "machine_maintenance.*,item_master.item_name,item_master.item_code";
        $data['join']['item_master'] = "item_master.id = machine_maintenance.machine_id";
        $data['where']['item_type'] = 5;
        
        $data['searchCol'][] = "item_name";
        $data['searchCol'][] = "CONCAT(trans_no,trans_prefix)";
        $data['searchCol'][] = "problem_title";
        $data['searchCol'][] = "DATE_FORMAT(problem_date,'%d-%m-%Y')";
        $data['searchCol'][] = "solution_detail";
        $data['searchCol'][] = "DATE_FORMAT(solution_date,'%d-%m-%Y')";

		$columns =array('','','item_name','trans_no','trans_prefix','problem_title','problem_date','solution_detail','solution_date');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function nextTransNo(){
        $data['select'] = "MAX(trans_no) as trans_no";
        $data['tableName'] = $this->machineMaintenance;
		$trans_no = $this->specificRow($data)->trans_no;
		$nextTransNo = (!empty($trans_no))?($trans_no + 1):1;
		return $nextTransNo;
    }

    public function getMachineName(){
	    $data['where']['item_type'] = 5;
        $data['tableName'] = $this->itemMaster;
        return $this->rows($data);
	}

    public function getDepartment(){
        $data['tableName'] = $this->deptMaster;
        return $this->rows($data);
	}

    public function getMachineTicket($id){
	    $data['where']['id'] = $id;
        $data['tableName'] = $this->machineMaintenance;
        return $this->row($data);
	}

    public function save($data){
        return $this->store($this->machineMaintenance,$data,'Machine Ticket');
    }

    public function delete($id){
        return $this->trash($this->machineMaintenance,['id'=>$id],'Machines Ticket');
    }


    public function getMachineTicketListByDate($data){
		$queryData = array();
		$queryData['tableName'] = $this->machineMaintenance;
		$queryData['select'] = "machine_maintenance.*,item_master.item_name,item_master.item_code";
		$queryData['join']['item_master'] = "item_master.id = machine_maintenance.machine_id";
		$queryData['customWhere'][] = "machine_maintenance.problem_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$mlogData= $this->rows($queryData);
		
		$tbody = '';
		if(!empty($mlogData)):
			$i=1; 
			foreach($mlogData as $row):
                $solutionBy=$row->solution_by;
                if($row->m_agency == 2){
                    $solutionBy=$this->party->getParty($row->solution_by)->party_name;
                }
				$tbody .= '<tr>
                <td>'.$i++.'</td>
                <td>'.formatDate($row->problem_date).'</td>
                <td>'.$row->trans_prefix.$row->trans_no.'</td>
                <td>'.$row->item_code.'</td>
                <td>'.date("d-m-Y H:i:s",strtotime($row->mstart_time)).'</td>
                <td>'.date("d-m-Y H:i:s",strtotime($row->mstart_time.' +'.$row->down_time.'minute')).'</td>
                <td>'.$row->down_time.'</td>
                <td>'.$row->problem_detail.'</td>
                <td>'.$row->solution_detail.'</td>
                <td>'.$solutionBy.'</td>
            </tr>';
			endforeach;
		endif;
		return ['status'=>1,'tbody'=>$tbody]; 
	}
}
?>