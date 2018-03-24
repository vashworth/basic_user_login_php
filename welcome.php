<?php
    // Initialize the session
    session_start();

    // If session variable is not set it will redirect to login page
    if(!isset($_SESSION['username']) || empty($_SESSION['username'])){
        header("location: login.php");
        exit;
    }
    include("header.php");

?>
<main class="main">
    <div class="container text-center greeting">
        <h1 class="display-3">Hello, <?php echo $_SESSION['username']; ?></h1>
    </div>
</main>
<?php
    include("footer.php");
?>
