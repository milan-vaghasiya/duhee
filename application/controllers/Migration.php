<?php
class Migration extends CI_Controller{

    public function __construct(){
        parent::__construct();
    }
    
    /*** Created By Jp@10-09-2022 update Old Stock to zero  Migration/toolStockUpdate ***/
    /*public function toolStockUpdate(){
        try{
            $this->db->trans_begin();
            
			$i=0;
		    $this->db->reset_query();
		    $this->db->select("SUM(stock_transaction.qty) as qty,stock_transaction.location_id,stock_transaction.batch_no,stock_transaction.item_id");
            $this->db->join('tool_stock','tool_stock.item_id = stock_transaction.item_id AND tool_stock.created_by = 3');
			$this->db->where('stock_transaction.is_delete',0);
			$this->db->where('tool_stock.qty >',0);
			$this->db->where('tool_stock.created_by',3);
			$this->db->where('tool_stock.item_id !=',0);
            $this->db->group_by('stock_transaction.item_id');
            $this->db->group_by('stock_transaction.location_id');
            $this->db->group_by('stock_transaction.batch_no');
            $stockData = $this->db->get('stock_transaction')->result();

            foreach($stockData as $row):
                //update Old stock
                if($row->qty != 0):
                    $stockTrans=array();
    				$trans_type = 0;$stock_qty=0;
    				if($row->qty > 0){$trans_type = 2;$stock_qty = ($row->qty * -1);}
    				if($row->qty < 0){$trans_type = 1;$stock_qty = abs($row->qty);}
    				$stockTrans = [
    					'location_id' => $row->location_id,
    					'batch_no' => $row->batch_no,
    					'trans_type' => $trans_type,
    					'item_id' => $row->item_id,
    					'qty' => $stock_qty,
    					'remark' => 'STOCK_ADJUST_BY_NYN',
    					'ref_type' => 999,
    					'ref_date' => date('Y-m-d')
    				];	$i++;
    				print_r($stockTrans);print_r('<hr>'); $i++;
    				$this->db->reset_query();
    				//$this->db->insert("stock_transaction",$stockTrans);
                endif;
		    endforeach;
            exit;
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo 'RM Stock updated successfully. '.$i.' Recored updated';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        } 
    }*/
    
    /*** Created By Jp@10-09-2022 Update current stock Migration/toolStockUpdateOP***/
    public function toolStockUpdateOP(){
        try{
            $this->db->trans_begin();

            $this->db->reset_query();
			$this->db->where('qty > ',0);
            $result = $this->db->get('tool_stock')->result();
			//print_r($this->db->last_query());exit;
            
			$i=0;
			foreach($result as $row):
			    
                /*$this->db->reset_query();
			    $this->db->where('is_delete',0);
			    $this->db->where('item_type',2);
                $this->db->where('item_code',trim($row->item_name));
                $itemData = $this->db->get('item_master')->row();
                
                if(!empty($itemData)):
                     $this->db->reset_query();
                     $this->db->where('id',$row->id);
                     $this->db->update('tool_stock',['item_id'=> $itemData->id]);
                 endif;*/
                
                /*$this->db->reset_query();
                if($row->qty != 0):
                    $stockTrans=array();
    				$stockTrans = [
    					'location_id' => $row->location_id,
    					'batch_no' => 'OS/01052023',
    					'trans_type' => 1,
    					'item_id' => $row->item_id,
    					'qty' => $row->qty,
    					'remark' => 'STOCK_OP_BY_NYN',
    					'ref_type' => -1,
    					'ref_date' => date('Y-m-d')
    				];	$i++;
    				print_r($stockTrans);print_r('<hr>');
    				$this->db->insert("stock_transaction",$stockTrans); 
    				
    				$this->db->reset_query();
    				$this->db->where('id',$row->item_id);
                    $this->db->set('qty',"qty + ".$row->qty,false);
                    $this->db->set('opening_qty',"opening_qty + ".$row->qty,false);
                    $this->db->update('item_master');
                endif;*/
			endforeach;
            exit;
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo 'RM Stock updated successfully. '.$i.' Recored updated';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        } 
    }
    
    
    /* NYN Migration/migrateInstrument */
    /*public function migrateInstrument(){
        try{
            $this->db->trans_begin();
            
            //$this->db->where('item_id != ',0);
            $itemData = $this->db->get('instrument')->result();
            
            $i=1;
            foreach($itemData as $row):
                $ranage = '';
                if(!empty($row->gauge_type) && $row->gauge_type == 3){ $ranage = explode('~',$row->range); }
                $range = (!empty($row->gauge_type) && $row->gauge_type == 3)? $ranage[0].'-'.$ranage[1] : $row->range;
                $updateData = [
                    'id' => NULL,
                    'unit_id'=>27,
                    'item_code'=>trim($row->cat_code).'-'.trim($row->store_id),
                    'batch_stock'=>2,
                    'category_id'=>$row->category_id,
                    'gauge_type'=>$row->gauge_type,
                    'instrument_range'=> $range,
                    'least_count'=>$row->least_count,
                    'cal_required'=>'Yes',
                    'cal_freq'=>$row->cal_freq,
                    'cal_reminder'=>$row->reminder_day,
                    'store_id'=>trim($row->store_id),
                    'description' => $row->description,
                    'item_type' => 6,
                    'full_name'=> trim($row->cat_code).'-'.trim($row->store_id).' '.$row->description.' '.$range,
                    'item_name'=> trim($row->cat_code).'-'.trim($row->store_id).' '.$row->description.' '.$range
                ];
                $this->db->insert('item_master',$updateData);
                //print_r($updateData);
                $i++;
            endforeach;
            //exit;
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo "Instrument Name Migration Success.".$i;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }*/
    
    /* NYN Migration/migrateGaugeData */
    public function migrateGaugeData(){
        try{
            $this->db->trans_begin();
            
            $this->db->reset_query();
            $itemData = $this->db->get('qc_instruments')->result();
            
            $i=1;
            foreach($itemData as $row):
                $next_cal_date = date('Y-m-d', strtotime($row->last_cal_date . "+".$row->cal_freq." months"));
            
                $updateData = [
                    'next_cal_date'=>$next_cal_date
                ];
            
                $this->db->reset_query();
				$this->db->where('id',$row->id);
                $this->db->update('qc_instruments',$updateData);
                //print_r($this->db->last_query()); print_r("<hr>");
                $i++;
            endforeach;
            exit;
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo "Instrument Category Migration Success.".$i;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    /* NYN Migration/migrateGagugeData */
    /*public function migrateGagugeData(){
        try{
            $this->db->trans_begin();
            
            $this->db->where('item_id != ',0);
            $itemData = $this->db->get('instrument')->result();
            
            $i=1;
            foreach($itemData as $row):
                
                $updateData = [
                    'id' => NULL,
                    'location_id'=>64,
                    'batch_no'=>trim($row->cat_code).'-'.trim($row->store_id).'-'.trim($row->sr_no),
                    'trans_type'=>1,
                    'item_id'=>$row->item_id,
                    'qty'=>1,
                    'ref_type'=>-1,
                    'ref_date'=>'2023-02-19',
                    'stock_type'=>'FRESH',
                    'stock_effect'=>1,
                    'remark'=>'MANUAL UPLOAD NYN'
                ];
                $this->db->insert('stock_transaction',$updateData);
                //print_r($this->db->last_query().'<br>');
                $i++;
            endforeach;
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Instrument Name Migration Success.".$i;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }*/
    
        
    /* NYN Migration/migrateMaterialGradeName */
    public function migrateMaterialGradeName(){
        try{
            $this->db->trans_begin();
            
            
            $itemData = $this->db->query("SELECT * FROM `item_master` where `item_master`.`is_delete` = 0 AND  `item_master`.`item_type` = 3")->result();
            
            $i=1;
            foreach($itemData as $row):
                
                
                $updateData = [
                    'material_grade'=>trim($row->material_grade)
                ];
                
                $this->db->where('id',$row->id);
                $this->db->update("item_master",$updateData);
                $i++;
            endforeach;
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Material Grade Migration Success.".$i;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    /* NYN Migration/migrateFullName */
    public function migrateFullName(){
        try{
            $this->db->trans_begin();
            
            $itemData = $this->db->query("SELECT `item_master`.*,`item_category`.`tool_type`,`item_category`.`category_name` FROM `item_master` LEFT JOIN `item_category` ON `item_category`.`id` = `item_master`.`category_id` WHERE `item_master`.`item_type` = 6")->result();
            
            $i=1;
            foreach($itemData as $row):
                $updateData = ['full_name'=>$row->tool_type.'-'.$row->item_code.'-'.$row->category_name.'-Range('.$row->instrument_range.')'];
                $this->db->where('id',$row->id);
                //$this->db->update("item_master",$updateData);
                $i++;
            endforeach;
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo "Instrument Name Migration Success.".$i;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    /* NYN Migration/migrateJWTrans */
    public function migrateJWTrans(){
        try{
            $this->db->trans_begin();
            
            $this->db->where('is_delete',0);
            $jwoData = $this->db->get('jobwork_order_trans')->result();
            
            foreach($jwoData as $row):
                $updateData = ['process_id'=>$row->process_id];
                $this->db->where('job_order_trans_id',$row->id);
                //$this->db->update("jobwork_transaction",$updateData);
            endforeach;
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo "JobWork Trans Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    public function defualtLedger(){
        $accounts = [
            ['name' => 'Sales Account', 'group_name' => 'Sales Account', 'group_code' => 'SA', 'system_code' => 'SALESACC'],
            
            ['name' => 'Sales Account GST', 'group_name' => 'Sales Account', 'group_code' => 'SA', 'system_code' => 'SALESGSTACC'],

            ['name' => 'Sales Account IGST', 'group_name' => 'Sales Account', 'group_code' => 'SA', 'system_code' => 'SALESIGSTACC'],

            ['name' => 'Sales Account Tax Free', 'group_name' => 'Sales Account', 'group_code' => 'SA', 'system_code' => 'SALESTFACC'],

            ['name' => 'Sales Account GST JOBWORK', 'group_name' => 'Sales Account', 'group_code' => 'SA', 'system_code' => 'SALESJOBGSTACC'],

            ['name' => 'Sales Account IGST JOBWORK', 'group_name' => 'Sales Account', 'group_code' => 'SA', 'system_code' => 'SALESJOBIGSTACC'],

            ['name' => 'Export GST', 'group_name' => 'Sales Account', 'group_code' => 'SA', 'system_code' => 'EXPORTGSTACC'],

            ['name' => 'Export Tax Free', 'group_name' => 'Sales Account', 'group_code' => 'SA', 'system_code' => 'EXPORTTFACC'],
            
            ['name' => 'CGST (O/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => 'CGSTOPACC'],
            
            ['name' => 'SGST (O/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => 'SGSTOPACC'],
            
            ['name' => 'IGST (O/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => 'IGSTOPACC'],
            
            ['name' => 'UTGST (O/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => 'UTGSTOPACC'],
            
            ['name' => 'CESS (O/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => ''],
            
            ['name' => 'TCS ON SALES', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => ''],
            
            ['name' => 'Purchase Account', 'group_name' => 'Purchase Account', 'group_code' => 'PA', 'system_code' => 'PURACC'],
            
            ['name' => 'Purchase Account GST', 'group_name' => 'Purchase Account', 'group_code' => 'PA', 'system_code' => 'PURGSTACC'],

            ['name' => 'Purchase Account IGST', 'group_name' => 'Purchase Account', 'group_code' => 'PA', 'system_code' => 'PURIGSTACC'],
            
            ['name' => 'Purchase Account Tax Free', 'group_name' => 'Purchase Account', 'group_code' => 'PA', 'system_code' => 'PURTFACC'],
            
            ['name' => 'Purchase Account GST JOBWORK', 'group_name' => 'Purchase Account', 'group_code' => 'PA', 'system_code' => 'PURJOBGSTACC'],

            ['name' => 'Purchase Account IGST JOBWORK', 'group_name' => 'Purchase Account', 'group_code' => 'PA', 'system_code' => 'PURJOBIGSTACC'],

            ['name' => 'Import GST', 'group_name' => 'Purchase Account', 'group_code' => 'PA', 'system_code' => 'IMPORTGSTACC'],

            ['name' => 'Import Tax Free', 'group_name' => 'Purchase Account', 'group_code' => 'PA', 'system_code' => 'IMPORTTFACC'],
            
            ['name' => 'CGST (I/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => 'CGSTIPACC'],
            
            ['name' => 'SGST (I/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => 'SGSTIPACC'],
            
            ['name' => 'IGST (I/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => 'IGSTIPACC'],
            
            ['name' => 'UTGST (I/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => 'UTGSTIPACC'],
            
            ['name' => 'CESS (I/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => ''],
            
            ['name' => 'TCS ON PURCHASE', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => ''],
            
            ['name' => 'TDS PAYABLE', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => ''],
            
            ['name' => 'TDS RECEIVABLE', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => ''],
            
            ['name' => 'GST PAYABLE', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => ''],
            
            ['name' => 'GST RECEIVABLE', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => ''],
            
            ['name' => 'ROUNDED OFF', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => 'ROFFACC'],
            
            ['name' => 'CASH ACCOUNT', 'group_name' => 'Cash-In-Hand', 'group_code' => 'CS', 'system_code' => 'CASHACC'],
            
            ['name' => 'ELECTRICITY EXP', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'OFFICE RENT EXP', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'GODOWN RENT EXP', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'TELEPHONE AND INTERNET CHARGES', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'PETROL EXP', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'SALES INCENTIVE', 'group_name' => 'Expenses (Direct)', 'group_code' => 'ED', 'system_code' => ''],
            
            ['name' => 'INTEREST PAID', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'INTEREST RECEIVED', 'group_name' => 'Income (Indirect)', 'group_code' => 'II', 'system_code' => ''],
            
            ['name' => 'SAVING BANK INTEREST', 'group_name' => 'Income (Indirect)', 'group_code' => 'II', 'system_code' => ''],
            
            ['name' => 'DISCOUNT RECEIVED', 'group_name' => 'Income (Indirect)', 'group_code' => 'II', 'system_code' => ''],
            
            ['name' => 'DISCOUNT PAID', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'SUSPENSE A/C', 'group_name' => 'Suspense A/C', 'group_code' => 'AS', 'system_code' => ''],
            
            ['name' => 'PROFESSIONAL FEES PAID', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'AUDIT FEE', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'ACCOUNTING CHARGES PAID', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'LEGAL FEE', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'SALARY', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'WAGES', 'group_name' => 'Expenses (Direct)', 'group_code' => 'ED', 'system_code' => ''],
            
            ['name' => 'FREIGHT CHARGES', 'group_name' => 'Expenses (Direct)', 'group_code' => 'ED', 'system_code' => ''],
            
            ['name' => 'PACKING AND FORWARDING CHARGES', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'REMUNERATION TO PARTNERS', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'TRANSPORTATION CHARGES', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'DEPRICIATION', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'PLANT AND MACHINERY', 'group_name' => 'Fixed Assets', 'group_code' => 'FA', 'system_code' => ''],
            
            ['name' => 'FURNITURE AND FIXTURES', 'group_name' => 'Fixed Assets', 'group_code' => 'FA', 'system_code' => ''],
            
            ['name' => 'FIXED DEPOSITS', 'group_name' => 'Deposits (Assets)', 'group_code' => 'DA', 'system_code' => ''],
            
            ['name' => 'RENT DEPOSITS', 'group_name' => 'Deposits (Assets)', 'group_code' => 'DA', 'system_code' => '']	
        ];

        try{
            $this->db->trans_begin();
            $accounts = (object) $accounts;
            foreach($accounts as $row):
                $row = (object) $row;
                $groupData = $this->db->where('group_code',$row->group_code)->get('group_master')->row();
                $ledgerData = [
                    'party_category' => 4,
                    'group_name' => $groupData->name,
                    'group_code' => $groupData->group_code,
                    'group_id' => $groupData->id,
                    'party_name' => $row->name,                    
                    'system_code' => $row->system_code
                ];

                $this->db->where('party_name',$row->name);
                $this->db->where('is_delete',0);
                $this->db->where('party_category',4);
                $checkLedger = $this->db->get('party_master');

                if($checkLedger->num_rows() > 0):
                    $id = $checkLedger->row()->id;
                    $this->db->where('id',$id);
                    $this->db->update('party_master',$ledgerData);
                else:
                    $this->db->insert('party_master',$ledgerData);
                endif;
            endforeach;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Defualt Ledger Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

    public function migratePartyAddress(){
        try{
            $this->db->trans_begin();
            
            $this->db->where('add_type',1);
            $this->db->where('is_delete',0);
            $addData = $this->db->get('address_detail')->result();
            
            foreach($addData as $row):
                $updateData = ['party_address'=>$row->party_address,'party_phone'=>$row->party_phone,'party_state_code'=>$row->party_state_code,'party_pincode'=>$row->party_pincode,
                'town_id'=>$row->town_id,'city_id'=>$row->city_id,'state_id'=>$row->state_id,'country_id'=>$row->country_id];
                print_r($updateData);print_r('<br>');
                $this->db->where('id',$row->party_id);
                //$this->db->update("party_master",$updateData);
            endforeach;
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Party Address Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    public function updateUserPermission(){
        try{
            $this->db->trans_begin();
            
            $this->db->where("emp_code NOT LIKE '10%'");
            $this->db->where('is_delete',0);
            $empData = $this->db->get('employee_master')->result();
            //print_r($this->db->last_query());print_r('<br>');
            foreach($empData as $row):
                $mainPermission = [
                    'emp_id' => $row->id,
                    'menu_id' => 2,
                    'is_read' => 1,
                    'created_by' => 1
                ];
				//print_r($mainPermission);print_r('<br>');
				//$this->db->insert('menu_permission',$mainPermission);
				$submenus = [9]; //$submenus = [9];
				foreach($submenus as $sub_menu_id):
					$subPermission = [
						'emp_id' => $row->id,
						'menu_id' => 2,
						'sub_menu_id' => $sub_menu_id,
                        'is_read' => 1,
                        'is_write' => 1,
                        'is_modify' => 1,
						'created_by' => 1
					];
					//print_r($subPermission);print_r('<br>');
					//$this->db->insert('sub_menu_permission',$subPermission);
				endforeach;
				//print_r('<hr>');
            endforeach;
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "User Permission Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    /*public function updateJobTransPrice(){
        try{
            $this->db->trans_begin();
            $this->db->reset_query();

            $this->db->select('id,job_order_id,item_id,process_id,com_qty');
            $this->db->where('is_delete',0);
            $this->db->where('id >=',1);
            $this->db->where('id <=',1000);
            $this->db->where('entry_type',1);
            $result = $this->db->get('jobwork_transaction')->result();

            $jobOrderIds = array();
            foreach($result as $row):
                $this->db->reset_query();

                $this->db->select('id,process_charge,scarp_rate_pcs,value_rate,igst');
                $this->db->where('is_delete',0);
                $this->db->where('order_id',$row->job_order_id);
                $this->db->where('item_id',$row->item_id);
                $this->db->where('process_id',$row->process_id);
                $jobOrderData = $this->db->get('jobwork_order_trans')->row();

                if(!empty($jobOrderData)):
                    $total_value = 0;$cgst_amount=0;$sgst_amount=0;$igst_amount=0;$net_amount=0;
                    $total_value = round($row->com_qty * $jobOrderData->value_rate,3);
                    $igst_amount = round((($total_value * $jobOrderData->igst) / 100),3);
                    $cgst_amount = round(($igst_amount / 2),3);
                    $sgst_amount = round(($igst_amount / 2),3);
                    $net_amount = round(($total_value + $igst_amount),3);

                    $this->db->where('id',$row->id);
                    $this->db->update('jobwork_transaction',['price'=>$jobOrderData->process_charge,'scarp_rate_pcs'=>$jobOrderData->scarp_rate_pcs,'job_order_trans_id'=>$jobOrderData->id,'value_rate'=>$jobOrderData->value_rate,'total_value'=>$total_value,'cgst_amount'=>$cgst_amount,'sgst_amount'=>$sgst_amount,'igst_amount'=>$igst_amount,'net_amount'=>$net_amount]);
                else:
                    $jobOrderIds[] = $row->job_order_id;
                endif;
            endforeach;
            
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "JobWork transaction Migration Success. Job Order Ids Not Found : ".implode(",",$jobOrderIds);
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }*/
    
    public function itemOpeningStock(){
        try{
            $this->db->trans_begin();
            $this->db->reset_query();
            
            $this->db->where('is_delete',0);
            $this->db->where('item_type',2);
            $itemData = $this->db->get('item_master')->result();
            
            $stockTransData = array();
            foreach($itemData as $row):
                $qty = 500;
                $stockTransData = [
                    'location_id' => 1,
                    'batch_no' => "General Batch",
                    'trans_type' => 1,
                    'item_id' => $row->id,
                    'qty' => $qty,
                    'ref_type' => -1,
                    'ref_date' => date("Y-m-d"),
                    'stock_type' => "FRESH",
                    'stock_effect' => 1,
                    'ref_batch' => "STM500"
                ];
                //print_r($stockTransData);
                //$this->db->insert('stock_transaction',$stockTransData);
                
                //$this->db->where('id',$row->id);
                //$this->db->set('qty',"qty + ".$qty,false);
                //$this->db->set('opening_qty',"opening_qty + ".$qty,false);
                //$this->db->update('item_master');
            endforeach; //exit;
            
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo "Opening Stock Saved Successfully.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    public function removeitemOpeningStockRM(){
        try{
            $this->db->trans_begin();
            $this->db->reset_query();
            
            $this->db->select('stock_transaction.id,item_master.item_type');
            $this->db->join('item_master','item_master.id = stock_transaction.item_id');
            $this->db->where('stock_transaction.is_delete',0);
            $this->db->where('item_master.item_type',3);
            $this->db->where('stock_transaction.ref_type',-1);
            $stockData = $this->db->get('stock_transaction')->result();
            
            $stockTransData = array();
            foreach($stockData as $row):
                
                print_r($row->id);print_r(',');
                $this->db->reset_query();
                //$this->db->where('id',$row->id);
                //$this->db->set('is_delete',1);
                $this->db->set('ref_batch',"JP_RM_REMOVE");
                //$this->db->update('stock_transaction');
            endforeach; exit;
            
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo "Opening Stock Saved Successfully.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    public function migrateGstfromHsn(){
        try{
            $this->db->trans_begin();
            $this->db->reset_query();
            
            $this->db->select('hsn_master.*,item_master.id,item_master.hsn_code as itm_hsn,item_master.gst_per');
            $this->db->join('hsn_master','item_master.hsn_code = hsn_master.hsn','left');
            $this->db->where('item_master.is_delete',0);
            $itemData = $this->db->get('item_master')->result();
            
            $stockTransData = array();
            foreach($itemData as $row):
                $this->db->reset_query();
                if(!empty($row->itm_hsn) AND floatVal($row->igst) > 0)
                {
                    print_r($row->id.'@@'.$row->itm_hsn.' => '.$row->gst_per.' = '.$row->igst.'<br>');
                    //$this->db->where('id',$row->id);
                    //$this->db->update('item_master',['gst_per'=>$row->igst]);
                }
            endforeach; 
            exit;
            
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo "HSN Migration Successfully.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    public function migrateGstfromHsnTopo(){
        try{
            $this->db->trans_begin();
            $this->db->reset_query();
            
            $this->db->select('purchase_order_trans.*,item_master.gst_per');
            $this->db->join('item_master','item_master.id = purchase_order_trans.item_id','left');
            $this->db->where('purchase_order_trans.is_delete',0);
            $itemData = $this->db->get('purchase_order_trans')->result();
            
            $stockTransData = array();
            foreach($itemData as $row):
                if(!empty($row->gst_per))
                {
                    $updateData['igst'] = $row->gst_per;
                    $updateData['cgst'] = round(($row->gst_per/2),2);
                    $updateData['sgst'] = round(($row->gst_per/2),2);
                    print_r($updateData);print_r('<br>');
                    //$this->db->reset_query();
                    //$this->db->where('id',$row->id);
                    //$this->db->update('purchase_order_trans',$updateData);
                }
            endforeach; 
            exit;
            
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo "HSN Migration Successfully.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    /* public function migrateTempStock(){
        try{
            $this->db->trans_begin();

            $this->db->reset_query();

            $result = $this->db->get('temp_stock')->result();
            $i=1;
            foreach($result as $row):
                $this->db->reset_query();

                $unix_date = ($row->bill_date - 25569) * 86400;
                $excel_date = 25569 + ($unix_date / 86400);
                $unix_date = ($excel_date - 25569) * 86400;
                $inv_date = gmdate("Y-m-d", $unix_date);

                //print_r($i++);print_r("**");print_r($inv_date);print_r("<hr>");
                $this->db->where('id',$row->id);
                //$this->db->update('temp_stock',['inv_date'=>$inv_date]);
                $i++;
            endforeach;
            //exit;

            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo $i." Temp Stock Date Migration Successfully.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    } */

    /* public function dispatchMaterilFifoMethod(){
        try{
            $this->db->trans_begin();
            $this->db->reset_query();

            $this->db->where('type',2);
            //$this->db->where('item_code','AA103');
            $this->db->order_by('inv_date,bill_no',"ASC");
            $result = $this->db->get('temp_stock')->result();

            foreach($result as $row):
                $this->db->reset_query();

                $batchData = $this->getBatchStock($row->item_code);
                $pendingInvQty = 0;
                $pendingInvQty = $row->qty;

                foreach($batchData as $batch):
                    $this->db->reset_query();

                    $stockQty = 0;
                    $stockQty = ($batch->qty - $batch->dispatch_qty);
                    
                    $issueQty = 0;
                    if($pendingInvQty <= $stockQty):
                        $issueQty = $pendingInvQty;
                    else:
                        $issueQty = $stockQty;
                    endif;  

                    $invData = [
                        'type' => 3,
                        'bill_date' => $row->inv_date,
                        'bill_no' => $row->bill_no,
                        'party_name' => $row->party_name,
                        'item_code' => $row->item_code,
                        'item_name' => $row->item_name,
                        'batch_no' => $batch->received_no,
                        'qty' => $issueQty,
                    ];
                    $this->db->insert('temp_dispatch',$invData);

                    $this->db->set('dispatch_qty','dispatch_qty + '.$issueQty,false);
                    $this->db->where('id',$batch->id);
                    $this->db->update('temp_stock');

                    $pendingInvQty -= $issueQty;
                    if($pendingInvQty <= 0): break; endif;
                endforeach;
            endforeach;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Dispatch Material Migration Successfully.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    } */

    /* public function getBatchStock($item_code){
        $this->db->where('type',1);
        $this->db->where("(qty - dispatch_qty) >",0);
        $this->db->where('item_code',$item_code);
        //$this->db->order_by('id',"ASC");        
        $this->db->order_by('inv_date,received_no',"ASC");
        $stockData = $this->db->get("temp_stock")->result();
        return $stockData;
    } */
	
	/*** By : JP @30.12.2022 ***/
	public function migrateOldShiftLog(){
        try{
            $this->db->trans_begin();
            $this->db->reset_query();
            $cmonth = date('m',strtotime(date('2022-12-01')));
            $cyear = date('Y',strtotime(date('2022-12-01')));
            $this->db->where('is_delete',0);
            $this->db->where('attendance_date >= ',date('2022-12-01'));
            $this->db->where('MONTH(attendance_date)',$cmonth);
            $osLog = $this->db->get('attendance_shiftlog')->result();
			
			if(!empty($osLog))
			{
				foreach($osLog as $osrow)
				{
					$punchData = Array();$empList = Array();
					if(!empty($osrow)):
						$punchData = json_decode($osrow->punchdata);
					endif;
					if(!empty($punchData))
					{
						foreach($punchData as $row)
						{
							$row->attendance_date = $osrow->attendance_date;
							if(!empty($row->shift_id))
							{
								$this->db->reset_query();
								$this->db->where('id',$row->shift_id);
								$shiftData = $this->db->get('shift_master')->row();
								
								$shiftData->latest_id = (!empty($shiftData->latest_id)) ? $shiftData->latest_id : 0;
								$day = date('d',strtotime($osrow->attendance_date));
				
								$prevData=Array();$empShiftLog = Array();
								$this->db->where('MONTH(month)',$cmonth);
								$this->db->where('YEAR(month)',$cyear);
								$this->db->where('emp_id',$row->emp_id);
								$this->db->where('is_delete',0);
								$prevData = $this->db->get('emp_shiftlog')->row();
								
								for($fkey=intVal($day);$fkey<=intVal(date('t',strtotime(date($cyear.'-'.$cmonth.'-01'))));$fkey++)
								{
									$empShiftLog['d'.$fkey]=$shiftData->latest_id;
								}
								$empShiftLog['created_by']=1;
								$empShiftLog['created_at']=date('Y-m-d H:i:s');
								
								/*
								$this->db->reset_query();
								if(empty($prevData)):
									$empShiftLog['month']=date($cyear.'-'.$cmonth.'-01');$empShiftLog['emp_id']=$row->emp_id;
									$this->db->insert('emp_shiftlog',$empShiftLog);//$inserted++;
								else:
									$this->db->where('id',$prevData->id);
									$this->db->update('emp_shiftlog',$empShiftLog);//$updated++;
								endif;*/
							}
						}
					}
				}
			}
			
            exit;
            
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo "Shift Migration Successfully.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
	
	/*** By : JP @30.12.2022 ***/
	public function migrateOldAttendanceLog(){
        try{
            $this->db->trans_begin();
            $this->db->reset_query();
			$cdate = date('2022-12-01');
            $cmonth = date('m',strtotime($cdate));
            $cyear = date('Y',strtotime($cdate));
            $this->db->where('is_delete',0);
            $this->db->where('punch_date >= ',date('2022-12-24'));
            $this->db->where('punch_date <= ',date('2022-12-30'));
            //$this->db->where('MONTH(punch_date)',$cmonth);
            $oaLog = $this->db->get('device_punches')->result();
			$i=0;
			if(!empty($oaLog))
			{
				foreach($oaLog as $oarow)
				{
					$punchData = Array();$empList = Array();
					if(!empty($oarow)):
						$punchData = json_decode($oarow->punch_data);
					endif;
					if(!empty($punchData))
					{
						foreach ($punchData as $punch) 
						{
							$this->db->reset_query();
							$this->db->where('punch_type',1);
							$this->db->where('device_id',1);
							$this->db->where('punch_date',date('Y-m-d H:i:s', strtotime(strtr($punch->PunchDate, '/', '-'))));
							$this->db->where('emp_code',$punch->Empcode);
							$oldData = $this->db->get('attendance_log')->row();

							if (empty($oldData)) :
								$logData = array();
								$this->db->reset_query();
								$this->db->select('id');
								$this->db->where('emp_code',$punch->Empcode);
								$empData = $this->db->get('employee_master')->row();

								if (!empty($empData)) {
									$logData['id'] = "";
									$logData['punch_type'] = 1;
									$logData['device_id'] = 1;
									$logData['punch_date'] = date('Y-m-d H:i:s', strtotime(strtr($punch->PunchDate, '/', '-')));
									$logData['emp_id'] = $empData->id;
									$logData['emp_code'] = $punch->Empcode;
									$logData['created_at'] = date('Y-m-d H:i:s');
									$logData['created_by'] = 1;
									//print_r($logData);print_r('<br>');
									/*
									$this->db->reset_query();
									$this->db->insert('attendance_log',$logData);*/
									$i++;
								}
							endif;
						}
					}
				}
				//print_r('<hr>');
			}
			
            exit;
            
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo $i." Record (Attendance Log) Migration Successfully.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
	
	/*** By : JP @30.12.2022 ***/
	public function migrateOldManualAttendanceLog(){
        try{
            $this->db->trans_begin();
            $this->db->reset_query();
			
            $this->db->select('emp_attendance.*,employee_master.emp_code');
            $this->db->where('emp_attendance.is_delete',0);
            $this->db->where('emp_attendance.source',2);
            $this->db->where('attendance_date >= ',date('2022-10-01'));
            $this->db->join('employee_master','employee_master.id = emp_attendance.emp_id','left');
            $maLog = $this->db->get('emp_attendance')->result();
			$i=0;
			if(!empty($maLog))
			{
				foreach($maLog as $row)
				{
					$logData = array();
					$logData['id'] = "";
					$logData['punch_type'] = $row->source;
					$logData['device_id'] = 1;
					$logData['punch_date'] = $row->punch_in;
					$logData['emp_id'] = $row->emp_id;
					$logData['emp_code'] = $row->emp_code;
					$logData['remark'] = $row->remark;
					$logData['created_at'] = date('Y-m-d H:i:s');
					$logData['created_by'] = 1;
					//print_r($logData);print_r('<br>');
					/*
					$this->db->reset_query();
					$this->db->insert('attendance_log',$logData);*/
					$i++;
				}
				//print_r('<hr>');
			}
			
            //exit;
            
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo $i." Record (Attendance Log) Migration Successfully.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
	/*** By : JP @30.12.2022 ***/
	public function migrateOldExtraPunchLog(){
        try{
            $this->db->trans_begin();
            $this->db->reset_query();
			
            $this->db->select('emp_attendance.*,employee_master.emp_code');
            $this->db->where('emp_attendance.is_delete',0);
            $this->db->where('emp_attendance.source',3);
            $this->db->where('attendance_date >= ',date('2022-10-01'));
            $this->db->join('employee_master','employee_master.id = emp_attendance.emp_id','left');
            $maLog = $this->db->get('emp_attendance')->result();
			$i=0;
			if(!empty($maLog))
			{
				foreach($maLog as $row)
				{
					$logData = array();
					$logData['id'] = "";
					$logData['punch_type'] = $row->source;
					$logData['device_id'] = 1;
					$logData['punch_date'] = date('Y-m-d H:i:s',strtotime($row->attendance_date.' 00:00:00'));
					$logData['emp_id'] = $row->emp_id;
					$logData['emp_code'] = $row->emp_code;
					$logData['xtype'] = $row->xtype;
					$logData['ex_hours'] = $row->ex_hours;
					$logData['ex_mins'] = $row->ex_mins;
					$logData['remark'] = $row->remark;
					$logData['created_at'] = date('Y-m-d H:i:s');
					$logData['created_by'] = 1;
					//print_r($logData);print_r('<br>');
					/*
					$this->db->reset_query();
					$this->db->insert('attendance_log',$logData);*/
					$i++;
				}
				//print_r('<hr>');
			}
			
            exit;
            
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo $i." Record (Attendance Log) Migration Successfully.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    public function migrateAttendanceLogs(){
        try{
            $this->db->trans_begin();
            $this->db->reset_query();
            
            $this->db->where('is_delete',1);
            $this->db->where('punch_type',1);
            $result = $this->db->get('attendance_log')->result();
            
            foreach($result as $row):
                $this->db->reset_query();
                
                $this->db->where('is_delete',0);
                $this->db->where('punch_type',$row->punch_type);
                $this->db->where('device_id',$row->device_id);
                $this->db->where('punch_date',$row->punch_date);
                $this->db->where('emp_id',$row->emp_id);
                $this->db->where('emp_code',$row->emp_code);
                $empAttendanceLog = $this->db->get('attendance_log')->result();
                
                print_r($empAttendanceLog);
                print_r("<hr>");
            endforeach;
            exit;
            
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo $i." Record (Attendance Log) Migration Successfully.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    public function migrateGateEntryData(){
        try{
            $this->db->trans_begin();
            
            $this->db->reset_query();
            $this->db->select('id,qty');
            $this->db->where('trans_type',1);
            $this->db->where('is_delete',0);
            $result = $this->db->get('mir')->result();
            
            foreach($result as $row):
            
                $this->db->reset_query();
                $this->db->select('SUM(qty) as total_qty');
                $this->db->where('trans_type',2);
                $this->db->where('ref_id',$row->id);
                $this->db->where('is_delete',0);
                $giData = $this->db->get('mir')->row();
                
                $geData = array();
                $geData['rec_qty'] = (!empty($giData->total_qty))?$giData->total_qty:0;
                $geData['trans_status'] = ($geData['rec_qty'] >= $row->qty)?1:0;
                
                /*print_r($geData);
                print_r('<hr>');*/
                
                $this->db->where('id',$row->id);
                //$this->db->update('mir',$geData);
                
            endforeach;
            
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo "Record (Gate Entry) Migration Successfully.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

	// Add Opening Stock To Packing Material
    public function addOpeningStockPACK(){
        try{
            $this->db->trans_begin();
            $this->db->reset_query();
            
            $this->db->where('is_delete',0);
            $this->db->where('item_type',9);
            $itemData = $this->db->get('item_master')->result();
            
            $stockTransData = array();$i=0;
            foreach($itemData as $row):
                $qty = 100000;
                $stockTransData = [
                    'location_id' => 46,
                    'batch_no' => "General Batch",
                    'trans_type' => 1,
                    'item_id' => $row->id,
                    'qty' => $qty,
                    'ref_type' => -1,
                    'ref_date' => date("Y-m-d"),
                    'stock_type' => "FRESH",
                    'stock_effect' => 1,
                    'ref_batch' => "OS07022023"
                ];
                print_r($stockTransData);$i++;
                //$this->db->insert('stock_transaction',$stockTransData);
                
                //$this->db->where('id',$row->id);
                //$this->db->set('qty',"qty + ".$qty,false);
                //$this->db->set('opening_qty',"opening_qty + ".$qty,false);
                //$this->db->update('item_master');
            endforeach; exit;
            
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo $i." Record Inserted Successfully.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

	// Update Emp Policy of Employee
    public function updateEmpPolicy(){
        try{
            $this->db->trans_begin();
            $this->db->reset_query();
            
            $this->db->where('is_delete',0);
            $policyData = $this->db->get('attendance_policy')->result();
            
            $i=0;
            foreach($policyData as $row):
                $updatePolicy= Array();
                if($row->policy_type==1){$updatePolicy['lt_policy'] = $row->id;}
                if($row->policy_type==2){$updatePolicy['eo_policy'] = $row->id;}
                if($row->policy_type==3){$updatePolicy['shl_policy'] = $row->id;}
                
                print_r($updatePolicy);print_r('<br>');
                
                $this->db->reset_query();
                $this->db->where('emp_category',$row->emp_category);
                //$this->db->update('employee_master',$updatePolicy);
            endforeach; exit;
            
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo " Record Updated Successfully.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

    public function migrateNPDResponsibility(){
        try{
            $this->db->trans_begin();
            $this->db->reset_query();
            $this->db->where('is_delete',0);
            $this->db->where('entry_type',3);
            $pfcData = $this->db->get('pfc_trans')->result();
            
            $i=0;
            foreach($pfcData as $row):
                $this->db->reset_query();
                $this->db->where('is_delete',0);
                $this->db->where('fmea_type',3);
                $this->db->where('ref_id',$row->id);
                $cpData = $this->db->get('qc_fmea')->result();
                
                $this->db->reset_query();
                $this->db->where('id',$cpData[0]->id);
                $this->db->update('qc_fmea',['process_detection'=>'OPR']);
                $i++;
                $this->db->reset_query();
                $this->db->where('id',$cpData[1]->id);
                $this->db->update('qc_fmea',['process_detection'=>'INSP']);
                $i++;
            endforeach; 
            // exit;
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo $i." Record Migrated Successfully.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    /*** Job HEAT Data - 04-10-2023 ***/
	public function migrateJobHeatData(){
        try{
            $this->db->trans_begin();
            $this->db->reset_query();
			
            $this->db->select('job_approval.*,job_card.job_number');
            $this->db->where('job_approval.is_delete',0);
            $this->db->where('job_approval.ok_qty > 0');
            $this->db->join('job_card','job_card.id = job_approval.job_card_id','left');
            $jobData = $this->db->get('job_approval')->result();
			$i=0;
			if(!empty($jobData))
			{
				foreach($jobData as $row)
				{
					$heatData = [
                        'id'=>'',
                        'job_card_id' => $row->job_card_id,
                        'job_approval_id'=>$row->id,
                        'process_id'=>$row->in_process_id,
                        'in_qty'=>($row->ok_qty+$row->total_rework_qty+$row->total_rejection_qty+$row->total_hold_qty),
                        'ok_qty'=>$row->ok_qty,
                        'out_qty'=>$row->total_out_qty,
                        'rej_rw_qty'=>($row->total_rework_qty+$row->total_rejection_qty+$row->total_hold_qty),
                        'batch_no'=>$row->job_number,
                    ];
                    $this->db->reset_query();
                    $this->db->insert('job_heat_trans',$heatData);

                    $this->db->reset_query();
                    $this->db->where('job_approval_id',$row->id);
                    $this->db->update('job_transaction',['batch_no'=>$row->job_number]);
                    // print_r($this->db->last_query());
                    // print_r("<hr>");
					$i++;
				}
				
			}
			
            exit;
            
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo $i." Record  Migration Successfully.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    /*** By : NYN @13.12.2023 /Migration/migratePayroll ***/
    public function migratePayroll(){
        try{
            $this->db->trans_begin();
            
            $this->db->reset_query();
            $pdata = $this->db->get('payroll_transaction')->result();
            
            $i=1;
            foreach($pdata as $row):
                $this->db->reset_query();
                $this->db->select('id');
                $this->db->where('emp_code',$row->emp_code);
                $empData = $this->db->get('employee_master')->row();
                
                if(!empty($empData->id)):
                    $this->db->reset_query();
                    $updateData = ['emp_id'=>$empData->id];
                    $this->db->where('id',$row->id);
                    //$this->db->update("payroll_transaction",$updateData);
                    $i++;
                endif;
            endforeach;
            exit;
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo "Instrument Name Migration Success.".$i;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    /*** By : NYN @03.04.2024 /Migration/migrateItemIdData ***/
    public function migrateItemIdData(){
        try{
            $this->db->trans_begin();
            
            $this->db->reset_query();
            $this->db->where('item_id',0);
            $result = $this->db->get('item_stock_trans')->result();
            
            foreach($result as $row):
                $this->db->reset_query();
                $this->db->select('id');
                $this->db->where('item_name',TRIM($row->item_name));
                $this->db->where('item_type',3);
                $this->db->where('is_delete',0);
                $rmData = $this->db->get('item_master')->row();
                
                $itmData = array();
                $itmData['item_id'] = (!empty($rmData->id))?$rmData->id:0;
                
                $this->db->reset_query();
                $this->db->where('id',$row->id);
                $this->db->update('item_stock_trans',$itmData);
            endforeach;
            //exit;
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Item Stock Trans Migration Successfully.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    /*** By : NYN @07.04.2024 /Migration/clearItemStock ***/
    public function clearItemStock(){
        try{
            $this->db->trans_begin(); 

            $this->db->select('stock_transaction.location_id,stock_transaction.batch_no,stock_transaction.item_id,SUM(stock_transaction.qty) as stock_qty, stock_transaction.stock_type, stock_transaction.stock_effect');
            $this->db->join('item_master','item_master.id = stock_transaction.item_id','left');
			$this->db->where('item_master.is_delete',0);
			$this->db->where('item_master.item_type',3);
			$this->db->where('stock_transaction.is_delete',0);
            $this->db->group_by("stock_transaction.location_id,stock_transaction.batch_no,stock_transaction.item_id,stock_transaction.stock_type, stock_transaction.stock_effect");
            $this->db->having("SUM(stock_transaction.qty) <> 0");
            $result = $this->db->get('stock_transaction')->result();

			$i=0;
            foreach($result as $row):
				$stockTrans=array();
				$trans_type = 0; $stock_qty=0;
				
				if($row->stock_qty > 0){$trans_type = 2;$stock_qty = ($row->stock_qty * -1);}
    			if($row->stock_qty < 0){$trans_type = 1;$stock_qty = abs($row->stock_qty);}
					
				$stockTrans = [
					'location_id' => $row->location_id,
					'batch_no' => $row->batch_no,
					'trans_type' => $trans_type,
					'item_id' => $row->item_id,
					'qty' => $stock_qty,
					'ref_batch' => 'NBT STOCK UPDATE',
					'ref_type' => 6,
					'ref_date' => date('Y-m-d'),
					'stock_type' => $row->stock_type,
					'stock_effect' => $row->stock_effect
				];	$i++;
			
                print_r($stockTrans);print_r("<hr>");
                //$this->db->insert("stock_transaction",$stockTrans);
            endforeach;
			exit;
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo "Item Stock removed Successfully. ".$i;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    /*** By : NYN @07.04.2024 /Migration/migrationStockMirToStockTrans ***/
    public function migrationStockMirToStockTrans(){
        try{
            $this->db->trans_begin(); 

            $this->db->reset_query();
            $result = $this->db->get('item_stock_trans')->result();

			$i=0;
            foreach($result as $row):
                
                $transData = [
                    'type' => 1,
                    'location_id'=>47,
                    'item_id'=>$row->item_id,
                    'qty'=>$row->qty,
                    'batch_no'=>$row->batch_no,
                    'heat_no'=>$row->heat_no,
                    'mill_heat_no'=>$row->heat_no,
                    'accepted_by'=>1,
                    'accepted_at'=>'2024-0407 00:00:00',
                    'trans_status'=>3
                ];
                $this->db->reset_query();
                print_r($transData);print_r("<br>");
                //$this->db->insert('mir_transaction',$transData);
                $transSave = $this->db->insert_id();
                
				$stockTrans=array();
				$stockTrans = [
					'location_id' => 47,
					'batch_no' => $row->batch_no,
					'ref_no' => $row->batch_no,
					'trans_ref_id' => $transSave,
					'trans_type' => 1,
					'item_id' => $row->item_id,
					'qty' => $row->qty,
					'ref_batch' => 'NBT STOCK UPDATE',
					'ref_type' => 1,
					'ref_date' => date('Y-m-d'),
					'stock_type' => 'FRESH',
					'stock_effect' => 1
				];	$i++;
			
                $this->db->reset_query();
                print_r($stockTrans);print_r("<hr>");
                //$this->db->insert("stock_transaction",$stockTrans);
            endforeach;
			exit;
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo "Item Stock removed Successfully. ".$i;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
}
?>