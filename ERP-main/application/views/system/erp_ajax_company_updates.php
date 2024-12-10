<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('dashboard', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>
<div class="box box-info">
    <div class="box-header with-border">
        <h4 class="box-title"><?php echo $this->lang->line('dashboard_company_updates');?></h4>
    </div>
    <div class="box-body" style="max-height: 300px; overflow-y: auto;">
        <ul class="products-list product-list-in-box">
            <?php
                if (!empty($datas)) {
                    $limitedData = array_slice($datas, 0, 10);
                    foreach ($limitedData as $data) {
                        ?>
                        <li class="item" style="display: flex; align-items: center;">
                            <div class="product-img" style="margin: 0; padding: 0;">
                                <i class="fa fa-bell-o" style="color: #ffa500; font-size: 16px;"></i>
                            </div>
                            <div class="product-info" style="display: flex; align-items: center; justify-content: space-between; width: 100%; margin-left: 8px;">
                                <div style="display: flex; align-items: center;">
                                    <a href="javascript:void(0)" onclick="createViews(<?php echo $data['id']; ?>)" class="product-title" data-id="<?php echo $data['id']; ?>" style="margin-right: 8px;">
                                        <?php echo $data['title']; ?>
                                    </a>
                                    <?php
                                        $first_word = explode(' ', trim($data['description'] ?? ''))[0];
                                    ?>
                                    <span class="product-description" style="margin-left: 8px;"><?php echo $first_word . '...'; ?></span>
                                </div>
                                <div style="text-align: right;">
                                    <span class="label label-warning"><?php echo isset($data['expiryDate']) ? $data['expiryDate'] : ''; ?></span>
                                    <div style="display: flex; align-items: center; justify-content: flex-end; margin-top: 4px;">
                                        <i class="glyphicon glyphicon-eye-open" style="color: #999; font-size: 12px; margin-right: 4px;"></i> 
                                        <span class="product-description" style="font-size: 12px; margin-right: 3px;">
                                            <?php echo isset($data['view_count']) ? $data['view_count'] : 0; ?> Views 
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <?php
                    }
                } else {
                    ?>
                        <li class="item"><?php echo $this->lang->line('dashboard_company_updates_not_available');?></li>
                    <?php
                }
            ?>
        </ul>
    </div>
</div>

<div class="modal fade" id="companyUpdatesDetailModal" tabindex="-1" role="dialog" aria-labelledby="companyUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 80%; width: 70%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title" id="companyUpdateModalLabel">Company Update Notification</h5>
            </div>
            <div class="modal-body" id='companyUpdatesDetail'>
               
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>

    function createViews(id){
        $.ajax({
            url: '<?php echo base_url("NotificationView/create"); ?>',
            type: 'POST',
            data: { id: id, documentID: 'CU' },
            success: function(response) {
                if (response.status === 's') {
                    }
            },
            error: function() {
                }
        });
        fetchUpdateDetails(id);
    }

    function fetchUpdateDetails(id) {
        $.ajax({
            url: '<?php echo base_url("NotificationView/getDetail"); ?>',
            type: 'POST',
            dataType: 'Html',
            data: { id: id, documentID: 'CU' },
            success: function(response) {
                $("#companyUpdatesDetail").html(response)
                $("#companyUpdatesDetailModal").modal('show')
            },
            error: function() {
                }
        });
    }

</script>
