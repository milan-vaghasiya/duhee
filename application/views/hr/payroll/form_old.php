<?php $this->load->view('includes/header'); ?>
<style>
	.countSalary{width:100px;}
</style>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4><u>Payroll Entry</u></h4>
                    </div>
                    <div class="card-body">
                        <form autocomplete="off" id="savePayRoll">
                            <input type="hidden" name="ledger_id" id="ledger_id" value="1" >
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label for="dept_id">Department</label>
                                    <select name="dept_id" id="dept_id" class="form-control single-select req">
                                        <option value="0">ALL Department</option>
                                        <?php
                                            foreach($deptRows as $row):
                                                $selected = (!empty($salaryData) && $salaryData[0]->dept_id == $row->id)?"selected":"";
                                                echo '<option value="'.$row->id.'" '.$selected.'>'.$row->name.'</option>';
                                            endforeach;
                                        ?>
                                    </select>
                                    <div class="error dept_id"></div>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label for="month">Month</label>
                                    <select name="month" id="month" class="form-control single-select req">
                                        <option value="">Select Month</option>
                                        <?php
                                            foreach($monthList as $row):
                                                $selected = (!empty($salaryData) && $salaryData[0]->month == $row)?"selected":((!empty($month) && $row == $month)?"selected":"");
                                                echo '<option value="'.$row.'" '.$selected.'>'.date("F-Y",strtotime($row)).'</option>';
                                            endforeach;
                                        ?>
                                    </select>
                                    <div class="error month"></div>
                                </div>
                                <div class="col-md-2 form-group">
                                    <label>&nbsp;</label>
                                    <button type="button" class="btn waves-effect waves-light btn-success btn-block loadSalaryData"  > Load</button>
                                </div>
                                <div class="col-md-2 form-group">
                                    <label>&nbsp;</label>
                                    <button type="button" class="btn waves-effect waves-light btn-info btn-block loadSalaryDataOP"  > On Paper Salary</button>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <div class="row form-group">
                                        <div class="table-responsive ">
                                            <table id="empSalary" class="table table-striped jpExcelTable">
                                                <thead class="thead-info" id="empSalaryHead">
                                                    <tr>
                                                        <th>Emp Code</th>
														<th>Emp Name</th>
														<th>Present<br>Days</th>
														<th>Week<br>Off</th>
														<th>Total<br>Days</th>
														<th>Working<br>Hours</th>
														<th>Basic</th>
														<th>HRA</th>
														<th class="bg-light-green">Gross Earnings</th>
														<th>P.F.</th>
														<th>E.S.I.</th>
														<th>Professional<br>Tax</th>
														<th>T.D.S.</th>
														<th>Advance</th>
														<th>Loan EMI</th>
														<th>Transport</th>
														<th>Food<br>Deduction</th>
														<th class="bg-light-green">Gross<br>Deduction</th>
														<th class="bg-warning">Net Salary</th>
														<th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="empSalaryData"></tbody>
                                            </table>
                                            <div class="hidden_inputs"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <div class="col-md-2 float-right form-group">
                            

                            <button type="button" class=" btn waves-effect waves-light btn-outline-success btn-block save-form" onclick="savePayRoll('savePayRoll');" ><i class="fa fa-check"></i> Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>

<script>
$(document).ready(function(){
    viewDataTable("empSalary");
    $(document).on('click','.loadSalaryData',function(){
       
        var dept_id = $("#dept_id :selected").val();
        var month = $("#month :selected").val();
        var valid = 1;

        if(dept_id == ""){ $(".dept_id").html("Department is required."); valid = 0; }
        if(month == ""){ $(".month").html("Month is required."); valid = 0; }

        if(valid == 1){
            $.ajax({
                url:base_url + controller + '/getEmployeeSalaryData',
                type: 'post',
                data : {dept_id:dept_id, month:month,view:0},
                dataType:'json',
                success:function(data){
                    $('#empSalary').DataTable().clear().destroy();
                    $("#empSalaryHead").html("");
                    $("#empSalaryHead").html(data.emp_salary_head);
                    $("#empSalaryData").html("");
                    $("#empSalaryData").html(data.emp_salary_html); 
                    $(".hidden_inputs").html(data.hidden_inputs); 
                    viewDataTable("empSalary");
                }
            });
        }
    });
    
    // Load On Paper Salary Data
    $(document).on('click','.loadSalaryDataOP',function(){
       
        var dept_id = $("#dept_id :selected").val();
        var month = $("#month :selected").val();
        var valid = 1;

        if(dept_id == ""){ $(".dept_id").html("Department is required."); valid = 0; }
        if(month == ""){ $(".month").html("Month is required."); valid = 0; }

        if(valid == 1){
            $.ajax({
                url:base_url + controller + '/getEmployeeSalaryDataOP',
                type: 'post',
                data : {dept_id:dept_id, month:month,view:0},
                dataType:'json',
                success:function(data){
                    $('#empSalary').DataTable().clear().destroy();
                    $("#empSalaryHead").html("");
                    $("#empSalaryHead").html(data.emp_salary_head);
                    $("#empSalaryData").html("");
                    $(".hidden_inputs").html(data.hidden_inputs); 
                    $("#empSalaryData").html(data.emp_salary_html); 
                    viewDataTable("empSalary");
                }
            });
        }
    });

    /* $(document).on('click','.saveItem',function(){
        var fd = $('#invoiceItemForm')[0];
        var formData = new FormData(fd);
        $.ajax({
            url: base_url + controller + '/getEmpSalaryJson',
            data:formData,
            processData: false,
            contentType: false,
            type: "POST",
            dataType:"json",
        }).done(function(data){
            AddRow(data.jsonData);
            $("#itemModel").modal('hide');
        });
    }); */

    $(document).on('keyup change','.orgEmiAmounts, .emiAmounts',function(){
        var id = $(this).data('id');
        var amount = $(this).val() || 0;
        var loan_amount = $("#loan_amount_"+id).val() || 0;
        $(".error").html("");

        var loan_pending_amount = parseFloat(parseFloat(loan_amount) - parseFloat(amount)).toFixed(0);
        if(parseFloat(loan_pending_amount) < 0){
            if($(this).hasClass(".orgEmiAmounts")){
                $(".org_emi_amount_"+id).html("Invalid EMI Amount.");
            }else{
                $(".emi_amount_"+id).html("Invalid EMI Amount.");
            }  
            $(this).val(0);
            $("#pendingAmount"+id).html(loan_amount);
        }else{
            $("#pendingAmount"+id).html(loan_pending_amount);
        }        
    });

    $(document).on('keyup change','.calculateSalary',function(){
        var earningAmountArray = $("#editEmployeeSalary .earnings").map(function(){return $(this).val();}).get();
        var earningAmount = 0;
        $.each(earningAmountArray,function(){earningAmount += parseFloat(this) || 0;});
        $("#editEmployeeSalary #total_earning").val(earningAmount.toFixed(0));	

        var orgEarningAmountArray = $("#editEmployeeSalary .org_earnings").map(function(){return $(this).val();}).get();
        var orgEarningAmount = 0;
        $.each(orgEarningAmountArray,function(){orgEarningAmount += parseFloat(this) || 0;});
        $("#editEmployeeSalary #org_total_earning").val(orgEarningAmount.toFixed(0));	

        if($("#editEmployeeSalary #pf_applicable").val() == 1){
            var hraAmount = $("#editEmployeeSalary .hra").val() || 0;
            var orgHraAmount = $("#editEmployeeSalary .org_hra").val() || 0;
            var key = $("#editEmployeeSalary .pf").data('key') || 0;
            var pf_cal_value = $("#editEmployeeSalary #pf_cal_value_"+key).val() || 0;
            var pf_cal_method = $("#editEmployeeSalary #pf_cal_method_"+key).val() || 0;

            var pfValuation = parseFloat(parseFloat(earningAmount) - parseFloat(hraAmount)).toFixed(0);
            if(pfValuation >= 15000){
                var pfAmount = parseFloat((15000 * parseFloat(pf_cal_value)) / 100).toFixed(0);
            }else{
                var pfAmount = parseFloat((pfValuation * parseFloat(pf_cal_value)) / 100).toFixed(0);
            }
            $("#editEmployeeSalary .pf").val(pfAmount);

            /*var orgPfValuation = parseFloat(parseFloat(orgEarningAmount) - parseFloat(orgHraAmount)).toFixed(0);
            if(orgPfValuation >= 15000){
                var orgPfAmount = parseFloat((15000 * parseFloat(pf_cal_value)) / 100).toFixed(0);
            }else{
                var orgPfAmount = parseFloat((orgPfValuation * parseFloat(pf_cal_value)) / 100).toFixed(0);
            }*/
            $("#editEmployeeSalary .org_pf").val(pfAmount);
        }else{
            $("#editEmployeeSalary .org_pf").val(0);
            $("#editEmployeeSalary .pf").val(0);
        }

        if(parseFloat(earningAmount) >= 12000){
            var key = $("#editEmployeeSalary .pt").data('key') || 0;
            var pt_cal_value = $("#editEmployeeSalary #pt_cal_value_"+key).val() || 0;
            pt_cal_value = parseFloat(pt_cal_value).toFixed(0);
            $("#editEmployeeSalary .pt").val(pt_cal_value);
			$("#editEmployeeSalary .org_pt").val(pt_cal_value);
        }else{
            $("#editEmployeeSalary .pt").val(0);
			$("#editEmployeeSalary .org_pt").val(0);
        }

        /*if(parseFloat(orgEarningAmount) >= 12000){
            var key = $("#editEmployeeSalary .org_pt").data('key') || 0;
            var pt_cal_value = $("#editEmployeeSalary #pt_cal_value_"+key).val() || 0;
            pt_cal_value = parseFloat(pt_cal_value).toFixed(0);
            $("#editEmployeeSalary .org_pt").val(pt_cal_value);
        }else{
            $("#editEmployeeSalary .org_pt").val(0);
        }*/
        

        var deductionAmountArray = $("#editEmployeeSalary .deductions").map(function(){return $(this).val();}).get();
        var deductionAmount = 0;
        $.each(deductionAmountArray,function(){deductionAmount += parseFloat(this) || 0;});
        $("#editEmployeeSalary #total_deduction").val(deductionAmount.toFixed(0));

        var orgDeductionAmountArray = $("#editEmployeeSalary .org_deductions").map(function(){return $(this).val();}).get();
        var orgDeductionAmount = 0;
        $.each(orgDeductionAmountArray,function(){orgDeductionAmount += parseFloat(this) || 0;});
        $("#editEmployeeSalary #org_total_deduction").val(orgDeductionAmount.toFixed(0));


        var advanceAmountArray = $("#editEmployeeSalary .advanceSalary").map(function(){return $(this).val();}).get();
        var advanceAmount = 0;
        $.each(advanceAmountArray,function(){advanceAmount += parseFloat(this) || 0;});
        $("#editEmployeeSalary #advance_deduction").val(advanceAmount.toFixed(0));

        var orgAdvanceAmountArray = $("#editEmployeeSalary .orgAdvanceSalary").map(function(){return $(this).val();}).get();
        var orgAdvanceAmount = 0;
        $.each(orgAdvanceAmountArray,function(){orgAdvanceAmount += parseFloat(this) || 0;});
        $("#editEmployeeSalary #org_advance_deduction").val(orgAdvanceAmount.toFixed(0));

        var emiAmountArray = $("#editEmployeeSalary .emiAmounts").map(function(){return $(this).val();}).get();
        var emiAmount = 0;
        $.each(emiAmountArray,function(){emiAmount += parseFloat(this) || 0;});
        $("#editEmployeeSalary #emi_amount").val(emiAmount.toFixed(0));

        var orgEmiAmountArray = $("#editEmployeeSalary .orgEmiAmounts").map(function(){return $(this).val();}).get();
        var orgEmiAmount = 0;
        $.each(orgEmiAmountArray,function(){orgEmiAmount += parseFloat(this) || 0;});
        $("#editEmployeeSalary #org_emi_amount").val(orgEmiAmount.toFixed(0));

        var netSalary = 0;
        netSalary = parseFloat(parseFloat(earningAmount) - parseFloat(deductionAmount)).toFixed(0);
        $("#editEmployeeSalary #net_salary").val(netSalary);

        var orgNetSalary = 0;
        orgNetSalary = parseFloat(parseFloat(orgEarningAmount) - parseFloat(orgDeductionAmount)).toFixed(0);
        $("#editEmployeeSalary #actual_sal").val(orgNetSalary);

        var sal_diff = 0;
        sal_diff = parseFloat(parseFloat(orgNetSalary) - parseFloat(netSalary)).toFixed(0);
        $("#editEmployeeSalary #sal_diff").val(sal_diff);
    });

   
    
    $(document).on('click','.reCalculatecSal',function(){
		var emp_id = $(this).data('id');
		var basic_salary = $("#basic_salary"+emp_id).val() || 0;
		var food = $("#food"+emp_id).val() || 0;
        var hra = $("#hra"+emp_id).val() || 0;
        var other_all = $("#other_all"+emp_id).val() || 0;

        var advance_salary = $("#advance_salary"+emp_id).val() || 0;
        var emp_pf = $("#emp_pf"+emp_id).val() || 0;
        var pt = $("#pt"+emp_id).val() || 0;
        var emp_esic = $("#emp_esic"+emp_id).val() || 0;
        var tds = $("#tds"+emp_id).val() || 0;
        var transport_charge = $("#transport_charge"+emp_id).val() || 0;
        var loan_emi = $("#loan_emi"+emp_id).val() || 0;
      
        var gross_amount = (parseFloat(basic_salary)+parseFloat(food)+parseFloat(hra)+parseFloat(other_all)).toFixed(0);
        var total_deduction = (parseFloat(advance_salary)+parseFloat(emp_pf)+parseFloat(pt) + parseFloat(emp_esic) +parseFloat(tds)+parseFloat(transport_charge)+parseFloat(loan_emi)).toFixed(0);

        var net_amount = gross_amount-total_deduction;

        $("#gross_sal"+emp_id).val(gross_amount);
        $("#gross_deduction"+emp_id).val(total_deduction);
        $("#net_salary"+emp_id).val(net_amount);
	});

    $(document).on('click','.reCalculatecSal00',function(){
		var emp_id = $(this).data('id');
		var salaryData = $('.salData'+emp_id).serialize();
		var present = $("#present"+emp_id).val() || 0;
        var week_off = $("#week_off"+emp_id).val() || 0;
        var pl = $("#pl"+emp_id).val() || 0;
        var cl = $("#cl"+emp_id).val() || 0;
        var advance_salary = $("#advance_salary"+emp_id).val() || 0;
        var food = $("#food"+emp_id).val() || 0;
        var total_days = $("#total_days"+emp_id).val() || 0;
        var ot_hrs = $("#ot_hrs"+emp_id).val() || 0;
        var loan_emi = $("#loan_emi"+emp_id).val() || 0;
        var salary_month = $("#salary_month").val() || '';
        var emp_name = $("#emp_name"+emp_id).val() || 0;
        var emp_code = $("#emp_code"+emp_id).val() || 0;
        var wh_hrs = $("#wh_hrs"+emp_id).val() || 0;
        var paid_holiday = $("#paid_holiday"+emp_id).val() || 0;
        var basic_salary = $("#basic_salary"+emp_id).val() || 0;
        var hra = $("#hra"+emp_id).val() || 0;
        var other_all = $("#other_all"+emp_id).val() || 0;
        var emp_pf = $("#emp_pf"+emp_id).val() || 0;
        var pt = $("#pt"+emp_id).val() || 0;
        var total_hrs = $("#total_hrs"+emp_id).val();
        var wages = $("#wages"+emp_id).val();
        var salary = $("#salary"+emp_id).val();
        var total_pay = $("#total_pay"+emp_id).val();
        if(emp_id)
		{
            $.ajax({
                url:base_url + controller + '/reCalculatecSalOP',
                type: 'post',
                data : {id:emp_id, sal_month:salary_month,emp_name:emp_name,emp_code:emp_code,wh_hrs:wh_hrs,present:present,paid_holiday:paid_holiday, week_off:week_off, pl:pl, cl:cl, advance_salary:advance_salary, food:food,total_days:total_days,ot_hrs:ot_hrs,loan_emi:loan_emi,basic_salary:basic_salary,hra:hra,other_all:other_all,emp_pf:emp_pf,pt:pt,total_hrs:total_hrs,wages:wages,salary:salary,total_pay:total_pay},
                dataType:'json',
                success:function(data){
                    $(".emp_line"+emp_id).html(data.empLine);
					$(".hiddenDiv"+emp_id).html(data.hidden_inputs);
                }
            });
        }
	});
	
	$(document).on('keyup change','.calWages',function(){
        var emp_id = $(this).data('id');
        var total_pay = $("#total_pay"+emp_id).val() || 0;
        var present = $("#present"+emp_id).val() || 0;
        var wages = (total_pay/present).toFixed(0);
        $("#wages"+emp_id).val(wages);
        $("#basic_salary"+emp_id).val(total_pay);
    });
    
    $(document).on('keyup change','.calTotalPay',function(){
        var emp_id = $(this).data('id');
        var wages = $("#wages"+emp_id).val() || 0;
        var present = $("#present"+emp_id).val() || 0;
        var total_pay = (wages*present).toFixed(0);
        $("#total_pay"+emp_id).val(total_pay);
        $("#basic_salary"+emp_id).val(total_pay);
    });
});

function Edit(row_index = "",salaryCode = ""){
    var formData = $("#empSalaryData #"+row_index+" :input").serializeArray();
    formData.push({ name: "key_value", value: row_index });
    formData.push({ name: "month", value: $("#month :selected").val() });
    formData.push({ name: "dept_id", value: $("#dept_id :selected").val() });
    formData.push({ name: "format_id", value: $("#format_id :selected").val() });

    var modal_id = "modal-md";
    $.ajax({ 
		url: base_url + controller + "/editEmployeeSalaryData",
        type: "POST",		
		data: formData,
	}).done(function(response){
        $("#"+modal_id).modal();
		$("#"+modal_id+' .modal-title').html("Update Employee Salary ["+salaryCode+"]");
        $("#"+modal_id+' .modal-body').html('');
		$("#"+modal_id+' .modal-body').html(response);
		$("#"+modal_id+" .modal-body form").attr('id',"editEmployeeSalary");
		$("#"+modal_id+" .modal-footer .btn-save").attr('onclick',"saveEmpSalary('editEmployeeSalary','saveEmployeeSalaryData');");
		$("#"+modal_id+" .modal-footer .btn-close").attr('data-modal_id',"editEmployeeSalary");

		$("#"+modal_id+" .modal-footer .btn-close").show();
        $("#"+modal_id+" .modal-footer .btn-save").show();
        $("#"+modal_id+" .modal-footer .btn-save-close").hide();
		
		$("#"+modal_id+" .scrollable").perfectScrollbar({suppressScrollX: true});
		initModalSelect();initMultiSelect();setPlaceHolder();
	});
}

function saveEmpSalary(formId,fnSave){
    var fd = $('#'+formId)[0];
    var formData = new FormData(fd);
	$.ajax({
		url: base_url + controller + '/' + fnSave,
		data:formData,
        processData: false,
        contentType: false,
		type: "POST",
		dataType:"json",
	}).done(function(data){
        var row_index = $("#row_index").val();
        $('#'+formId)[0].reset();$(".modal").modal('hide');
        $('#empSalary').DataTable().destroy();
        $("#"+row_index).html(data.salary_data);
        viewDataTable("empSalary");
	});
}

function savePayRoll(formId){
	
	var fd = $('#'+formId)[0];
    var formData = new FormData(fd);
	$.ajax({
		url: base_url + controller + '/save',
		data:formData,
        processData: false,
        contentType: false,
		type: "POST",
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {
				$("."+key).html(value);
			});
		}else if(data.status==1){
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
            window.location = base_url + controller;
		}else{
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}				
	});
}

function viewDataTable(tableId){
	var table = $('#'+tableId).DataTable( {
		lengthChange: false,
		responsive: true,
		'stateSave':false,
		retrieve: true,
		paging: false,
		// buttons: [ 'excel']
	});
	table.buttons().container().appendTo( '#'+tableId+'_wrapper .col-md-6:eq(0)' );
	return table;
};

function removeEmployee(button){
    //Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#empSalary")[0];
	table.deleteRow(row[0].rowIndex);

    viewDataTable("empSalary");
}
</script>