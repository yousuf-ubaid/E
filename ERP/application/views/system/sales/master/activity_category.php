<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_maraketing_masters_customer_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = 'Activity Category';
echo head_page($title, true);


?>


<form id="mfq_itemCategory" method="post">
    <input type="hidden" value="4" name="categoryType">
    <div class="row">
        <div class="form-group col-sm-3">
            <label class="title"><?php echo 'Category Name' ?><!--Master Field--> </label>
        </div>
        <div class="form-group col-sm-3">
        <span class="input-req" title="Required Field">
            <input type="text" name="description" id="master_field_description" class="form-control" required>
            <span class="input-req-inner"></span>
        </span>
        </div>

        <div class="form-group col-sm-3">
            <button type="button" onclick="save_mainCategory()" class="btn btn-primary btn-sm"><i
                    class="fa fa-plus"></i>
                <?php echo $this->lang->line('common_add') ?><!--Add-->
            </button>
        </div>
    </div>
</form>


<div class="treeContainer" style="min-height: 200px;">
    <!--via ajax -->
</div>

<div id="add_feild_well" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="field_well_title">Add</h5>
                </div>
                <form role="form" id="field_well_id" class="form-horizontal">
                    <div class="modal-body">
                        <input type="hidden" id="filed_well_id" name="filed_well_id" value="" />
                        <input type="hidden" id="filed_well_type" name="filed_well_type" value="" />
                        <input type="hidden" id="action" name="action" value="" />
                        <input type="text" id="filed_well_name" name="filed_well_name" class="form-control" value="" />
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default"
                                    type="button"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                        <button class="btn btn-primary" type="button"
                                onclick="saveFieldWellDetails()"><?php echo $this->lang->line('common_save_change'); ?>
                    </div>
                </form>
            </div>
        </div>
</div>



<script>

    $(document).ready(function () {
        load_jobsmaster_category();
    });


    function load_jobsmaster_category() {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Jobs/get_jobs_rigs_hoist'); ?>",
            data: {categoryType: 4},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $(".treeContainer").empty();
                $(".treeContainer").html(data);

            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;
    }

    function save_mainCategory(){

        var master_field = $('#master_field_description').val();

        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Jobs/save_jobs_master_field'); ?>",
            data: {'master_field_name': master_field,'type' : 4},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                refreshNotifications(true);
                load_jobsmaster_category();
                

            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });

    }

    function saveFieldWellDetails(){
        var data = $('#field_well_id').serializeArray();

        
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Jobs/save_fields_well'); ?>",
            data: data,
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                refreshNotifications(true);
                load_jobsmaster_category();
                $('#add_feild_well').modal('hide');

            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }


    function delete_modal(id,level,name){
        
        var data;

        swal({
            title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
            text: "Do you want to proceed",/*You want to delete this customer!*/
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Confirm",/*Delete*/
            cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
        },
        function () {
            
            $.ajax({
                async: true,
                type: 'post',
                //dataType: 'json',
                data: {id:id,level:level},
                url: "<?php echo site_url('Jobs/delete_field_well'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    load_jobsmaster_category();
                }, error: function () {
                    stopLoad();
                    //swal("Cancelled", "Your file is safe :)", "error");
                }
            });
            
        });

    }

</script>