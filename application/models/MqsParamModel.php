<?php
class MqsParamModel extends MasterModel{
    private $mqsParam = "mqs_param";
    private $familyGroup = "family_group";

    public function getMQSParameterList($type){
        $data['tableName'] = $this->familyGroup;
        $data['where_in']['type'] = $type;
        return $this->rows($data);
    }

    public function save($data){
        try {
            $this->db->trans_begin();
            foreach($data['parameter'] as $key=>$value){
                $queryData = [
                    'id'=>$data['id'][$key],
                    'grade_id'=>$data['grade_id'],
                    'process_id'=>$data['process_id'],
                    'parameter'=>$value,
                    'specification_type'=>$data['specification_type'][$key], 
                    'min'=>$data['min'][$key], 
                    'max'=>$data['max'][$key], 
                    'other'=>$data['other'][$key], 
                    'inspection_method'=>$data['inspection_method'][$key], 
                    'created_by'=>$data['created_by'], 
                ];
                $result = $this->store($this->mqsParam,$queryData);
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

    public function getMQSReport($postdata){
        $data['tableName'] = $this->mqsParam;
        $data['select'] = "mqs_param.*,family_group.type";
        $data['leftJoin']['family_group'] = "family_group.family_name = mqs_param.parameter";
        if(!empty($postdata['grade_id'])){$data['where_in']['mqs_param.grade_id'] = $postdata['grade_id']; }
        if(!empty($postdata['process_id'])){$data['where']['mqs_param.process_id'] = $postdata['process_id']; }
        return $this->rows($data);
    }

    public function getMQSDetail($id){
        $data['tableName'] = $this->mqsParam;
        $data['select'] = "mqs_param.*,family_group.type";
        $data['leftJoin']['family_group'] = "family_group.family_name = mqs_param.parameter";
        $data['where']['mqs_param.id'] = $id;
        return $this->row($data);
    }
}
?>