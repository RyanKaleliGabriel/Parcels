document.addEventListener("DOMContentLoaded", function () {
  const addNewButton = document.querySelector(".newparc");
  const popupForm = document.querySelector(".popup-form");
  const closePopup = document.querySelector(".close");

  addNewButton.addEventListener("click", function () {
    popupForm.style.display = "block";
    document.body.style.overflow = "hidden"; // Prevent scrolling on the background
  });

  window.addEventListener("click", function (event) {
    if (event.target === popupForm) {
      popupForm.style.display = "none";
      document.body.style.overflow = ""; // Restore scrolling on the background
    }
  });
});

let editPopupForm = document.querySelector(".edit-popup-form");
let deletePopupForm = document.querySelector(".delete-popup-form");
let endTripForm = document.querySelector(".end-trip-form");
let startTripForm = document.querySelector(".start-trip-form");

function showEditForm(
  id,
  departureDate,
  arrivalDate,
  departurePoint,
  pickupPoint,
  driver,
  clerk,
  progress
) {
  // Show the edit popup form
  editPopupForm.style.display = "block";
  document.body.style.overflow = "hidden";
  // Fill the form fields with the corresponding row's details
  document.getElementById("edit-departurep").value = departurePoint;
  document.getElementById("edit-pickp").value = pickupPoint;
  document.getElementById("edit-driver").value = driver;
  document.getElementById("edit-clerk").value = clerk;
  document.getElementById("edit-departured").value = departureDate;
  document.getElementById("edit-arrivald").value = arrivalDate;
  document.getElementById("edit-trip-id").value = id;
  document.getElementById("edit-progress-id").value = progress;
}

function showDeleteForm(id) {
  deletePopupForm.style.display = "block";
  document.body.overflow = "hidden";
  document.getElementById("delete-id").value = id;
}

function showEndTripForm(id) {
  endTripForm.style.display = "block";
  document.body.overflow = "hidden";
  document.getElementById("trip_Id").value = id;
}

function showStartTripForm(id) {
  startTripForm.style.display = "block";
  document.body.overflow = "hidden";
  document.getElementById("tripId").value = id;

  document.getElementById("trip-start-id").innerHTML = id;
}

function clearParcel(id, email){
  startTripForm.style.display = "block"
  document.body.overflow = "hidden";
  document.getElementById("tripId").value = id;
  document.getElementById("recipient_email").value = email;
}

function showEndForm(id) {
  deletePopupForm.style.display = "block";
  document.body.overflow = "hidden";
  document.getElementById("trip-id").value = id;
}

window.addEventListener("click", function (event) {
  if (event.target === editPopupForm || event.target === deletePopupForm) {
    editPopupForm.style.display = "none";
    deletePopupForm.style.display = "none";
    startTripForm.style.display = "none";
    endTripForm.style.display = "none";
    document.body.style.overflow = ""; // Restore scrolling on the background
  }
});

window.addEventListener("click", function (event) {
  if (event.target === deletePopupForm) {
    deletePopupForm.style.display = "none";
    document.body.style.overflow = ""; // Restore scrolling on the background
  }
});

window.addEventListener("click", function (event) {
  if (event.target === startTripForm) {
    startTripForm.style.display = "none";
    document.body.style.overflow = ""; // Restore scrolling on the background
  }
});

window.addEventListener("click", function (event) {
  if (event.target === endTripForm) {
    endTripForm.style.display = "none";
    document.body.style.overflow = ""; // Restore scrolling on the background
  }
});




