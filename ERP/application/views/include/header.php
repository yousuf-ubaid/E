<?php defined('BASEPATH') OR exit('No direct script access allowed');
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('footer', $primaryLanguage);
$companyInfo = get_companyInfo();
$productID = $companyInfo['productID'] ?? null;
if ($productID == 2) {
    $theme = 'skin-blue-dark skin-blue';
} else {
    $theme = 'skin-blue-dark skin-blue';
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $title; ?></title>
    <link rel="icon" href="<?php echo base_url().'favicon.ico'; ?>" type="image/x-icon"/>
    <link rel="shortcut icon" href="<?php echo base_url().'favicon.ico'; ?>" type="image/x-icon"/>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap/css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/font-awesome/css/font-awesome.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/jvectormap/jquery-jvectormap-1.2.2.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/dist/css/AdminLTE.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/dist/css/custom1.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/dist/css/skins/_all-skins.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/animate/animate.css'); ?>"/>
    <link rel="stylesheet" href="<?php echo base_url('plugins/iCheck/all.css'); ?>"/>
    <link rel="stylesheet" href="<?php echo base_url('plugins/themify-icons/themify-icons.css'); ?>"/>
    <link rel="stylesheet"
          href="<?php echo base_url('plugins/datetimepicker/build/css/bootstrap-datetimepicker.css'); ?>"/>
    <link rel="stylesheet" href="<?php echo base_url('plugins/tapmodo-Jcrop-1902fbc/css/jquery.Jcrop.min.css'); ?>"/>

    <!--Bootstrap Country flag-->
    <link rel="stylesheet" href="<?php echo base_url('plugins/country_flag/flags.css'); ?>"/>

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script src="<?php echo base_url('plugins/jQuery/jQuery-2.1.4.min.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/bootstrap/js/bootstrap.min.js'); ?>"></script>
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
    <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>
</head>
<?php
$bar_top = '';
$side_bar = get_cookie('SIDE_BAR');
if (isset($side_bar)) {
    $bar_top = $side_bar;
}

//side bar mode change dark/light
$sidebar_mode = 'light-mode';
$side_barMode = get_cookie('SIDE_BAR_MODE');
if (isset($side_barMode)) {
    $sidebar_mode = $side_barMode;
} else{
    $sidebar_mode;
}
?>
<style type="text/css">
    .dataTable_selectedTr {
        background-color: rgb(138 98 124 / 20%) !important;
    }
    .progressbr {
        height: 5px !important;
        margin-bottom: 0 !important;;
    }
    /*Access Denied modal*/
    .fade-scale {
        transform: scale(0);
        opacity: 0;
        -webkit-transition: all .25s linear;
        -o-transition: all .25s linear;
        transition: all .25s linear;
    }
    .fade-scale.in {
        opacity: 1;
        transform: scale(1);
    }
</style>
<?php $map_key = $this->config->item('google_map_key');?>
<!-- <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $map_key; ?>&callback=initialize" async defer></script> -->

<script>

var map = null;
var gmarkers = [];
var intervalNumber = 0;

var rad = function(x) {
  return x * Math.PI / 180;
};

var getDistance = function(l1, l2,l3,l4) {
  var R = 6378137; // Earth’s mean radius in meter
  var dLat = rad(l3 - l1);
  var dLong = rad(l4 - l2);
  var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
    Math.cos(rad(l1)) * Math.cos(rad(l3)) *
    Math.sin(dLong / 2) * Math.sin(dLong / 2);
  var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  var d = R * c;
  return d; // returns the distance in meter
};

function initialize() {
    var mapElement = document.getElementById('map_canvas');

    if (mapElement) {
        // initialize the map on page load.
        var mapOptions = {
            center: new google.maps.LatLng(7.8774, 80.7003),
            zoom: 12,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        map = new google.maps.Map(mapElement,
        mapOptions);

        // add the markers to the map if they have been loaded already.
        if (gmarkers.length > 0) {
            for (var i = 0; i < gmarkers.length; i++) {
                gmarkers[i].setMap(map);
            }
        }
    }
}

///=========================
function update_map(data) {
    if(data){
        var bounds = new google.maps.LatLngBounds();
        // delete all existing markers first
        for (var i = 0; i < gmarkers.length; i++) {
            gmarkers[i].setMap(null);
        }
        gmarkers = [];

        $('#user_list').html('');
        let a=[];
        // add new markers from the JSON data
        for (var i = 0, length = data.length; i < length; i++) {

            let firstlat=(data[i].lat_group).split(',').shift();
            let firstlng=(data[i].lng_group.split(',')).shift();

            let j=i+1;
        
            if( ! a.includes(data[i].device_id) ){
                a.push(data[i].device_id);

                $('#user_list').append(`
                    <tr><td>${i+1}</td>
                    <td>${data[i].device_id}</td>
                    <td>${(j*50) /1000} km</td>
                    <td>${data[i].created_date}</td> 
                    <td>${data[i].is_online}</td>
                `);
            }

            const image="https://cdn.pixabay.com/photo/2013/07/12/11/58/car-145008_1280.png";      
            const icon = {
                url: image, // url
                scaledSize: new google.maps.Size(20, 25), // scaled size
                origin: new google.maps.Point(0,0), // origin
                anchor: new google.maps.Point(0, 0) // anchor
            };

            latLng = new google.maps.LatLng(data[i].lat, data[i].lng);
            bounds.extend(latLng);
            var marker = new google.maps.Marker({
                position: latLng,
                map: map,
                title: data[i].title,
                icon:icon
            });
            var infoWindow = new google.maps.InfoWindow();
            google.maps.event.addListener(marker, "click", function (e) {
                infoWindow.setContent(data.description+"<br>"+marker.getPosition().toUrlValue(6));
                infoWindow.open(map, marker);
            });
            (function (marker, data) {
                google.maps.event.addListener(marker, "click", function (e) {
                    infoWindow.setContent(data.description+"<br>"+marker.getPosition().toUrlValue(6));
                    infoWindow.open(map, marker);
                });
            })(marker, data[i]);
            gmarkers.push(marker);
        }

        // zoom the map to show all the markers, may not be desirable.
        map.fitBounds(bounds);
    }
}

setInterval(function () {
    var mapElement = document.getElementById('map_canvas');
    if (mapElement) {
        $.ajax({
            url: "<?php echo site_url('Tracking/load_user_location'); ?>",
            global: false ,
            success: function (response) {
                update_map(response);
                intervalNumber++;
            }
        });
    }
    else{
        }
}, 10000);

//================================

$(document).ajaxComplete(function(){
    var mapElement = document.getElementById('map_canvas');
    if (mapElement) {
        initialize();
        var lock=0;
    }
});
</script>

<body class="sidebar-mini fixed hold-transition  <?php echo $theme.' '.$bar_top.' '.$sidebar_mode ?>">
