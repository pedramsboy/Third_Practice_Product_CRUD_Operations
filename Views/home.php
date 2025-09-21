<h1>Welcome</h1>

<?php if (empty($_SESSION['user_id'])): ?>
<a href="/signup">sign up for an api key</a>

or

<a href="/login">log in</a>

    or

    <a href="/auth/google">Sign in with Google</a>

<?php else: ?>

    <a href="/profile">View profile</a>

    or

    <a href="/logout">log out</a>

<?php endif; ?>
