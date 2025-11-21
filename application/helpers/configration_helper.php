<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/* get Pagewise Table Header */
function getConfigDtHeader($page){
    /* terms header */
    $data['terms'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['terms'][] = ["name"=>"#","style"=>"width:5%;"]; 
    $data['terms'][] = ["name"=>"Title"];
    $data['terms'][] = ["name"=>"Type"];
    $data['terms'][] = ["name"=>"Conditions"];

    /* Shift Header */
    $data['shift'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['shift'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['shift'][] = ["name"=>"Shift Name"];
	$data['shift'][] = ["name"=>"Start Time"];
	$data['shift'][] = ["name"=>"End Time"];
	$data['shift'][] = ["name"=>"Production Time"];
	$data['shift'][] = ["name"=>"Lunch Time"];
	$data['shift'][] = ["name"=>"Shift Hour"];

    /* Holidays Header */
    $data['holidays'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['holidays'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['holidays'][] = ["name"=>"Holiday Date"];
    $data['holidays'][] = ["name"=>"Holiday Type"];
    $data['holidays'][] = ["name"=>"Title"];

    /* category Header */
    $data['category'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['category'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['category'][] = ["name"=>"Category Name"];
    $data['category'][] = ["name"=>"Over Time"];

    /* Attendance Policy Header */
    $data['attendancePolicy'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['attendancePolicy'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['attendancePolicy'][] = ["name"=>"Policy Name"];
	$data['attendancePolicy'][] = ["name"=>"Policy Type"];
    $data['attendancePolicy'][] = ["name"=>"Max./Day"];
    $data['attendancePolicy'][] = ["name"=>"Max./Month"];
    $data['attendancePolicy'][] = ["name"=>"Penalty"];
    $data['attendancePolicy'][] = ["name"=>"Penalty Hours"];

     /* Master Detail header */
     $data['masterDetail'][] = ["name"=>"Action","style"=>"width:5%;"];
     $data['masterDetail'][] = ["name"=>"#","style"=>"width:5%;"]; 
     $data['masterDetail'][] = ["name"=>"Title"];
     $data['masterDetail'][] = ["name"=>"Type"];
     $data['masterDetail'][] = ["name"=>"Remark"];
     
     /* HSN Master header */
     $data['hsnMaster'][] = ["name"=>"Action","style"=>"width:5%;"];
     $data['hsnMaster'][] = ["name"=>"#","style"=>"width:5%;"]; 
     $data['hsnMaster'][] = ["name"=>"HSN"];
     $data['hsnMaster'][] = ["name"=>"CGST"];
     $data['hsnMaster'][] = ["name"=>"SGST"];
     $data['hsnMaster'][] = ["name"=>"IGST"];
     $data['hsnMaster'][] = ["name"=>"Description"];

    /* Expense Master Header */
    $data['expenseMaster'][] = ["name" => "Action", "style" => "width:5%;"];
    $data['expenseMaster'][] = ["name" => "#", "style" => "width:5%;"];
    $data['expenseMaster'][] = ["name" => "Exp. Name"];
    $data['expenseMaster'][] = ["name" => "Entry Name"];
    $data['expenseMaster'][] = ["name" => "Sequence"];
    $data['expenseMaster'][] = ["name" => "Calcu. Type"];
    $data['expenseMaster'][] = ["name" => "Ledger Name"];
    $data['expenseMaster'][] = ["name" => "Is Active"];
    $data['expenseMaster'][] = ["name" => "Add/Deduct"];

    /* Tax Master Header */
    $data['taxMaster'][] = ["name" => "Action", "style" => "width:5%;"];
    $data['taxMaster'][] = ["name" => "#", "style" => "width:5%;"];
    $data['taxMaster'][] = ["name" => "Tax Name"];
    $data['taxMaster'][] = ["name" => "Tax Type"];
    $data['taxMaster'][] = ["name" => "Calcu. Type"];
    $data['taxMaster'][] = ["name" => "Ledger Name"];
    $data['taxMaster'][] = ["name" => "Is Active"];
    $data['taxMaster'][] = ["name" => "Add/Deduct"];
    
    /* Transport Header*/
    $data['transport'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['transport'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['transport'][] = ["name"=>"Transport Name"];
    $data['transport'][] = ["name"=>"Transport ID"];
    $data['transport'][] = ["name"=>"Address"];

    /* Banking Header*/
    $data['banking'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['banking'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['banking'][] = ["name"=>"Bank Name"];
    $data['banking'][] = ["name"=>"Branch Name"];
    $data['banking'][] = ["name"=>"IFSC Code"];
    $data['banking'][] = ["name"=>"Address"];
    
    /* Material Grade header */
    $data['materialGrade'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['materialGrade'][] = ["name"=>"#","style"=>"width:5%;"]; 
    $data['materialGrade'][] = ["name"=>"Material Grade"];
    $data['materialGrade'][] = ["name"=>"Standard"];
    $data['materialGrade'][] = ["name"=>"Scrap Group"];
    $data['materialGrade'][] = ["name"=>"Colour Code"];
    
    /* Vehicle Type header */
    $data['vehicleType'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['vehicleType'][] = ["name"=>"#","style"=>"width:5%;"]; 
    $data['vehicleType'][] = ["name"=>"Vehicle Type"];
    $data['vehicleType'][] = ["name"=>"Remark"];
    
    /* Measurement Technique header */
    $data['measurementTechnique'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['measurementTechnique'][] = ["name"=>"#","style"=>"width:5%;"]; 
    $data['measurementTechnique'][] = ["name"=>"Measurement Technique"];
    $data['measurementTechnique'][] = ["name"=>"Remark"];
    
    /* RTS Question Heading header */
    $data['rtsQuestionHeading'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['rtsQuestionHeading'][] = ["name"=>"#","style"=>"width:5%;"]; 
    $data['rtsQuestionHeading'][] = ["name"=>"Question Heading"];
    
    /* RTS Question header */
    $data['rtsQuestion'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['rtsQuestion'][] = ["name"=>"#","style"=>"width:5%;"]; 
    $data['rtsQuestion'][] = ["name"=>"Question"];

     /* Furnace Master header */
    $data['furnaceMaster'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['furnaceMaster'][] = ["name"=>"#","style"=>"width:5%;"]; 
    $data['furnaceMaster'][] = ["name"=>"Type"];
    $data['furnaceMaster'][] = ["name"=>"Furnace No."];
    $data['furnaceMaster'][] = ["name"=>"Remark"];
    return tableHeader($data[$page]);
}

/* Terms Table Data */
function getTermsData($data){
    $deleteParam = $data->id.",'Terms'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editTerms', 'title' : 'Update Terms'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->title,str_replace(',',', ',$data->type),$data->conditions];
}

/* get Shift Data */
function getShiftData($data){
    $deleteParam = $data->id.",'Shift'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editShift', 'title' : 'Update Shift'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->shift_name,$data->shift_start,$data->shift_end,$data->production_hour,$data->total_lunch_time,$data->total_shift_time];
}

/* get Holidays Data */
function getHolidaysData($data){
    $deleteParam = $data->id.",'Holidays'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editHolidays', 'title' : 'Update Holidays'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

	$action = getActionButton($editButton.$deleteButton);
    $holidayType = ($data->holiday_type == "1")?"Public Holiday":"Special Holiday";
    return [$action,$data->sr_no,$data->holiday_date,$holidayType,$data->title];
}

/* get Category Data */
function getCategoryData($data){
    $deleteParam = $data->id.",'Category'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editCategory', 'title' : 'Update Employee Category'}";

    $editButton = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->category,$data->overtime];
}

/* get Attendance Policy Data */
function getAttendancePolicyData($data){
    $deleteParam = $data->id.",'Attendance Policy'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editAttendancePolicy', 'title' : 'Update Attendance Policy'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

	$action = getActionButton($editButton.$deleteButton);

    return [$action,$data->sr_no,$data->policy_name,$data->policy_type,$data->minute_day.$data->min_lbl,$data->day_month.' <small>Days</small>',$data->penalty_lbl,$data->penalty_hrs.' <small>Hours</small>'];
}

/* Master Detail Table Data */
function getMasterDetailData($data){
    $deleteParam = $data->id.",'Master Detail'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editMasterDetail', 'title' : 'Master Detail'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->title,$data->typeName,$data->remark];
}

/* HSN Master Table Data */
function getHSNMasterData($data){
    $deleteParam = $data->id.",'HSN Master'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editHsnMaster', 'title' : 'HSN Master'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->hsn,$data->cgst,$data->sgst,$data->igst,$data->description];
}

/* Expense Master Table Data */
function getExpenseMasterData($data){
    $deleteParam = $data->id.",'Expense'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editExpense', 'title' : 'Update Expense'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($editButton.$deleteButton);    

    return [$action,$data->sr_no,$data->exp_name,$data->entry_name,$data->seq,$data->calc_type_name,$data->party_name,$data->is_active_name,$data->add_or_deduct_name];
}

function getTaxMasterData($data){
    $deleteParam = $data->id.",'Tax'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editTax', 'title' : 'Update Tax'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($editButton.$deleteButton);    

    return [$action,$data->sr_no,$data->name,$data->tax_type_name,$data->calc_type_name,$data->acc_name,$data->is_active_name,$data->add_or_deduct_name];
}

/* Transport Data */
function getTransportData($data){
	$deleteParam = $data->id.",'Transport'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editShift', 'title' : 'Update Transport'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->transport_name,$data->transport_id,$data->address];
}

/* Banking Data */
function getBankingData($data){
	$deleteParam = $data->id.",'Banking'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editShift', 'title' : 'Update Banking Details'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->bank_name,$data->branch_name,$data->ifsc_code,$data->address];
}


/* Material Grade Table Data */
function getMaterialData($data){
    $deleteParam = $data->id.",'Material Grade'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'edit', 'title' : 'Update Material Grade'}";
    $editButton = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $insParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'tcParameter', 'title' : 'TC Parameter', 'fnedit' : 'getInspectionParam', 'fnsave' : 'saveInspectionParam'}";
    $insParamButton = '<a class="btn btn-info btn-approval permission-modify" href="javascript:void(0)" datatip="TC Parameter" flow="down" onclick="edit('.$insParam.');"><i class="fa fa-file" ></i></a>';

	$action = getActionButton($insParamButton.$editButton.$deleteButton);
    return [$action,$data->sr_no,$data->material_grade,$data->standard,$data->group_name,$data->color_code];
}


/* Vehicle Type Table Data */
function getVehicleTypeData($data){
    $deleteParam = $data->id.",'Vehicle Type'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'edit', 'title' : 'Update Vehicle Type '}";
    $editButton = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->vehicle_type,$data->remark];
}

/* Measurement Technique Table Data */
function getMeasurementTechniqueData($data){
    $deleteParam = $data->id.",'Measurement Technique'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'edit', 'title' : 'Update Vehicle Type '}";
    $editButton = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->measurement_technique,$data->remark];
}

/* Terms Table Data */
function getRtsQuestionHeadingData($data){
    $deleteParam = $data->id.",'Terms'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editQuestionHeading', 'title' : 'Update Question Heading'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
	$action = getActionButton($editButton.$deleteButton);
    $description = '<a href="'.base_url($data->controller.'/questionIndex/'.$data->id).'" >'.$data->description.'</a>';
    return [$action,$data->sr_no,$description];
}
/* Terms Table Data */
function getRtsQuestionData($data){
    $deleteParam = $data->id.",'Terms'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editQuestion','fnedit':'editQuestion', 'title' : 'Update Question'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->description];
}

/* Furnace Table Data */
function getFurnaceData($data){
    $deleteParam = $data->id.",'Furnace Master'"; $type = "";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editFurnace', 'title' : 'Update Furnace'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    if($data->furnace_type == 1){
        $type = "Hardening";
    }else{
        $type = "Tempering";
    }
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$type,$data->furnace_no,$data->remark];
}
?>