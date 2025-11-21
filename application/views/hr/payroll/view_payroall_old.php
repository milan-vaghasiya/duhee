<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
									<tr>
                                        <th>Emp Code</th>
                                        <th>Emp Name</th>
                                        <th>Present<br>Days</th>
                                        <th>Week<br>Off</th>
                                        <th>Total<br>Days</th>
                                        <th>Working<br>Hours</th>
                                        <th>Basic</th>
                                        <th>HRA</th>
                                        <th>Gross Earnings</th>
                                        <th>P.F.</th>
                                        <th>E.S.I.</th>
                                        <th>Professional<br>Tax</th>
                                        <th>T.D.S.</th>
                                        <th>Advance</th>
                                        <th>Loan EMI</th>
                                        <th>Transport</th>
                                        <th>Food<br>Deduction</th>
                                        <th>Gross<br>Deduction</th>
                                        <th>Net Salary</th>
                                    </tr>
								</thead>
                                <tbody>
									<?=$payrollData?>
								</tbody>
							</table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>


<?php $this->load->view('includes/footer'); ?>
