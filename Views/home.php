<!--<h1>Welcome</h1>-->
<!---->
<?php //if (empty($_SESSION['user_id'])): ?>
<!--<a href="/signup">sign up for an api key</a>-->
<!---->
<!--or-->
<!---->
<!--<a href="/login">log in</a>-->
<!---->
<!--    or-->
<!---->
<!--    <a href="/auth/google">Sign in with Google</a>-->
<!---->
<?php //else: ?>
<!---->
<!--    <a href="/profile">View profile</a>-->
<!---->
<!--    or-->
<!---->
<!--    <a href="/logout">log out</a>-->
<!---->
<?php //endif; ?>


<!DOCTYPE html>
<html>
<head>
    <title>Home - Product CRUD</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/5/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

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

        body,h1,h2,h3,h4,h5,h6 {font-family: "Lato", sans-serif}
        .w3-bar,h1,button {font-family: "Montserrat", sans-serif}
        .fa-anchor,.fa-coffee {font-size:200px}

    </style>
</head>
<body>

<?php include 'templates/partials/navbar.php'; ?>




<!-- Header -->
<header class="w3-container w3-red w3-center" style="padding:128px 16px">
    <h1 class="w3-margin w3-jumbo">Welcome</h1>
    <p class="w3-xlarge">Pedram's Electronical Gadgets</p>

    <?php if (empty($_SESSION['user_id'])): ?>
    <button class="w3-button w3-black w3-padding-large w3-large w3-margin-top"
            onclick="window.location.href='/login'">Login to See Products</button>
    <?php else: ?>
    <button class="w3-button w3-black w3-padding-large w3-large w3-margin-top">See Products</button>
    <?php endif; ?>
</header>

<!-- First Grid -->
<div id="about" class="w3-row-padding w3-padding-64 w3-container">
    <div class="w3-content">
        <div class="w3-twothird">
            <h1>About Us</h1>
            <h5 class="w3-padding-32">Our Story: More Than Just Gadgets<br>

                Hello, I'm Pedram, the founder behind this venture.<br>

                This project started from my own desk, tinkering with components and dreaming of building a better way for people to discover amazing tech. I was often frustrated by the disconnect between flashy marketing and a product's actual performance.</h5>

            <p class="w3-text-grey">That's why I built this app—a place where functionality, design, and value truly meet. My small team and I are personally involved in testing products and ensuring that what you get enhances your life. This isn't a faceless corporation; it's a passion project, and your satisfaction is our greatest reward.

                We're here to help you find that perfect gadget. Feel free to reach out to me directly at <strong> <a href="mailto:sboy.pedram@gmail.com">sboy.pedram@gmail.com</a></strong> with any thoughts or questions.</p>
        </div>

        <div class="w3-third w3-center">
            <i class="fa fa-anchor w3-padding-64 w3-text-red"></i>
        </div>
    </div>
</div>

<!-- Second Grid -->
<div id="contact" class="w3-row-padding w3-light-grey w3-padding-64 w3-container">
    <div class="w3-content">
        <div class="w3-third w3-center">
            <i class="fa fa-coffee w3-padding-64 w3-text-red w3-margin-right"></i>
        </div>

        <div class="w3-twothird">
            <h1 class="w3-text-grey">Contact Us</h1>
            <h5 class="w3-padding-32 w3-text-grey">Shoot us an email: sboy.pedram@gmail.com<br>

                Give us a call: +98 937 657 3261</h5>

            <p class="w3-text-grey">Get in Touch! Have a question about a gadget or need help with your order? We love hearing from you! Drop us a line or give us a call, and our tech-savvy team will be happy to help.We're looking forward to connecting with you!</p>
        </div>
    </div>
</div>

<div class="w3-container w3-black w3-center w3-opacity w3-padding-64">
    <h1 class="w3-margin w3-xlarge">Quote of the day: Believe you can and you're halfway there</h1>
</div>

<!-- Footer -->
<footer class="w3-container w3-padding-64 w3-center w3-opacity">
    <div class="w3-xlarge w3-padding-32">
        <a href="https://www.instagram.com/pedramsboy/"> <i class="fa fa-instagram w3-hover-opacity"></i></a>
    </div>
</footer>

</body>
</html>