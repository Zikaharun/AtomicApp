<?php
require 'functions.php';



if (isset($_POST["submit"])) {

    if(register($_POST) > 0) {

        echo "<script>
              alert('succes!')
            </script>";

    } else {

        echo mysqli_error($connect);

    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Register | Atomic App</title>
</head>
<body class="bg-gray-50">
    <div class="flex justify-center items-center min-h-screen">
        <div class="bg-white p-8 w-full max-w-md border border-gray-300 rounded-lg shadow-md space-y-6">
            <h1 class="text-center text-3xl font-bold text-gray-800">Atomic App</h1>
            <h2 class="text-center text-xl font-semibold text-gray-600">Register Account</h2>

            <form action="<?php htmlspecialchars($_SERVER['PHP_SELF'])?>" method="post" class="space-y-4">
                <div>
                    <label for="username" class="block text-sm font-semibold text-gray-700">Username:</label>
                    <input class="mt-1 px-4 py-2 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" type="text" name="username" id="username" placeholder="Username" required />
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700">Password:</label>
                    <input class="mt-1 px-4 py-2 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" type="password" name="password" id="password" placeholder="Password" required />
                </div>

                <div>
                    <label for="password-confirm" class="block text-sm font-semibold text-gray-700">Confirm Password:</label>
                    <input class="mt-1 px-4 py-2 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" type="password" name="password-confirm" id="password-confirm" placeholder="Confirm Password" required />
                </div>

                <button type="submit" name="submit" class="w-full bg-blue-500 text-white rounded-lg font-semibold py-2 hover:bg-blue-600 transition duration-200">Register</button>
            </form>

            <p class="text-center text-sm text-gray-600">Have an account? <a href="login.php" class="text-blue-500 font-semibold underline">Login</a></p>
        </div>
    </div>
</body>
</html>