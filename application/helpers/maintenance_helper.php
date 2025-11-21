<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/* get Pagewise Table Header */
function getMaintenanceDtHeader($page){
    /* Machine Header */
    $data['machines'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['machines'][] = ["name"=>"#","style"=>"width:5%;"]; 
    $data['machines'][] = ["name"=>"Machine Name"];
    $data['machines'][] = ["name"=>"Machine No."];
    $data['machines'][] = ["name"=>"Make/Model"];
    $data['machines'][] = ["name"=>"Capacity/Size"];
    $data['machines'][] = ["name"=>"Location"];
    $data['machines'][] = ["name"=>"Process"];
    $data['machines'][] = ["name"=>"Remark"];

    /* Machine Ticket Header */
    $data['machineTicket'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['machineTicket'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['machineTicket'][] = ["name"=>"Ticket Date"];
    $data['machineTicket'][] = ["name"=>"Ticket No."];
    $data['machineTicket'][] = ["name"=>"Machine No."];
    $data['machineTicket'][] = ["name"=>"Ticket Title"];
    $data['machineTicket'][] = ["name"=>"Solution"];
    $data['machineTicket'][] = ["name"=>"Solved Date"];

    /* Machine Activities Header */
    $data['machineActivities'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['machineActivities'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['machineActivities'][] = ["name"=>"Machine Activities"];
	
	/* Machine Type Header */
    $data['machineType'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['machineType'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['machineType'][] = ["name"=>"Machine Type"];
    
	/* Preventive Maintenance */    
    $data['preventiveMaintenance'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['preventiveMaintenance'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['preventiveMaintenance'][] = ["name"=>"Machine","style"=>"width:5%;"];
    $data['preventiveMaintenance'][] = ["name"=>"Activity","style"=>"width:5%;"];
    $data['preventiveMaintenance'][] = ["name"=>"Last Maintenance Date","style"=>"width:5%;"];
    $data['preventiveMaintenance'][] = ["name"=>"Due Date","style"=>"width:5%;"];
    $data['preventiveMaintenance'][] = ["name"=>"Schedule Date","style"=>"width:5%;"];

    return tableHeader($data[$page]);
}

/* Machine Table Data */
function getMachineData($data){
    $deleteParam = $data->id.",'Machine'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'editMachine', 'title' : 'Update Machine'}";
    $activityParam = "{'machine_id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'machine_activity', 'title' : 'Preventive Maintanance Checklist'}";

    $activityButton = '';
    if($data->prev_maint_req == 'Yes')
        $activityButton = '<a class="btn btn-info btn-activity permission-modify" href="javascript:void(0)" datatip="Machine Activity" flow="down" onclick="setActivity('.$activityParam.');"><i class="fa fa-check-square"></i></a>';

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

	$action = getActionButton($activityButton.$editButton.$deleteButton);
	
    return [$action,$data->sr_no,$data->item_name,$data->item_code,$data->make_brand,$data->size,$data->location,$data->process_name,$data->description];
}

/* Machine Ticket Data */
function getMachineTicketData($data){
    $deleteParam = $data->id.",'Machine Ticket'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editMachineTicket', 'title' : 'Update Machine Ticket'}";
    $solutionParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'machineSolution', 'title' : 'Machine Solution', 'fnedit' : 'getMachineSolution', 'fnsave' : 'saveMachineSolution'}";

    $solutionButton = '<a class="btn btn-info btn-solution permission-modify" href="javascript:void(0)" datatip="Machine Solution" flow="down" onclick="edit('.$solutionParam.');"><i class="fa fa-check"></i></a>';

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $requisitionButton ='<a class="btn btn-success btn-edit permission-modify addRequisition" data-button="both" data-modal_id="modal-xl" data-function="addRequisition/" data-id="'.$data->id.'" datatip="Add Requisition" data-form_title="Requisition at '.date("d-m-Y H:i:s").'" data-fnsave="saveRequisition" flow="down"><i class="fa fa-plus"></i></a>';
	$action = getActionButton($requisitionButton.$solutionButton.$editButton.$deleteButton);

    return [$action,$data->sr_no,formatDate($data->problem_date),$data->trans_prefix.$data->trans_no,'[' . $data->item_code . ']' . $data->item_name,$data->problem_title,$data->solution_detail,formatDate($data->solution_date)];
}

/* Machine Activities Data  */
function getMachineActivitiesData($data){
    $deleteParam = $data->id.",'Machine Activities'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editMachineActivities', 'title' : 'Update Machine Activities'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->activities];
}

/* Machine Type Data  */
function getMachineTypeData($data){
    $deleteParam = $data->id.",'Machine Type'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editMachineType', 'title' : 'Update Machine Type'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->typeof_machine];
}

/** Machine Maintance Plan Data */
function getPreventiveMaintenanceData($data){
    $deleteParam = $data->id.",'Machine Type'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'updatePlan', 'title' : 'Update Maintanance Plan','fnsave' : 'saveUpdatedPlan'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,'['.$data->item_code.'] '.$data->item_name,$data->activities,formatDate($data->last_maintence_date),formatDate($data->due_date),formatDate($data->schedule_date)];
}
?>