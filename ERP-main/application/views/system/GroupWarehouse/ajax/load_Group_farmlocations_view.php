<style>
    #cTable tbody tr.highlight td {
        background-color: #96ff74;
    }
</style>

<table id="cTable" class="table " style="width: 100%">
    <thead>
    <tr>
    <!--    <th style="width: 200px">#</th>-->
        <th style="width: 150px">Area</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php
    if ($area) {
        foreach ($area as $main) {
            ?>
            <tr class="subheader">
                <td><i class="fa fa-minus-square" aria-hidden="true"></i> <?php echo $main['mainArea'] ?></td>
                <td><span class="pull-right">
                        <a onclick="load_duplicate_area(<?php echo $main['groupLocationID'] ?>)"><span title="Replicate" rel="tooltip"class="glyphicon glyphicon-duplicate"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                        <a onclick="link_group_area(<?php echo $main['groupLocationID'] ?>)"><span title="Link" rel="tooltip" class="glyphicon glyphicon-link" ></span></a>&nbsp;|&nbsp;
                          <a onclick="create_group_sub_area(<?php echo $main['groupLocationID'] ?>)"><span title="Create Sub Area" rel="tooltip" class="fa fa-plus"></span></a> &nbsp;&nbsp;|&nbsp;&nbsp;
                        <a onclick="edit_group_main_area(<?php echo $main['groupLocationID'] ?>, '<?php echo $main['mainArea'] ?>')"><span title="Edit Area" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>
                       <!-- <a onclick="edit_chart_of_accont(<?php /*echo $main['groupLocationID'] */?>)"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>-->
                    </span>
                </td>
            </tr>
            <?php
            if ($subArea) {
                foreach ($subArea as $sub) {
                    if ($sub['masterID'] == $main['groupLocationID']) {
                        ?>
                        <tr class="subdetails">
                            <td style="padding-left: 60px"><?php echo $sub['subArea'] ?></td>
                            <td>
                                <span class="pull-right">
                                    <a onclick="load_duplicate_area(<?php echo $sub['groupLocationID']?>, <?php echo $sub['masterID']?>)"><span title="Replicate" rel="tooltip"class="glyphicon glyphicon-duplicate"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                                      <a onclick="link_group_sub_area(<?php echo $sub['groupLocationID']?>, <?php echo $sub['masterID']?>)"><span title="Link" rel="tooltip" class="glyphicon glyphicon-link" ></span></a>&nbsp;|&nbsp;
                                      <a onclick="edit_group_sub_area(<?php echo $sub['groupLocationID'] ?>, <?php echo $sub['masterID'] ?>, '<?php echo $sub['subArea'] ?>')"><span title="Edit Area" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>
                                </span>
                            </td>
                        </tr>
                        <?php
                    }
                }
            }
        }
    }
    ?>
    </tbody>
</table>

<script>

    $('#cTable').on('click', 'tr', function (e) {
        $('#cTable').find('tr.highlight').removeClass('highlight');
        $(this).addClass('highlight');
    });


    function highlightSearch(searchtext) {
        $('#cTable tr').each(function () {
            $(this).removeClass('highlight');
        });
        if(searchtext !==''){
            $('#cTable tr').each(function () {
                if ($(this).find('td').text().toLowerCase().indexOf(searchtext.toLowerCase()) == -1) {

                    $(this).removeClass('highlight');
                }
                else {
                    $(this).addClass('highlight');
                }
            });
        }
    }


</script>