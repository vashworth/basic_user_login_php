<?php
    session_start();

    //Check if already logged in
    if(isset($_SESSION['username']) || !empty($_SESSION['username'])){
        header("location: welcome.php");
        exit;
    }

    //initialize variables
    require_once "config.php";
    $username_error = "";
    $password_error = "";
    $username_validity_class = "";
    $password_validity_class = "";
    $username = "";

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        //Get the username and password from the request, trim any trailing spaces
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        //Check for username validation errors
        if(empty($username)){
            $username_error = 'Please enter your username.';
            $username_validity_class = 'is-invalid';
        } else {
            $username_error = "";
            $username_validity_class = "";
        }

        //Check for password validation errors
        if(empty($password)){
            $password_error = 'Please enter your password.';
            $password_validity_class = 'is-invalid';
        } else {
            $password_error = "";
            $password_validity_class = "";
        }

        //If there are no errors in the form, continue
        if(empty($username_error) && empty($password_error)){

            //Prepare sql statement
            $sql = "INSERT INTO users (username, password) VALUES (?, ?)";

            if($stmt = $conn->prepare($sql)){
                //Bind variables to prepared statement
                $stmt->bind_param("ss", $param_username, $param_password);

                //Set bind variables
                $param_username = $username;
                //Creates a password hash using php's default, which is currently BCRYPT
                $param_password = password_hash($password, PASSWORD_DEFAULT);

                //Try to execute the statement
                if($stmt->execute()){
                    //Save the username to the session
                    //Redirect to welcome page
                    $_SESSION['username'] = $username;
                    header("location: welcome");

                } else {
                    echo "<script>
                        alert('The statement could not be executed.');
                        </script>";
                }
            }
            $stmt->close();

        }
        $conn->close();
    }
    include("header.php");

?>
<main class="main">
    <form class="form-signup" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="text-center mb-4">
            <img class="mb-4" src="https://getbootstrap.com/assets/brand/bootstrap-solid.svg" alt="" width="72" height="72">
            <h1 class="h3 mb-3 font-weight-normal">Please sign up for an account</h1>
        </div>
        <div class="form-label-group">
            <input type="email" id="inputEmail" class="form-control <?php echo $username_validity_class; ?>" placeholder="Email address" name="username" value="<?php echo $username; ?>">
            <label for="inputEmail">Email address</label>
            <div class="invalid-feedback"><?php echo $username_error; ?></div>
        </div>
        <div class="form-label-group">
            <input type="password" id="inputPassword" class="form-control <?php echo $password_validity_class; ?>" placeholder="Password" name="password">
            <label for="inputPassword">Password</label>
            <div class="invalid-feedback"><?php echo $password_error; ?></div>
        </div>
        <input type="submit" class="btn btn-lg btn-primary btn-block" value="Sign Up">
        <br>
        <p>Already have an account? <a href="login">Login now</a>.</p>
    </form>
</main>
<?php
    include("footer.php");
?>
