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

    .contact-box .align-left {
        float: left;
        margin: 0 7px 0 0;
        padding: 2px;
        border: 1px solid #ccc;
    }

    img {
        vertical-align: middle;
        border: 0;
        -ms-interpolation-mode: bicubic;
    }

    .headrowtitle {
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

    .actionicon {
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
</style>
<?php
if (!empty($prDetails)) { ?>
    <div class="table-responsive">
        <table id="prq_list_table" class="<?php echo table_class(); ?>">
            <thead>
                <tr>
                        <th>#</th>
                        <th>Doc Number</th>
                        <th>Date</th>
                        <th>Narration</th>
                        <th>Exp Delivery Date</th>
                        <th>Requester</th>
                        <th>Value</th>
                        <th>Action</th>
                </tr>
            </thead>
            <?php
            $x = 1;
            foreach ($prDetails as $val) {
                ?>
                <tr>
                    <td class="mailbox-name">
                        <?php echo $x; ?>
                    </td>
                    <td class="mailbox-name" style="min-width: 150px">
                        <?php echo $val['purchaseRequestCode']; ?>
                    </td>
                    <td class="mailbox-name" style="min-width: 150px">
                        <?php echo $val['documentDate']; ?>
                    </td>
                    <td class="mailbox-name" style="min-width: 150px">
                        <?php echo $val['narration']; ?>
                    </td>
                    <td class="mailbox-name" style="min-width: 150px">
                        <?php echo $val['expectedDeliveryDate']; ?>
                    </td>
                    <td class="mailbox-name" style="min-width: 150px">
                        <?php echo $val['requestedByName']; ?>
                    </td>
                    <td align="right" class="mailbox-name" style="min-width: 150px;">
                        <?php echo $val['transactionAmount']; ?>
                    </td>
                    <td width="5%">
                        <!--<div class="skin skin-square">
                            <div class="skin-section extraColumns"><input
                                    id="supplier_<?php //echo $val['id'] ?>" type="checkbox"
                                    data-caption="" class="columnSelected supplieritem_checkbox"
                                    name="" onclick="assign_checklist_selected_check(this)"
                                    value="<?php // echo $val['id'] ?>"><label for="checkbox">&nbsp;</label>
                            </div>
                        </div>-->
                    </td>
                </tr>
                <?php
                $x++;
            }
            ?>

            </tbody>
        </table><!-- /.table -->
    </div>
    <?php
} else { ?>
    <br>
    <div class="search-no-results">THERE ARE NO CHECKLIST TO DISPLAY.</div>
    <?php
}
?>

<div class="table-responsive">
    <table id="jobs_table" class="<?php echo table_class(); ?>">
        <thead>
            <tr>
                <th style="width: 5%">#</th>

                <th style="width: 15%">Doc Number</th>

                <th style="width: 20%">Date</th>
                
                <th style="width: 12%">Narration</th>
                
                <th style="width: 20%">Ref Number</th>                
                
                <th style="width: 5%">Status</th>
                
                <th style="width: 8%"><?php echo $this->lang->line('common_action'); ?></th>
                <!--Action-->
            </tr>
        </thead>
    </table>
</div>

<script type="text/javascript">
    function load_order_review_srm(){

        Otable = $('#jobs_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "sAjaxSource": "<?php echo site_url('srm_master/fetch_order_review_srm'); ?>",
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
                {"mData": "inquiryID"},
                {"mData": "documentCode"},
                {"mData": "documentDate"},
                {"mData": "narration"},
                {"mData": "referenceNumber"},
                {"mData": "isRfqSubmitted"},
                {"mData": "action"}
            ],
            "columnDefs": [
            
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                //aoData.push({"name": "datefrom", "value": $("#IncidateDateFrom").val()});
            
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
</script>