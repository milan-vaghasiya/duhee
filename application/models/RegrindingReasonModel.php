<?php
class RegrindingReasonModel extends MasterModel{
    private $rejectionComment = "rejection_comment";
    public function getDTRows($data){
        $data['tableName'] = $this->rejectionComment;
		$data['where']['type'] = $data['type'];
        $data['searchCol'][] = "remark";
        
		$columns =array('','','remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }


    public function getRegrindingData($id){
        $data['tableName'] = $this->rejectionComment;
        $data['where']['id'] = $id;
        return $this->row($data);
    }

    public function save($data){
        return $this->store($this->rejectionComment,$data,'Regrinding Reason');
    }

    public function delete($id){
        return $this->trash($this->rejectionComment,['id'=>$id],'Regrinding Reason');
    }

    public function getRegrindingReasonList(){
        $data['tableName'] = $this->rejectionComment;
        $data['where']['type'] = 5;
        return $this->rows($data);
    }
}
?>