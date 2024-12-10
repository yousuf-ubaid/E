<?php
$primaryLanguage = getPrimaryLanguage();
$this->load->helper('boq_helper');
echo head_page('Edit Checklist', false);
$checkListmasterID = $this->input->post('page_id');

?>
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>">
<style>
    #emp {
    . TriSea-technologies-Switch > label:: after
    }

    @media (max-width: 767px) {
        #currency-div {
        / / float: left !important;
        }
    }

    .TriSea-technologies-Switch > input[type="checkbox"] {
        display: none;
    }

    .TriSea-technologies-Switch > label {
        cursor: pointer;
        height: 0px;
        position: relative;
        width: 40px;
    }

    .TriSea-technologies-Switch > label::before {
        background: rgb(0, 0, 0);
        box-shadow: inset 0px 0px 10px rgba(0, 0, 0, 0.5);
        border-radius: 8px;
        content: '';
        height: 12px;
        margin-top: -8px;
        position: absolute;
        opacity: 0.3;
        transition: all 0.4s ease-in-out;
        width: 32px;
    }

    .TriSea-technologies-Switch > label::after {
        background: #00a65a;
        border-radius: 16px;
        box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.3);
        content: '';
        height: 20px;
        left: -4px;
        margin-top: -8px;
        position: absolute;
        top: -4px;
        transition: all 0.3s ease-in-out;
        width: 20px;
    }

    .TriSea-technologies-Switch > input[type="checkbox"]:checked + label::before {
        background: inherit;
        opacity: 0.5;
    }

    .TriSea-technologies-Switch > input[type="checkbox"]:checked + label::after {
        background: inherit;
        left: 20px;
    }

</style>
<div class="row" style="margin: 1%">
    <ul class="nav nav-tabs" id="main-tabs">
        <li class="active"><a href="#headerinformation" onclick="headerinformation(<?php echo $checkListmasterID?>)" data-toggle="tab">Header Information</a></li>
        <li><a href="#detailinformation" data-toggle="tab" onclick="detailinformation(<?php echo $checkListmasterID?>)">Detail Information</a></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane active" id="headerinformation">
            <div id="headerinformationview"></div>
        </div>

        <div class="tab-pane" id="detailinformation">
            <div id="detailinformationview"></div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>
<script type="text/javascript">
    var checklistID = null;
    $(document).ready(function () {
        $(".select2").select2();
        $('.headerclose').click(function () {
            fetchPage('system/pm/checklist_master', '', 'Project');
        });

        checklistID = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        headerinformation(checklistID)
    });
    function headerinformation(checklistID)
    {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'checklistID': checklistID},
            url: "<?php echo site_url('Boq/fetch_headerinformation'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#headerinformationview').html(data);

                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function detailinformation(checklistID) {
        var headerID = $('#headerID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'checklistID': checklistID},
            url: "<?php echo site_url('Boq/feth_checklist_detailtemp'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#detailinformationview').html(data);

                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function update_checkliststatus(checklistID,val) {
        var activestatus =  ($("#isactive").is(':checked'))?0: 1;

        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Boq/update_activestatuscheklist'); ?>",
            data: {id: checklistID, value: activestatus},
            cache: false,
            beforeSend: function () {
               startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0],data[1])
                if (data[0] == 's') {
                    headerinformation(checklistID)
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function save_checklistdetail()
    {
        var data = $('#checklist_detailform').serializeArray();
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Boq/update_checklistDetail'); ?>",
            data: data,
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0],data[1])
                if (data[0] == 's') {
                    headerinformation(checklistID)
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });

    }
    function generate_noofrows(checklistID) {
        var noofrows = $('#noofrows').val();
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Boq/update_critiriadetil'); ?>",
            data: {'checklistID': checklistID,'noofrows':noofrows},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0],data[1])
                if (data[0] == 's') {
                    detailinformation(checklistID)
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function update_checklist_isheader(criteriaID,val) {
        var activestatus =  ($("#istitle_"+criteriaID).is(':checked'))?0: 1;
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Boq/update_critertia_headerstatus'); ?>",
            data: {id: criteriaID, value: activestatus},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0],data[1])
                if (data[0] == 's') {
                    detailinformation(checklistID)
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function add_new_update_critiriadetil(checklistID) {
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Boq/add_new_update_critiriadetil'); ?>",
            data: {'checklistID':checklistID},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0],data[1])
                if (data[0] == 's') {
                    detailinformation(checklistID);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function delete_criteriadetail(criteriaID) {

        swal({
                title: "Are you sure?",
                text: "You want to delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete"
            },
            function () {
                $.ajax({
                    type: 'POST',
                    dataType: 'JSON',
                    url: "<?php echo site_url('Boq/delete_criteriadetail'); ?>",
                    data: {'criteriaID':criteriaID},
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0],data[1])
                        if (data[0] == 's') {
                            detailinformation(checklistID);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        myAlert('e', '<br>Message: ' + errorThrown);
                    }
                });
            });

    }

</script>