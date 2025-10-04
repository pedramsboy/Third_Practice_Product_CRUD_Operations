<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>

        body {
            margin: 0;
            padding: 0;
            width: 100%;
            min-width: 100%;
            padding-top: 50px;
        }

        nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            background-color: #333333;
        }

        ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            overflow: hidden;
            background-color: #333333;
        }

        ul li {
            float: left;
        }

        ul li a {
            display: block;
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }

        ul li a:hover {
            background-color: #111111;
        }

        button {
            margin-top: 10px;
        }

        .content {
            padding: 20px;
            max-width: 500px;
            margin: 0 auto;
        }
    </style>
</head>



<body>
<?php include 'templates/partials/navbar.php'; ?>

<div class="content">
<h1>Login</h1>

<?php if (isset($error)): ?>

    <p><?= $error ?></p>

<?php endif; ?>

<form method="post" action="/login">
    <label for="email">email</label>
    <input type="email" name="email" id="email"
           value="<?= htmlspecialchars($data['email'] ?? '') ?>">

    <label for="password">Password</label>
    <input type="password" name="password" id="password">

    <!-- Haven't signed up yet? section -->
    <div class="signup-prompt">
        Haven't signed up yet?<br>
        <a href="/signup" class="signup-link">Create an account</a>
    </div>

    <button class="margin-top: 30px">Log in</button>
</form>

</div>
</body>
</html>