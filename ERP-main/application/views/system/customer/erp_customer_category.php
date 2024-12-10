<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_maraketing_masters_customer_master', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('sales_maraketing_masters_customer_category');
echo head_page($title, false);

/*echo head_page('Customer Category', false);*/ ?>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row table-responsive">
    <div class="col-md-5">

    </div>
    <div class="col-md-7 text-right">
        <button type="button" class="btn btn-primary-new size-sm pull-right" onclick="opencustomercategorymodel()"><i class="fa fa-plus"></i> <?php echo $this->lang->line('sales_maraketing_masters_create_category');?>  </button><!--Create Category-->
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="customer_category_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 2%">#</th>
            <th style="min-width: 40%"><?php echo $this->lang->line('common_description');?> </th><!--Description-->
            <th style="min-width: 2%"><?php echo $this->lang->line('common_action');?></th><!--Action-->
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div aria-hidden="true" role="dialog" tabindex="-1" id="customer_category_modal" class=" modal fade bs-example-modal-lg"
     style="display: none;">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title" id="categoryHead"></h5>
            </div>
            <?php echo form_open('', 'role="form" id="customer_category_form"'); ?>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" id="partyCategoryID" name="partyCategoryID">
                        <div class="form-group col-md-12">
                            <label><?php echo $this->lang->line('common_category');?> </label><!--Category-->
                            <input type="text" name="categoryDescription" id="categoryDescription" class="form-control">

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-primary " onclick="saveCategory()"> <i class="fa fa-plus"></i><?php echo $this->lang->line('common_save');?>
                        </button><!--Save-->
                        <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close');?> </button><!--Close-->
                    </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        customer_category_table_table();
    });

    function customer_category_table_table() {
        var Otable = $('#customer_category_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Customer/fetch_customer_category'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "partyCategoryID"},
                {"mData": "categoryDescription"},
                {"mData": "edit"}
            ],
            "columnDefs": [{"searchable": false, "targets": [0]}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                //aoData.push({ "name": "filter","value": $(".pr_Filter:checked").val()});
                //aoData.push({ "name": "subcategory","value": $("#subcategory").val()});
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }


    function saveCategory(){
        var data = $("#customer_category_form").serializeArray();
        $.ajax({
            async : true,
            type : 'post',
            dataType : 'json',
            data: data,
            url :"<?php echo site_url('Customer/saveCategory'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success : function(data){
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    customer_category_table_table();
                    $('#customer_category_modal').modal('hide');
                    $('#categoryDescription').val('');
                    $('#partyCategoryID').val('');
                }
            },error : function(){
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                myAlert(data[0], data[1]);
            }
        });
    }

    function editcustomercategory(partyCategoryID) {
        $.ajax({
            async : true,
            type : 'post',
            dataType : 'json',
            data : {'partyCategoryID':partyCategoryID},
            url :"<?php echo site_url('Customer/getCategory'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success : function(data){
                stopLoad();
                if (data) {
                    $('#categoryDescription').val(data['categoryDescription']);
                    $('#partyCategoryID').val(partyCategoryID);
                    $('#categoryHead').html('<?php echo $this->lang->line('sales_maraketing_masters_edit_category');?>');/*Edit Category*/
                    $('#customer_category_modal').modal('show');
                }
            },error : function(){
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function opencustomercategorymodel(){
        $('#partyCategoryID').val('');
        $('#categoryHead').html('<?php echo $this->lang->line('sales_maraketing_masters_add_new_category');?>');/*Add New Category*/
        $('#customer_category_form')[0].reset();
        $('#customer_category_modal').modal('show');
    }

    function delete_category(partyCategoryID){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('sales_maraketing_masters_you_want_to_delete_this_category');?>",/*You want to delete this Category!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>"/*Delete*/
            },
            function () {
                $.ajax({
                    async : true,
                    type : 'post',
                    dataType : 'json',
                    data : {'partyCategoryID':partyCategoryID},
                    url :"<?php echo site_url('Customer/delete_category'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            customer_category_table_table();
                        }
                    },error : function(){
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }
</script>