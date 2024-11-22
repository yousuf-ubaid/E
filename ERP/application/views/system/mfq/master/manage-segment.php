<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('mfq', $primaryLanguage);
$title = $this->lang->line('manufacturing_manage_segment');
echo head_page($title, false);
/*$main_category_arr = all_main_category_drop();
$key = array_filter($main_category_arr, function ($a) {
    return $a == 'FA | Fixed Assets';
});
unset($main_category_arr[key($key)]);*/
$mfqSegmentID = isset($page_id) && !empty($page_id) ? $page_id : 0;


?>
<link href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/datatables/customer-style-datatable.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/custom-mfq.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/buttons/button.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>


<form method="post" id="from_add_edit_segment">
    <input type="hidden" value="" id="mfqSegmentID" name="mfqSegmentID"/>

    <div class="row">
        <div class="col-md-12 animated zoomIn">
            <header class="head-title">
                <h2><?php echo $this->lang->line('manufacturing_segment_detail') ?><!--Segment Detail--> </h2>
            </header>
            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-1">
                    &nbsp;
                </div>
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('common_code') ?><!--Code--> </label>
                </div>

                <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                    <input type="text" name="segmentCode" id="segmentCode" class="form-control" placeholder="<?php echo$this->lang->line('manufacturing_segment_code') ?>"
                           required>
                    <span class="input-req-inner"></span>
                </span>
                </div>

            </div>

            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-1">
                    &nbsp;
                </div>
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('common_description') ?><!--Description--> </label>
                </div>

                <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                    <input type="text" name="description" id="description" class="form-control" placeholder="<?php echo $this->lang->line('common_description') ?>"
                           required>
                    <span class="input-req-inner"></span>
                </span>
                </div>

            </div>


        </div>



    </div>

    <div class="col-md-12 animated zoomIn">
        <div class="row" style="margin-top: 10px;">
            <div class="col-sm-7">
                <div class="pull-right">
                    <button class="btn btn-primary" type="submit" id="submitSegmentBtn"><i class="fa fa-plus"></i> <?php echo $this->lang->line('manufacturing_add_segment') ?><!--Add Segment-->
                    </button>
                </div>
            </div>
        </div>
    </div>

</form>


<?php echo footer_page('Right foot', 'Left foot', false); ?>


<script type="text/javascript">

    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/mfq/mfq_segment','','Segment')
        });

        $("#from_add_edit_segment").submit(function (e) {
            addEditSegment();
            return false;
        });
        loadSegmentDetail();
    });


    function addEditSegment() {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("MFQ_SegmentMaster/add_edit_segment"); ?>',
            dataType: 'json',
            data: $("#from_add_edit_segment").serialize(),
            async: false,
            success: function (data) {
                if (data['error'] == 1) {
                    myAlert('e', data['message']);
                }
                else if (data['error'] == 0) {
                    if (data['code'] == 1) {
                        $("#from_add_edit_segment")[0].reset();
                    }
                    myAlert('s', data['message']);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                myAlert('e', xhr.responseText);
            }
        });
    }

    function loadSegmentDetail() {
        var mfqSegmentID = '<?php echo $mfqSegmentID ?>';
        if (mfqSegmentID > 0) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("MFQ_SegmentMaster/loadSegmentDetail"); ?>',
                dataType: 'json',
                data: {mfqSegmentID: mfqSegmentID},
                async: false,
                success: function (data) {
                    if (data['error'] == 0) {
                        myAlert('s', data['message']);
                        $("#submitSegmentBtn").html('<i class="fa fa-pencil"></i> Edit Segment');
                        $("#mfqSegmentID").val(mfqSegmentID);
                        $("#segmentCode").val(data['segmentCode']);
                        $("#description").val(data['description']);

                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    myAlert('e', xhr.responseText);
                }
            });
        }
    }


</script>