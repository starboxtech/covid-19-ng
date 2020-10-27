<?php 
require_once('../../private/initialize.php');
$page_title = 'Edit Record - ' . $page_title_default;

// Authorized page access
require_login() ;

// Get record & Redirect if 'No Record Found'
$record = get_this_record($_GET['record_id']);
if ($record === null) {
    $_SESSION['message'] .= '<div>No Record Found</div>';
    redirect_to(ADMIN_ROOT);
    unset($_SESSION['message']);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $_SESSION['message'] = delete_ncdc_time_series_record($record);
    redirect_to(ADMIN_ROOT);
    unset($_SESSION['message']);
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ncdc_date']) && trim($_POST['ncdc_date']) !== '') {
    $edit_record = $_POST;
    $edit_record['ncdc_time_series_id'] = $record['ncdc_time_series_id'];
    $_SESSION['message'] = update_ncdc_time_series_record($edit_record);
    redirect_to(ADMIN_ROOT . '/edit.php?record_id=' . $_GET['record_id']);
    unset($_SESSION['message']);
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['message'] = '<div>Please set the \'last updated\' date</div>';
}
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

        <h4 class="spaced">Edit Record<small> - <?= fdate_words($record['ncdc_date']) ?></small></h4>

        <form method="post" action="">
            <?php $_POST['ncdc_time_series_id'] = $record['ncdc_time_series_id']?? ''; ?>

            <div class="form-group my-4">
                <input class="form-control bordered bordered-dark bordered-all p-3" id="datetimepicker" type="text"
                    name="ncdc_date" placeholder="Last updated" value="<?= fdate_datepicker($record['ncdc_date']) ?>"
                    autocomplete="off">
            </div>

            <!-- States-->
            <div class="card bordered bordered-all shadowed">
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
                        $regions = regions($_GET['record_id']);
                        usort($regions, function($a, $b) {
                            return $b[1] <=> $a[1];
                        });
                        foreach ($regions as $value) : ?>
                        <tr>
                            <td><?= $value[0] ?></td>
                            <td><input class="form-control" type="number" readonly
                                    placeholder="<?= $record[$value[3]] ?>">
                            </td>
                            <td><input class="form-control calc-add" type="number" min="0" name="<?= $value[3] ?>"
                                    value="<?= $record[$value[3]] ?>"></td>
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
                                    value="<?= $record['ng_confirmed'] ?>">
                            </td>
                            <td><input id="confirmed-total" class="form-control" type="number" readonly min="0" value=""
                                    name="ng_confirmed"></td>
                        </tr>
                        <tr class="table-danger">
                            <td>Deaths</td>
                            <td><input class="form-control" type="number" readonly value="<?= $record['ng_deaths'] ?>">
                            </td>
                            <td><input class="form-control calc-sub" type="number" min="0" name="ng_deaths"
                                    value="<?= $record['ng_deaths'] ?>"></td>
                        </tr>
                        <tr class="table-success">
                            <td>Discharged</td>
                            <td><input class="form-control" type="number" readonly
                                    value="<?= $record['ng_recovered'] ?>"></td>
                            <td><input class="form-control calc-sub" type="number" min="0" name="ng_recovered"
                                    value="<?= $record['ng_recovered'] ?>"></td>
                        </tr>
                        <tr class="table-secondary">
                            <td>Active</td>
                            <td><input class="form-control" type="number" readonly value="<?= $record['ng_active'] ?>">
                            </td>
                            <td><input id="active-total" class="form-control" type="number" readonly min="0" value=""
                                    name="ng_active">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div> <!-- end Country Total -->

            <div class="form-group spaced shadowed bordered bordered bordered-all">
                <textarea class="form-control bordered bordered bordered-all" id="notes" name="notes" rows="4"
                    placeholder="Notes"><?= $record['notes'] ?? ''?></textarea>
            </div>

            <div class="form-group spaced">
                <div class="row no-gutters justify-content-between">
                    <button type="submit" class="btn btn-success col-md-5 my-2 bordered bordered-all">Update</button>
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-danger col-md-5 my-2 bordered bordered-all" data-toggle="modal"
                        data-target="#confirmDelete">
                        Delete
                    </button>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="confirmDelete" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-body text-center">
                            Are you sure you want to permanently delete this record?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-block btn-secondary bordered bordered-all"
                                data-dismiss="modal">Cancel</button>
                            <button type="submit" name="delete"
                                class="btn btn-block btn-danger bordered bordered-all">Yes, Delete</button>
                        </div>
                    </div>
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