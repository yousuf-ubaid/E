<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
/*$title = $this->lang->line('transaction_add_new_material_issue');
echo head_page($title, false);*/

echo head_page($_POST['page_name'], false);
?>

<div class="form-group">
    <button class="btn btn-danger" onclick="call_the_fn()">Make adjusment</button>
    <button class="btn btn-danger" onclick="call_the_adjusment()">Do Adjusment</button>
    <button class="btn btn-danger" onclick="call_the_adjusment2()">Do Adjusment Step 2</button>
    <button class="btn btn-danger" onclick="call_the_adjusment3()">Do Adjusment Step 3</button>
</div>


<script>

  function call_the_fn(){

    var data;
    
    $.ajax(
    {
        async: false,
        type: 'post',
        dataType: 'json',
        data: data,
        url: "<?php echo site_url('Pos/make_the_adjusment_batch'); ?>",
        beforeSend: function () {
            startLoad();
        },
        success: function (data) {
            HoldOn.close();
            refreshNotifications(true);
           
        },
        error: function () {
            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            HoldOn.close();
            refreshNotifications(true);
        }
    });

  }

  function call_the_adjusment(){
        var data;
        
        $.ajax(
        {
            async: false,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Pos/make_the_adjusment'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                HoldOn.close();
                refreshNotifications(true);
            
            },
            error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                HoldOn.close();
                refreshNotifications(true);
            }
        });
  }

  function call_the_adjusment2(){
        var data;
        
        $.ajax(
        {
            async: false,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Pos/make_the_adjusment_step2'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                HoldOn.close();
                refreshNotifications(true);
            
            },
            error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                HoldOn.close();
                refreshNotifications(true);
            }
        });
  }

  function call_the_adjusment3(){
        var data;
        
        $.ajax(
        {
            async: false,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Pos/make_the_adjusment_step3'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                HoldOn.close();
                refreshNotifications(true);
            
            },
            error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                HoldOn.close();
                refreshNotifications(true);
            }
        });
  }

</script>