<?php
class RejectionCommentModel extends MasterModel{
    private $rejectionComment = "rejection_comment";
    public function getDTRows($data){
        $data['tableName'] = $this->rejectionComment;
		$data['where']['type'] = $data['type'];
        $data['searchCol'][] = "remark";
		$columns =array('','','remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getCommentList(){
        $data['tableName'] = $this->rejectionComment;
		$data['where']['type'] = 1;
        return $this->rows($data);
    }

    public function getIdleReason(){
        $data['tableName'] = $this->rejectionComment;
		$data['where']['type'] = 2;
        return $this->rows($data);
    }

    public function getComment($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->rejectionComment;
        return $this->row($data);
    }

    public function save($data){
        return $this->store($this->rejectionComment,$data,'Rejection Comment');
    }

    public function delete($id){
        return $this->trash($this->rejectionComment,['id'=>$id],'Rejection Comment');
    }

    public function getCommentsOnRejectionStage($stageId){
        $data['where']['type'] = 1;
	    $data['customWhere'][] = 'find_in_set("'.$stageId.'", process_id)';
        $data['tableName'] = $this->rejectionComment;
        return $this->rows($data);
    }

    
    public function getReworkCommentList(){
        $data['tableName'] = $this->rejectionComment;
		$data['where']['type'] = 4;
        return $this->rows($data);

    }

}
?>