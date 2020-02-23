<?php require('simple_html_dom.php'); ?>

<html>
<h1>Zoek</h1>
<p>Zoek een product of reeks producten</p>
<form method='GET' action=''>
    <input type='text' id='searchbox' name='searchbox' placeholder='Wat wil je zoeken?'>
    <button type="submit">Zoek!</button>
</form>

</html>



<?php
if(!empty($_GET['searchbox'])) {
    echo "<h1>Je hebt gezocht naar " . $_GET['searchbox'] . "</h1>";
    $zoekterm = str_replace(' ', '%20', $_GET['searchbox']);
    $html = file_get_html('https://www.dodax.de/nl-nl/search/f-cd-QQQ6F29MQA/?s='.$zoekterm.'&m=mostrelevant');
    $extensions = array("nl", "de", "fr", "co.uk", "at", "es", "it", "ch", "pl", "com", "ca", "co.jp");
    echo "
    <table style='width:100%'>
    <tr>
    <th align='center'>Afbeelding</th>
    <th align='left'>Naam Item</th>
    <th align='left'>Type Item</th>";
    foreach($extensions as $extension) {
        if($extension == "co.uk") {
            $extension = "uk";
        }
        if($extension == "co.jp") {
            $extension = "jp";
        }
        if($extension == "com") {
            $extension = "us";
        }
        echo "<th align='left'>Prijs ". strtoupper($extension) ."</th>";
    }
    echo "</tr>";
    $itemPrices = array();
    foreach($html->find('div.product') as $item) {
        $itemdetails = $item->find('a.js-product', 0);
        $itemurl = $itemdetails->href;
        $itemtitel = $itemdetails->find('p.product_title', 0);
        $itemafbeelding = $item->find('img.img-fluid', 0);
        $itemafbeeldingurl = $itemafbeelding->src;
        $itemtype = $itemdetails->find('p.product_type', 0);
        $i = 0;
        foreach($extensions as $extension) {
            $itempage = file_get_html('https://www.dodax.'.$extension.$itemurl);
            $itemprice = $itempage->find('span.current_price', 0);
            if(empty($itemprice)) {
                $itemprice = "Uitverkocht";
            }
            $itemPrices[$i]["imageurl"] = $itemafbeeldingurl;
            $itemPrices[$i]["url"] = 'https://www.dodax.'.$extension.$itemurl;
            $itemPrices[$i]["price"] = $itemprice;
            $i++;
        }
        echo "<tr>
        <td align='center'><img src=".$itemafbeeldingurl." height='100' width='100'/></td>
        <td>".$itemtitel."</td>
        <td>".$itemtype."</td>";
        for($i = 0; $i < sizeof($extensions); $i++) {
            echo "<td><a href=".$itemPrices[$i]["url"].">
            ".$itemPrices[$i]["price"]."</a></td>";
        }
        echo "</tr>";
    }
    echo "</table>";
}

?>