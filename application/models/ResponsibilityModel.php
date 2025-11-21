<?php
class ResponsibilityModel extends MasterModel
{
    private $rejection_comment = "rejection_comment";
	
    public function getDTRows($data)
    {
        $data['tableName'] = $this->rejection_comment;
        $data['select'] = "rejection_comment.*";
    
		$data['where']['type'] = 6;

        $data['searchCol'][] = "remark";
		$columns = array('','','remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getEmployeeResponsibility($id)
    {
        $data['where']['id'] = $id;
        $data['tableName'] = $this->rejection_comment;
        return $this->row($data);
    }

    public function save($data)
    {
        return $this->store($this->rejection_comment,$data);
    }

    public function delete($id)
    {
        return $this->trash($this->rejection_comment,['id'=>$id]);
    }

    public function getResponsibilityList(){
        $data['tableName'] = 'rejection_comment';
		$data['where']['type'] = 6;
        return $this->rows($data);
    }

    public function getResponsibilitiesByIds($ids)
    {
        $data['where_in']['id'] = $ids;
        $data['tableName'] = $this->rejection_comment;
        return $this->rows($data);
    }
}
?>