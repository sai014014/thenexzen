// carousel function
const carouselFrames = Array.from(document.querySelectorAll(".carousel-frame"));

function makeCarousel(frame) {
  const carouselSlide = frame.querySelector(".carousel-slide");
  const carouselImages = getImagesPlusClones();
  const prevBtn = frame.querySelector(".carousel-prev");
  const nextBtn = frame.querySelector(".carousel-next");
  const navDots = Array.from(frame.querySelectorAll(".carousel-dots li"));

  let imageCounter = 1;

  function getImagesPlusClones() {
    let images = frame.querySelectorAll(".carousel-slide img");

    const firstClone = images[0].cloneNode();
    const lastClone = images[images.length - 1].cloneNode();

    firstClone.className = "first-clone";
    lastClone.className = "last-clone";

    // we need clones to make an infinite loop effect
    carouselSlide.append(firstClone);
    carouselSlide.prepend(lastClone);

    // must reassign images to include the newly cloned images
    images = frame.querySelectorAll(".carousel-slide img");

    return images;
  }

  function initializeNavDots() {
    if (navDots.length) navDots[0].classList.add("active-dot");
  }

  function initializeCarousel() {
    carouselSlide.style.transform = "translateX(-100%)";
  }

  function slideForward() {
    // first limit counter to prevent fast-change bugs
    if (imageCounter >= carouselImages.length - 1) return;
    carouselSlide.style.transition = "transform 400ms";
    imageCounter++;
    carouselSlide.style.transform = `translateX(-${100 * imageCounter}%)`;
  }

  function slideBack() {
    // first limit counter to prevent fast-change bugs
    if (imageCounter <= 0) return;
    carouselSlide.style.transition = "transform 400ms";
    imageCounter--;
    carouselSlide.style.transform = `translateX(-${100 * imageCounter}%)`;
  }

  function makeLoop() {
    // instantly move from clones to originals to produce 'infinite-loop' effect
    if (carouselImages[imageCounter].classList.contains("last-clone")) {
      carouselSlide.style.transition = "none";
      imageCounter = carouselImages.length - 2;
      carouselSlide.style.transform = `translateX(-${100 * imageCounter}%)`;
    }

    if (carouselImages[imageCounter].classList.contains("first-clone")) {
      carouselSlide.style.transition = "none";
      imageCounter = carouselImages.length - imageCounter;
      carouselSlide.style.transform = `translateX(-${100 * imageCounter}%)`;
    }
  }

  function goToImage(e) {
    carouselSlide.style.transition = "transform 400ms";
    imageCounter = 1 + navDots.indexOf(e.target);
    carouselSlide.style.transform = `translateX(-${100 * imageCounter}%)`;
  }

  function highlightCurrentDot() {
    navDots.forEach((dot) => {
      if (navDots.indexOf(dot) === imageCounter - 1) {
        dot.classList.add("active-dot");
      } else {
        dot.classList.remove("active-dot");
      }
    });
  }

  function addBtnListeners() {
    nextBtn.addEventListener("click", slideForward);
    prevBtn.addEventListener("click", slideBack);
  }

  function addNavDotListeners() {
    navDots.forEach((dot) => {
      dot.addEventListener("click", goToImage);
    });
  }

  function addTransitionListener() {
    carouselSlide.addEventListener("transitionend", () => {
      makeLoop();
      highlightCurrentDot();
    });
  }

  function autoAdvance() {
    let play = setInterval(slideForward, 5000);

    frame.addEventListener("mouseover", () => {
      clearInterval(play); // pause when mouse enters carousel
    });

    frame.addEventListener("mouseout", () => {
      play = setInterval(slideForward, 5000); // resume when mouse leaves carousel
    });

    document.addEventListener("visibilitychange", () => {
      if (document.hidden) {
        clearInterval(play); // pause when user leaves page
      } else {
        play = setInterval(slideForward, 5000); // resume when user returns to page
      }
    });
  }

  function buildCarousel() {
    initializeCarousel();
    initializeNavDots();
    addNavDotListeners();
    addBtnListeners();
    addTransitionListener();
    autoAdvance();
  }

  buildCarousel();
}

carouselFrames.forEach((frame) => makeCarousel(frame));

// scroll top
// Scroll to top functionality
(function (e) {
  "use strict";

  // Smooth scroll to the top when scroll-top element is clicked
  if (e(".scroll-top")) {
    var scrollButton = document.querySelector(".scroll-top");
    var scrollPath = document.querySelector(".scroll-top path");
    var pathLength = scrollPath.getTotalLength();

    // Set initial path style for smooth transition
    scrollPath.style.transition = scrollPath.style.WebkitTransition = "none";
    scrollPath.style.strokeDasharray = pathLength + " " + pathLength;
    scrollPath.style.strokeDashoffset = pathLength;
    scrollPath.getBoundingClientRect();
    scrollPath.style.transition = scrollPath.style.WebkitTransition =
      "stroke-dashoffset 10ms linear";

    // Update stroke offset based on scroll position
    var updateStroke = function () {
      var scrollTop = e(window).scrollTop();
      var docHeight = e(document).height() - e(window).height();
      var offset = pathLength - (scrollTop * pathLength) / docHeight;
      scrollPath.style.strokeDashoffset = offset;
    };

    updateStroke();
    e(window).scroll(updateStroke);

    // Show or hide the scroll-to-top button based on scroll position
    jQuery(window).on("scroll", function () {
      if (jQuery(this).scrollTop() > 50) {
        jQuery(scrollButton).addClass("show");
      } else {
        jQuery(scrollButton).removeClass("show");
      }
    });

    // Animate scroll to the top when scrollButton is clicked
    jQuery(scrollButton).on("click", function (e) {
      e.preventDefault();
      jQuery("html, body").animate(
        {
          scrollTop: 0,
        },
        750
      );
      return false;
    });
  }
})(jQuery);

// preloader
$(document).ready(function () {
  setTimeout(function () {
    $("#container").addClass("loaded");
    // Once the container has finished, the scroll appears
    if ($("#container").hasClass("loaded")) {
      // It is so that once the container is gone, the entire preloader section is deleted
      $("#preloader")
        .delay(1000)
        .queue(function () {
          $(this).remove();
        });
    }
  }, 2000);
});

// form validation and submission
function submitForm() {
  clearErrors();

  const formData = {
    name: getElement("name").value.trim(),
    email: getElement("email").value.trim(),
    phone: getElement("phone").value.trim(),
    company: getElement("company").value.trim(),
  };

  if (!validateFormData(formData)) {
    return;
  }

  sendFormData(formData);
}

function validateFormData({ name, email, phone }) {
  let isValid = true;

  if (!name) {
    showError("nameError", "Name is required");
    isValid = false;
  }

  if (!email) {
    showError("emailError", "Email is required");
    isValid = false;
  } else if (!isValidEmail(email)) {
    showError("emailError", "Invalid email format");
    isValid = false;
  }

  if (!phone) {
    showError("phoneError", "Phone is required");
    isValid = false;
  } else if (!/^\d{10}$/.test(phone)) {
    showError("phoneError", "Phone must be a 10-digit number");
    isValid = false;
  }

  return isValid;
}

function sendFormData(formData) {
  fetch("request-form-submit", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(formData),
  })
    .then((response) => response.json())
    .then(handleFormResponse)
    .catch((error) => {
      console.error("Failed to submit form:", error);
      showError("messageError", "Failed to request. Please try again.");
      setTimeout(() => {
        window.location.reload();
      }, 3000);
    });
}

function handleFormResponse(data) {
  const messageElement = getElement("messageError");

  if (data.status === "error") {
    showError("messageError", data.message);
  } else {
    messageElement.innerText = data.message;
    messageElement.style.color = "green";
    getElement("contactForm").reset();

    setTimeout(() => {
      messageElement.innerText = "";
      messageElement.style.color = "red";
    }, 3000);
  }
}

function showError(elementId, message) {
  const errorElement = getElement(elementId);
  if (errorElement) {
    errorElement.innerText = message;
  }
}

function clearErrors() {
  const errors = document.querySelectorAll(".error-msg");
  errors.forEach((error) => (error.innerText = ""));
}

function isValidEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}
