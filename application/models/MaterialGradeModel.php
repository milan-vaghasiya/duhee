<?php
class MaterialGradeModel extends MasterModel{
    private $materialMaster = "material_master";
    private $materialSpeci = "material_specification";
    private $scrapGroup = "scrap_group";
    private $itemMaster = "item_master";
	
	public function getDTRows($data){
        $data['tableName'] = $this->materialMaster;
        $data['select'] = "material_master.*,item_master.item_name as group_name,";
        $data['leftJoin']['item_master'] = "item_master.id = material_master.scrap_group";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "material_master.material_grade";
        $data['searchCol'][] = "item_master.item_name"; 
        $data['searchCol'][] = "material_master.color_code";
        
		$columns =array('','','material_master.material_grade','item_master.item_name','material_master.color_code');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getScrapList(){
        $data['tableName'] = 'item_master';
        $data['where']['item_type'] = 10;
        return $this->rows($data);
    }

    public function getMaterialGrades(){
        $data['tableName'] = $this->materialMaster;
        return $this->rows($data);
    }

    public function getMaterial($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->materialMaster;
        return $this->row($data);
    }

    public function getMaterialGrade($material_grade){
        $data['where']['material_grade'] = $material_grade;
        $data['tableName'] = $this->materialMaster;
        return $this->row($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();
            $data['material_grade'] = trim($data['material_grade']);
            if($this->checkDuplicate($data['material_grade'],$data['id']) > 0):
                $errorMessage['material_grade'] = "Material Grade is duplicate.";
                $result = ['status'=>0,'message'=>$errorMessage];
            else:
                $result = $this->store($this->materialMaster,$data,'Material Grade');
            endif;

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function checkDuplicate($materialGrade,$id=""){
        $data['tableName'] = $this->materialMaster;
        $data['where']['material_grade'] = $materialGrade;
        
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();
            $result = $this->trash($this->materialMaster,['id'=>$id],'Material Grade');
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getStandardName(){
        $data['tableName'] = $this->materialMaster;
        $data['select'] = "DISTINCT(standard)";
        return $this->rows($data);
    }

    public function getMaterialParam(){
        $data['tableName'] = 'family_group';
		$data['where']['type != '] = 1;
        return $this->rows($data);
    }

    public function getMaterialSpecification($grade_id){
        $data['tableName'] = $this->materialSpeci;
        $data['where']['grade_id'] = $grade_id;
        return $this->rows($data);
    }
    
    public function getItemWiseMaterialGrade($data){ 
        $data['tableName'] = $this->materialMaster;
        $data['where_in']['id'] = $data['material_grade'];
        return $this->rows($data);
    }
    
    
    // Created By Meghavi @20/05/2023
    public function saveInspectionParam($masterData,$specification){
        try{
            $result = $this->store($this->materialMaster,$masterData,'Material Grade');

            foreach($specification['id'] as $key=>$value):
                $specifData = [
                    'id' => $value,
                    'min_value' => $specification['min_value'][$key],
                    'max_value' => $specification['max_value'][$key]
                ];
                $this->store($this->materialSpeci,$specifData,'Material Specification');
            endforeach;

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }
}
?>