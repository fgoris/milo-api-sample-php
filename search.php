<?php
include 'settings.php';
?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Local Shopper : <?php echo $_GET['keywords']; ?></title>
    <link rel="stylesheet" href="css/common.css" type="text/css" />
    <link rel="stylesheet" href="css/search.css" type="text/css" />
</head>
<body>
<h1>Local Shopper : Results for "<?php echo $_GET['keywords']; ?>"<?php
if (isset($_GET['location'])) {
    echo " near {$_GET['location']}";
}
?></h1>
<div id="main">
<?php
flush();
$url = "https://api.x.com/milo/v3/products?key=" . API_KEY;
$url .= "&q=" . urlencode($_GET['keywords']);
$url .= "&latitude=" . $_GET['latitude'];
$url .= "&longitude=" . $_GET['longitude'];
$url .= "&radius=" . RADIUS;
$url .= "&show=Img100";
$api_response = file_get_contents($url);
if ($api_response) {
    $results = json_decode($api_response, true);
    $pagination = $results['pagination'];
    $products = $results['products'];
?>
    <p id="summary" class="container top">We found <?php echo $pagination['total_results']; ?> results (showing <?php
    if ($pagination['total_pages'] == 1) {
        echo "all";
    } else {
        echo "first {$pagination['per_page']}";
    }
?>):</p>
    <ul id="results" class="container bottom">
<?php
    for ($i = 0, $len = count($products); $i < $len; $i++) {
        $product = $products[$i];
?>
        <li class="product"><img src="<?php echo $product['image_100']; ?>" /><a class="title" href="product.php?id=<?php echo $product['product_id']; ?>&latitude=<?php echo $_GET['latitude']; ?>&longitude=<?php echo $_GET['longitude']; ?>"><?php echo $product['name']; ?></a></li>
<?php
    }
?>
    </ul>
<?php
} else {
?>
    <p class="container error">Sorry, an error occurred.</p>
<?php
}
?>
</div>
</body>
</html>
