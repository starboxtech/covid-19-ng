<?php 
header('Content-type: text/json');
require_once('../../../private/initialize.php');

// Build nigeriaGeoData
function nigeria_geo() {
    $state_cases = regions();
    
    $nigeria_geo_chart_data = '{"cols": [{"label": "States", "id": "States", "type": "string"},{"label": "Cases", "id": "Cases", "type": "number"}], "rows": [';
    foreach ($state_cases as $value) {
        $nigeria_geo_chart_data .= '{"c":[{"v":"' . strtoupper(str_replace('_','-',$value[3])) . '","f":"' . $value[0] . '"},{"v":' . ($value[1] === 0 ? 'null' : $value[1]) . ',"f":"' . ($value[1] === 0 ? '0' : $value[1]) . '"}]},' ;
    }
    $nigeria_geo_chart_data = rtrim($nigeria_geo_chart_data, ", ");
    $nigeria_geo_chart_data .= "]}";
    
    db_disconnect();

    return $nigeria_geo_chart_data;  
}
echo nigeria_geo();
?>