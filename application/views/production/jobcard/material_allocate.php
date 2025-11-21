<form id="materialRequest">
    <div class="error general_error"></div>
    <div class="col-md-12">
        <input type="hidden" name="job_card_id" id="job_card_id" value="<?=$jobCardData->id?>">
        <input type="hidden" name="product_id" id="product_id" value="<?=$jobCardData->product_id?>">
        <div class="row">
            <div class="error error_msg"></div>
            <div class="table-responsive">
                <table id="requestItems" class="table table-bordered align-items-center">
                    <?=$bomDataHtml?>
                </table>
            </div>
        </div>
    </div>
</form>