<?php 
header('Content-type: text/json');
require_once('../../../private/initialize.php');

// Format date objects for JSON
function date_chart_format($record_date) {
    // Instantiate DateTime objects and current year
    $date_time = new DateTime($record_date);
    $now_date_time = new DateTime('now');
    // Value
    $date_value = $date_time ->format('Y m d');
    $date_value = explode(' ', $date_value);
    // Format month for javascript
    $date_value[1] = $date_value[1] - 1;
    // Format text to display
    $date_format = $date_time ->format('d M Y');
    $date_format = explode(' ', $date_format);
    // Remove year if in current year
    if (strcmp($date_format[2], $now_date_time -> format('Y')) === 0) {
        $date_format[2] = '';
    }
    $date = [$date_value, $date_format];
    
    return $date;
}

// Build nigeriaGeoData
function nigeria_stats() {
    // Build array of values
    $all_records = all_records();
    foreach ($all_records as $record) {
        $nigeria_stats_chart_data[] = [
            date_chart_format($record['ncdc_date']),
            ($record['ng_confirmed'] == 0 ? 'null' : $record['ng_confirmed']),
            ($record['ng_deaths'] == 0 ? 'null' : $record['ng_deaths']),
            ($record['ng_recovered'] == 0 ? 'null' : $record['ng_recovered']),
            $record['ncdc_date']
        ];

    }
    // Build data string
    $nigeria_stats_string = '{"cols": [{"label": "Timeline", "id": "Timeline", "type": "date"},{"label": "Confirmed", "id": "Confirmed", "type": "number"},{"label": "Deaths", "id": "Deaths", "type": "number"},{"label": "Discharged", "id": "Recovered", "type": "number"}], "rows": [';
    foreach ($nigeria_stats_chart_data as $value) {
        $nigeria_stats_string .= '{"c":[{"v":"Date(' . $value[0][0][0] . ',' . $value[0][0][1] . ',' . $value[0][0][2] . ')"' . ',"f":"' . $value[0][1][0] . ' ' . $value[0][1][1] . ' ' . $value[0][1][2] . '"},{"v":' . $value[1] . ',"f":null},{"v":' . $value[2] . ',"f":null},{"v":' . $value[3] . ',"f":null}]},' ;
    }
    $nigeria_stats_string = rtrim($nigeria_stats_string, ', ');
    $nigeria_stats_string .= ']';
    $nigeria_stats_string .= '}';

    // Build hAxis format string
    $h_axis[0] = $nigeria_stats_chart_data[0][4];
    $middle= ceil(count($nigeria_stats_chart_data)/2);
    $h_axis[1] = $nigeria_stats_chart_data[$middle][4];
    $h_axis[2] = end($nigeria_stats_chart_data)[4];
    $start_date = new DateTime($h_axis[0]);
    $start_date_v = $start_date -> format('Y-m-d');
    $start_date_f = $start_date -> format('M Y');
    $middle_date = new DateTime($h_axis[1]);
    $middle_date_v = $middle_date -> format('Y-m-d');
    $middle_date_f = $middle_date -> format('M Y');
    $end_date = new DateTime($h_axis[2]);
    $end_date_v = $end_date -> format('Y-m-d');
    $end_date_f = $end_date -> format('M Y');
    
    db_disconnect(); 
    
    return json_encode(
        array(
            'Data' => $nigeria_stats_string,
            'startDateV' => $start_date_v,
            'startDateF' => $start_date_f,
            'middleDateV' => $middle_date_v,
            'middleDateF' => $middle_date_f,
            'endDateV' => $end_date_v,
            'endDateF' => $end_date_f
        )
    );
}
echo nigeria_stats();
?>