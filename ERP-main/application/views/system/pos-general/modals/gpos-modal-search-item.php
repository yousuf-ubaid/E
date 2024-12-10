<div aria-hidden="true" role="dialog" tabindex="-1" id="modal_search_item" data-keyboard="true" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close close-btn-pos" data-dismiss="modal" style="opacity: unset;">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </button>
                <h4 class="modal-title">
                    <i class="fa fa-search"></i> Search Item
                </h4>      
            </div>
            <div class="row" style="padding: 12px 5px 5px;">
                <div class="col-sm-2">
                    <span class="modal_search_item_label">keyword (Ctrl+F)</span>
                </div>
                <div class="col-sm-8">
                    <input type="text" class="form-control form-item-search-modal" id="searchKeyword" placeholder="Enter keyword to Search items ...." style="border-radius: 20px !important;" /><!--onkeyup="searchByKeyword()"-->
                </div>
                <div class="col-sm-2 pt-10">
                    <span id="loader_itemSearch" style="display: none;">
                    <i class="fa fa-refresh fa-spin fa-2x"></i></span>
                </div>
            </div>
            <hr style="margin: 10px;border: 1px solid #696CFF;">    
            <div class="modal-body" style="max-height: 500px;overflow: auto;padding-top: 0px;">
                <div id="result_output_searchItem"></div>
                <div>
                    <style>
                        #posg_search_item_modal.table-striped>tbody>tr:nth-of-type(odd) {
                            /*background-color: #ece9e9 !important;*/
                        }
                    </style>
                    <table class="table table-bordered table-hover table-striped" id="posg_search_item_modal">
                        <thead>
                        <tr style="background: #bdbbbb !important;">
<!--                            <th class="hidden">Image</th>-->
                            <th>Secondary Code</th>
                            <th>Barcode <i class="fa fa-barcode" aria-hidden="true"></i></th>
                            <th>Description</th>
                            <th>current stock</th>
                            <th>Price</th>
                            <th>UOM</th>
                            <th>&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody id="itemSearchResultTblBody">
                        <?php
                        $imgPath = base_url() . 'images/item/';
                        foreach ($items as $key => $rowItem) {
                            echo '<tr>
                                     <!-- <td class="hidden"><img src="' . $imgPath . '' . $rowItem['itemImage'] . '" style="max-width: 30px; max-height: 30px;" /></td> -->
                                     <td>' . $rowItem['seconeryItemCode'] . '</td>
                                     <td>' . $rowItem['itemDescription'] . '</td>
                                     <td>' . round($rowItem['currentStock'], 2) . '</td>
                                     <td>' . round($rowItem['companyLocalSellingPrice'], 2) . '</td>
                                     <td>' . $rowItem['defaultUnitOfMeasure'] . '</td>
                                     <td>
                                        <button type="button" onclick="loadToInvoiceList(\'' . $rowItem['barcode'] . '\')" class="btn btn-xs btn-default">
                                        <i class="fa fa - plus"></i> Add </button>
                                     </td>
                                  </tr>';
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" data-dismiss="modal" class="btn btn-block btn-danger-new size-sm btn-flat">
                    <i class="fa fa-angle-double-left" aria-hidden="true"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal image start-->
<div class="modal fade" id="getCodeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-md">
      <div class="modal-content">
       <div class="modal-header">
         <button type="button" class="close close-btn-pos" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
         <h4 class="modal-title" id="myModalLabel"> Item Preview </h4>
       </div>
       <div class="modal-body" id="getCode">
          
       </div>
    </div>
   </div>
 </div>
<!-- Modal image end-->

<script>
    function searchItem_modal() {
        pos_form[0].reset();
        pos_form.bootstrapValidator('resetForm', true);
        $('#is-edit').val('');
        $('#searchKeyword').val('');
        searchByKeyword();
        $("#modal_search_item").modal('show');

        setTimeout(function () {
            $("#searchKeyword").focus();
        }, 500);
    }

    $(document).ready(function() {
        $("#searchKeyword").keyup(function (e) {
            searchByKeyword(null,e);
        })
    });


    function searchByKeyword(initialSearch = null, e = null) {
        e = e || window.event;
        if (e != null && (e.keyCode != '38' && e.keyCode != '40' && e.keyCode != '13')) {
            // up arrow
            $('#is-edit').val('');
            var imgPath = '<?php echo base_url(); ?>images/item/';
            var customer_selected = $('#customerCode').val();

            pos_form.bootstrapValidator('resetForm', true);

            var keyword = (initialSearch == null) ? $("#searchKeyword").val() : '-';
            var urlReq = (initialSearch == null) ? "<?php echo site_url('Pos/item_search?q='); ?>"+ keyword +'&customer='+customer_selected  : "<?php echo site_url('Pos/item_initialSearch'); ?>"+'?customer='+customer_selected;
            if (keyword.trim() != '') {
                $.ajax({
                    async: true,
                    type: 'get',
                    dataType: 'json',
                    url: urlReq,
                    beforeSend: function () {
                        $("#itemSearchResultTblBody").html('');
                        $("#loader_itemSearch").show();
                        //startLoad();
                    },
                    success: function (data) {
                        $("#loader_itemSearch").hide();
                        $("#itemSearchResultTblBody").html('');
                        if (data == null || data == '') {

                        } else {

                            $.each(data, function (i, v) {
                                var num = parseFloat(v.currentStock);
                                var tr_data = '<tr><td>' + v.seconeryItemCode + '</td><td>' + v.barcode + '</td> <td>' + v.itemDescription + '</td> <td onclick="promptWareHousQty(\'' + v.barcode + '\')">' + num.toFixed(2) + '</td> <td>' + v.companyLocalSellingPrice + '<td>' + v.defaultUnitOfMeasure + '</td></td><td><button type="button" onclick="loadToInvoiceList(\'' + v.barcode + '\')" class="btn btn-xs btn-default"><i class="fa fa-plus"></i> Add </button></td></tr>';
                                $("#itemSearchResultTblBody").append(tr_data);
                            });
                        }

                    }, error: function (jqXHR, textStatus, errorThrown) {
                        $("#loader_itemSearch").hide();
                        if (jqXHR.status == false) {
                            myAlert('w', 'No Internet, Please try again');
                        } else {
                            myAlert('e', 'Message: ' + errorThrown);
                        }
                    }
                });

            } else {
                //$("#itemSearchResultTblBody").html('');
                

                $.ajax({
                    async: true,
                    type: 'get',
                    dataType: 'json',
                    url: '<?php echo site_url("Pos/itemLoadDefault"); ?>'+'?customer='+customer_selected,
                    beforeSend: function () {
                        $("#itemSearchResultTblBody").html('');
                        $("#loader_itemSearch").show();
                        //startLoad();
                    },
                    success: function (data) {
                        $("#loader_itemSearch").hide();
                        $("#itemSearchResultTblBody").html('');
                        if (data == null || data == '') {

                        } else {
                            $.each(data, function (i, v) {

                                var currentStock = (v.currentStock < 0) ? 0: v.currentStock;

                                var num = parseFloat(currentStock).toFixed(2);
                                var tr_data = '<tr><td>' + v.seconeryItemCode + '</td><td>' + v.barcode + '</td> <td>' + v.itemDescription + '</td><td>' + num + '</td><td style="text-align: right;">' + parseFloat(v.companyLocalSellingPrice).formatMoney(v.companyLocalCurrencyDecimalPlaces, ',', '.') + '</td> <td>' + v.defaultUnitOfMeasure + '</td><td class="td-inline p-xy-6"><a onclick="loadToInvoiceList(\'' + v.barcode + '\')" class="btn btn-xs btn-success set-p-pos"><i class="fa fa-plus"></i></a><a class="btn btn-xs btn-primary set-p-img set-p-pos"  onclick="get_item_master_image(\'' + v.itemAutoID + '\',\'' + v.itemImage + '\')"><i class="fa fa-eye"></i></a></td></tr>';
                                $("#itemSearchResultTblBody").append(tr_data);
                            });
                        }

                    }, error: function (jqXHR, textStatus, errorThrown) {
                        $("#loader_itemSearch").hide();
                        if (jqXHR.status == false) {
                            myAlert('w', 'No Internet, Please try again');
                        } else {
                            myAlert('e', 'Message: ' + errorThrown);
                        }
                    }
                });
            }
        }
    }

    function get_item_master_image(itemAutoID,itemImage){
               
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'itemAutoID': itemAutoID,
                'itemImage': itemImage
            },
            url: "<?php echo site_url('Pos/get_item_master_image'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                stopLoad();
                $("#getCodeModal").modal("show");
                $("#getCode").html(data).show();
            },
            error: function() {
                stopLoad();
            }
        });
    }

    function loadToInvoiceList(systemCode) {
        if(isGroupBasedTaxPolicy==1){
            localStorage.setItem('dataStoredTD','13');//this position has use to store and retrieve data in the table.
        }else{
            localStorage.setItem('dataStoredTD','11');//this position has use to store and retrieve data in the table.
        }
        $("#pos-add-btn").prop('disabled', false);
        item_search_loadToInvoice(systemCode);
    }

    $(document).on('keyup',function(evt) {
        if (evt.keyCode == 27) {
            if (typeof $('#print_template').data()['bs.modal'] !== "undefined" && $('#print_template').data()['bs.modal'].isShown) {
                close_posPrint();
            }
            if (typeof $('#customer_modal').data()['bs.modal'] !== "undefined" && $('#customer_modal').data()['bs.modal'].isShown) {
                $('#customer_modal').modal('hide')
            }
        }
    });
</script>