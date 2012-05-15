<?php
include 'settings.php';
?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Local Shopper</title>
    <link rel="stylesheet" href="css/index.css" type="text/css" />
</head>
<body>
<h1>Local Shopper</h1>
<form action="search.php">
    <label for="keywords">Search for:</label>
    <input id="keywords" type="text" name="keywords" disabled="disabled" />
    <input type="submit" value="Search" disabled="disabled" />
    <input id="latitude" type="hidden" name="latitude" value="" />
    <input id="longitude" type="hidden" name="longitude" value="" />
    <input id="location" type="hidden" name="location" value="" />
</form>
<p>We'll search these stores within <?php echo RADIUS ?> miles of <span id="address">your location</span>:</p>
<div id="spinner">Determining location...</div>
<ul id="stores">
</ul>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">
// On Back button, fields may still be enabled, so force them to be disabled again:
$("input").attr("disabled", "disabled");
$(document).ready(function() {
    var defaultLocation = {
        latitude: 37.8,
        longitude: -122.4
    };
    function gotStores(data) {
        for (var i = 0, len = data.store_addresses.length; i < len; i++) {
            address = data.store_addresses[i];
            $("#stores").append("<li>" + address.merchant_name + " (" + address.street + ", " + address.city + ")</li>");
        }
        $("#spinner").hide();
        $("#stores").show();
    }
    function showStores(location) {
        $("#spinner").text("Finding stores...");
        var params = {
                latitude: location.latitude,
                longitude: location.longitude,
                radius: 3,
                show: ["Mid"].join("")
            };
        var url = "ajax/store_addresses.php?" + $.param(params);
        $.getJSON(url, gotStores);
    }
    function setLocation(location) {
        if (locationTimeout) {
            window.clearTimeout(locationTimeout);
        }
        $("#latitude").val(location.latitude);
        $("#longitude").val(location.longitude);
        $("input").attr("disabled", null);
        var geocoder = new google.maps.Geocoder();
        var latlng = new google.maps.LatLng(location.latitude, location.longitude);
        geocoder.geocode({'latLng': latlng}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                if (results[1]) {
                    $("#address").text(results[1].formatted_address);
                    $("#location").val(results[1].formatted_address);
                    showStores(location);
                } else {
                    alert("No results found");
                }
            } else {
                alert("Geocoder failed due to: " + status);
            }
        });
    }
    var locationTimeout = null;
    if (navigator.geolocation) {
        locationTimeout = window.setTimeout(function() {
            setLocation(defaultLocation);
        }, 10000);
        navigator.geolocation.getCurrentPosition(function(position) {
            setLocation(position.coords);
        }, function() {
            setLocation(defaultLocation);
        });
    } else {
        setLocation(defaultLocation);
    }
});
</script>
</body>
</html>
