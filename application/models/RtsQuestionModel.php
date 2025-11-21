<?php
class RtsQuestionModel extends MasterModel
{
    private $rts_quest_master = "rts_quest_master";

   
    public function getDTRows($data)
    {
        $data['tableName'] = $this->rts_quest_master;
        $data['select'] = "rts_quest_master.*";
        $data['where']['rts_quest_master.type'] = $data['type'];
        if(!empty($data['ref_id'])){ $data['where']['rts_quest_master.ref_id'] = $data['ref_id']; }
    
        $data['searchCol'][] = "rts_quest_master.description";
        $columns = array('', '', 'rts_quest_master.description');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        $result = $this->pagingRows($data);
        return $result;
    }

    public function save($data){
        try {
            $this->db->trans_begin();
			$result = $this->store($this->rts_quest_master,$data);
			if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		} catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}	
    }

    public function getRTSQuest($id){
        $data['tableName'] = $this->rts_quest_master;
        $data['select'] = "rts_quest_master.*";
        $data['where']['rts_quest_master.id'] = $id;
        return $this->row($data);
    }
    public function delete($id){
        try {
            $this->db->trans_begin();
			$result = $this->trash($this->rts_quest_master,['id'=>$id]);
			if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		} catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}	
    }

    public function getQuestions(){
        $queryData['tableName'] = 'rts_quest_master';
        $queryData['select'] = 'rts_quest_master.*,heading.description as heading';
        $queryData['leftJoin']['rts_quest_master as heading'] ="rts_quest_master.ref_id = heading.id";
        $queryData['where']['rts_quest_master.type'] = 3;
        $questionList = $this->rows($queryData);
        return $questionList;
    }

    public function getMianHeadings(){
        $queryData['tableName'] = 'rts_quest_master';
        $queryData['select'] = 'rts_quest_master.*';
        $queryData['where']['rts_quest_master.type'] =1;
        $questionList = $this->rows($queryData);
        return $questionList;
    }
}
