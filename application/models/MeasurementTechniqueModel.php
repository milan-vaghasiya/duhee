<?php
class MeasurementTechniqueModel extends MasterModel{
    private $measurementTechnique = "measurement_technique";

    public function getDTRows($data){
        $data['tableName'] = $this->measurementTechnique;

        $data['searchCol'][] = "measurement_technique";
        $data['serachCol'][] = "remark";
		$columns =array('','','measurement_technique','remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }
    
    public function getMeasurementTechniqueList(){
        $queryData['tableName'] = $this->measurementTechnique;
        $result = $this->rows($queryData);
        return $result;
    }

    public function getMeasurementTechnique($id){
        $data['tableName'] = $this->measurementTechnique;
        $data['where']['id'] = $id;
        return $this->row($data);
    }

    public function save($data){
        return $this->store($this->measurementTechnique,$data,'Measurement Technique');
    }

    public function delete($id){
        return $this->trash($this->measurementTechnique,['id'=>$id],'Measurement Technique');
    }
	
}