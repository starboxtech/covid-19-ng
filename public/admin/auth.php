<?php 
require_once('../../private/initialize.php');
$page_title = 'Log In - ' . $page_title_default;

unset_admin_session();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $log_in_details = $_POST;
    $message = log_in_admin($log_in_details);
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
        <?= $_SERVER['REQUEST_METHOD'] == 'POST' ? '<div class="alert alert-info mt-4" role="alert">' . $message . '</div>' : '' ; ?>
        <h4>Administrator Log In</h4>
        <form method="post" action="#" autocomplete="on" class="spaced">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control bordered bordered-dark bordered-all p-3" id="email"
                    name="username" placeholder="user@email.com" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control bordered bordered-dark bordered-all p-3" id="password"
                    name="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" placeholder="Your password" required>
                <input type="checkbox" class="m-2" id="show-password" onclick="showPassword()">Show Password
            </div>
            <button type="submit" class="btn btn-dark col-md-4 bordered bordered-all">Log In</button>
        </form>
    </main>

    <!-- Footer -->
    <?php include(SHARED_PATH . '/footer.php') ?>

    <!-- Page scripts -->
    <?php include(SHARED_PATH . '/page_scripts.php') ?>
</body>

</html>