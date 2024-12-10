<?php echo head_page($_POST['page_name'], false);
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/crm_style.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/pipeline.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">

<div id="filter-panel" class="collapse filter-panel"></div>
<div id="customer_editView"></div>

<script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
<script type="text/javascript">
    $(document).ready(function () {
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            customerAutoID = p_id;
            getCustomerManagement_editView(customerAutoID);
        }
        masterID = '<?php if(isset($_POST['data_arr']) && !empty($_POST['data_arr'])) { echo $_POST['data_arr']; }?>';
        $('.headerclose').click(function () {
            fetchPage('system/srm/srm_customermaster', '', 'Customer Master');
        });

    });

    function getCustomerManagement_editView(customerAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {customerAutoID: customerAutoID},
            url: "<?php echo site_url('Srm_master/load_customer_editView'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#customer_editView').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function opportunity_edit_view_close() {

        fetchPage('system/srm/srm_customermaster', '', 'Supplier Master');

    }

</script>