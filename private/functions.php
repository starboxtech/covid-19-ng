<?php
// General functions

function redirect_to($location) {
    header('Location: ' . $location);
    exit;
}

// User auth functions

function check_auth() {
    echo
    '<div class="check-auth">' . ($_SESSION['admin_username'] ?? "") . '<br>' . ($_SESSION['admin_name'] ?? "") . '<br>' . (date("H:i:s Y/m/d", $_SESSION['last_login']) ?? "") . '</div>';
}

function set_maintenance() {
    global $maintenance;
    if ($maintenance === 'on') {
        redirect_to(MAINTENANCE_PAGE);
    }
}

function show_session_message() {
    if (isset($_SESSION['message'])) {
        echo '
        <div class="alert alert-info mt-4 p-3 bordered bordered-dark bordered-all shadowed" role="alert">
        '. $_SESSION['message'] .'
        </div>
        ';
        unset($_SESSION['message']);
    }
}

function is_logged_in() {
    return isset($_SESSION['admin_username']);
}

function require_login() {
    if (!is_logged_in()) {
        redirect_to(ADMIN_LOGIN);
    }
}

function admin_logout() {
    log_user_event('logout');
    unset_admin_session();
    redirect_to(ADMIN_LOGIN);
}

function unset_admin_session() {
    unset($_SESSION['admin_username']);
    unset($_SESSION['admin_name']);
    unset($_SESSION['last_login']);
    unset($_SESSION['message']);
}

function show_admin_panel() {
    if (is_logged_in()) {
        echo '
        <form method="post" action=""
            class="bg-dark text-white align-items-center d-flex justify-content-between row no-gutters p-3 mt-4 shadowed bordered bordered-all">
            <div class="col">Hello <b>' . ($_SESSION['admin_name'] ?? "User") . '</b></div>
            <div class="col-sm-8 mt-3 mt-sm-0 btn-group" role="group">
                <a href="' . ADMIN_ROOT . '" class="btn btn-sm btn-outline-light">Dashboard</a>
                <a href="' . ADMIN_ROOT . '/update.php" class="btn btn-sm btn-outline-light">New Record</a>
                <a href="' . ADMIN_ROOT . '/log.php" class="btn btn-sm btn-outline-light">Logs</a>
                <a href="' . ADMIN_LOGOUT . '/logout.php" class="btn btn-sm btn-outline-light">Log Out</a>
            </div>
        </form>
        ';
    }
}

function log_in_admin($log_in_details) {
    global $db;

    $sql = "SELECT * FROM `admins` WHERE `username` = ";
    $sql .= "'" . mysqli_real_escape_string($db, $log_in_details['username']) . "'";
    $query_result = mysqli_query($db, $sql);
    $admin_details = mysqli_fetch_assoc($query_result);
    mysqli_free_result($query_result);
    
    if ($admin_details['username']) {
        $pass_verified = password_verify($log_in_details['password'], $admin_details['hashed_password']);
        if ($pass_verified) {
            session_regenerate_id();
            $_SESSION['admin_username'] = $admin_details['username'];
            $_SESSION['admin_name'] = split_name($admin_details['name'],'first');
            $_SESSION['last_login'] = time();
            log_user_event('login');
            redirect_to(ADMIN_ROOT);
        } else {
            log_user_event('login attempt');
            return 'Sorry, the email or password you entered is incorrect.';
        }
    } else {
        log_user_event('login attempt');
        return 'User not found.';
    }
}

function create_new_admin($new_admin) {
    global $db;

    $password_hash = password_hash($new_admin['password'], PASSWORD_BCRYPT);
    $new_admin['hashed_password'] = $password_hash;
    unset($new_admin['password']);
    
    $fields = '';
    foreach ($new_admin as $key => $value) {
        $fields .= "`" . $key . "`, ";
    }
    $fields = rtrim($fields, ", ");

    $values = '';
    foreach ($new_admin as $key => $value) {
        $values .= "'" .  mysqli_real_escape_string($db, $value) . "', ";
    }
    $values = rtrim($values, ", ");

    $sql = "INSERT into `admins` ( ";
    $sql .= $fields . " ) ";
    $sql .= "values ( " . $values . " ); ";

    $result = insert_into_db($sql);
    if ($result === true) {
        log_user_event('new user');
        return "New user account created successfully<br>Database successfully backed up."; 
    } else {
        return  $result;
    }
}

// Application data functions

function world_stats() {
    $world_stats = json_decode(file_get_contents('https://corona.lmao.ninja/v2/all'), true);
    return $world_stats;  
}

function get_ncdc_latest() {
    
    $curl = curl_init('https://covid19.ncdc.gov.ng/');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10); 
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    curl_setopt($curl, CURLOPT_NOSIGNAL, 1);
    $html = curl_exec($curl);
    curl_close($curl);

    // Requires php.ini edit to work
    // $html = file_get_contents('https://covid19.ncdc.gov.ng/');
    if (!empty($html)) {
        $dom = new DOMDocument();
        $dom->validateOnParse = true;
        $dom->loadHTML($html);
        // Get Cases table by ID
        $table = $dom->getElementById('custom1');
        
        if (!empty($table)) {
            foreach($table->getElementsByTagName('td') as $td) {
                $row = $td->textContent;
                // $output_html[] = explode("\n",$row);
                $output_html[] = $row;
            }
    
            // foreach ($output_html as $value) {
                //     $raw_cases[] = trim($value[1]);
                // }
                
            $raw_cases[0] = null;
            foreach ($output_html as $value) {
                $raw_cases[] = trim($value);
            }
    
            $i = 0;
            $y = 0;
            $j = 0;
            $cases = [];
            foreach ($raw_cases as $value) {
                if ($y == 0) {
                    $cases[$i][$y] = $value;
                } else {
                    $cases[$i][$y] = (int) filter_var(($value), FILTER_SANITIZE_NUMBER_INT);
                }
                $y++;
                if ($j % 5 == 0) {
                    $y = 0;
                    $i++;
                }
                $j++;
            }

            foreach ($cases as $value) {
                $total_con += $value[1];
                $total_act += $value[2];
                $total_dis += $value[3];
                $total_dea += $value[4];
            }

            $end_of_array = ['Total', $total_con, $total_act, $total_dis, $total_dea];
            array_push($cases, $end_of_array);

            unset($cases[0]);
            return $cases;
        }
    }

    $_SESSION['message'] .= '<div>There was an error connecting to the NCDC website.<br>Please try again later or input data manually.</div>';

    return null;
}

function search_for_cases($val, $array) {
    foreach ($array as $value) {
        if ($value[0] === $val) {
            return $value[1];
        }
    }
    return null;
}

function all_records() {
    global $db;
    
    $sql = "SELECT * FROM `ncdc_time_series` ";
    $sql .= "ORDER BY `ncdc_date` ";
    $query_result = mysqli_query($db, $sql);
    $result_all = mysqli_fetch_all($query_result, MYSQLI_ASSOC);
    mysqli_free_result($query_result);
    
    return $result_all;
}

function last_record() {
    global $db;
    
    $sql = "SELECT * FROM `ncdc_time_series` ";
    $sql .= "ORDER BY `ncdc_date` DESC ";
    $sql .= "LIMIT 1 ";
    $query_result = mysqli_query($db, $sql);
    $result = mysqli_fetch_assoc($query_result);
    mysqli_free_result($query_result);
    
    return $result;
}

function prev_day() {
    global $db;

    $sql = "SELECT * FROM `ncdc_time_series` ";
    // $sql .= "WHERE DATE(`ncdc_date`) >= subdate(current_date, 2) ";
    $sql .= "ORDER BY `ncdc_date` DESC ";
    $sql .= "LIMIT 1,1; ";
    $query_result = mysqli_query($db, $sql);
    $result = mysqli_fetch_assoc($query_result);
    mysqli_free_result($query_result);
    
    return $result;
}

function all_logs() {
    global $db;
    
    $sql = "SELECT * FROM `logs` ";
    $sql .= "ORDER BY `created_on` DESC ";
    $query_result = mysqli_query($db, $sql);
    $result_all = mysqli_fetch_all($query_result);
    mysqli_free_result($query_result);
    
    return $result_all;
}

function get_this_record($id) {
    global $db;
    
    $id = explode('-',$id);
    $sql = "SELECT * FROM `ncdc_time_series` ";
    $sql .= "WHERE `ng_confirmed` = $id[0] ";
    $sql .= "AND `ng_deaths` = $id[1] ";
    $sql .= "AND `ng_recovered` = $id[2] ";
    $sql .= "LIMIT 1 ";
    $query_result = mysqli_query($db, $sql);
    $result = mysqli_fetch_assoc($query_result);
    mysqli_free_result($query_result);
    
    if ($result['ng_confirmed'] == $id[0] && $result['ng_deaths'] == $id[1] &&$result['ng_recovered'] == $id[2]) {
        return $result;
    } else {
        return null;
    }
}

function new_ncdc_time_series_record($record) {
    global $db;

    $db_format = format_for_db($record, 'new');
    $sql = "INSERT into `ncdc_time_series` ( ";
    foreach ($db_format as $key => $value) {
        $sql .= "`" . $key . "`, ";
    }
    $sql = rtrim($sql, ", ");
    $sql .= " ) values ( ";
    foreach ($db_format as $key => $value) {
        $sql .= $value . ", ";
    }
    $sql = rtrim($sql, ", ");
    $sql .= " ) ";

    // return $sql;
    $result = insert_into_db($sql);
    if ($result === true) {
        log_user_event('new');
        return "<div>New record created<br>Database successfully backed up.</div>"; 
    }else {
        return  $result;
    }
}

function update_ncdc_time_series_record($record) {
    global $db;

    $db_format = format_for_db($record, 'update');

    $sql = "UPDATE `ncdc_time_series` SET ";
    foreach ($db_format as $key => $value) {
        $sql .= "`" . $key . "` = " . $value . ", ";
    }
    $sql = rtrim($sql, ', ');
    $sql .= " WHERE `ncdc_time_series_id` = '$record[ncdc_time_series_id]';";
    
    // return $sql;
    $result = insert_into_db($sql);
    if ($result === true) {
        log_user_event('edit');
        return "<div>Record edited<br>Database successfully backed up.</div>";
    }else {
        return $result;
    }
}

function delete_ncdc_time_series_record($record) {
    global $db;

    $sql = "DELETE FROM `ncdc_time_series` ";
    $sql .= "WHERE `ncdc_time_series_id` = $record[ncdc_time_series_id];";

    // return $sql;
    $result = insert_into_db($sql);
    if ($result === true) {
        log_user_event('delete');
        return "<div>Record deleted<br>Database successfully backed up.</div>"; 
    } else {
        return  $result;
    }
}

function insert_into_db($sql) {
    global $db;
    if (mysqli_query($db, $sql)) {
        backup_db();
        return true;
    } else {
        $error = "Error: " . $sql . "<br>" . mysqli_error($db);
        return $error;
    }    
    mysqli_close($db);
}

function backup_db() {
    exec(SQL_PATH . ' --single-transaction --user=' . DB_USER . ' --password=' . DB_PASS . ' --host=' . DB_HOST . ' ' . DB_NAME .' | gzip  >' . BACKUP_FILE . ' 2>&1', $output);

    return '<div>Database successfully backed up.</div>';
}

function get_ip_address(){
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
        if (array_key_exists($key, $_SERVER) === true){
            foreach (explode(',', $_SERVER[$key]) as $ip){
                $ip = trim($ip); 

                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                    return $ip;
                }
            }
        }
    }
}

// Format data functions

function format_for_db($record, $option = 'update') {
    global $db;

    // Set 'last_modified' stamp
    $record['last_modified_by'] = $_SESSION['admin_username'];
    $record['last_modified_on'] = fdate_db('now');

    // Set 'new record' stamp
    if ($option === 'new') {
        $record['created_by'] = $_SESSION['admin_username'];
        $record['created_on'] = fdate_db('now');
        unset($record['last_modified_by']);
        unset($record['last_modified_on']);
    }

    foreach ($record as $key => $value) {
        // Handle integers, default to zero, put in quotes... e.g. '0'
        if (substr($key,0,3) === 'ng_') {
            if ($value === '0' || $value === 0 || trim($value) == '') {
                $f_record[$key] = "'" . 0 . "'";
            } else {
                $f_record[$key] = "'" . $value . "'";
            }
        }
        // Handle text
        if (($key === 'created_by') || ($key === 'last_modified_by') || ($key === 'notes')) {
            $f_record[$key] = "'" . mysqli_real_escape_string($db, $value) . "'";
        }
        // Handle 'ncdc_date'
        if (isset($record['ncdc_date'])) {
            $f_record['ncdc_date'] = "STR_TO_DATE('" . $record['ncdc_date'] ."', '%Y/%m/%d %H:%i')";
        }
        // Handle other dates
        if (($key === 'created_on') || ($key === 'last_modified_on')) {
            $f_record[$key] = "STR_TO_DATE('" . $value ."', '%Y/%m/%d %H:%i:%s')";
        }
    }
    
    return $f_record;
}

function regions($param = null) {
    if (isset($param)) {
        $last_record = get_this_record($param);
    } else {
        $last_record = last_record();
    }
    $prev_day = prev_day();
    $regions = array(
        ['FCT', (int)$last_record['ng_fc'], (int)$prev_day['ng_fc'], 'ng_fc'],
        ['Abia', (int)$last_record['ng_ab'], (int)$prev_day['ng_ab'], 'ng_ab'],
        ['Adamawa', (int)$last_record['ng_ad'], (int)$prev_day['ng_ad'], 'ng_ad'],
        ['Akwa Ibom', (int)$last_record['ng_ak'], (int)$prev_day['ng_ak'], 'ng_ak'],
        ['Anambra', (int)$last_record['ng_an'], (int)$prev_day['ng_an'], 'ng_an'],
        ['Bauchi', (int)$last_record['ng_ba'], (int)$prev_day['ng_ba'], 'ng_ba'],
        ['Bayelsa', (int)$last_record['ng_by'], (int)$prev_day['ng_by'], 'ng_by'],
        ['Benue', (int)$last_record['ng_be'], (int)$prev_day['ng_be'], 'ng_be'],
        ['Borno', (int)$last_record['ng_bo'], (int)$prev_day['ng_bo'], 'ng_bo'],
        ['Cross River', (int)$last_record['ng_cr'], (int)$prev_day['ng_cr'], 'ng_cr'],
        ['Delta', (int)$last_record['ng_de'], (int)$prev_day['ng_de'], 'ng_de'],
        ['Ebonyi', (int)$last_record['ng_eb'], (int)$prev_day['ng_eb'], 'ng_eb'],
        ['Edo', (int)$last_record['ng_ed'], (int)$prev_day['ng_ed'], 'ng_ed'],
        ['Ekiti', (int)$last_record['ng_ek'], (int)$prev_day['ng_ek'], 'ng_ek'],
        ['Enugu', (int)$last_record['ng_en'], (int)$prev_day['ng_en'], 'ng_en'],
        ['Gombe', (int)$last_record['ng_go'], (int)$prev_day['ng_go'], 'ng_go'],
        ['Imo', (int)$last_record['ng_im'], (int)$prev_day['ng_im'], 'ng_im'],
        ['Jigawa', (int)$last_record['ng_ji'], (int)$prev_day['ng_ji'], 'ng_ji'],
        ['Kaduna', (int)$last_record['ng_kd'], (int)$prev_day['ng_kd'], 'ng_kd'],
        ['Kano', (int)$last_record['ng_kn'], (int)$prev_day['ng_kn'], 'ng_kn'],
        ['Katsina', (int)$last_record['ng_kt'], (int)$prev_day['ng_kt'], 'ng_kt'],
        ['Kebbi', (int)$last_record['ng_ke'], (int)$prev_day['ng_ke'], 'ng_ke'],
        ['Kogi', (int)$last_record['ng_ko'], (int)$prev_day['ng_ko'], 'ng_ko'],
        ['Kwara', (int)$last_record['ng_kw'], (int)$prev_day['ng_kw'], 'ng_kw'],
        ['Lagos', (int)$last_record['ng_la'], (int)$prev_day['ng_la'], 'ng_la'],
        ['Nasarawa', (int)$last_record['ng_na'], (int)$prev_day['ng_na'], 'ng_na'],
        ['Niger', (int)$last_record['ng_ni'], (int)$prev_day['ng_ni'], 'ng_ni'],
        ['Ogun', (int)$last_record['ng_og'], (int)$prev_day['ng_og'], 'ng_og'],
        ['Ondo', (int)$last_record['ng_on'], (int)$prev_day['ng_on'], 'ng_on'],
        ['Osun', (int)$last_record['ng_os'], (int)$prev_day['ng_os'], 'ng_os'],
        ['Oyo', (int)$last_record['ng_oy'], (int)$prev_day['ng_oy'], 'ng_oy'],
        ['Plateau', (int)$last_record['ng_pl'], (int)$prev_day['ng_pl'], 'ng_pl'],
        ['Rivers', (int)$last_record['ng_ri'], (int)$prev_day['ng_ri'], 'ng_ri'],
        ['Sokoto', (int)$last_record['ng_so'], (int)$prev_day['ng_so'], 'ng_so'],
        ['Taraba', (int)$last_record['ng_ta'], (int)$prev_day['ng_ta'], 'ng_ta'],
        ['Yobe', (int)$last_record['ng_yo'], (int)$prev_day['ng_yo'], 'ng_yo'],
        ['Zamfara', (int)$last_record['ng_za'], (int)$prev_day['ng_za'], 'ng_za']
    );
    return $regions;
}

function no_of_states() {
    $states = regions();
    $i = 0;
    foreach ($states as  $value) {
        if ($value[1] === 0) {
           continue;
        }
        $i++;
    }
    $i = ($i - 1) . ' + FCT';
    return $i;
}

function country_confirmed_diff() {
    global $last_record;
    global $prev_day;
    $result = $last_record['ng_confirmed'] - $prev_day['ng_confirmed'];
    return $result;
}

function country_recovered_diff() {
    global $last_record;
    global $prev_day;
    $result = $last_record['ng_recovered'] - $prev_day['ng_recovered'];
    return $result;
}

function country_deaths_diff() {
    global $last_record;
    global $prev_day;
    $result = $last_record['ng_deaths'] - $prev_day['ng_deaths'];
    return $result;
}

function country_active_diff() {
    global $last_record;
    global $prev_day;
    $result = $last_record['ng_active'] - $prev_day['ng_active'];  
    return $result;
}

function percent_deaths() {
    global $last_record;
    $result = round(($last_record['ng_deaths'] / $last_record['ng_confirmed']) * 100, 1);
    $result .= '%';
    return $result;
}

function percent_recovered() {
    global $last_record;
    $result = round(($last_record['ng_recovered'] / $last_record['ng_confirmed']) * 100, 1);
    $result .= '%';
    return $result;
}

function percent_active() {
    global $last_record;
    $result = round(($last_record['ng_active'] / $last_record['ng_confirmed']) * 100, 1);
    $result .= '%';
    return $result;
}

function disp_region_if_cases() {
    $regions = regions();
    foreach ($regions as $region) {
        if ($region[1] > 0) {
            $result[] = [$region[0], $region[1], $region[2]];
        } 
    }
    usort($result, function($a, $b) {
        return $b[1] <=> $a[1];
    });

    return $result;
}

function fdate_index() {
    global $last_record;

    $result = new DateTime($last_record['ncdc_date']); 
    $result = $result -> sub(new DateInterval('PT1H')); // Offset back to UTC
    $result = $result -> format(DATE_ISO8601);
    return $result;
}

function fdate_datepicker($date) {
    $result = new DateTime($date); 
    $result = $result -> format('Y/m/d H:i');
    return $result;
}

function fdate_words($date) {
    $result = new DateTime($date); 
    $result = $result -> format('j M Y H:i');
    return $result;
}

function fdate_db($date) {
    $result = new DateTime($date); 
    $result = $result -> format('Y/m/d H:i:s');
    return $result;
}

function show_diff($param, $color = 'warning') {
    $result = null;
    if ($param > 0) {
        if ($param > 1000) {
            $param = round(($param/1000),1);
            $param .= 'k';
        }
        $result = '<span class="badge badge-' . $color . '">+' . $param . '</span>';
    } elseif ($param < 0) {
        if ($param < -1000) {
            $param = round(($param/1000),1);
            $param .= 'k';
        }
        $result = '<span class="badge badge-success">' . $param . '</span>';
    }
    return $result;
}

function split_name($full_name, $options = null) {
    $full_name_array = explode(" ", $full_name);

    $first = $full_name_array[0];
    if (count($full_name_array) > 1) {
        $last = end($full_name_array);
        if (count($full_name_array) > 2)  {
            $middle = implode(' ', array_slice($full_name_array, 1, -1));
        }
    }

    switch ($options) {
        case 'first':
            return $first;
            break;

        case 'middle':
            return $middle;
            break;

        case 'last':
            return $last;
            break;

        case 'first last':
            return $first . ' ' . $last;
            break;
        case 'full':
            return $first . ' ' . $middle . ' ' . $last;
            break;
        case 'array':
            return array('first' => ($first ?? ''), 'middle' => ($middle ?? ''), 'last' => ($last ?? ''));
            break;
        default:
            return $first;
            break;
    }
}

function log_user_event($event = null){
    global $db;
    $user = $_SESSION['admin_username'] ?? "unknown";
    $ip_address = get_ip_address() ?? "";

    // Check session duration
    if (isset($_SESSION['last_login'])) {
        $timestamp = '@' . $_SESSION['last_login'];
    } else {
        $timestamp = 'now';
    }
    $date1 = new DateTime ($timestamp);
    $date2 = new DateTime('now');
    $diff = $date2->diff($date1);
    $date_diff = $diff->format('%h hrs %i mins');

    switch ($event) {
        case 'login':
            $event = 'User logged in';
            break;
        case 'login attempt':
            $event = 'Unsuccessful login attempt';
            break;
        case 'logout':
            $event = 'User logged out. Session duration ' . $date_diff;
            break;
        case 'new':
            $event = 'User added a new record to NCDC Time Series';
            break;
        case 'edit':
            $event = 'User updated a record in NCDC Time Series';
            break;
        case 'delete':
            $event = 'User deleted a record from NCDC Time Series';
            break;
        case 'new user':
            $event = 'User created a new admin user';
            break;
        default:
            $event = 'Unknown event';
            break;
    }
    $log = array('ip_address' => $ip_address, 'user' => $user, 'event' => $event);
    
    $fields = '`created_on`, ';
    foreach ($log as $key => $value) {
        $fields .= "`" . $key . "`, ";
    }
    $fields = rtrim($fields, ", ");

    $values = "STR_TO_DATE('" . mysqli_real_escape_string($db, fdate_db('now')) ."', '%Y/%m/%d %H:%i:%s'), ";
    foreach ($log as $key => $value) {
        $values .= "'" .  mysqli_real_escape_string($db, $value) . "', ";
    }
    $values = rtrim($values, ", ");

    $sql = "INSERT into `logs` ( ";
    $sql .= $fields . " ) ";
    $sql .= "values ( " . $values . " ); ";
    
    // return $sql;
    insert_into_db($sql);
}