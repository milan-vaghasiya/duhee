<?php
class VehicleTypeModel extends MasterModel{
    private $vehicleType = "vehicle_types";

    public function getDTRows($data){
        $data['tableName'] = $this->vehicleType;

        $data['searchCol'][] = "vehicle_type";
        $data['serachCol'][] = "remark";
		$columns =array('','','vehicle_type','remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }
    
    public function getVehicleTypeList(){
        $queryData['tableName'] = $this->vehicleType;
        $result = $this->rows($queryData);
        return $result;
    }

    public function getVehicleType($id){
        $data['tableName'] = $this->vehicleType;
        $data['where']['id'] = $id;
        return $this->row($data);
    }

    public function save($data){
        return $this->store($this->vehicleType,$data,'Vehicle Type');
    }

    public function delete($id){
        return $this->trash($this->vehicleType,['id'=>$id],'Vehicle Type');
    }
	
}