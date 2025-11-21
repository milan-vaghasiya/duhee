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
                        <form autocomplete="off" id="savePR">
                            <input type="hidden" name="id" id="id" value="" >
                            <input type="hidden" name="ledger_id" id="ledger_id" value="1" >
                            <div class="row">
                               
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
                               
                                <div class="col-md-6 form-group">
                                    <label for="pr_excel">Upload Excel</label>
                                    <div class="input-group">
                                        <input type="file" class="form-control-file" name="pr_excel" id="pr_excel" accept=".xlsx, .xls" style="width:80%;">
                                        <div class="input-group-append">
											<button class="btn btn-outline-secondary " id="readButton" type="button">Read Excel</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <div class="row form-group">
                                        <div class="error itemData"></div>
                                        <div class="table-responsive ">
                                            <table id="empSalary" class="table table-striped jpExcelTable">
                                                <thead class="thead-info" id="empSalaryHead">
                                                    <tr>
                                                        <th>Emp Code</th>
                                                        <th>Emp Name</th>
                                                        <th>Present off <br>Abs<br>C.L<br>P.H</th>
                                                        <th>BasicConve. Allow<br>H.R.A<br>Madical<br>Child Educa.<br> Office wear all.
                                                        <th>Total Pay</th>
                                                        <th>Basic</th>
                                                        <th>Food Allow</th>
                                                        <th>H.R.A</th>
                                                        <th>Other allow</th>
                                                        <th>Gross Amount</th>
                                                        <th>TDS</th>
                                                        <th>Other</th>
                                                        <th>Advance</th>
                                                        <th>PF</th>
                                                        <th>PT</th>
                                                        <th>Total Ded.</th>
                                                        <th>Net  Payable</th>
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
                            <button type="button" class=" btn waves-effect waves-light btn-outline-success btn-block save-form" onclick="savePayRoll();" ><i class="fa fa-check"></i> Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url(); ?>assets/js/xlsx.full.min.js?v=<?=time()?>"></script>

<script>
$(document).ready(function(){
    $(document).on("click",'#readButton',function() {
        
        var fileInput = document.getElementById('pr_excel');
        var file = fileInput.files[0];
        $(".excel_file").html("");
        
        if(file){
            var errorCount = $('#input_excel_column .error:not(:empty)').length;

            if(errorCount == 0){
                var columnCount = $('table#empSalary thead tr').first().children().length;
                $("table#empSalary > TBODY").html('<tr><td id="noData" colspan="'+columnCount+'" class="text-center">Loading...</td></tr>'); 

                setTimeout(function(){
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var data = new Uint8Array(e.target.result);
                        var workbook = XLSX.read(data, { type: 'array' });

                        var sheetName = workbook.SheetNames[0]; // Assuming the first sheet
                        var worksheet = workbook.Sheets[sheetName];

                        var jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });
                        var fileData = [];
                        // Process the data or display it in the table

                        //Remove blank line.
                        $('table#empSalary > TBODY').html("");              
                        
                        var postData = [];
                        $.each(jsonData,function(ind,row){ // console.log(row);
                            postData = [];
                            if(ind >0){
                                var emp_id = "";
                                if(row[1]){
                                    row[1] = row[1] || -1;
                                    $.ajax({
                                        url : base_url + 'hr/payroll/getEmpDetails',
                                        type : 'post',
                                        data : { emp_code : row[0]},
                                        global:false,
                                        async:false,
                                        dataType:'json'
                                    }).done(function(res){
                                        console.log(res);
                                        emp_id = "";
                                        if(res != ""){
                                            var empDetail = res.data.empDetail;//console.log(empDetail);
                                            if(empDetail != null){
                                                emp_id = empDetail.id;
                                            }                            
                                        }
                                        if(emp_id != ""){
                                            postData = {
                                                'emp_id':emp_id,
                                                'emp_code':row[0],
                                                'emp_name':row[1],
                                                'present':row[2],
                                                'wages':parseFloat(row[3]).toFixed(2),
                                                'total_pay':parseFloat(row[4]).toFixed(2),
                                                'basic_salary':parseFloat(row[5]).toFixed(2),
                                                'food':parseFloat(row[6]).toFixed(2),
                                                'hra':parseFloat(row[7]).toFixed(2),
                                                'other_all':parseFloat(row[8]).toFixed(2),
                                                'gross_sal':parseFloat(row[9]).toFixed(2),
                                                'tds':parseFloat(row[10]).toFixed(2),
                                                'other':parseFloat(row[11]).toFixed(2),
                                                'advance_salary':parseFloat(row[12]).toFixed(2),
                                                'emp_pf':parseFloat(row[13]).toFixed(2),
                                                'pt':parseFloat(row[14]).toFixed(2),
                                                'gross_deduction':parseFloat(row[15]).toFixed(2),
                                                'net_salary':parseFloat(row[16]).toFixed(2)
                                            };
                                            AddRow(postData);
                                        }
                                      
                                    }); 
                                } 
                            } 
                        });
                    };
                    reader.readAsArrayBuffer(file); 
                },200);
            }
        }else{
            $(".excel_file").html("Please Select File.");
        }         
    });
});

function AddRow(data){
    var tblName = "empSalary";

    //Remove blank line.
	$('table#'+tblName+' tr#noData').remove();

    //Get the reference of the Table's TBODY element.
	var tBody = $("#" + tblName + " > TBODY")[0];    

    var ind = -1 ;
	row = tBody.insertRow(ind);
    
    //Add index cell
	var countRow = ($('#' + tblName + ' tbody tr:last').index() + 1);
    $(row).attr('id',countRow);

    var ledgerIdInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][ledger_id]",  value: $("#ledger_id").val() });

    var empIdInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][emp_id]",id:'emp_id_'+countRow,  value: data.emp_id });
  
    var empCodeInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][emp_code]",  value: data.emp_code});

    cell = $(row.insertCell(-1));
    cell.html(data.emp_code);
    cell.append(empCodeInput);
    cell.append(ledgerIdInput);
    cell.append(empIdInput);

    var empNameInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][emp_name]",  value: data.emp_name });
    cell = $(row.insertCell(-1));
    cell.html(data.emp_name);
    cell.append(empNameInput);

    var presentInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][present]",id:'present_'+countRow,  value: data.present });
    cell = $(row.insertCell(-1));
    cell.html(data.present);
    cell.append(presentInput);

    var wagesInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][wages]",  value: data.wages});
    cell = $(row.insertCell(-1));
    cell.html(data.wages);
    cell.append(wagesInput);

    var totalPayInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][total_pay]",  value: data.total_pay });
    cell = $(row.insertCell(-1));
    cell.html(data.total_pay);
    cell.append(totalPayInput);

    var basicSalaryInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][basic_salary]",  value: data.basic_salary });
    cell = $(row.insertCell(-1));
    cell.html(data.basic_salary);
    cell.append(basicSalaryInput);

    var foodInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][food]",  value: data.food});
    cell = $(row.insertCell(-1));
    cell.html(data.food);
    cell.append(foodInput);

    var hraInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][hra]",  value: data.hra });
    cell = $(row.insertCell(-1));
    cell.html(data.hra);
    cell.append(hraInput);

    var otherAllInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][other_all]",  value: data.other_all });
    cell = $(row.insertCell(-1));
    cell.html(data.other_all);
    cell.append(otherAllInput);

    var grossSalInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][gross_sal]",  value: data.gross_sal });
    cell = $(row.insertCell(-1));
    cell.html(data.gross_sal);
    cell.append(grossSalInput);

    var tdsInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][tds]",  value: data.tds });
    cell = $(row.insertCell(-1));
    cell.html(data.tds);
    cell.append(tdsInput);

    var otherInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][other]",  value: data.other });
    cell = $(row.insertCell(-1));
    cell.html(data.other);
    cell.append(otherInput);

    var advanceSalaryInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][advance_salary]",  value: data.advance_salary });
    cell = $(row.insertCell(-1));
    cell.html(data.advance_salary);
    cell.append(advanceSalaryInput);

    var empPfInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][emp_pf]",  value: data.emp_pf });
    cell = $(row.insertCell(-1));
    cell.html(data.emp_pf);
    cell.append(empPfInput);

    var ptInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][pt]",  value: data.pt });
    cell = $(row.insertCell(-1));
    cell.html(data.pt);
    cell.append(ptInput);

    var grossDeductionInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][gross_deduction]",  value: data.gross_deduction });
    cell = $(row.insertCell(-1));
    cell.html(data.gross_deduction);
    cell.append(grossDeductionInput);

    var netSalaryInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][net_salary]",  value: data.net_salary });
    cell = $(row.insertCell(-1));
    cell.html(data.net_salary);
    cell.append(netSalaryInput);
}

function savePayRoll(){ 
	var form = $('#savePR')[0];
	var fd = new FormData(form); 
	$.ajax({
		url: base_url + controller + '/savePayRoll',
		data:fd,
		type: "POST",
		processData:false,
		contentType:false,
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {$("."+key).html(value);});
		}else if(data.status==1){
			initTable(); $('#savePR')[0].reset();$(".modal").modal('hide');
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}else{
			initTable(); $('#savePR')[0].reset();$(".modal").modal('hide');
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}
	});
}

</script>