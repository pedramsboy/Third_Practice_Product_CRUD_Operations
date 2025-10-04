<nav>
    <ul>
        <li><a href="/">Home</a></li>
        <?php if (!empty($_SESSION['user_id'])): ?>
        <li><a href="/products/create">Add a New Products</a></li>
        <?php endif; ?>
        <li><a href="/#about">About</a></li>
        <li><a href="/#contact">Contact</a></li>

        <!-- Authentication Links - will be shown based on login status -->
        <?php if (empty($_SESSION['user_id'])): ?>
            <li style="float:right" ><a href="/login">Log In</a></li>
            <li style="float:right"><a href="/signup">Sign Up</a></li>
            <li style="float:right"><a href="/auth/google">Sign in with Google</a></li>
        <?php else: ?>
            <li style="float:right"><a href="/logout">Log Out</a></li>
            <li style="float:right"><a href="/profile">Profile</a></li>
        <?php endif; ?>
    </ul>
</nav>
