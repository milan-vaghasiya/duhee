<?php
class FamilyGroupModel extends MasterModel{
    private $familyGroup = "family_group";
    
	public function getDTRows($data){
        $data['tableName'] = $this->familyGroup;
        if(!empty($data['type']) && $data['type'] == 1){ 
            $data['where']['type'] = $data['type'];
        }else{ 
            $data['where']['type != '] = 1; 
        }
        
        $data['searchCol'][] = "family_group.family_name";
        $data['searchCol'][] = "item_category.remark";
		$columns =array('','','family_name','remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getFamilyGroup($id){
        $data['tableName'] = $this->familyGroup;
        $data['where']['id'] = $id;
        return $this->row($data);
    }

    public function save($data){
        if($this->checkDuplicate($data['family_name'],$data['type'],$data['id']) > 0):
            $errorMessage['family_name'] = "Family Name is duplicate.";
            return ['status'=>0,'message'=>$errorMessage];
        else:
            return $this->store($this->familyGroup,$data);
        endif;
    }

    public function checkDuplicate($name,$type,$id=""){
        $data['tableName'] = $this->familyGroup;
        $data['where']['family_name'] = $name;
        $data['where']['type'] = $type;

        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }

    public function delete($id){
        return $this->trash($this->familyGroup,['id'=>$id]);
    }
}
?>