<?php
class ItemCategoryModel extends MasterModel{
    private $itemCategory = "item_category";

    public function mainCategoryList($type=0){
        $data['tableName'] = $this->itemCategory;
        $data['where']['final_category'] = 0;
        $data['where']['ref_id'] = 0;
        if(!empty($type)){ $data['where']['category_type'] = $type; }
        $data['order_by']['category_level'] = 'ASC';
        return $this->rows($data);
    }
    
    public function categoryList(){
        $data['tableName'] = $this->itemCategory;
        $data['where']['final_category'] = 1;
        $data['order_by']['category_level'] = 'ASC';
        return $this->rows($data);
    }
    
    public function getCategoryList($type=0){
		if(!empty($type)){$data['where_in']['ref_id'] = $type;}
        $data['where']['final_category'] = 1;
        $data['tableName'] = $this->itemCategory;
        return $this->rows($data);
    }

    public function getCategory($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->itemCategory;
        return $this->row($data); 
    }

    public function save($data){
        if($this->checkDuplicate($data['category_name'],$data['id']) > 0):
            $errorMessage['category_name'] = "Category Name is duplicate.";
            return ['status'=>0,'message'=>$errorMessage];
        else:
            return $this->store($this->itemCategory,$data,'Item Category');
        endif;
    }

    public function checkDuplicate($name,$id=""){
        $data['tableName'] = $this->itemCategory;
        $data['where']['category_name'] = $name;
        
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }

    public function delete($id){
        return $this->trash($this->itemCategory,['id'=>$id],'Item Category');
    }
    

    public function getNextCategoryLevel($ref_id){
        $data['tableName'] = $this->itemCategory;
        $data['where']['ref_id'] = $ref_id;
        return $this->rows($data);
    }

    //Created By Karmi @25/02/2022
    public function getSubCategory($id)
    {
        $data['tableName'] = $this->itemCategory; 
        $data['select'] = 'item_category.*,employee_master.emp_code,GROUP_CONCAT(employee_master.emp_name) as ename'; 
        $data['leftJoin']['employee_master'] = 'FIND_IN_SET(employee_master.id, item_category.auth_detail)';
        $data['where']['item_category.ref_id'] = $id;
        $data['group_by'][] = 'item_category.id';
        $result= $this->rows($data);
        return $result;
    }
    
    public function getItemAuthorisedBy($item_id)
    {
        $data['tableName'] = "item_category";
        $data['select'] = "employee_master.emp_code, employee_master.emp_name";
        $data['leftJoin']['item_master'] = 'item_master.category_id = item_category.id';
        $data['leftJoin']['employee_master'] = 'FIND_IN_SET(employee_master.id, item_category.auth_detail)';
        $data['where']['item_master.id'] = $item_id;;
        $authData = $this->rows($data);
        $auths = array();
        if (!empty($authData)) {
            
            foreach ($authData as $row) {
                $auths[] = (!empty($row->emp_code)) ? '[' . $row->emp_code . '] ' . $row->emp_name : $row->emp_name;
            };
            $authDetail = '<div class="col-md-12 form-group bg-light-info" style="border:1px solid #000000;padding:5px 7px;"><b>Authorised By : </b>';
            $authDetail .= implode(', ', $auths) . '</div>';

            $authRow = 'Authorised By : '.(implode(', ', $auths));
        }

        return ["status" => 1, "authDetail" => $authDetail, "authRow" => $authRow];
    }
    
    public function getInstrumentByCode($cate_code){
        $data['where_in']['tool_type'] = (int)$cate_code;
        $data['where_in']['ref_id'] = '6,7';
        $data['tableName'] = $this->itemCategory;
        return $this->row($data);
    }

    public function getCategoryBYIds($ids){
        $data['where_in']['id'] = $ids;
        $data['tableName'] = $this->itemCategory;
        return $this->rows($data);
    }
}
?>