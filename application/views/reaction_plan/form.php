<div class="col-md-12">
    <form id="addDescription">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
            <input type="hidden" name="type" id="type" value="1" />

            <div class="col-md-12 form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" class="form-control req" value="<?= (!empty($dataRow->title)) ? $dataRow->title : ""; ?>" />
                </select>
            </div>

            <div class="col-md-10 form-group">
                <label for="description">Description</label>
                <input type="text" name="description" id="description" rows="2" class="form-control req" value="<?= (!empty($dataRow->description)) ? $dataRow->description : ""; ?>" />
            </div>
            <div class="col-md-2 form-group">
                <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form mt-30" onclick="saveReactionPlan('addDescription','save');"><i class="fa fa-plus"></i> Add</button>
            </div>
        </div>
    </form>
</div>
<hr>
<div class="row">
    <div class="col-md-12">

        <div class="table-responsive">
            <table id="inspection" class="table table-bordered align-items-center">
                <thead class="thead-info">
                    <tr>
                        <th style="width:5%;">#</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th class="text-center" style="width:10%;">Action</th>
                    </tr>
                </thead>
                <tbody id="reactionPlanBody">
                    <?php
                    if (!empty($rpData)) :
                        $i = 1;
                        foreach ($rpData as $row) :
                            echo '<tr>
                                        <td>' . $i++ . '</td>
                                        <td>' . $row->title . '</td>
                                        <td>' . $row->description . '</td>
                                        <td class="text-center">
                                            <button type="button" onclick="trashPlan(' . $row->id . ');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="ti-trash"></i></button>
                                        </td>
                                    </tr>';
                        endforeach;
                    else :
                        echo '<tr><td colspan="7" style="text-align:center;">No Data Found</td></tr>';
                    endif;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $(document).on('change keyup', '#ref_id', function() {
            $("#title").val($('#ref_idc').val());
        });

        $(document).on('keyup', '#ref_idc', function() {
            $('#title').val($(this).val());
        });
    });

    function saveReactionPlan(formId, fnsave) {
        //var fd = $('#'+formId).serialize();
        setPlaceHolder();
        if (fnsave == "" || fnsave == null) {
            fnsave = "save";
        }
        var form = $('#' + formId)[0];
        var fd = new FormData(form);
        $.ajax({
            url: base_url + controller + '/' + fnsave,
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
                initTable(); //$('#'+formId)[0].reset();$(".modal").modal('hide');   
                toastr.success(data.message, 'Success', {
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp",
                    "closeButton": true,
                    positionClass: 'toastr toast-bottom-center',
                    containerId: 'toast-bottom-center',
                    "progressBar": true
                });
                $("#reactionPlanBody").html(data.tbodyData);
                $("#description").val("");
            } else {
                initTable();
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
</script>