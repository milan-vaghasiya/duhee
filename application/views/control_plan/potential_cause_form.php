<div class="col-md-12">
    <form id="potential_cause_form">
        <div class="row">
            <input type="hidden" name="id" id="id" class="id" value="" />
            <input type="hidden" name="ref_id" id="ref_id" class="ref_id" value="<?= $ref_id ?>" />
            <input type="hidden" name="fmea_type" id="fmea_type" value="2" />
            <div class="col-md-4">
                <label for="potential_cause">Potential Cause</label>
                <input type="text" class="form-control" name="potential_cause" id="potential_cause">
            </div>
            <div class="col-md-2">
                <label for="occur">Occur</label>
                <input type="text" class="form-control floatOnly" name="occur" id="occur">
            </div>
            <div class="col-md-6">
                <label for="process_prevention">Prevention</label>
                <div class="input-group">
                    <input type="text" class="form-control" name="process_prevention" id="process_prevention"><button type="button" class="btn waves-effect waves-light btn-outline-success btn-save ml-2" onclick="savePotentialCause('potential_cause_form');"><i class="fa fa-plus"></i> Add</button>
                </div>
            </div>
        </div>
    </form>
    <hr>
    <div class="row">
        <div class="table-responsive">
            <table id="prevTble" class="table table-bordered align-items-center">
                <thead class="thead-info">
                    <tr>
                        <th style="width:5%;">#</th>
                        <th>Potential Cause</th>
                        <th>Occur</th>
                        <th>Prevention</th>
                        <th class="text-center" style="width:10%;">Action</th>
                    </tr>
                </thead>
                <tbody id="prevBody">
                    <?=$tbody?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function savePotentialCause(formId) {
        // var fd = $('#'+formId).serialize();
        setPlaceHolder();
       
        var form = $('#' + formId)[0];
        var fd = new FormData(form);
        $.ajax({
            url: base_url + controller + '/savePotentialCause',
            data: fd,
            type: "POST",
            processData: false,
            contentType: false,
            dataType: "json",
        }).done(function(data) {
            if (data.status === 0) {
                $(".error").html("");
                $.each(data.message, function(key, value) {
                    $("." + key).html(value);
                });
            } else if (data.status == 1) {
                initTable(0); //$('#'+formId)[0].reset();//$(".modal").modal('hide');   
                toastr.success(data.message, 'Success', {
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp",
                    "closeButton": true,
                    positionClass: 'toastr toast-bottom-center',
                    containerId: 'toast-bottom-center',
                    "progressBar": true
                });
                $("#prevBody").html(data.html);
                $("#id").val("");
                $("#potential_cause").val("");
                $("#occur").val("");
                $("#process_prevention").val("");
            } else {
                initTable(0);
                $('#' + formId)[0].reset();
                $(".modal").modal('hide');
                toastr.error(data.message, 'Error', {
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp",
                    "closeButton": true,
                    positionClass: 'toastr toast-bottom-center',
                    containerId: 'toast-bottom-center',
                    "progressBar": true
                });
            }

        });
    }

    function deleteCause(id,ref_id,name = 'Record') {
        var send_data = { id: id,ref_id:ref_id  };
        $.confirm({
            title: 'Confirm!',
            content: 'Are you sure want to Remove this Record? <br> All related records will be removed and will not be recovered',
            type: 'red',
            buttons: {
                ok: {
                    text: "ok!",
                    btnClass: 'btn waves-effect waves-light btn-outline-success',
                    keys: ['enter'],
                    action: function() {
                        $.ajax({
                            url: base_url + controller + '/deletePotentialCause',
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
                                    initTable(0);
                                    toastr.success(data.message, 'Success', {
                                        "showMethod": "slideDown",
                                        "hideMethod": "slideUp",
                                        "closeButton": true,
                                        positionClass: 'toastr toast-bottom-center',
                                        containerId: 'toast-bottom-center',
                                        "progressBar": true
                                    });
                                    $("#prevBody").html(data.html);
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

    function editCause(id,button){
        if(id){
            $.ajax({
                url: base_url + controller + '/editPotentialCause',
                data: {id:id},
                type: "POST",
                dataType:'json',
                success:function(data){
                    $("#id").val(data.id);
                    $("#potential_cause").val(data.potential_cause);
                    $("#occur").val(data.occur);
                    $("#process_prevention").val(data.process_prevention);

                    var row = $(button).closest("TR");  
                    var table = $("#prevTble")[0];
                    table.deleteRow(row[0].rowIndex);
                    $('#prevBody tbody tr td:nth-child(1)').each(function(idx, ele) {
                        ele.textContent = idx + 1;
                    });
                }
            });
        }
    }
</script>