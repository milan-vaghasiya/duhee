<?php
class ProductSetupModel extends MasterModel
{
    private $prod_setup_request = "prod_setup_request";
    private $prod_setup_trans = 'prod_setup_trans';
    private $job_approval = 'job_approval';
   
    public function getDTRows($data)
    {
        $data['tableName'] = $this->prod_setup_request;
        $data['select'] = "prod_setup_request.*,item_master.item_name,item_master.full_name,item_master.item_code,employee_master.emp_name,job_card.job_number,process_master.process_name,mc.item_name as machine_name,mc.item_code as machine_code,qci.emp_name as qc_inspector";

        $data['leftJoin']['employee_master'] = "prod_setup_request.created_by = employee_master.id";
        $data['leftJoin']['job_card'] = "job_card.id = prod_setup_request.job_card_id";
        $data['leftJoin']['process_master'] = "process_master.id = prod_setup_request.process_id";
        $data['leftJoin']['item_master'] = "item_master.id = prod_setup_request.product_id";
        $data['leftJoin']['item_master as mc'] = "mc.id = prod_setup_request.machine_id";
        $data['leftJoin']['employee_master as qci'] = "prod_setup_request.qci_id = qci.id";
        $data['where']['prod_setup_request.setup_type'] = $data['setup_type'];
        if($this->loginID != 1){
            $data['where']['prod_setup_request.setter_id'] = $this->session->userdata('loginId');
        }

        $data['order_by']['prod_setup_request.id'] = "DESC";

        $data['searchCol'][] = "DATE_FORMAT(prod_setup_request.created_at,'%d-%m-%Y')";
        $data['searchCol'][] = "prod_setup_request.req_no";
        $data['searchCol'][] = "employee_master.emp_name";
        $data['searchCol'][] = "job_card.job_number";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "item_master.full_name";
        $data['searchCol'][] = "mc.item_name";
        $data['searchCol'][] = "qci.emp_name";
        $data['searchCol'][] = "prod_setup_request.remark";

        $columns = array('', '', 'prod_setup_request.created_at', 'prod_setup_request.req_no', 'employee_master.emp_name', 'job_card.job_number', 'process_master.process_name', 'item_master.full_name', 'mc.item_name', 'qci.emp_name', 'prod_setup_request.remark', '');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        $result = $this->pagingRows($data);
        return $result;
    }

    public function acceptSetupRequest($data){
        try {
            $this->db->trans_begin();
            $result = $this->store($this->prod_setup_request,['id'=>$data['id'],'status'=>1]);

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getSetterReportDTRows($data)
    {
        $data['tableName'] = $this->prod_setup_trans;
        $data['select'] = "prod_setup_trans.*";
        $data['where']['prod_setup_trans.setup_id'] = $data['setup_id'];

        $data['order_by']['prod_setup_trans.created_at'] = "DESC";
        $data['order_by']['prod_setup_trans.id'] = "DESC";

        $data['searchCol'][] = "DATE_FORMAT(prod_setup_trans.created_at,'%d-%m-%Y')";
        $data['searchCol'][] = "prod_setup_trans.setup_start_time";
        $data['searchCol'][] = "prod_setup_trans.setup_end_time";
        $data['searchCol'][] = "prod_setup_trans.setter_note";
        $data['searchCol'][] = "prod_setup_trans.qci_note";

        $columns = array('', '', 'prod_setup_trans.created_at', 'prod_setup_trans.setup_start_time', 'prod_setup_trans.setup_end_time', 'prod_setup_trans.setter_note','','prod_setup_trans.qci_note');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        $result = $this->pagingRows($data);
        return $result;
    }

    public function getSetupRequestData($setup_id){
        $data['tableName'] = $this->prod_setup_request;
        $data['select'] = "prod_setup_request.*,item_master.item_name,item_master.full_name,item_master.item_code,employee_master.emp_name,job_card.job_number,process_master.process_name,mc.item_name as machine_name,mc.item_code as machine_code,qci.emp_name as qc_inspector,setter.emp_name as setter_name";

        $data['leftJoin']['employee_master'] = "prod_setup_request.created_by = employee_master.id";
        $data['leftJoin']['job_card'] = "job_card.id = prod_setup_request.job_card_id";
        $data['leftJoin']['process_master'] = "process_master.id = prod_setup_request.process_id";
        $data['leftJoin']['item_master'] = "item_master.id = prod_setup_request.product_id";
        $data['leftJoin']['item_master as mc'] = "mc.id = prod_setup_request.machine_id";
        $data['leftJoin']['employee_master as qci'] = "prod_setup_request.qci_id = qci.id";
        $data['leftJoin']['employee_master as setter'] = "prod_setup_request.setter_id = setter.id";
        $data['where']['prod_setup_request.id'] = $setup_id;
        return $this->row($data);
    }

    public function save($data){
        try {
            $this->db->trans_begin();
            $result = $this->store($this->prod_setup_trans,$data);
            if($data['setup_status'] == 3){
                $this->store($this->prod_setup_request,['id'=>$data['setup_id'],'status'=>2]);
            }
            if($data['setup_status'] == 5){
                $setupReqData = $this->getSetupRequestData($data['setup_id']);
                $this->store($this->job_approval,['id'=>$setupReqData->job_approval_id,'status'=>1]);
                $this->store($this->prod_setup_request,['id'=>$data['setup_id'],'status'=>3]);
            }
            if($data['setup_status'] == 6){
                $this->store($this->prod_setup_request,['id'=>$data['setup_id'],'status'=>4]);
            }
            if($data['setup_status'] ==7){
                $this->store($this->prod_setup_request,['id'=>$data['setup_id'],'status'=>5]);
            }
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function completeReport($data){
        $reportData = $this->getSetupRequestTrans($data['id']);
        if(!empty($reportData->submit_to_qc)){
            $data['setup_status'] = 3;
        }else{
            $data['setup_status'] = 2;
        }
        return $this->save($data);
    }

    public function delete($id){
        try {
                $this->db->trans_begin();
                $result = $this->trash($this->prod_setup_trans,['id'=>$id]);
                if ($this->db->trans_status() !== FALSE) :
                    $this->db->trans_commit();
                    return $result;
                endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }
    /***************************************************************************************/
    /************************************Asign Inspector************************************/

    public function getSetupDTRows($data)
    {
        $data['tableName'] = $this->prod_setup_request;
        $data['select'] = "prod_setup_request.*,item_master.item_name,item_master.full_name,item_master.item_code,employee_master.emp_name,job_card.job_number,process_master.process_name,mc.item_name as machine_name,mc.item_code as machine_code,qci.emp_name as qc_inspector";

        $data['leftJoin']['employee_master'] = "prod_setup_request.created_by = employee_master.id";
        $data['leftJoin']['job_card'] = "job_card.id = prod_setup_request.job_card_id";
        $data['leftJoin']['process_master'] = "process_master.id = prod_setup_request.process_id";
        $data['leftJoin']['item_master'] = "item_master.id = prod_setup_request.product_id";
        $data['leftJoin']['item_master as mc'] = "mc.id = prod_setup_request.machine_id";
        $data['leftJoin']['employee_master as qci'] = "prod_setup_request.qci_id = qci.id";
        
        if(empty($data['inspector'])){
            if(empty( $data['status'])){ $data['where']['prod_setup_request.qci_id'] = ''; }
            else{ $data['where']['prod_setup_request.qci_id !='] = ''; }
        }else{
            $data['where']['prod_setup_request.qci_id'] = $this->session->userdata('loginId');
            $data['where_in']['prod_setup_request.status'] = '0,1,2';
        }

        $data['order_by']['prod_setup_request.created_at'] = "DESC";
        $data['order_by']['prod_setup_request.id'] = "DESC";

        $data['searchCol'][] = "DATE_FORMAT(prod_setup_request.created_at,'%d-%m-%Y')";
        $data['searchCol'][] = "prod_setup_request.req_no";
        $data['searchCol'][] = "employee_master.emp_name";
        $data['searchCol'][] = "job_card.job_number";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "item_master.full_name";
        $data['searchCol'][] = "mc.item_name";
        $data['searchCol'][] = "qci.emp_name";
        $data['searchCol'][] = "prod_setup_request.remark";

        $columns = array('', '', 'prod_setup_request.created_at', 'prod_setup_request.req_no', 'employee_master.emp_name', 'job_card.job_number', 'process_master.process_name', 'item_master.full_name', 'mc.item_name', 'qci.emp_name', 'prod_setup_request.remark', '');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        $result = $this->pagingRows($data);
        return $result;
    }

    public function saveAsignedInspector($data){
        try {
            $this->db->trans_begin();
            $result = $this->store($this->prod_setup_request,$data);
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getSetupApprovalDTRows($data)
    {
        $data['tableName'] = $this->prod_setup_trans;
        $data['select'] = "prod_setup_trans.*,prod_setup_request.req_no,prod_setup_request.req_prefix,item_master.item_name,item_master.full_name,item_master.item_code,employee_master.emp_name,job_card.job_number,process_master.process_name,mc.item_name as machine_name,mc.item_code as machine_code,qci.emp_name as qc_inspector";
        $data['leftJoin']['prod_setup_request'] = "prod_setup_request.id = prod_setup_trans.setup_id";
        $data['leftJoin']['employee_master'] = "prod_setup_trans.setter_id = employee_master.id";
        $data['leftJoin']['job_card'] = "job_card.id = prod_setup_request.job_card_id";
        $data['leftJoin']['process_master'] = "process_master.id = prod_setup_request.process_id";
        $data['leftJoin']['item_master'] = "item_master.id = prod_setup_request.product_id";
        $data['leftJoin']['item_master as mc'] = "mc.id = prod_setup_request.machine_id";
        $data['leftJoin']['employee_master as qci'] = "prod_setup_request.qci_id = qci.id";
        if($this->loginID != 1){$data['where']['prod_setup_request.qci_id'] = $this->loginID;}
        if(empty($data['status'])){ $data['where_in']['prod_setup_trans.setup_status'] = '3,4'; }
        else{ $data['where_in']['prod_setup_trans.setup_status'] = '5,6,7'; }

        $data['order_by']['prod_setup_request.created_at'] = "DESC";
        $data['order_by']['prod_setup_request.id'] = "DESC";

        $data['searchCol'][] = "DATE_FORMAT(prod_setup_request.created_at,'%d-%m-%Y')";
        $data['searchCol'][] = "prod_setup_request.req_no";
        $data['searchCol'][] = "job_card.job_number";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "item_master.full_name";
        $data['searchCol'][] = "mc.item_name";
        $data['searchCol'][] = "employee_master.emp_name";
        $data['searchCol'][] = "prod_setup_trans.setter_note";
        $data['searchCol'][] = "prod_setup_trans.qci_note";

        $columns = array('', '', 'prod_setup_request.created_at', 'prod_setup_request.req_no', 'job_card.job_number', 'process_master.process_name', 'item_master.full_name', 'mc.item_name', 'employee_master.emp_name', 'prod_setup_trans.setter_note','','"prod_setup_trans.qci_note');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        $result = $this->pagingRows($data);
        return $result;
    }

    public function acceptSetupInspector($data){
        try {
            $this->db->trans_begin();
            $result = $this->store($this->prod_setup_trans,['id'=>$data['id'],'qc_accepted_at'=>date("Y-m-d H:i:s"),'setup_status'=>4,'qci_id'=>$this->session->userdata('loginId')]);
            $setupData = $this->getSetupRequestTrans($data['id']);
            $this->store($this->prod_setup_request,['id'=>$setupData->setup_id,'status'=>6]);
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getSetupRequestTrans($id){
        $data['tableName'] = $this->prod_setup_trans;
        $data['select'] = "prod_setup_trans.*,prod_setup_request.product_id,prod_setup_request.process_id";
        $data['leftJoin']['prod_setup_request'] = "prod_setup_request.id = prod_setup_trans.setup_id";
        $data['where']['prod_setup_trans.id'] = $id;
        return $this->row($data);
    }

    public function getSetupRequestJobApprovalWise($id){
        $data['tableName'] = $this->prod_setup_request;
        $data['select'] = "prod_setup_request.*,employee_master.emp_name,job_card.job_number,process_master.process_name,mc.item_name as machine_name,mc.item_code as machine_code,qci.emp_name as qc_inspector";
        $data['leftJoin']['employee_master'] = "prod_setup_request.created_by = employee_master.id";
        $data['leftJoin']['job_card'] = "job_card.id = prod_setup_request.job_card_id";
        $data['leftJoin']['process_master'] = "process_master.id = prod_setup_request.process_id";
        $data['leftJoin']['item_master'] = "item_master.id = prod_setup_request.product_id";
        $data['leftJoin']['item_master as mc'] = "mc.id = prod_setup_request.machine_id";
        $data['leftJoin']['employee_master as qci'] = "prod_setup_request.qci_id = qci.id";
        $data['where']['prod_setup_request.job_approval_id'] = $id;
        return $this->rows($data);
    }

    public function getSetupRequestTransList($id){
        $data['tableName'] = $this->prod_setup_trans;
        $data['select'] = "prod_setup_trans.*,prod_setup_request.product_id,prod_setup_request.process_id";
        $data['leftJoin']['prod_setup_request'] = "prod_setup_request.id = prod_setup_trans.setup_id";
        $data['where']['prod_setup_trans.setup_id'] = $id;
        return $this->rows($data);
    }
}
