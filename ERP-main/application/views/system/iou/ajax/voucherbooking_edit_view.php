<?php echo head_page($_POST['page_name'], false);
$this->load->helper('crm_helper');
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/crm_style.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>"/>
<div id="filter-panel" class="collapse filter-panel"></div>
<div id="contactMaster_editView">

</div>
<script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/pipeline.css'); ?>">
<script type="text/javascript">
    $(document).ready(function () {
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            iouvoucherid = p_id;
            getiouManagement_editView(iouvoucherid);
        }

        masterID = '<?php if((isset($_POST['data_arr'])) && !empty($_POST['data_arr'])){ echo $_POST['data_arr']; } ?>';

        if(masterID != ''){
            $('.headerclose').click(function () {

            });
        }else{
            $('.headerclose').click(function () {
                fetchPage('system/iou/iou_voucher', '', 'IOU Voucher');
            });
        }
    });

    function getiouManagement_editView(iouvoucherid) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {iouvoucherid: iouvoucherid},
            url: "<?php echo site_url('Iou/load_iou_voucher_detail_editView'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#contactMaster_editView').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

</script>