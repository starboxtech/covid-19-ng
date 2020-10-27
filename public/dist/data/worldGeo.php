<?php 
header('Content-type: text/json');

// Build worldGeoData
function world_geo() {
    $world_countries = json_decode(file_get_contents('https://corona.lmao.ninja/v2/countries'), true);
    // return $world_countries;

    $world_geo_chart_data = '{"cols": [{"label": "Countries", "id": "Countries", "type": "string"},{"label": "Cases per million", "id": "Cases", "type": "number"},{"label": "Tooltip","type": "string","role": "tooltip","p": {"html": true}}], "rows": [';
    foreach ($world_countries as $value) {
        $world_geo_chart_data .= '{"c":[{"v":"' . $value['countryInfo']['iso2'] . '","f":"' . $value[country] . '"},{"v":' . $value['casesPerOneMillion'] . ',"f":null},{"v":"'.
             '<span>Confirmed:</span> <span>' . $value['cases'] . '</span><br>'  .
             '<span>Deaths: </span> <span>' . $value['deaths'] . '</span><br>'  .
             '<span>Discharged: </span> <span>' . $value['recovered'] . '</span><br>'  .
             '<span>Active: </span> <span>' . $value['active'] . '</span><br>'  .
             '","f":null}]},' ;
    }
    $world_geo_chart_data = rtrim($world_geo_chart_data, ", ");
    $world_geo_chart_data .= "]}";
    
    return $world_geo_chart_data;  
}
// print_r(world_geo());
echo world_geo();
?>