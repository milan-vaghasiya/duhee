<?php
class TermsModel extends MasterModel{
    private $terms = "terms";
	
    public function getDTRows($data){
        $data['tableName'] = $this->terms;
        $data['searchCol'][] = "title";
        $data['serachCol'][] = "conditions";
		$columns =array('','','title','','conditions');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getTerms($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->terms;
        return $this->row($data);
    }

    public function save($data){
        return $this->store($this->terms,$data,'Terms');
    }

    public function delete($id){
        return $this->trash($this->terms,['id'=>$id],'Terms');
    }

    public function getTermsList(){
        $data['tableName'] = $this->terms;
        return $this->rows($data);
    }
}
?>