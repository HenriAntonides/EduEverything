<!-- File for uniform header -->


<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-6QDZGG51XB"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-6QDZGG51XB');
</script>

<!-- This is the HTML for the header of different pages across the website -->
<header class="topbar">
    <div class="headerCont">
        <link rel="stylesheet" href="css/header.css" />

        <a href="dashboard.php" class="logo">EduEverything</a>
        <form class="searchBar" mehod="GET" action="./dashboard.php">
            <input type="text" name="search" placeholder="Search for everything..." />
            <button type="submit">Search</button>
        </form>

        <!-- Here it determines if you are logged in or out -->
        <nav class="navBar">
            <?php if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true){ ?>
                <a href="login/login.html">Sign in</a>
            <?php }else{ ?>
                <span>Welcome, <?php echo $_SESSION["username"]; ?></span>
                <a href="logout.php">Logout</a>
            <?php } ?>
            
            <a href="sell.php" id="sellItem">Sell</a>

            <form class="cartForm" method="POST" action="cart.php">
                <button type="submit" id="goToCart">Cart</button>
            </form>

            <form class="profileForm" method="POST" action="profile.php">
                <button type="submit" id="goToProfile">Profile</button>
            </form>
        </nav>

        <!-- To ensure that the user can't use Sell or Cart without proper login -->
        <?php include 'loginPrompt.html'; ?>
        
        <script>
            window.isLoggedIn = <?= json_encode($_SESSION["loggedin"] ?? false); ?>;
        </script>
    
        <script src="javascript/checkLogin.js"></script>

        <?php if (isset($_SESSION["cart_error"])){ 
            ?>
            <script>
                window.addEventListener("DOMContentLoaded", () => {
                    showError("<?= htmlspecialchars($_SESSION['cart_error']) ?>", false);
                });
            </script>
            <?php unset($_SESSION["cart_error"]); ?>
        <?php } ?>

        <link rel="stylesheet" href="css/loginPrompt.css" />
        
    </div>   
</header>