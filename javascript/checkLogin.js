
//Check to see if you can access your cart / or if you want to sell something, by being logged in
document.addEventListener("DOMContentLoaded", function () {
    // Check to see if it came from the addToCartForm
    const form = document.querySelector("#addToCartForm");
    if (form) {
        // if pressed, check if logged in, if not redirect to showError
        form.addEventListener("submit", function (e) {
            if (!window.isLoggedIn) {
                e.preventDefault();
                // Get the current product page
                sessionStorage.setItem("redirectAfterLogin", window.location.pathname + window.location.search);

                showError("You must be logged in to add items to your cart.", true);
            }
        });
    }

    //Or sellItem 
    const sellBtn = document.getElementById("sellItem");
    if (sellBtn) {
        sellBtn.addEventListener("click", function (e) {
            if (!window.isLoggedIn) {
                e.preventDefault();
                // Make sure that after login, you are redirected to the sell page
                sessionStorage.setItem("redirectAfterLogin", "sell.php");
                showError("You must be logged in to list an item for sale.", true);
            } else {
                window.location.href = "sell.html";
            }
        });
    }

    //Or cartForm
    const cartForm = document.querySelector(".cartForm");
    if (cartForm) {
        // if pressed, check if logged in, if not redirect to showError
        cartForm.addEventListener("submit", function (e) {
            if (!window.isLoggedIn) {
                e.preventDefault();
                // Make sure that after login, you are redirected to the cart page
                sessionStorage.setItem("redirectAfterLogin", "cart.php");
                showError("You must be logged in to check the items in your cart.", true);
            }
        });
    }

    //Or profileForm
    const profileForm = document.querySelector(".profileForm");
    if (profileForm) {
        // if pressed, check if logged in, if not redirect to showError
        profileForm.addEventListener("submit", function (e) {
            if (!window.isLoggedIn) {
                e.preventDefault();
                // Make sure that after login, you are redirected to the cart page
                sessionStorage.setItem("redirectAfterLogin", "profile.php");
                showError("You must be logged in to check your profile.", true);
            }
        });
    }
    
});

// Get the error message, and also check to ensure it is a login error or normal error
function showError(customMessage = null, isLoginError = false) {
    const modal = document.getElementById("loginPrompt");
    if (modal) {
        modal.style.display = "flex";

        // Change the heading and message such as needed
        const heading = modal.querySelector("h2");
        const message = modal.querySelector("p");
        
        if (customMessage) {
            message.innerText = customMessage;
        }

        const loginBtns = document.getElementById("loginButtons");
        if (isLoginError) {
            heading.innerText = "Login Required";
            loginBtns.style.display = "flex";
        }else{
            heading.innerText = "Error";
            loginBtns.style.display = "none";
        }
    }
}

