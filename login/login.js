// Function for login form validation
function validateLoginForm() {
    //Getting the values
    let username=document.getElementById("username").value.trim();
    let password=document.getElementById("password").value.trim();
 
    //Get error messages
    let userNameError =document.getElementById("userNameError");
    let passwordError =document.getElementById("passwordError");
 
    //clear Errors
    userNameError.innerHTML="";
    passwordError.innerHTML="";
    let isValid=true;
   
    if(username===""){
        userNameError.innerHTML="Username is required";
        isValid=false;
    }
 
    if(password===""){
        passwordError.innerHTML="Password is required";
        isValid=false;
    }

    return isValid;
}