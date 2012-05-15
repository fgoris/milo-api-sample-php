<?php
include 'settings.php';
$url = "https://api.x.com/milo/v3/products?key=" . API_KEY;
$url .= "&product_ids=" . urlencode($_GET['id']);
$url .= "&show=DescImg200";
$api_response = file_get_contents($url);
if ($api_response) {
    $results = json_decode($api_response, true);
    if (count($results['products']) > 0) {
        $product = $results['products'][0];
    } else {
        $product = array(
            'name' => 'Invalid Product ID'
        );
    }
} else {
    $product = array(
        'name' => 'Error'
    );
}
?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Local Shopper : <?php echo $product['name']; ?></title>
    <link rel="stylesheet" href="css/common.css" type="text/css" />
    <link rel="stylesheet" href="css/product.css" type="text/css" />
</head>
<body>
<h1>Local Shopper : <?php echo $product['name']; ?></h1>
<?php
if (isset($product['description'])) {
?>
<div id="main">
    <div id="pimg" class="container top">
        <img src="<?php echo $product['image_200']; ?>" />
    </div>
    <div id="pdesc" class="container bottom">
        <?php echo $product['description']; ?>
    </div>
    <h2 id="avail_title" class="container top">Availability</h2>
    <div id="spinner" class="container bottom">Checking availability...</div>
    <div id="avail" class="container bottom">
        <table>
            <tr>
                <th>&nbsp;</th>
                <th>Price</th>
                <th>Store</th>
                <th>Address</th>
            </tr>
        </table>
    </div>
</div>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript">
var merchants = {},
    locations = {},
    availabilities = {};
var inStockCount = 0;
function centsToDollars(cents) {
    var dollars = '' + cents / 100;
    if (cents % 100 == 0) {
        dollars += '.00';
    } else if (cents % 10 == 0) {
        dollars += '0';
    }
    return dollars;
}
function newMerchant(merchant) {
    merchants[merchant.id] = merchant;
}
function newLocation(location) {
    locations[location.id] = location;
}
function newAvailability(result) {
    var merchant = merchants[result.merchant_id];
    var store = locations[result.location_id];
    var availability = result.availability;
    if ((availability == 'limited') || (availability == 'in_stock')) {
        availability = 'In Stock';
        if (!availabilities[result.location_id] || (availabilities[result.location_id] != 'In Stock')) {
            inStockCount++;
            $("#avail_title").text("Available at " + inStockCount + " store" + (inStockCount > 1 ? 's' : '') + " near you.");
        }
    } else if ((availability == 'never') || (availability == 'out_of_stock')) {
        availability = 'Out of Stock';
    } else {
        availability = 'Call Store';
    }
    if (availabilities[result.location_id]) {
        $("#av" + result.location_id).text = availability;
    } else {
        var newRow = $('<tr><td id="av' + result.location_id + '" class="av">' + availability + '</td><td class="price">$' + centsToDollars(result.price) + '</td><td class="name">' + merchant.name + '</td><td class="addr">' + store.street + ", " + store.city + "</td></tr>");
        $("#avail table tr:last").after(newRow);
    }
    availabilities[result.location_id] = availability;
    $("#spinner").hide();
    $("#avail").show();
}
function handleAvailability(data) {
    if (data.merchant) {
        newMerchant(data.merchant);
    } else if (data.location) {
        newLocation(data.location);
    } else if (data.result) {
        newAvailability(data.result);
    }
}
$(document).ready(function() {
    var params = {
        product_id: <?php echo $_GET['id']; ?>,
        callback: 'top.handleAvailability',
        latitude: <?php echo $_GET['latitude']; ?>,
        longitude: <?php echo $_GET['longitude']; ?>,
        radius: <?php echo RADIUS; ?>
    };
    var url = "ajax/availability.php?" + $.param(params);
    var iframe = $('<iframe class="hidden"></iframe>');
    $('body').append(iframe);
    iframe.attr('src', url);
});
</script>
<?php
}
?>
</body>
</html>
