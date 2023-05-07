<?php
include 'TableFromJson.class.php';

 $url = 'https://tehila-designs.com/xxx/gas-stations.json';
 $table = new TableFromJson($url);
?>

<!DOCTYPE html>
<html dir="rtl" lang="he">
	<head>
		<title>תחנות דלק בב"ש</title>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Assistant:400,500,600,700?v=17" />
		<link type="text/css" rel="stylesheet" href="style.css?v=1" />						
	</head>

    <body>
        <form action="index.php" method="get">
        מילות חיפוש: 
        <input type="text" value="<?php echo htmlspecialchars($_GET['searchtext']); ?>" name="searchtext"> 
        <input type="submit" name="form_submit" value="חפש"><br><br>

        </form>

        <?php
            $search_input = $_GET['searchtext'];
            $table_titles = ['כתובת', 'שם תחנה', 'חברת דלק'];
            echo $table->build_table($table_titles, $search_input);
        ?>

        <!--MAP-->
        <div id="map"></div>

        <script>
        var center = {lat: 31.25857868665641, lng: 34.79623124164612};
        var locations = <?php echo $table->location_array($search_input); ?>;
        </script>

        <script type="text/javascript" src="index.js"></script>
        <script async defer src="https://maps.googleapis.com/maps/api/js?key=xxx&callback=initMap"></script>
    </body>
</html>

