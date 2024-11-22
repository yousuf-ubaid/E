<?php
$c_rsctheme = $this->input->cookie('_rsctheme', TRUE);
if (isset($c_rsctheme)){
    switch ($c_rsctheme) {
        case "glass-theme":
            require_once('pos_restaurant-view1-modern.php');
            break;
        case "classic-theme":
            require_once('pos_restaurant-view1-modern.php');
            break;
        case "material-theme":
            require_once('pos_restaurant-view1-modern.php');
            break;
        case "the-life":
            require_once('pos_restaurant-view1-new-layout.php');
            break;
        default:
            require_once('pos_restaurant-view1-default.php');
    }
} else{
    require_once('pos_restaurant-view1-default.php');
}

