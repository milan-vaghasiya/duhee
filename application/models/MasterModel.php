<?php /* Master Modal Ver. : 1.1  */
class MasterModel extends CI_Model{

    /* Get Paging Rows */
    public function pagingRows($data){
        $draw = $data['draw'];
		$start = $data['start'];
		$rowperpage = $data['length']; // Rows display per page
		$searchValue = $data['search']['value'];		
		
		/********** Total Records without Filtering ***********/
		{
            if(isset($data['select'])):
                if(!empty($data['select'])):
                    $this->db->select($data['select']);
                endif;
            endif;
    
            if(isset($data['join'])):
                if(!empty($data['join'])):
                    foreach($data['join'] as $key=>$value):
                        $this->db->join($key,$value);
                    endforeach;
                endif;
            endif;
    
            if(isset($data['leftJoin'])):
                if(!empty($data['leftJoin'])):
                    foreach($data['leftJoin'] as $key=>$value):
                        $this->db->join($key,$value,'left');
                    endforeach;
                endif;
            endif;
    
            if(isset($data['where'])):
                if(!empty($data['where'])):
                    foreach($data['where'] as $key=>$value):
                        $this->db->where($key,$value);
                    endforeach;
                endif;            
            endif;
            if(isset($data['whereFalse'])):
                if(!empty($data['whereFalse'])):
                    foreach($data['whereFalse'] as $key=>$value):
                        $this->db->where($key,$value,false);
                    endforeach;
                endif;            
            endif;
            if(isset($data['customWhere'])):
                if(!empty($data['customWhere'])):
                    foreach($data['customWhere'] as $value):
                        $this->db->where($value);
                    endforeach;
                endif;
            endif;
            $this->db->where($data['tableName'].'.is_delete',0);
    
            if(isset($data['where_in'])):
                if(!empty($data['where_in'])):
                    foreach($data['where_in'] as $key=>$value):
                        $this->db->where_in($key,$value,false);
                    endforeach;
                endif;
            endif;

    		if (isset($data['having'])) :
				if (!empty($data['having'])) :
					foreach ($data['having'] as $value) :
						$this->db->having($value);
					endforeach;
				endif;
			endif;

    		if(isset($data['group_by'])):
                if(!empty($data['group_by'])):
                    foreach($data['group_by'] as $key=>$value):
                        $this->db->group_by($value);
                    endforeach;
                endif;
            endif;
    		
            $totalRecords = $this->db->get($data['tableName'])->num_rows();
            //print_r($this->db->last_query());
		}
        /********** End Count Total Records without Filtering ***********/
        
        
        
        /********** Count Total Records with Filtering ***********/
        {
    		if(isset($data['select'])):
                if(!empty($data['select'])):
                    $this->db->select($data['select']);
                endif;
            endif;
    
            if(isset($data['join'])):
                if(!empty($data['join'])):
                    foreach($data['join'] as $key=>$value):
                        $this->db->join($key,$value);
                    endforeach;
                endif;
            endif;
    
            if(isset($data['leftJoin'])):
                if(!empty($data['leftJoin'])):
                    foreach($data['leftJoin'] as $key=>$value):
                        $this->db->join($key,$value,'left');
                    endforeach;
                endif;
            endif;
    
            if(isset($data['where'])):
                if(!empty($data['where'])):
                    foreach($data['where'] as $key=>$value):
                        $this->db->where($key,$value);
                    endforeach;
                endif;            
            endif;
			if(isset($data['whereFalse'])):
				if(!empty($data['whereFalse'])):
					foreach($data['whereFalse'] as $key=>$value):
						$this->db->where($key,$value);
					endforeach;
				endif;            
			endif;
            if(isset($data['customWhere'])):
                if(!empty($data['customWhere'])):
                    foreach($data['customWhere'] as $value):
                        $this->db->where($value);
                    endforeach;
                endif;
            endif;
            $this->db->where($data['tableName'].'.is_delete',0);
    
            if(isset($data['where_in'])):
                if(!empty($data['where_in'])):
                    foreach($data['where_in'] as $key=>$value):
                        $this->db->where_in($key,$value,false);
                    endforeach;
                endif;
            endif;
    		if (isset($data['having'])) :
				if (!empty($data['having'])) :
					foreach ($data['having'] as $value) :
						$this->db->having($value);
					endforeach;
				endif;
			endif;
			
    		$c=0;
    		// General Search
    		if(!empty($searchValue)):
                if(isset($data['searchCol'])):
                    if(!empty($data['searchCol'])):
                        $this->db->group_start();
    						foreach($data['searchCol'] as $key=>$value):
    						    if(!empty($value)){
        							if($key == 0):
        								$this->db->like($value,str_replace(" ", "%", $searchValue),'both',false);
        							else:
        								$this->db->or_like($value,str_replace(" ", "%", $searchValue),'both',false);
        							endif;
    						    }
    						endforeach;
                        $this->db->group_end();
                    endif;
                endif;
    		endif;
    		
    		// Column Search
    		if(isset($data['searchCol'])):
    			if(!empty($data['searchCol'])):
    				foreach($data['searchCol'] as $key=>$value):
    					if(!empty($value)){
    						$csearch = $data['columns'][$key]['search']['value'];
    						if(!empty($csearch)){$this->db->like($value,$csearch);}
    					}
    				endforeach;
    			endif;
    		endif;
    		
    		if(isset($data['group_by'])):
                if(!empty($data['group_by'])):
                    foreach($data['group_by'] as $key=>$value):
                        $this->db->group_by($value);
                    endforeach;
                endif;
            endif;
    		
    		$totalRecordwithFilter = $this->db->get($data['tableName'])->num_rows();
    		//print_r($this->db->last_query());
        }
        /********** End Count Total Records with Filtering ***********/
		
		
        /********** Total Records with Filtering ***********/
        {
            if(isset($data['select'])):
                if(!empty($data['select'])):
                    $this->db->select($data['select']);
                endif;
            endif;
    
            if(isset($data['join'])):
                if(!empty($data['join'])):
                    foreach($data['join'] as $key=>$value):
                        $this->db->join($key,$value);
                    endforeach;
                endif;
            endif;  
            
            if(isset($data['leftJoin'])):
                if(!empty($data['leftJoin'])):
                    foreach($data['leftJoin'] as $key=>$value):
                        $this->db->join($key,$value,'left');
                    endforeach;
                endif;
            endif;
    
            if(isset($data['where'])):
                if(!empty($data['where'])):
                    foreach($data['where'] as $key=>$value):
                        $this->db->where($key,$value);
                    endforeach;
                endif;            
            endif;
			if(isset($data['whereFalse'])):
				if(!empty($data['whereFalse'])):
					foreach($data['whereFalse'] as $key=>$value):
						$this->db->where($key,$value);
					endforeach;
				endif;            
			endif;

            
            if(isset($data['customWhere'])):
                if(!empty($data['customWhere'])):
                    foreach($data['customWhere'] as $value):
                        $this->db->where($value);
                    endforeach;
                endif;
            endif;
            $this->db->where($data['tableName'].'.is_delete',0);
    
            if(isset($data['where_in'])):
                if(!empty($data['where_in'])):
                    foreach($data['where_in'] as $key=>$value):
                        $this->db->where_in($key,$value,false);
                    endforeach;
                endif;
            endif;
    
    		$c=0;
    		// General Search
    		if(!empty($searchValue)):
                if(isset($data['searchCol'])):
                    if(!empty($data['searchCol'])):
                        $this->db->group_start();
                        foreach($data['searchCol'] as $key=>$value):
                            if(!empty($value)){
                                if($key == 0):
                                    $this->db->like($value,str_replace(" ", "%", $searchValue),'both',false);
                                else:
                                    $this->db->or_like($value,str_replace(" ", "%", $searchValue),'both',false);
                                endif;
                            }
                        endforeach;
                        $this->db->group_end();
                    endif;
                endif;
    		endif;
    		
    		// Column Search
    		if(isset($data['searchCol'])):
    			if(!empty($data['searchCol'])):
    				foreach($data['searchCol'] as $key=>$value):
    					if(!empty($value)){
    						$csearch = $data['columns'][$key]['search']['value'];
    						if(!empty($csearch)){$this->db->like($value,$csearch);}
    					}
    				endforeach;
    			endif;
    		endif;
            
            if(isset($data['order_by'])):
                if(!empty($data['order_by'])):
                    foreach($data['order_by'] as $key=>$value):
                        $this->db->order_by($key,$value);
                    endforeach;
                endif;
            endif;
    
            
    		if (isset($data['having'])) :
				if (!empty($data['having'])) :
					foreach ($data['having'] as $value) :
						$this->db->having($value);
					endforeach;
				endif;
			endif;


            if(isset($data['group_by'])):
                if(!empty($data['group_by'])):
                    foreach($data['group_by'] as $key=>$value):
                        $this->db->group_by($value);
                    endforeach;
                endif;
            endif;
    
            $resultData = $this->db->limit($rowperpage, $start)->get($data['tableName'])->result();
            //print_r($this->db->last_query());
        }
        /********** End Total Records with Filtering ***********/
        
        $response = [
            "draw" => intval($draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalRecordwithFilter,
            "data" => $resultData
        ]; 
        return $response;
    }

    /* Get All Rows */
    public function rows($data){

        if(isset($data['select'])):
            if(!empty($data['select'])):
                $this->db->select($data['select']);
            endif;
        endif;

        if(isset($data['join'])):
            if(!empty($data['join'])):
                foreach($data['join'] as $key=>$value):
                    $this->db->join($key,$value);
                endforeach;
            endif;
        endif;

        if(isset($data['leftJoin'])):
            if(!empty($data['leftJoin'])):
                foreach($data['leftJoin'] as $key=>$value):
                    $this->db->join($key,$value,'left');
                endforeach;
            endif;
        endif;

        if(isset($data['where'])):
            if(!empty($data['where'])):
                foreach($data['where'] as $key=>$value):
                    $this->db->where($key,$value);
                endforeach;
            endif;            
        endif;
        
        if(isset($data['whereFalse'])):
            if(!empty($data['whereFalse'])):
                foreach($data['whereFalse'] as $key=>$value):
                    $this->db->where($key,$value,false); 
                endforeach;
            endif;            
        endif;
        
        if(isset($data['customWhere'])):
            if(!empty($data['customWhere'])):
                foreach($data['customWhere'] as $value):
                    $this->db->where($value);
                endforeach;
            endif;
        endif;

        if(isset($data['where_in'])):
            if(!empty($data['where_in'])):
                foreach($data['where_in'] as $key=>$value):
                    $this->db->where_in($key,$value,false);
                endforeach;
            endif;
        endif;

        if (isset($data['having'])) :
			if (!empty($data['having'])) :
				foreach ($data['having'] as $value) :
					$this->db->having($value);
				endforeach;
			endif;
		endif;

        if(isset($data['like'])):
            if(!empty($data['like'])):
                $i=1;
                $this->db->group_start();
                foreach($data['like'] as $key=>$value):
                    if($i == 1):
                        $this->db->like($key,$value,'both',false);
                    else:
                        $this->db->or_like($key,$value,'both',false);
                    endif;
                    $i++;
                endforeach;
                $this->db->group_end();
            endif;
        endif;

        if(isset($data['columnSearch'])):
            if(!empty($data['columnSearch'])):                
                $this->db->group_start();
                foreach($data['columnSearch'] as $key=>$value):
                    $this->db->like($key,$value);
                endforeach;
                $this->db->group_end();
            endif;
        endif;

        if(isset($data['order_by'])):
            if(!empty($data['order_by'])):
                foreach($data['order_by'] as $key=>$value):
                    $this->db->order_by($key,$value);
                endforeach;
            endif;
        endif;

        if(isset($data['group_by'])):
            if(!empty($data['group_by'])):
                foreach($data['group_by'] as $key=>$value):
                    $this->db->group_by($value);
                endforeach;
            endif;
        endif;
		if(isset($data['limit'])):
            if(!empty($data['limit'])):
                $this->db->limit($data['limit']);
            endif;
        endif;

        if(isset($data['start']) && isset($data['length'])):
            if(!empty($data['length'])):
                $this->db->limit($data['length'],$data['start']);
            endif;
        endif;
        
        if(isset($data['all'])):
            if(!empty($data['all'])):
                foreach($data['all'] as $key=>$value):
                    $this->db->where_in($key,$value,false);
                endforeach;
            endif;
        else:
            $this->db->where($data['tableName'].'.is_delete',0);
        endif;
        
        //$this->db->where($data['tableName'].'.is_delete',0);
        $result = $this->db->get($data['tableName'])->result();
        //print_r($this->db->last_query());
        return $result;
    }

    /* Get Single Row */
    public function row($data){
        if(isset($data['select'])):
            if(!empty($data['select'])):
                $this->db->select($data['select']);
            endif;
        endif;

        if(isset($data['join'])):
            if(!empty($data['join'])):
                foreach($data['join'] as $key=>$value):
                    $this->db->join($key,$value);
                endforeach;
            endif;
        endif;

        if(isset($data['leftJoin'])):
            if(!empty($data['leftJoin'])):
                foreach($data['leftJoin'] as $key=>$value):
                    $this->db->join($key,$value,'left');
                endforeach;
            endif;
        endif;

        if(isset($data['where'])):
            if(!empty($data['where'])):
                foreach($data['where'] as $key=>$value):
                    $this->db->where($key,$value);
                endforeach;
            endif;            
        endif;
        if(isset($data['customWhere'])):
            if(!empty($data['customWhere'])):
                foreach($data['customWhere'] as $value):
                    $this->db->where($value);
                endforeach;
            endif;
        endif;
        $this->db->where($data['tableName'].'.is_delete',0);

        if(isset($data['where_in'])):
            if(!empty($data['where_in'])):
                foreach($data['where_in'] as $key=>$value):
                    $this->db->where_in($key,$value,false);
                endforeach;
            endif;
        endif;

        if(isset($data['like'])):
            if(!empty($data['like'])):
                $i=1;
                $this->db->group_start();
                foreach($data['like'] as $key=>$value):
                    if($i == 1):
                        $this->db->like($key,$value);
                    else:
                        $this->db->or_like($key,$value);
                    endif;
                    $i++;
                endforeach;
                $this->db->group_end();
            endif;
        endif;

        if(isset($data['order_by'])):
            if(!empty($data['order_by'])):
                foreach($data['order_by'] as $key=>$value):
                    $this->db->order_by($key,$value);
                endforeach;
            endif;
        endif;

        if(isset($data['group_by'])):
            if(!empty($data['group_by'])):
                foreach($data['group_by'] as $key=>$value):
                    $this->db->group_by($value);
                endforeach;
            endif;
        endif;
		if(isset($data['limit'])):
            if(!empty($data['limit'])):
                $this->db->limit($data['limit']);
            endif;
        endif;
		
		$result = $this->db->get($data['tableName'])->row();
 		//print_r($this->db->last_query());
        return $result;
    }

    public function customRow($data){
        if(isset($data['select'])):
            if(!empty($data['select'])):
                $this->db->select($data['select']);
            endif;
        endif;

        if(isset($data['join'])):
            if(!empty($data['join'])):
                foreach($data['join'] as $key=>$value):
                    $this->db->join($key,$value);
                endforeach;
            endif;
        endif;

        if(isset($data['leftJoin'])):
            if(!empty($data['leftJoin'])):
                foreach($data['leftJoin'] as $key=>$value):
                    $this->db->join($key,$value,'left');
                endforeach;
            endif;
        endif;

        foreach($data['where'] as $key=>$value):
            $this->db->where($key,$value);
        endforeach;
        if(isset($data['customWhere'])):
            if(!empty($data['customWhere'])):
                foreach($data['customWhere'] as $value):
                    $this->db->where($value);
                endforeach;
            endif;
        endif;
		
        if(isset($data['where_in'])):
            if(!empty($data['where_in'])):
                foreach($data['where_in'] as $key=>$value):
                    $this->db->where_in($key,$value,false);
                endforeach;
            endif;
        endif;
		
		$result = $this->db->get($data['tableName'])->row();
        return $result;
    }

    /* Save and Update Row */
    public function store($tableName,$data,$msg = "Record"){
        $id = $data['id'];
        unset($data['id']);
        if(empty($id)):
            $this->db->insert($tableName,$data);
            $insert_id = $this->db->insert_id();
            return ['status'=>1,'message'=>$msg." saved Successfully.",'insert_id'=>$insert_id,'qry'=>$this->db->last_query()];
        else:
            $this->db->where('id',$id);
            $this->db->update($tableName,$data);
            return ['status'=>1,'message'=>$msg." updated Successfully.",'insert_id'=>-1,'qry'=>$this->db->last_query()];
        endif;
    }

    /* Update Row */
    public function edit($tableName,$where,$data,$msg = "Record"){
        if(!empty($where)):
            foreach($where as $key=>$value):
                $this->db->where($key,$value);
            endforeach;
        endif;
        $this->db->update($tableName,$data);
        return ['status'=>1,'message'=>$msg." updated Successfully.",'insert_id'=>-1];
    }

    /* Update Row */
    public function editCustom($tableName,$customWhere,$data,$where=Array()){
        if(!empty($where)):
            foreach($where as $key=>$value):
                $this->db->where($key,$value);
            endforeach;
        endif;
		if(isset($customWhere)):
            if(!empty($customWhere)):
                foreach($customWhere as $value):
                    $this->db->where($value);
                endforeach;
            endif;
        endif;
        $this->db->update($tableName,$data);
        return ['status'=>1,'message'=>"Record updated Successfully.",'insert_id'=>-1];
    }

    /* Get Numbers of Rows */
    public function numRows($data){
        if(isset($data['select'])):
            if(!empty($data['select'])):
                $this->db->select($data['select']);
            endif;
        endif;

        if(isset($data['join'])):
            if(!empty($data['join'])):
                foreach($data['join'] as $key=>$value):
                    $this->db->join($key,$value);
                endforeach;
            endif;
        endif;

        if(isset($data['leftJoin'])):
            if(!empty($data['leftJoin'])):
                foreach($data['leftJoin'] as $key=>$value):
                    $this->db->join($key,$value,'left');
                endforeach;
            endif;
        endif;

        if(isset($data['where'])):
            if(!empty($data['where'])):
                foreach($data['where'] as $key=>$value):
                    $this->db->where($key,$value);
                endforeach;
            endif;            
        endif;
        if(isset($data['customWhere'])):
            if(!empty($data['customWhere'])):
                foreach($data['customWhere'] as $value):
                    $this->db->where($value);
                endforeach;
            endif;
        endif;
        $this->db->where($data['tableName'].'.is_delete',0);

        if(isset($data['where_in'])):
            if(!empty($data['where_in'])):
                foreach($data['where_in'] as $key=>$value):
                    $this->db->where_in($key,$value,false);
                endforeach;
            endif;
        endif;

        if(isset($data['like'])):
            if(!empty($data['like'])):
                $i=1;
                $this->db->group_start();
                foreach($data['like'] as $key=>$value):
                    if($i == 1):
                        $this->db->like($key,$value);
                    else:
                        $this->db->or_like($key,$value);
                    endif;
                    $i++;
                endforeach;
                $this->db->group_end();
            endif;
        endif;

        if(isset($data['order_by'])):
            if(!empty($data['order_by'])):
                foreach($data['order_by'] as $key=>$value):
                    $this->db->order_by($key,$value);
                endforeach;
            endif;
        endif;

        if(isset($data['group_by'])):
            if(!empty($data['group_by'])):
                foreach($data['group_by'] as $key=>$value):
                    $this->db->group_by($value);
                endforeach;
            endif;
        endif;

        return $this->db->get($data['tableName'])->num_rows();
    }

    /* Set Deleteed Flage */
    public function trash($tableName,$where,$msg = "Record"){
        if(!empty($where)):
            foreach($where as $key=>$value):
                $this->db->where($key,$value);
            endforeach;
        endif;
        $this->db->update($tableName,['is_delete'=>1]);
        return ['status'=>1,'message'=>$msg." deleted Successfully."];
    }

    /* Delete Recored Permanent */
    public function remove($tableName,$where,$msg = ""){
        if(!empty($where)):
            foreach($where as $key=>$value):
                $this->db->where($key,$value);
            endforeach;
        endif;
        $this->db->delete($tableName);
        return ['status'=>1,'message'=>$msg." deleted Successfully."];
    }

    /* Get Specific Row. Like : SUM,MAX,MIN,COUNT ect... */
    public function specificRow($data){
        if(isset($data['select'])):
            if(!empty($data['select'])):
                $this->db->select($data['select']);
            endif;
        endif;

        if(isset($data['join'])):
            if(!empty($data['join'])):
                foreach($data['join'] as $key=>$value):
                    $this->db->join($key,$value);
                endforeach;
            endif;
        endif;

        if(isset($data['leftJoin'])):
            if(!empty($data['leftJoin'])):
                foreach($data['leftJoin'] as $key=>$value):
                    $this->db->join($key,$value,'left');
                endforeach;
            endif;
        endif;

        if(isset($data['where'])):
            if(!empty($data['where'])):
                foreach($data['where'] as $key=>$value):
                    $this->db->where($key,$value);
                endforeach;
            endif;            
        endif;
        if(isset($data['customWhere'])):
            if(!empty($data['customWhere'])):
                foreach($data['customWhere'] as $value):
                    $this->db->where($value);
                endforeach;
            endif;
        endif;
        $this->db->where($data['tableName'].'.is_delete',0);

        if(isset($data['where_in'])):
            if(!empty($data['where_in'])):
                foreach($data['where_in'] as $key=>$value):
                    $this->db->where_in($key,$value,false);
                endforeach;
            endif;
        endif;

        if(isset($data['like'])):
            if(!empty($data['like'])):
                $i=1;
                $this->db->group_start();
                foreach($data['like'] as $key=>$value):
                    if($i == 1):
                        $this->db->like($key,$value);
                    else:
                        $this->db->or_like($key,$value);
                    endif;
                    $i++;
                endforeach;
                $this->db->group_end();
            endif;
        endif;

        if(isset($data['order_by'])):
            if(!empty($data['order_by'])):
                foreach($data['order_by'] as $key=>$value):
                    $this->db->order_by($key,$value);
                endforeach;
            endif;
        endif;

        if(isset($data['group_by'])):
            if(!empty($data['group_by'])):
                foreach($data['group_by'] as $key=>$value):
                    $this->db->group_by($value);
                endforeach;
            endif;
        endif;
            
        if(isset($data['resultType'])):
            if($data['resultType'] == "numRows")
                return $this->db->get($data['tableName'])->num_rows();            
            if($data['resultType'] == "resultRows")
                return $this->db->get($data['tableName'])->result();
        endif;

        $result =  $this->db->get($data['tableName'])->row();
		// print_r($this->db->last_query());
		return $result;
    }

	/* Print Executed Query */
    public function printQuery(){  print_r($this->db->last_query());exit; }

	/* Custom Set OR Update Row */
    public function setValue($data){
		if(!empty($data['where'])):
			if(isset($data['where'])):
				if(!empty($data['where'])):
					foreach($data['where'] as $key=>$value):
						$this->db->where($key,$value);
					endforeach;
				endif;            
			endif;
			
			if(isset($data['set'])):
				if(!empty($data['set'])):
					foreach($data['set'] as $key=>$value):
						$v = explode(',',$value);
						$setVal = "`".$v[0]."` ".$v[1];
						$this->db->set($key, $setVal, FALSE);
					endforeach;
				endif;            
			endif;
            $this->db->update($data['tableName']);
            return ['status'=>1,'message'=>"Record updated Successfully.",'qry'=>$this->db->last_query()];
        endif;
		return ['status'=>0,'message'=>"Record updated Successfully.",'qry'=>"Query not fired"];
    }

    /* Save and Update Row */
    public function storeBatch($tableName,$itemData,$masterKey,$affectedData = Array()){
		
		$transData = $this->getTransactionArray($tableName,$itemData,$masterKey);
		$batchData = $transData['tansItems'];
		$newRecords = Array();
		$updatedRecords = Array();
		$removedRecords = $transData['removedTrans'];
		foreach($batchData as $data):
			$id = $data['id'];
			unset($data['id']);
			if(empty($id)):
				$this->db->insert($tableName,$data);
				// $insert_id = $this->db->insert_id();
				$newRecords = $data;
				/***	NEW ENTRY : UPDATE OTHER AFFECTED TABLE FIELDS WITH CURERNT VALUE (FOR Ex. qty FOR STOCK UPDATE)	***/
				if(!empty($affectedData)):
					foreach($affectedData as $aData):
						$setVal = '';
						if(!empty($aData['operation'])):
							$operator = ($aData['operation'] == 'plus') ? '+' : '-';
							$setVal = "`".$aData['tp_field_key']."` ".$operator." ".$data[$aData['field_key']];
						else:
							$setVal = $data[$aData['field_key']];
						endif;
						$this->db->set($aData['tp_field_key'], $setVal, FALSE);
						$this->db->where('id', $data[$aData['foreign_key']]);
						$this->db->update($aData['table_name']);
					endforeach;
				endif;
			else:
				$queryData=array();
                $queryData['tableName'] = $tableName;
                $queryData['where']['id'] = $id;
                $transRow = $this->row($queryData);
				$updatedRecords[] = $transRow;
				$this->db->where('id',$id);
				$this->db->update($tableName,$data);
				
				/***	MODIFIED ENTRY : UPDATE OTHER AFFECTED TABLE FIELDS WITH CURERNT VALUE (FOR Ex. qty FOR STOCK UPDATE)	***/
				if(!empty($affectedData)):
					foreach($affectedData as $aData):
						
						$setVal = '';
						if(!empty($aData['operation'])):
							if($aData['operation'] == 'plus'):
								$field_val = $data[$aData['field_key']] - $transRow->{$aData['field_key']};
								$setVal = "`".$aData['tp_field_key']."` + ".$field_val;
							else:
								$field_val = $data[$aData['field_key']] - $transRow->{$aData['field_key']};
								$setVal = "`".$aData['tp_field_key']."` - ".$field_val;
							endif;
						else:
							$setVal = $data[$aData['field_key']];
						endif;
						$this->db->set($aData['tp_field_key'], $setVal, FALSE);
						$this->db->where('id', $data[$aData['foreign_key']]);
						$this->db->update($aData['table_name']);
					endforeach;
				endif;
			endif;
		endforeach;
		
		/***	REMOVED ENTRY : UPDATE OTHER AFFECTED TABLE FIELDS WITH CURERNT VALUE (FOR Ex. qty FOR STOCK UPDATE)	***/
		if(!empty($affectedData)):
			if(!empty($removedRecords)):
				foreach($removedRecords as $row):
					foreach($affectedData as $aData):
						$operator = ($aData['operation'] == 'plus') ? '-' : '+';
						$setVal = "`".$aData['tp_field_key']."` ".$operator." ".$row->{$aData['field_key']};
						$this->db->set($aData['tp_field_key'], $setVal, FALSE);
						$this->db->where('id', $row->{$aData['foreign_key']});
						$this->db->update($aData['table_name']);
					endforeach;
				endforeach;
			endif;
		endif;
		
		return ["allRecords" => $batchData,"newRecords" => $newRecords,"updatedRecords" => $updatedRecords,"removedRecords" => $removedRecords];
    }
	
	/* Set Transaction Aray For Store Batch */
	public function getTransactionArray($tableName,$itemData,$masterKey)
	{
		$transItems = Array();$allIds = Array();$removedTrans = Array();
		if(!empty($itemData))
		{
			$totalItems = count($itemData['id']);
			for($idx=0;$idx<$totalItems;$idx++)
			{
				$itms = Array();
				foreach($itemData as $key=>$value):
					if($key == 'id'):
						if(empty($value[$idx]))
							$itms[$masterKey[0]] = $masterKey[1];
						else
							$allIds[] = $value[$idx];
					endif;
					$itms[$key] = $value[$idx];
				endforeach;
				$transItems[] = $itms;
			}
		}
		$oldData['where'][$masterKey[0]] = $masterKey[1];
		$oldData['tableName'] = $tableName;
		$oldTransData = $this->rows($oldData);
		
		foreach($oldTransData as $row):
			if(!in_array($row->id,$allIds)):
				$removedTrans[] = $row;
				$this->trash($tableName,['id'=>$row->id]);
			endif;
		endforeach;
		
		return ["tansItems" => $transItems, "removedTrans" => $removedTrans];
	}

	/* Company Information */
	public function getCompanyInfo(){
		$data['tableName'] = 'company_info';
		$data['where']['id'] = 1;
		return $this->row($data);
	}

	/* Master Options */
	public function getMasterOptions(){
		$data['tableName'] = 'master_options';
		$data['where']['id'] = 1;
		return $this->row($data);
	}	
		
	/* Get Entry Log Detail By Table */
	public function getEntryLog($id,$tableName){
        $data['tableName'] = $tableName;
        $data['select'] = $tableName.".created_at, ".$tableName.".updated_at, ".$tableName.".approved_at, employee_master.emp_name as created_name, approve.emp_name as approve_name, update.emp_name as update_name";
        $data['leftJoin']['employee_master'] = "employee_master.id = ".$tableName.".created_by";
        $data['leftJoin']['employee_master as approve'] = "approve.id = ".$tableName.".approved_by";
        $data['leftJoin']['employee_master as update'] = "update.id = ".$tableName.".updated_by";
        $data['where'][$tableName.'.id'] = $id;
        return $this->row($data);
    }

}
?>