<?php
echo head_page('PR to GRV Report', false);
$date_format_policy = date_format_policy();
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('config', $primaryLanguage);
$this->lang->load('procurement_approval', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$token_details = [
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
];

if($this->session->userdata("companyType") == 1){
    $segment_arr = fetch_segment(true,false);
}else{
   $segment_arr = fetch_group_segment(true,false);
}
?>
<style>
    .bgc {
        background-color: #e1f1e1;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/pagination/styles.css'); ?>" class="employee_master_styles">
<div id="filter-panel" class="collapse filter-panel">
</div>
    <form role="form" id="pr_to_grv_filter_frm" class="" autocomplete="off">
    <input type="hidden" name="<?=$token_details['name'];?>" value="<?=$token_details['hash'];?>" />
    <div class="row">
        <div class="form-group col-sm-2">
            <label for=""><?php echo $this->lang->line('common_date_from')?><!-- From Date --></label>
            <br>
            <span class="input-req" title="Required Field">
            <div class="input-group datepic" id="dateStartDate">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type="text" name="prDateFrom" id="prDateFrom" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" class="form-control startDate" required>
            </div>
            <span class="input-req-inner"></span>
        </span>
        </div>
        <div class="form-group col-sm-2">
            <label for=""><?php echo $this->lang->line('common_date_to')?><!-- To Date --></label>
            <br>
            <span class="input-req" title="Required Field">
            <div class="input-group datepic" id="dateStartDate">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type="text" name="prDateTo" id="prDateTo" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" class="form-control startDate" required>
            </div>
            <span class="input-req-inner"></span>
        </span>
        </div>
        <div class="form-group col-sm-3">
            <label for=""><?php echo $this->lang->line('common_item')?><!-- Item --></label>
            <br>
            <?php echo form_dropdown('items[]', fetch_item_dropdown(false,false,100,1), '', 'class="form-control select2 items" id="items"  multiple="" style="z-index: 0;"'); ?>
        </div>
        <div class="form-group col-sm-5">
            <div class="form-group col-sm-3">
                <label for=""><?php echo $this->lang->line('config_document_id')?> <!-- Document ID --></label>
                <?php echo form_dropdown('documentID', array('1'=>'PR','2'=>'PO'), '', 'class="form-control select2 documentID" id="documentID" '); ?>
            </div>
            <div class="form-group col-sm-6">
                <label for=""><?php echo $this->lang->line('common_document_code')?> <!-- Document Code --></label>
                <input type="text" name="doccode" id="doccode" style="text-align: left;" placeholder="Document Code" class="form-control number doccode">
            </div>
        </div>
       
    
        


    </div>

    <div class="row">
    <div class="form-group col-sm-4">
    <label for="supplierPrimaryCode"> PO <?php echo $this->lang->line('common_segment')?> </label> <br><!--segment-->
        <?php echo form_dropdown('posegment[]', $segment_arr, '', 'class="form-control" id="posegment" multiple="multiple"'); ?>
    </div>
        <div class="form-group col-sm-4">
            <label>Receipt Status</label>
            <select class="form-control select2" id="receiptStatusFilter">
                <option value="-1">All</option>
                <option value="fully_received">Fully Received</option>
                <option value="partially_received">Partially Received</option>
                <option value="not_received">Not Received</option>
            </select>
        </div>
    <div class="form-group col-sm-2 pull-right">
            <label for="">&nbsp;</label>
            <br>
            <button type="button" onclick="fetch_pr_to_grv()" class="btn btn-primary btn-sm">
            <?php echo $this->lang->line('common_generate')?><!--Generate -->
            </button>
        </div>

    </div>
</form>
<div id="div_pr_to_grv">
</div>
<div class="col-xs-12" style="padding-right: 5px;">
    <div class="pagination-content clearfix" id="emp-master-pagination" style="padding-top: 10px">
        <p id="filterDisplay"></p>

        <nav>
            <ul class="list-inline" id="pagination-ul">
            </ul>
        </nav>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script>
    var per_page = 10;
    $(document).ready(function (e) {
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            //$('#purchase_order_form').bootstrapValidator('revalidateField', 'expectedDeliveryDate');
        });

        $('.select2').select2();
        $("#items").select2({
            tags: true,
            containerCssClass : "items-input"
        });

        $('#posegment').multiselect2({
                enableCaseInsensitiveFiltering: true,
                includeSelectAllOption: true,
                numberDisplayed: 1,
                buttonWidth: '180px',
                maxHeight: '30px'
        });



        fetch_pr_to_grv();
  
  
  
  
    });
    $('.items-input').on('keyup', '.select2-search__field', function (e) {
        load_items_dropdown(e.target.value);
    });
    var currentRequest = null;
    function load_items_dropdown(skey) {
        let selected = $("#items").val();

        currentRequest = $.ajax({
            async: false,
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Pos/load_items_dropdown'); ?>",
            data: {skey:skey,selected:selected,'issystemcode':1},
            beforeSend: function () {
                if (currentRequest != null) {
                    currentRequest.abort();
                }
            },
            success: function (data) {
                var Name = "";
                var ID = "";
                data.items.forEach(function (item, index) {
                    Name =  item.itemSystemCode + ' | ' +item.seconeryItemCode + ' | ' + item.itemName;
                    ID = item.itemAutoID;
                    $("#items").append("<option value='"+ID+"'>"+Name+"</option>");
                    [].slice.call(items.options)
                        .map(function(a){
                            if(this[a.innerText]){
                                items.removeChild(a);
                            } else {
                                this[a.innerText]=1;
                            }
                        },{});
                });
            }
        });
    }

    function pagination(obj) {
        $('.employee-pagination').removeClass('paginationSelected');
        $(obj).addClass('paginationSelected');

        var data_pagination = $('.employee-pagination.paginationSelected').attr('data-emp-pagination');
        var uriSegment = (data_pagination == undefined) ? per_page : ((parseInt(data_pagination) - 1) * per_page);
        var filtervalue = '#';
        fetch_pr_to_grv(data_pagination, uriSegment);
    }

    function fetch_pr_to_grv(pageID,uriSegment = 0)
    {

        var data = $("#pr_to_grv_filter_frm").serializeArray();
        data.push({'name': 'itemautoID', 'value':$('#items').val()});
        data.push({'name': 'pageID', 'value':pageID});
        var receiptStatusFilter = $("#receiptStatusFilter").val();
        data.push({'name': 'receiptStatusFilter', 'value':receiptStatusFilter});
        /*   var itemautoID = $('#items').val();
          var documentID = $("#documentID").val();
          var doccode = $("#doccode").val();
          var prDateFrom = $("#prDateFrom").val();
          var prDateTo = $("#prDateTo").val();
          var selected = [];
          alert(selected); */
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data : data,
            url: "<?php echo site_url('Procurement/fetch_pr_to_grv'); ?>/"+uriSegment,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_pr_to_grv').html(data['view']);
                $('#pagination-ul').html(data.pagination);
                $('#filterDisplay').html(data.filterDisplay);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function export_excel_prToGrv() {
        var form = document.getElementById('pr_to_grv_filter_frm');
        form.target = '_blank';
        form.method = 'post';
        form.action = '<?php echo site_url('Procurement/export_excel_pr_to_grv_report'); ?>';
        form.submit();
    }
</script>