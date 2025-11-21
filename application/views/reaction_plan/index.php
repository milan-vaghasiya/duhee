<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">Reaction Plan</h4>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew permission-write" data-button="close" data-modal_id="modal-lg" data-function="addDescription" data-form_title="Add Reaction Plan"><i class="fa fa-plus"></i> Add Reaction Plan</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='titleTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="itemModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1">Item List</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="party_so" method="post" action="">
                <div class="modal-body">
                    <div class="col-md-12"><b></span></b></div>
                    <input type="hidden" name="id" id="id" value="">
                    <input type="hidden" name="party_name" id="party_name" value="">
                    <div class="col-md-12">
                        <div class="error general"></div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-info">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Title</th>
                                        <th class="text-center">Description</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="itemData">
                                    <tr>
                                        <td class="text-center" colspan="4">No Data Found</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn waves-effect waves-light btn-outline-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>

                </div>
            </form>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script>
    $(document).ready(function() {

        $(document).on('click', '.createItemList', function() {
            var id = $(this).data('ref_id');

            $.ajax({
                url: base_url + controller + '/getItemList',
                type: 'post',
                data: {
                    id: id
                },
                dataType: 'json',
                success: function(data) {
                    $("#itemModal").modal();
                    $("#ref_id").val(data.ref_id);
                    $("#description").val(data.description);
                }
            });
        });

    });

    function trashPlan(id, name = 'Record') {
        var send_data = {
            id: id
        };
        $.confirm({
            title: 'Confirm!',
            content: 'Are you sure want to delete this ' + name + '?',
            type: 'red',
            buttons: {
                ok: {
                    text: "ok!",
                    btnClass: 'btn waves-effect waves-light btn-outline-success',
                    keys: ['enter'],
                    action: function() {
                        $.ajax({
                            url: base_url + controller + '/delete',
                            data: send_data,
                            type: "POST",
                            dataType: "json",
                            success: function(data) {
                                if (data.status == 0) {
                                    toastr.error(data.message, 'Sorry...!', {
                                        "showMethod": "slideDown",
                                        "hideMethod": "slideUp",
                                        "closeButton": true,
                                        positionClass: 'toastr toast-bottom-center',
                                        containerId: 'toast-bottom-center',
                                        "progressBar": true
                                    });
                                } else {
                                    $("#reactionPlanBody").html(data.tbodyData);
                                    toastr.success(data.message, 'Success', {
                                        "showMethod": "slideDown",
                                        "hideMethod": "slideUp",
                                        "closeButton": true,
                                        positionClass: 'toastr toast-bottom-center',
                                        containerId: 'toast-bottom-center',
                                        "progressBar": true
                                    });
                                }
                            }
                        });
                    }
                },
                cancel: {
                    btnClass: 'btn waves-effect waves-light btn-outline-secondary',
                    action: function() {

                    }
                }
            }
        });
    }
</script>