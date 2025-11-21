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
