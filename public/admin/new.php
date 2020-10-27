<?php 
require_once('../../private/initialize.php');
$page_title = 'Create New Admin - ' . $page_title_default;

// Authorized access page
require_login();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_admin = $_POST;
    $_SESSION['message'] = create_new_admin($new_admin);
    if ($_SESSION['message']) {
        $_SESSION['message'] .= '<div><a href="' . ADMIN_LOGIN . '" class="btn btn-info">Go to Login Page</a></div>';
    }
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
        <?php show_session_message() ?>
        <h4>Create New Administrator</h4>
        <form method="post" action="#" autocomplete="on" class="spaced">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control bordered bordered-dark bordered-all p-3" id="name" name="name"
                    placeholder="First Last" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control bordered bordered-dark bordered-all p-3" id="email"
                    name="username" placeholder="user@email.com" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control bordered bordered-dark bordered-all p-3" id="password"
                    name="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" placeholder="Min 8 characters"
                    required>
                <small class="d-block">Min 8 characters with at least 1 number, 1 uppercase and 1 lowercase
                    letter</small>
                <input type="checkbox" class="m-2" id="show-password" onclick="showPassword()">Show Password
            </div>
            <button type="submit" class="btn btn-dark col-md-4 bordered bordered-all">Create new admin</button>
        </form>
    </main>
    <!-- Footer -->
    <?php include(SHARED_PATH . '/footer.php') ?>

    <!-- Page scripts -->
    <?php include(SHARED_PATH . '/page_scripts.php') ?>
</body>

</html>