<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('communityNgo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('CommunityNgo_com_families');
echo head_page($title, false);

/*echo head_page('Donor Commitments', false);*/
$date_format_policy = date_format_policy();
?>

    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/crm_style.css'); ?>">
    <style>
        .search-no-results {
            text-align: center;
            background-color: #f6f6f6;
            border: solid 1px #ddd;
            margin-top: 10px;
            padding: 1px;
        }

        .label {
            display: inline;
            padding: .2em .8em .3em;
        }
        .actionicon{
            display: inline-block;
            font-weight: normal;
            font-size: 12px;
            background-color: #89e68d;
            -moz-border-radius: 2px;
            -khtml-border-radius: 2px;
            -webkit-border-radius: 2px;
            border-radius: 2px;
            padding: 2px 5px 2px 5px;
            line-height: 14px;
            vertical-align: text-bottom;
            box-shadow: inset 0 -1px 0 #ccc;
            color: #888;
        }
        .headrowtitle{
            font-size: 11px;
            line-height: 30px;
            height: 30px;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 0 25px;
            font-weight: bold;
            text-align: left;
            text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.3);
            color: rgb(130, 130, 130);
            background-color: white;
            border-top: 1px solid #ffffff;
        }
        .task-cat-upcoming {
            border-bottom: solid 1px #f76f01;
        }

        .task-cat-upcoming-label {
            display: inline;
            float: left;
            color: #f76f01;
            font-weight: bold;
            margin-top: 5px;
            font-size: 15px;
        }

        .taskcount {
            display: inline-block;
            font-weight: normal;
            font-size: 12px;
            background-color: #eee;
            -moz-border-radius: 2px;
            -khtml-border-radius: 2px;
            -webkit-border-radius: 2px;
            border-radius: 2px;
            padding: 1px 3px 0 3px;
            line-height: 14px;
            margin-left: 8px;
            margin-top: 9px;
            vertical-align: text-bottom;
            box-shadow: inset 0 -1px 0 #ccc;
            color: #888;
        }
        .numberColoring{
            font-size: 13px;
            font-weight: 600;
            color: saddlebrown;
        }
    </style>

    <div id="filter-panel" class="collapse filter-panel">
    </div>
    <div class="row">
        <div class="col-md-5">
        </div>
        <div class="col-md-4 text-center">
            &nbsp;
        </div>
        <div class="col-md-3 text-right">
            <button type="button" class="btn btn-primary pull-right"
                    onclick="fetchPage('system/communityNgo/ngo_mo_familyCreate',null,'<?php echo $this->lang->line('CommunityNgo_add_new_family');?>'/*Add New Family*/,'NGO');"><i
                    class="fa fa-plus"></i> <?php echo $this->lang->line('CommunityNgo_create_family');?><!--Create Family-->
            </button>
        </div>
    </div>
    <div class="row" style="margin-top: 2%;">
        <div class="col-sm-4" style="margin-left: 2%;">

            <div class="col-sm-12">
                <div class="box-tools">
                    <div class="has-feedback">
                        <input name="searchTask" type="text" class="form-control input-sm"
                               placeholder="<?php echo $this->lang->line('CommunityNgo_search_by_leader');?>, <?php echo $this->lang->line('CommunityNgo_family');?>  "
                               id="searchTask" onkeypress="startMasterSearch()"><!--Search by leader--><!--leader-->
                        <span class="glyphicon glyphicon-search form-control-feedback"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-7">
            <div class="col-sm-1 hide" id="search_cancel">
                    <span class="tipped-top"><a id="cancelSearch" href="#" onclick="clearSearchFilter()"><img
                                src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive mailbox-messages" id="familyMaster_view">
                <!-- /.table -->
            </div>

        </div>
    </div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
    <script type="text/javascript">
        var Otable;
        $(document).ready(function () {
            $('.headerclose').click(function () {
                fetchPage('system/communityNgo/ngo_mo_createFamilyMem','','Donor Commitments');
            });
            load_family_commitments('#', 1);
            //getNgoFamilyMasterTable();

        });

        function referback_family_creation(FamMasterID){
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'FamMasterID': FamMasterID},
                url: "<?php echo site_url('CommunityNgo/referback_family_creation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    myAlert(data[0],data[1]);
                    load_family_commitments();
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        $('#searchTask').bind('input', function(){
            startMasterSearch();
        });

        function getNgoFamilyMasterTable(filtervalue) {
            var searchTask = $('#searchTask').val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'q': searchTask ,'filtervalue':filtervalue},
                url: "<?php echo site_url('CommunityNgo/load_familyMasterView'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#familyMaster_view').html(data);
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function delete_commitment_project(id) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'commitmentAutoId': id},
                        url: "<?php echo site_url('CommunityNgo/delete_commitment_project'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            refreshNotifications(true);
                            stopLoad();
                            getNgoFamilyMasterTable();
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }

        function startMasterSearch() {
            $('#search_cancel').removeClass('hide');
            getNgoFamilyMasterTable();
        }

        function clearSearchFilter() {
            $('#search_cancel').addClass('hide');
            $('#searchTask').val('');
            $('#sorting_1').addClass('selected');
            getNgoFamilyMasterTable();
        }

        function load_family_commitments(value, id){

            $('#sorting_'+ id).addClass('selected');
            if(value != '#'){
                $('#search_cancel').removeClass('hide');
            }
            getNgoFamilyMasterTable(value)
        }


    </script>
<?php
