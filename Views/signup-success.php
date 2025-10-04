<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
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

        .content {
            padding: 20px;
        }
    </style>
</head>


<body>

<?php include 'templates/partials/navbar.php'; ?>

<div class="content">

<h1>Signup Confirmation</h1>

<p>Thank you for signing up. You can now <a href="/login">log in</a>.</p>

</div>

</body>
</html>