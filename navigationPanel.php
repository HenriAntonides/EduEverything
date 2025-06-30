<!-- File for uniform navigation panel -->

<!-- This is the HTML for the Navigation panel of different pages across the website -->
<link rel="stylesheet" href="css/navigationPanel.css" />
<nav class="categoryNav">
    <ul>
        <li>
        <a href="category.php?category=1">Electronics</a>
        <ul class="dropdown">
            <li><a href="category.php?category=3">Laptops</a></li>
            <li><a href="category.php?category=4">Smartphones</a></li>
            <li><a href="category.php?category=5">TVs</a></li>
            <li><a href="category.php?category=9">Smartwatches</a></li>
            <li><a href="category.php?category=11">Cameras</a></li>
        </ul>
        </li>
        <li>
        <a href="category.php?category=2">Fashion</a>
        <ul class="dropdown">
            <li><a href="category.php?category=6">Men's Clothing</a></li>
            <li><a href="category.php?category=7">Women's Clothing</a></li>
            <li><a href="category.php?category=8">Shoes</a></li>
            <li><a href="category.php?category=10">Accessories</a></li>
        </ul>
        </li>
        <li>
        <a href="category.php?category=12">Home & Garden</a>
        <ul class="dropdown">
            <li><a href="category.php?category=16">Furniture</a></li>
            <li><a href="category.php?category=17">Kitchen</a></li>
            <li><a href="category.php?category=18">Outdoor</a></li>
        </ul>
        </li>
        <li>
            <a href="category.php?category=13">Toys</a>
        <ul class="dropdown">
            <li><a href="category.php?category=19">Kid's toys</a></li>
            <li><a href="category.php?category=20">Board Games</a></li>
            <li><a href="category.php?category=21">Card Games</a></li>
        </ul>
        </li>
        <li><a href="category.php?category=14">Motors</a>
        <ul class="dropdown">
            <li><a href="category.php?category=22">New Car</a></li>
            <li><a href="category.php?category=23">Old Car</a></li>
            <li><a href="category.php?category=24">Audi</a></li>
            <li><a href="category.php?category=25">Mercedes</a></li>
        </ul>
        </li>
        <li><a href="category.php?category=15">Pets</a>
        <ul class="dropdown">
            <li><a href="category.php?category=26">Pet Food</a></li>
            <li><a href="category.php?category=27">Pet Bed</a></li>
            <li><a href="category.php?category=28">Accessories</a></li>
        </ul>
        </li>
    </ul>
</nav>

<script>
    // This is to diferenciate between the Desktop and mobile 
    function enableMobileDropdowns() {
        const isMobile = window.innerWidth <= 768;
        const links = document.querySelectorAll('.categoryNav > ul > li > a');

        links.forEach(link => {
            link.onclick = function (e) {
                // Skip if desktop
                if (!isMobile) {
                    return;
                } 

                e.preventDefault();

                const parentLi = this.parentElement;
                const isActive = parentLi.classList.contains('active');

                // Remove active from all
                document.querySelectorAll('.categoryNav > ul > li').forEach(li => {
                    li.classList.remove('active');
                });

                // Toggle the clicked one
                if (!isActive) {
                    parentLi.classList.add('active');
                }
            };
        });
    }

    window.addEventListener('load', enableMobileDropdowns);
    window.addEventListener('resize', enableMobileDropdowns);
</script>