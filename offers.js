// Function that could trigger an event, e.g., opening a booking form or redirecting to a booking page
function bookNow(packageName) {
    alert(`You have chosen the ${packageName}. Redirecting to the booking page...`);
    // You could also redirect to an actual booking page here
     window.location.href = "order.html";
}
