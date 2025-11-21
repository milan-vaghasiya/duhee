<div class="col-md-12">
    <form>
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <!-- Column -->
                    <div class="col-lg-12 col-xlg-12 col-md-12">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Qty</th>
                                    <th>Return Status</th>
                                    <th>Reason</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="returnTbody">
                                <?php
                                if (!empty($returnTransData)) {
                                    $i = 1;
                                    foreach ($returnTransData as $row) {
                                ?>
                                        <tr>
                                            <td><?= $i++ ?></td>
                                            <td><?= $row->qty ?></td>
                                            <td>
                                                <?php
                                                if ($row->return_status == 1) {
                                                    echo 'Used';
                                                } elseif ($row->return_status == 2) {
                                                    echo 'Fresh';
                                                } elseif ($row->return_status == 3) {
                                                    echo 'Missed';
                                                } elseif ($row->return_status == 4) {
                                                    echo 'Broken';
                                                }
                                                ?>
                                            </td>
                                            <td><?= $row->reason ?></td>
                                            <td>
                                                <button type="button" class="btn btn-block btn-outline-danger" onclick="trashReturn(<?= $row->id ?>,<?= $row->ref_id ?>)"><i class="fa fa-trash"></i></button>
                                            </td>
                                        </tr>
                                <?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    function trashReturn(id,ref_id, name = 'Record') {
        var send_data = {
            id: id,ref_id:ref_id
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
                            url: base_url + controller + '/deleteReturn',
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
                                    initTable();
                                    initMultiSelect();
                                    toastr.success(data.message, 'Success', {
                                        "showMethod": "slideDown",
                                        "hideMethod": "slideUp",
                                        "closeButton": true,
                                        positionClass: 'toastr toast-bottom-center',
                                        containerId: 'toast-bottom-center',
                                        "progressBar": true
                                    });
                                    $("#returnTbody").html("");
                                    $("#returnTbody").html(data.html);
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