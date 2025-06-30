// Function for login form validation
function validateLoginForm() {
    //Getting the values
    let itemName=document.getElementById("itemName").value.trim();
    let description=document.getElementById("description").value.trim();
    let categoryID=document.getElementById("categoryID").value.trim();
    let mediaFiles=document.getElementById("mediaFiles[]").value.trim();
    let price=document.getElementById("price").value.trim();

    //Get error messages
    let itemNameError =document.getElementById("userNameError");
    let descriptionError =document.getElementById("passwordError");
    let categoryIDError =document.getElementById("userNameError");
    let mediaFilesError =document.getElementById("passwordError");
    let priceError =document.getElementById("passwordError");
 
    //clear Errors
    itemNameError.innerHTML="";
    descriptionError.innerHTML="";
    let isValid=true;
   
    if(itemName===""){
        itemNameError.innerHTML="Enter a name for the item";
        isValid=false;
    }
 
    if(description===""){
        descriptionError.innerHTML="Enter a Description for the item";
        isValid=false;
    }

    if(categoryID===""){
        categoryIDError.innerHTML="Select a category";
        isValid=false;
    }
 
    if(mediaFiles===""){
        mediaFilesError.innerHTML="Upload some Images for use";
        isValid=false;
    }

    if(price===""){
        priceError.innerHTML="Enter a price";
        isValid=false;
    }

    return isValid;
}