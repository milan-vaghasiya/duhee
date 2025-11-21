<?php
defined( 'BASEPATH' )OR exit( 'No direct script access allowed' );

header('Content-Type:application/json');
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin:*");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE,OPTIONS");
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers:{$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    exit(0);
}
class MachineLog extends CI_Controller{
    public function __construct(){
        parent::__construct();
    }

    public function idleReasonList(){
        $this->db->where('type',2);
        $this->db->where('is_delete',0);
        $result = $this->db->get('rejection_comment')->result();
        echo json_encode(['status'=>1,'message'=>'Recored found.','data'=>['idleReasonList'=>$result]]);
    }

    public function saveProductionLogs(){
        try{
            $data = json_decode(file_get_contents('php://input'), true);
            $errorMessage = array();
            if(empty($data['machine_id']))
                $errorMessage['machine_id'] = "Machine ID is required.";
            if(empty($data['part_code']))
                $errorMessage['part_code'] = "Part Code is required.";
            if(empty($data['part_count']))
                $errorMessage['part_count'] = "Part Count is required.";
            if(empty($data['operator_id']))
                $errorMessage['operator_id'] = "Operator ID is required.";

            if(!empty($errorMessage)):
                echo json_encode(['status'=>0,'message'=>$errorMessage]);
            else:
                $data['start_time'] =  date('Y-m-d H:i:s',strtotime($data['start_date'].' '.$data['start_time']));
                $data['end_time'] = date('Y-m-d H:i:s',strtotime($data['end_date'].' '.$data['end_time']));
                $data['log_date'] = date('Y-m-d H:i:s',strtotime($data['start_time']));
                $data['id']="";
                $data['process_id'] = $data['part_job'];
                unset($data['start_date'],$data['end_date'],$data['part_job']);

                $this->db->trans_begin();

                $this->db->insert('machine_log',$data);
                $insert_id = $this->db->insert_id();

                if ($this->db->trans_status() !== FALSE):
                    $this->db->trans_commit();
                    echo json_encode(['status'=>1,'message'=>"Log saved Successfully.",'insert_id'=>$insert_id]);
                endif;
            endif;
        }catch(\Exception $e){
			$this->db->trans_rollback();
		   return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
    }

    public function saveIdleReasonLog(){
        try{
            $data = $this->input->post();
            $errorMessage = array();

            if(empty($data['machine_id']))
                $errorMessage['machine_id'] = "Machine is required.";
            if(empty($data['start_time']))
                $errorMessage['start_time'] = "Start Time is required.";
            if(empty($data['end_time']))
                $errorMessage['end_time'] = "End Time is required.";
            if(empty($data['idle_reason_id']))
                $errorMessage['idle_reason_id'] = "Idle Reason is required.";

            if(!empty($errorMessage)):
                echo json_encode(['status'=>0,'message'=>$errorMessage]);
            else:
                $this->db->where('machine_id',$data['machine_id']);
                $this->db->where('is_delete',0);
                $this->db->order_by('id',"DESC");
                $this->db->limit(1);
                $logData = $this->db->get('machine_log')->row();

                if(!empty($logData)):
                    $this->db->trans_begin();

                    $to_time = strtotime(date("Y-m-d H:i:s",strtotime($data['end_time'])));
                    $from_time = strtotime(date("Y-m-d H:i:s",strtotime($data['start_time'])));
                    $idle_time = round(abs($to_time - $from_time) / 60,2);

                    $postData = [
                        'prod_type' => 1,
                        'log_date' => date("Y-m-d H:i:s"),
                        'machine_id' => $logData->machine_id,
                        'part_code' => $logData->part_code,
                        'job_card_id' => $logData->job_card_id,
                        'process_id' => $logData->process_id,
                        'programme_no' => $logData->programme_no,
                        'operator_id' => $logData->operator_id,
                        'idle_reason' => $data['idle_reason_id'],
                        'start_time' => date("Y-m-d H:i:s",strtotime($data['start_time'])),
                        'end_time' => date("Y-m-d H:i:s",strtotime($data['end_time'])),
                        'idle_time' => $idle_time,
                        'cycle_status' => $logData->cycle_status
                    ];

                    $this->db->insert('machine_log',$postData);
                    $insert_id = $this->db->insert_id();
    
                    if ($this->db->trans_status() !== FALSE):
                        $this->db->trans_commit();
                        echo json_encode(['status'=>1,'message'=>"Log saved Successfully.",'insert_id'=>$insert_id]);
                    endif;
                else:
                    echo json_encode(['status'=>2,'message'=>'Recored not found.']);
                endif;
            endif;
        }catch(\Exception $e){
			$this->db->trans_rollback();
		   return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}
    }
}
?>