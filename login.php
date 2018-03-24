<?php
    session_start();
    //Check if already logged in
    if(isset($_SESSION['username']) || !empty($_SESSION['username'])){
        header("location: welcome.php");
        exit;
    }

    //initialize variables
    $username_error = "";
    $password_error = "";
    $username_validity_class = "";
    $password_validity_class = "";
    $username = "";

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        //Get the username and password from the request, trim any trailing spaces
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        //Check for username validaiton errors
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
            require_once "config.php";

            //Prepare sql statement
            $sql = "SELECT username, password FROM users WHERE username = ?";

            if($stmt = $conn->prepare($sql)){
                //Bind variables to prepared statement
                $stmt->bind_param("s", $param_username);

                //Set bind variables
                $param_username = $username;

                //Try to execute the statement
                if($stmt->execute()){
                    $stmt->store_result();
                    //Check how many rows were returned, if there is 1, the username was found
                    if($stmt->num_rows == 1){
                        //Bind result variables
                        $stmt->bind_result($username, $hashed_password);
                        if($stmt->fetch()){
                            //verify hashed password
                            if(password_verify($password, $hashed_password)){
                                $_SESSION['username'] = $username;
                                header("location: welcome");
                                exit;
                            } else {
                                $password_error = "Incorrect password.";
                                $password_validity_class = 'is-invalid';
                            }
                        }
                    } else {
                        $username_error = 'Username does not exist.';
                        $username_validity_class = 'is-invalid';
                    }

                } else {
                    //echo "The statement could not be executed";
                }
            }
            $stmt->close();

        }
        $conn->close();
    }
    include("header.php");
?>
<main class="main">
    <form class="form-login" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="text-center mb-4">
            <img class="mb-4" src="https://getbootstrap.com/assets/brand/bootstrap-solid.svg" alt="" width="72" height="72">
            <h1 class="h3 mb-3 font-weight-normal">Please log in to your account</h1>
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
        <input type="submit" class="btn btn-lg btn-primary btn-block" value="Login">
        <br>
        <p>Don't have an account? <a href="signup">Sign up now</a>.</p>
    </form>
</main>
<?php
    include("footer.php");
?>
