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

<h1>Signup</h1>

<?php if (isset($errors)): ?>

    <ul>
        <?php foreach ($errors as $field): ?>
            <?php foreach ($field as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </ul>

<?php endif; ?>

<form method="post" action="/signup">
    <label for="name">Name</label>
    <input type="text" name="name" id="name"
           value="<?= htmlspecialchars($data['name'] ?? '') ?>">

    <label for="email">email</label>
    <input type="email" name="email" id="email"
           value="<?= htmlspecialchars($data['email'] ?? '') ?>">

    <label for="password">Password</label>
    <input type="password" name="password" id="password">

    <label for="password_confirmation">Repeat password</label>
    <input type="password" name="password_confirmation"
           id="password_confirmation">

    <button>Sign up</button>
</form>

</div>
</body>
</html>