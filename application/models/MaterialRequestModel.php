<?php
class MaterialRequestModel extends MasterModel{
    private $jobMaterialDispatch = "job_material_dispatch";

    public function getDTRows($data){
        $data['tableName'] = $this->jobMaterialDispatch;
        $data['select'] = "job_material_dispatch.*,job_card.job_no,job_card.job_prefix";
        $data['leftJoin']['job_card'] = "job_material_dispatch.job_card_id = job_card.id";
        $data['where']['job_material_dispatch.req_type'] = 1;

        $data['searchCol'][] = "job_card.job_no";
        $data['searchCol'][] = "DATE_FORMAT(job_material_dispatch.req_date,'%d-%m-%Y')";
        $data['searchCol'][] = "job_material_dispatch.req_item_id";
        $data['searchCol'][] = "job_material_dispatch.req_qty";

        $columns =array('','','job_card.job_no','job_material_dispatch.req_date','job_material_dispatch.req_item_id','job_material_dispatch.req_qty');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function save($data){
        if(empty($data['id'])):
            $materialDispatchData = [
                'id' => "",
                'req_type'=>1,
                'material_type' => $data['material_type'],
                'job_card_id' => $data['job_card_id'],
                'req_date' => $data['req_date'],
                'req_item_id' => $data['req_item_id'],
                'req_qty' => $data['req_qty'],
                'process_id' => $data['process_id'], 
                'remark' => $data['remark'],                            
                'created_by' => $data['created_by']
            ];
            $this->store($this->jobMaterialDispatch,$materialDispatchData);
        else:
            $requestData = $this->getRequestData($data['id']);
            if($requestData->dispatch_qty != "0.000"):
                return ['status'=>2,'message'=>'Material Issued you cant update this request.'];
            endif;
            $materialDispatchData = [
                'id' => $data['id'],
                'req_type'=>1,
                'material_type' => $data['material_type'],
                'job_card_id' => $data['job_card_id'],
                'req_date' => $data['req_date'],
                'req_item_id' => $data['req_item_id'],
                'req_qty' => $data['req_qty'],
                'process_id' => $data['process_id'], 
                'remark' => $data['remark'],                            
                'created_by' => $data['created_by']
            ];
            $this->store($this->jobMaterialDispatch,$materialDispatchData);
        endif;
        return ['status'=>1,'message'=>'Material Request send successfully.'];
    }

    public function getRequestData($id){
        $data['tableName'] = $this->jobMaterialDispatch;
        $data['where']['id'] = $id;
        return $this->row($data);
    }

    public function delete($id){
        $requestData = $this->getRequestData($id);
        if($requestData->dispatch_qty != "0.000"):
            return ['status'=>0,'message'=>'Material Issued you cant delete this request.'];
        endif;
        return $this->trash($this->jobMaterialDispatch,['id'=>$id],'Request');
    }
}
?>