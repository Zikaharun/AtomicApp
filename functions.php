<?php

$server = "localhost";
$user = "root";
$pass = "rooting";
$database = "atomic_app";

$connect = mysqli_connect($server, $user, $pass, $database);

function register($data) {

    global $connect;

    $username = strtolower(stripslashes($data["username"]));
    $email = $data["email"];
    $password = mysqli_real_escape_string( $connect, $data["password"]);
    $password2 = mysqli_real_escape_string( $connect, $data["password-confirm"]);

   $result = mysqli_query($connect,"SELECT username FROM account WHERE
                                         username = '$username'");

    if (mysqli_fetch_assoc($result)) {

        echo "<script>
                alert('username have been registered!')
              </script>";

        return false;
    }


    if( $password !== $password2 ) {
        echo "<script>
                alert('password doesn\'t match!')
              </script>";

              return false;
    }

    $password = password_hash($password, PASSWORD_DEFAULT);

    $query = "INSERT INTO account (username, email, password) VALUES (?,?,?)";

    if($stmt = mysqli_prepare($connect, $query)) {
        mysqli_stmt_bind_param($stmt, "sss", $username, $email, $password);

        if(mysqli_stmt_execute($stmt)) {
            echo "<script>
                   alert('User registered successfully!')
                   </script>";

            mysqli_stmt_close($stmt);
            return true;

        } else {

            echo "<script>
                    alert('error: ". mysqli_error($connect) . "');
                </script>";
                
            mysqli_stmt_close($stmt);
            return false;

        }

        

    }  else {
        echo "<script>
                alert('Failed to prepare statement');
              </script>";
        return false;
    }

    

}

function query($query) {
    global $connect;

    $result = mysqli_query($connect, $query);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}


?>