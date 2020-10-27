<?php 
require_once('../../private/initialize.php');
$page_title = 'New Record - ' . $page_title_default;

// Authorized page access
require_login() ;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancel'])) {
    admin_logout();
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ncdc_date']) && trim($_POST['ncdc_date']) !== '') {
    $new_record = $_POST;
    $_SESSION['message'] = new_ncdc_time_series_record($new_record);
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['message'] = '<div>Please set the \'last updated\' date</div>';
}

$last_record = last_record();
$cases = get_ncdc_latest();

?>

<!doctype html>
<html lang="en">

<!-- html header -->
<?php include(SHARED_PATH . '/head.php') ?>

<body class="flex-page">
    <!-- navigation -->
    <?php include(SHARED_PATH . '/nav.php') ?>

    <main class="boxed w-100">
        <?php show_admin_panel() ?>
        <?php show_session_message() ?>
        <h4 class="spaced">Add New Record</h4>

        <form method="post" action="">

            <div class="form-group my-4">
                <input class="form-control bordered bordered-dark bordered-all p-3" id="datetimepicker" type="text"
                    name="ncdc_date" placeholder="Last updated" autocomplete="off">
            </div>

            <!-- States-->
            <div class="card bordered bordered bordered-all shadowed">
                <table class="table table-borderless table-update table-theme">
                    <thead>
                        <tr>
                            <th scope="col">States</th>
                            <th scope="col">Current</th>
                            <th scope="col">New</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $regions = regions();
                        usort($regions, function($a, $b) {
                            return $b[1] <=> $a[1];
                        });

                        // New NCDC Data
                        $i = 0;
                        foreach ($regions as $region) {
                            $regions[$i][4] = search_for_cases($region[0], $cases);
                            $i++;
                        }

                        foreach ($regions as $value) : ?>
                        <tr>
                            <td><?= $value[0] ?></td>
                            <td><input class="form-control" type="number" readonly
                                    placeholder="<?= $last_record[$value[3]] ?>">
                            </td>
                            <td><input class="form-control calc-add" type="number" min="0" name="<?= $value[3] ?>"
                                    value="<?= $value[4] ?? $last_record[$value[3]] ?>"></td>
                        </tr>
                        <?php endforeach; ?>



                    </tbody>
                </table>
            </div> <!-- end States -->

            <!-- Country Total -->
            <div class="card spaced bordered bordered bordered-all shadowed">
                <table class="table table-borderless table-update table-theme">
                    <thead>
                        <tr>
                            <th scope="col">Nigeria</th>
                            <th scope="col">Current</th>
                            <th scope="col">New</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table-warning">
                            <td>Total</td>
                            <td><input class="form-control" type="number" readonly
                                    value="<?= $last_record['ng_confirmed'] ?>">
                            </td>
                            <td><input id="confirmed-total" class="form-control" type="number" readonly min="0" value=""
                                    name="ng_confirmed"></td>
                        </tr>
                        <tr class="table-danger">
                            <td>Deaths</td>
                            <td><input class="form-control" type="number" readonly
                                    value="<?= $last_record['ng_deaths'] ?>"></td>
                            <td><input class="form-control calc-sub" type="number" min="0" name="ng_deaths"
                                    value="<?= end($cases)[4] ?? $last_record['ng_deaths'] ?>"></td>
                        </tr>
                        <tr class="table-success">
                            <td>Discharged</td>
                            <td><input class="form-control" type="number" readonly
                                    value="<?= $last_record['ng_recovered'] ?>"></td>
                            <td><input class="form-control calc-sub" type="number" min="0" name="ng_recovered"
                                    value="<?= end($cases)[3] ?? $last_record['ng_recovered'] ?>"></td>
                        </tr>
                        <tr class="table-secondary">
                            <td>Active</td>
                            <td><input class="form-control" type="number" readonly
                                    value="<?= $last_record['ng_active'] ?>"></td>
                            <td><input id="active-total" class="form-control" type="number" readonly min="0" value=""
                                    name="ng_active">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div> <!-- end Country Total -->

            <div class="form-group spaced shadowed bordered bordered bordered-all">
                <textarea class="form-control bordered bordered bordered-all" id="notes" name="notes" rows="4"
                    placeholder="Notes"></textarea>
            </div>

            <div class="form-group spaced">
                <div class="row no-gutters justify-content-between">
                    <button type="submit" class="btn btn-success col-md-5 my-2 bordered bordered-all">Create</button>
                    <button type="submit" name="cancel"
                        class="btn btn-danger col-md-5 my-2 bordered bordered-all">Cancel and Log
                        Out</button>
                </div>
            </div>

        </form>
    </main>

    <!-- Footer -->
    <?php include(SHARED_PATH . '/footer.php') ?>



    <!-- Page scripts -->
    <!-- Datetimepicker -->
    <?php include(SHARED_PATH . '/page_scripts.php') ?>
    <link rel="stylesheet" type="text/css" href="<?= DIST_ROOT . '/datetimepicker/jquery.datetimepicker.min.css' ?>" />
    <script src="<?= DIST_ROOT . '/datetimepicker/jquery.datetimepicker.full.min.js' ?>"></script>
    <script>
        // Calculate totals
        $(document).ready(function () {
            // Calc cases
            var total = 0;
            $(".calc-add").each(function () {
                if (!isNaN(parseInt($(this).val()))) {
                    total += parseInt($(this).val());
                }
            });
            $("#confirmed-total").val(total);

            var activeTotal = 0;
            $(".calc-sub").each(function () {
                if (!isNaN(parseInt($(this).val()))) {
                    activeTotal += parseInt($(this).val());
                }
            });
            var confirmedTotal = 0;
            $(".calc-add").each(function () {
                if (!isNaN(parseInt($(this).val()))) {
                    confirmedTotal += parseInt($(this).val());
                }
            });
            grandTotal = confirmedTotal - activeTotal;
            $("#active-total").val(grandTotal);

            // Update calculation on input
            $(".calc-add").on("input", function () {
                var total = 0;
                $(".calc-add").each(function () {
                    if (!isNaN(parseInt($(this).val()))) {
                        total += parseInt($(this).val());
                    }
                });
                $("#confirmed-total").val(total);
            });
            // Calc active cases
            $(".calc-sub").on("input", function () {
                var activeTotal = 0;
                $(".calc-sub").each(function () {
                    if (!isNaN(parseInt($(this).val()))) {
                        activeTotal += parseInt($(this).val());
                    }
                });
                var confirmedTotal = 0;
                $(".calc-add").each(function () {
                    if (!isNaN(parseInt($(this).val()))) {
                        confirmedTotal += parseInt($(this).val());
                    }
                });
                grandTotal = confirmedTotal - activeTotal;
                $("#active-total").val(grandTotal);
            });

        })
        // Datetimepicker
        jQuery('#datetimepicker').datetimepicker();
    </script>
</body>

</html>