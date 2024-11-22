<?php

    $primaryLanguage = getPrimaryLanguage();
    $this->lang->load('sales_maraketing_masters_customer_master', $primaryLanguage);
    $this->lang->load('common', $primaryLanguage);
    $title = 'Jobs Field / Well';
    echo head_page($title, true);

?>

<form id="mfq_itemCategory" method="post">
    <input type="hidden" value="1" name="categoryType">
    <div class="row">
        <div class="form-group col-sm-3">
            <label class="title"><?php echo 'Master Field' ?><!--Master Field--> </label>
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

<div class="modal fade" id="add_feild_well" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-md" role="document">
        <form role="form" id="field_well_id">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                                <h5 class="modal-title" id="field_well_title">Add</h5>
                </div>
                
                        <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <input type="hidden" id="filed_well_id" name="filed_well_id" value="" />
                                            <input type="hidden" id="filed_well_type" name="filed_well_type" value="" />
                                            <input type="hidden" id="action" name="action" value="" />                        

                                            <div class="form-group">
                                                <label>Well Name  </label>
                                                <input type="text" id="filed_well_name" name="filed_well_name" class="form-control" value="" />
                                            </div>
                                            <div class="form-group" id="well-type">
                                                <label>Well Type  </label>
                                                <input type="text" name="well_type_op" id="well_type_op" class="form-control">
                                            </div>
                                            <div class="form-group" id="well-no">
                                                <label>Well No.</label>
                                                <input type="text" name="well_no_op" id="well_no_op" class="form-control" value="">
                                            </div>
                                        </div>    
                                    </div>
                        </div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default-new size-sm"
                                        type="button"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                            <button class="btn btn-primary-new size-sm" type="button"
                                    onclick="saveFieldWellDetails()"><?php echo $this->lang->line('common_save_change'); ?>
                        </div>
                
            </div>
        </form>
    </div>
</div>



<script>
    $(document).ready(function () {
        load_jobsmaster_category();
    });

    function save_mainCategory(){

        var master_field = $('#master_field_description').val();

        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Jobs/save_jobs_master_field'); ?>",
            data: {'master_field_name': master_field},
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

    function load_jobsmaster_category() {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Jobs/get_jobs_master_fileds'); ?>",
            data: {categoryType: 1},
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