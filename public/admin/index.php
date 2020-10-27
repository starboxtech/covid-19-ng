<?php 
require_once('../../private/initialize.php');
$page_title = 'Dashboard - ' . $page_title_default;

// Authorized page access
require_login() ;
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
        <h4 class="spaced">All Records</h4>

        <!-- All Cases-->
        <div class="card mt-3 bordered bordered bordered-all shadowed">
            <div class="table-responsive-lg">
                <table class="table table-borderless table-update table-theme">
                    <thead>
                        <tr>
                            <th scope="col">NCDC Date</th>
                            <th scope="col">Last User</th>
                            <th scope="col">Conf.</th>
                            <th scope="col">Deat.</th>
                            <th scope="col">Disc.</th>
                            <th scope="col">Acti.</th>
                            <th scope="col">Notes</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $all_records = all_records();
                        usort($all_records, function($a, $b) {
                            return $b['ncdc_date'] <=> $a['ncdc_date'];
                        });
                        foreach ($all_records as $value) : ?>
                        <tr>
                            <td style="width:150px;"><?= date('j M y H:i',strtotime($value['ncdc_date'])) ?></td>
                            <td><?= substr(($value['last_modified_by'] ?? $value['created_by']),0,11) . '...' ?></td>
                            <td><?= $value['ng_confirmed'] ?></td>
                            <td><?= $value['ng_deaths'] ?></td>
                            <td><?= $value['ng_recovered'] ?></td>
                            <td><?= $value['ng_active'] ?></td>
                            <td><?= $value['notes'] ? substr($value['notes'],0,12).'...' : '' ?></td>
                            <td style="width:50px;"><a
                                    href="<?= ADMIN_ROOT . '/edit.php?record_id=' . $value['ng_confirmed'] . '-' . $value['ng_deaths'] . '-' . $value['ng_recovered'] ?>"
                                    class="btn btn-sm btn-outline-dark">Edit</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div> <!-- end All Cases -->
    </main>

    <!-- Footer -->
    <?php include(SHARED_PATH . '/footer.php') ?>

    <!-- Page scripts -->
    <?php include(SHARED_PATH . '/page_scripts.php') ?>
</body>

</html>