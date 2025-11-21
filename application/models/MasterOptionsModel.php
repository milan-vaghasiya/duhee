<?php
class MasterOptionsModel extends MasterModel{
    private $masterOptions = "master_options";

	public function save($data){    
		/* $data['material_grade'] = implode(',', array_unique(explode(',',$data['material_grade'])));
		$data['color_code'] = implode(',', array_unique(explode(',',$data['color_code'])));
		$data['thread_types'] = implode(',', array_unique(explode(',',$data['thread_types']))); */

        $result = $this->store($this->masterOptions,$data,'Master Options');
        return $result;	
    }
}
?>