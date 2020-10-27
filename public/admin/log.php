<?php 
require_once('../../private/initialize.php');
$page_title = 'View Logs - ' . $page_title_default;

// Authorized page access
require_login();
?>

<!-- html header -->
<?php include(SHARED_PATH . '/head.php') ?>

<!doctype html>
<html lang="en">

<!-- html header -->
<?php include(SHARED_PATH . '/head.php') ?>

<body class="flex-page">
    <!-- navigation -->
    <?php include(SHARED_PATH . '/nav.php') ?>

    <main class="boxed w-100">
        <?php show_admin_panel() ?>
        <h4 class="spaced">Application log</h4>

        <!-- Logs-->
        <div class="card mt-3 bordered bordered bordered-all shadowed">
            <div class="table-responsive-lg">
                <table class="table table-borderless table-theme table-log table-hover">
                    <thead>
                        <tr>
                            <th scope="col">Log ID</th>
                            <th scope="col">Date</th>
                            <th scope="col">IP Address</th>
                            <th scope="col">User</th>
                            <th scope="col">Event</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $all_logs = all_logs();
                        foreach ($all_logs as $value) : ?>
                        <tr>
                            <td><?= $value[0] ?></td>
                            <td><?= $value[1] ?></td>
                            <td><?= $value[2] ?></td>
                            <td><?= $value[3] ?></td>
                            <td><?= $value[4] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- end Logs -->
    </main>

    <!-- Footer -->
    <?php include(SHARED_PATH . '/footer.php') ?>

    <!-- Page scripts -->
    <?php include(SHARED_PATH . '/page_scripts.php') ?>
</body>

</html>