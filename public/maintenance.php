<?php 
require_once('../private/initialize.php');
$page_title = 'Under maintenance - ' . $page_title_default;
?>

<!doctype html>
<html lang="en">

<!-- html header -->
<?php include(SHARED_PATH . '/head.php') ?>

<body class="flex-page">
    <!-- navigation -->
    <?php include(SHARED_PATH . '/nav.php') ?>

    <main class="boxed w-100">
        <h4>Sorry this page is under maintenance. <br>Please check back soon.</h4>
    </main>

    <!-- Footer -->
    <?php include(SHARED_PATH . '/footer.php') ?>

    <!-- Page scripts -->
    <?php include(SHARED_PATH . '/page_scripts.php') ?>
</body>

</html>