function validateForm(){

    //Getting the values
    let username= document.getElementById("username").value.trim();
    let password= document.getElementById("password").value.trim();
    let confirmPassword= document.getElementById("confirmpassword").value.trim();

    let name= document.getElementById("name").value.trim();
    let surname= document.getElementById("surname").value.trim();
    let email= document.getElementById("email").value.trim();
    let phoneNr= document.getElementById("phoneNr").value;
    let birthDate= document.getElementById("birthDate").value.trim();
    let idNumber= document.getElementById("idNumber").value;

    //Get error messages
    let userNameError= document.getElementById("userNameError");
    let passwordError= document.getElementById("passwordError");
    let confirmpasswordError= document.getElementById("confirmpasswordError");

    let nameError= document.getElementById("nameError");
    let surnameError= document.getElementById("surnameError");
    let emailError= document.getElementById("emailError");
    let phoneNrError= document.getElementById("phoneNrError");
    let birthDateError= document.getElementById("birthDateError");
    let idNumberError= document.getElementById("idNumberError");

    //clear Errors
    userNameError.innerHTML="";
    passwordError.innerHTML="";
    confirmpasswordError.innerHTML="";
    nameError.innerHTML="";
    surnameError.innerHTML="";
    emailError.innerHTML="";
    phoneNrError.innerHTML="";
    birthDateError.innerHTML="";
    idNumberError.innerHTML="";

    // If anything is false, form doesn't execute
    let isValid = true;

    // Ensure username isn't empty
    if(username === ""){
        userNameError.innerHTML="Username is required";
        isValid = false;
    }
    // Ensure password is long enough
    if(password === ""){
        passwordError.innerHTML="Password is required";
        isValid = false;
    }else if(password.length < 6){
        passwordError.innerHTML="Password must be at least 6 characters long";
        isValid = false;
    }
    //Ensure the confirmed password is the same as the previous password
    if(confirmPassword === ""){
        confirmpasswordError.innerHTML="Confirm Password is required";
        isValid = false;
    }else if(confirmPassword != password){
        confirmpasswordError.innerHTML="Password need to be the same";
        isValid = false;
    }
 
    
    // Ensure name isn't empty
    if(name === ""){
        nameError.innerHTML="Name is required";
        isValid = false;
    }

    
    // Ensure Surname isn't empty
    if(surname === ""){
        surnameError.innerHTML= "Surname is required";
        isValid = false;
    }

    // make sure the email is a valid email format
    let emailPattern=/^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
    if(email===""){
        emailError.innerHTML= "Email is required";
        isValid=false;
    }else if(!email.match(emailPattern)){
        emailError.innerHTML= "invalid email format";
        isValid=false;
    }

    // Ensure phoneNr is correct
    if(phoneNr === ""){
        phoneNrError.innerHTML= "Phone Number is required";
        isValid = false;
    }else if(phoneNr.length != 10){
        phoneNrError.innerHTML= "Phone Number is not correct length";
        isValid = false;
    }
    
    const today = new Date();
    
    // Ensure birth date is legal
    if(birthDate === ""){
        birthDateError.innerHTML= "Birth Date is required";
        isValid = false;
    }else if(birthDate > today) {
        birthDateError.innerHTML= "Enter a valid date";
        isValid = false;
    }
 
    // Ensure the ID number is correct
    if(idNumber === ""){
        idNumberError.innerHTML= "ID Number is required";
        isValid = false;
    }else if(idNumber.length > 15){
        idNumberError.innerHTML= "ID Number is not correct length";
        isValid = false;
    } 

    return isValid;
}