
<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_markating_approval', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = 'User Locations';
echo head_page($title, false);

/*echo head_page('Invoice Approval', false);*/ ?>
 <style>
        #map_canvas{
  height: 100%;
  width: 100%;
  margin: 0px;
  padding: 0px
  min-height:400px;
}
    </style>
<div id="filter-panel" class="collapse filter-panel"></div>


<div class="row">
    <div class="col-lg-8">
        <div id="map_canvas" style="border: 2px solid #3872ac;height: 500px;" ></div>
    </div>
    <div class="col-lg-4">
        <table class="table">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Vehicle #</th>
     <th scope="col">Distance</th>
      <th scope="col">Last Active</th>
      <th scope="col">Status</th>
    </tr>
  </thead>
  <tbody  id="user_list">
       
        </tbody>
</table>
    </div>

 

</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

<script>


</script>
  

