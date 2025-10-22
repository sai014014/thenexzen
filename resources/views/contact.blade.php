<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Data -->
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1" />
    <title>Contact Us - TheNexZen</title>

    <link rel="icon" type="image/png" sizes="32x32"
        href="{{ asset('homePage/assets/img/businesslogowhite.svg') }}" />
    <meta name="msapplication-TileColor" content="#fa7070" />
    <meta name="theme-color" content="#fa7070" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- font -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&family=Racing+Sans+One&display=swap"
        rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet" />
    <!-- Dependency Styles -->
    <link rel="stylesheet" href="{{ asset('homePage/dependencies/bootstrap/css/bootstrap.min.css') }}"
        type="text/css" />

    <link rel="stylesheet" href="{{ asset('homePage/dependencies/wow/css/animate.css') }}" type="text/css" />
    <!-- Site Stylesheet -->
    <link rel="stylesheet" href="{{ asset('homePage/assets/css/style.css') }}" type="text/css" />
</head>

<body id="home-version-1" class="home-color-two" data-style="default">
    <div id="loader" class="loader-container">
        <div class="loader"></div>
    </div>
    <a href="#main_content" data-type="section-switch" class="return-to-top">
        <i class="fa fa-chevron-up"></i>
    </a>

    <div id="preloader">
        <div id="container" class="container-preloader">
            <div class="animation-preloader">
                <div class="spinner"></div>
                <div class="txt-loading">
                    <span preloader-text="T" class="characters">T</span>
                    <span preloader-text="H" class="characters">H</span>
                    <span preloader-text="E" class="characters">E</span>
                    <span preloader-text="N" class="characters">N</span>
                    <span preloader-text="E" class="characters">E</span>
                    <span preloader-text="X" class="characters">X</span>
                    <span preloader-text="Z" class="characters">Z</span>
                    <span preloader-text="E" class="characters">E</span>
                    <span preloader-text="N" class="characters">N</span>
                </div>
            </div>
            <div class="loader-section section-left"></div>
            <div class="loader-section section-right"></div>
        </div>
    </div>
    <!-- /.page-loader -->

    <div id="main_content">
        <!--=========================-->
        <!--=        Navbar         =-->
        <!--=========================-->
        <header class="site-header header-two toggle-light header_trans-fixed" data-top="992">
            <div class="container">
                <div class="header-inner">
                    <div class="site-mobile-logo">
                        <a href="{{ url('/') }}" class="logo">
                            <img src="{{ asset('homePage/assets/img/businesslogowhite.svg') }}" alt="site logo"
                                class="main-logo" />
                            <img src="{{ asset('homePage/assets/img/businesslogo.svg') }}" alt="site logo"
                                class="sticky-logo" />
                        </a>
                    </div>
                    <!-- /.site-mobile-logo -->

                    <div class="toggle-menu">
                        <span class="bar"></span>
                        <span class="bar"></span>
                        <span class="bar"></span>
                    </div>
                    <!-- /.toggle-menu -->
                    <nav class="site-nav nav-two">
                        <div class="close-menu">
                            <img src="{{ asset('homePage/assets/img/businesslogo.svg') }}" alt="site logo"
                                class="main-logo" />
                            <span>Close</span>
                            <i class="ei ei-icon_close"></i>
                        </div>

                        <div class="site-logo">
                            <a href="{{ url('/') }}" class="logo">
                                <img src="{{ asset('homePage/assets/img/businesslogowhite.svg') }}" alt="site logo"
                                    class="main-logo" />
                                <img src="{{ asset('homePage/assets/img/businesslogo.svg') }}" alt="site logo"
                                    class="sticky-logo" />
                            </a>
                        </div>

                        <div class="menu-wrapper" data-top="992">
                            <ul class="site-main-menu">
                                <li class="home">
                                    <a href="{{ url('/') }}">Home</a>
                                </li>
                                <li><a href="{{ route('contact') }}">Contact</a></li>
                            </ul>
                            <div class="nav-right">
                                <div class="dropdown">
                                    <button class="nav-btn style-two dropdown-toggle" type="button" id="loginDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Login <i class="fas fa-chevron-down ms-1"></i>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="loginDropdown">
                                        <li><a class="dropdown-item" href="{{ route('business.login') }}">Business Login</a></li>
                                        <li><a class="dropdown-item" href="{{ route('super-admin.login') }}">Super Admin Login</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- /.menu-wrapper -->
                    </nav>
                    <!-- /.site-nav -->
                </div>
                <!-- /.header-inner -->
            </div>
            <!-- /.container -->
        </header>
        <!-- /.site-header -->

        <!--===========================-->
        <!--=         Contact         =-->
        <!--===========================-->
        <section class="contactus">
            <img class="contactbg_img" src="{{ asset('homePage/assets/img/contactbg.svg') }}" alt="" />
            <div class="container">
                <div class="row contactus_row">
                    <div class="col-lg-6">
                        <div class="contactus_content">
                            <div class="contact_title two wow pixFadeUp" data-wow-delay="0.4s">
                                <h3>Get in</h3>
                                <h3>Touch with Us</h3>
                                <img class="title_dec" src="{{ asset('homePage/assets/img/titledec.svg') }}"
                                    alt="" />
                            </div>
                            <div class="contact_sub">
                                <p>
                                    Have questions about NexZen Go? We're here to help! Reach out to our team for support, 
                                    demos, or any inquiries about our car rental management platform.
                                </p>
                            </div>
                            <div class="contact_list">
                                <ul class="price-feture">
                                    <li class="cont_list">
                                        <i class="fas fa-phone me-2"></i>+91 90140 12010
                                    </li>
                                    <li class="cont_list">
                                        <i class="fas fa-envelope me-2"></i>support@nexzen.com
                                    </li>
                                    <li class="cont_list">
                                        <i class="fas fa-map-marker-alt me-2"></i>India
                                    </li>
                                    <li class="cont_list">
                                        <i class="fas fa-clock me-2"></i>24/7 Customer Support
                                    </li>
                                </ul>
                            </div>
                            <div class="callus">
                                <p>Give us a call at</p>
                                <span>+91 90140 12010</span>
                            </div>
                            <img class="managestar2" src="{{ asset('homePage/assets/img/magedecor2.svg') }}"
                                alt="" />
                            <img class="managestar3" src="{{ asset('homePage/assets/img/managedecor3.svg') }}"
                                alt="" />
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="contact_form">
                            <div class="form_title">
                                <h4>Send us a Message</h4>
                            </div>
                            <form id="contactForm" novalidate>
                                <div class="form-group">
                                    <input class="form-control" type="text" id="name" name="name" placeholder="Name" required />
                                    <span class="error-msg" id="nameError"></span>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" type="email" id="email" name="email" placeholder="Email" required />
                                    <span class="error-msg" id="emailError"></span>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" type="tel" id="phone" name="phone" placeholder="Phone" length="10"
                                        pattern="[0-9]{10}" maxlength="10" />
                                    <span class="error-msg" id="phoneError"></span>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" type="text" id="company" name="company" placeholder="Company Name" />
                                    <span class="error-msg" id="companyError"></span>
                                </div>
                                <div class="form-group">
                                    <textarea class="form-control" id="message" name="message" placeholder="Your Message" rows="4" required></textarea>
                                    <span class="error-msg" id="messageError"></span>
                                </div>
                                <span class="error-msg" id="formError"></span>
                                <div class="form-group">
                                    <button type="button" class="send_btn" onclick="submitContactForm()">Send Message</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!--=========================-->
        <!--=        Footer         =-->
        <!--=========================-->
        <footer id="footer" class="footer-app">
            <div class="container-wrap bg-footer-color">
                <div class="container">
                    <div class="footer-inner">
                        <div class="row wow fadeInUp">
                            <div class="col-lg-4 col-md-6 col-sm-6">
                                <div class="widget footer-widget widget-about">
                                    <a href="#" class="footer-logo"><img
                                            src="{{ asset('homePage/assets/img/businesslogowhite.svg') }}"
                                            alt="" /></a>
                                    <p>Tosser amongst jolly good do one no biggie hunky.</p>

                                    <h4 class="footer-title">Social</h4>
                                    <ul class="social-share-link">
                                        <li>
                                            <a href="#" class="share_facebook"><i class="fab fa-facebook-f"></i></a>
                                        </li>
                                        <li>
                                            <a href="#" class="share_twitter"><i class="fab fa-twitter"></i></a>
                                        </li>
                                        <li>
                                            <a href="#" class="share_pinterest"><i class="fab fa-pinterest-p"></i></a>
                                        </li>
                                        <li>
                                            <a href="#" class="share_linkedin"><i class="fab fa-linkedin-in"></i></a>
                                        </li>
                                    </ul>
                                </div>
                                <!-- /.widget footer-widget -->
                            </div>
                            <!-- /.col-lg-3 col-md-6 col-sm-6 -->
                            <div class="col-lg-4 col-md-6 col-sm-6">
                                <div class="widget footer-widget widget-contact">
                                    <h3 class="widget-title">Main Office</h3>

                                    <ul class="widget-contact-info">
                                        <li>
                                            <i class="ei ei-icon_pin_alt"></i>426 Maryam Springs
                                            Suite 230 New York, USA
                                        </li>
                                        <li>
                                            <i class="ei ei-icon_phone"></i>+(623) 698 235 426
                                        </li>
                                    </ul>
                                </div>
                                <!-- /.widget footer-widget -->
                            </div>
                            <!-- /.col-lg-3 col-md-6 col-sm-6 -->

                            <div class="col-lg-2 col-md-6 col-sm-6">
                                <div class="widget footer-widget">
                                    <h3 class="widget-title">Company</h3>
                                    <ul class="footer-menu">
                                        <li><a href="#">About Us</a></li>
                                        <li><a href="#">Careers</a></li>
                                        <li><a href="{{ route('contact') }}">Contact Us</a></li>
                                        <li><a href="#">Terms of Service</a></li>
                                        <li><a href="#">Privacy Policy</a></li>
                                    </ul>
                                </div>
                                <!-- /.widget footer-widget -->
                            </div>
                            <!-- /.col-lg-3 col-md-6 col-sm-6 -->

                            <div class="col-lg-2 col-md-6 col-sm-6">
                                <div class="widget footer-widget">
                                    <h3 class="widget-title">Platform</h3>

                                    <ul class="footer-menu">
                                        <li><a href="#">About</a></li>
                                        <li><a href="#">Legal Notice</a></li>
                                        <li><a href="#">Pricing</a></li>
                                        <li><a href="#">Enterprise</a></li>
                                        <li><a href="#">User Journey</a></li>
                                    </ul>
                                </div>
                                <!-- /.widget footer-widget -->
                            </div>
                            <!-- /.col-lg-3 col-md-6 col-sm-6 -->
                        </div>
                        <!-- /.row -->
                    </div>
                    <!-- /.footer-inner -->

                    <div class="site-info">
                        <div class="copyright text-center">
                            <p>
                                © 2025 All Rights The NexZen
                                <a href="#" target="_blank"></a>
                            </p>
                        </div>
                    </div>
                </div>
                <!-- /.container -->
            </div>
            <!-- /.container-wrap -->
        </footer>
        <!-- /#footer -->
        <!-- /#footer -->
        <div class="scroll-top show">
            <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
                <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" style="
              transition: stroke-dashoffset 10ms linear;
              stroke-dasharray: 307.919, 307.919;
              stroke-dashoffset: 36.8192;
            "></path>
            </svg>
        </div>
    </div>
    <!-- /#site -->
    <!-- Dependency Scripts -->

    <script src="{{ asset('homePage/dependencies/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('homePage/dependencies/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('homePage/dependencies/wow/js/wow.min.js') }}"></script>
    
    <!-- Bootstrap CSS for dropdown -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script type="text/javascript"
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDk2HrmqE4sWSei0XdKGbOMOHN3Mm2Bf-M&amp;ver=2.1.6">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <!-- Site Scripts -->
    <script src="{{ asset('dist/js/common.js') }}"></script>
    <script src="{{ asset('homePage/assets/js/header.js') }}"></script>
    <script src="{{ asset('homePage/assets/js/app.js') }}"></script>
    <script src="{{ asset('homePage/assets/js/home.js') }}"></script>
    
    <script>
    function submitContactForm() {
        clearErrors();

        const formData = {
            name: getElement("name").value.trim(),
            email: getElement("email").value.trim(),
            phone: getElement("phone").value.trim(),
            company: getElement("company").value.trim(),
            message: getElement("message").value.trim(),
        };

        if (!validateContactFormData(formData)) {
            return;
        }

        sendContactFormData(formData);
    }

    function validateContactFormData({ name, email, phone, message }) {
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

        if (!message) {
            showError("messageError", "Message is required");
            isValid = false;
        }

        return isValid;
    }

    function sendContactFormData(formData) {
        fetch("{{ route('contact.submit') }}", {
            method: "POST",
            headers: { 
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(formData),
        })
        .then((response) => response.json())
        .then(handleContactFormResponse)
        .catch((error) => {
            console.error("Failed to submit form:", error);
            showError("formError", "Failed to send message. Please try again.");
        });
    }

    function handleContactFormResponse(data) {
        const messageElement = getElement("formError");

        if (data.status === "error") {
            showError("formError", data.message);
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

    function getElement(id) {
        return document.getElementById(id);
    }
    </script>
</body>

</html>