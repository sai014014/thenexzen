<!DOCTYPE html>
<html lang="en">

    <head>
    <!-- Meta Data -->
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1" />
    <title>TheNexZen</title>

    <link rel="icon" type="image/png" sizes="32x32"
        href="{{ asset('homePage/assets/img/businesslogowhite.svg') }}" />
    <meta name="msapplication-TileColor" content="#fa7070" />
    <meta name="theme-color" content="#fa7070" />

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
                                        Login
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

        <!--==========================-->
        <!--=         Banner         =-->
        <!--==========================-->
        <section class="banner banner-gritt">
            <div class="decor1">
                <img src="{{ asset('homePage/assets/img/bannerdecor1.svg') }}" alt="" />
            </div>
            <div class="container">
                <div class="banner-content-wrap-four">
                    <div class="row">
                        <div class="col-lg-5 col-md-12">
                            <div class="banner-content">
                                <div class="star_decor">
                                    <img src="{{ asset('homePage/assets/img/star.svg') }}" alt="" />
                                </div>
                                <h1>Effortless Car Rental Management with The NexZen</h1>

                                <p class="description wow pixFadeUp" data-wow-delay="0.5s">
                                    NexZen Go is designed to empower car rental agencies in
                                    India with an innovative, user-friendly platform that
                                    simplifies booking, vendor management, and customer
                                    relations.
                                </p>
                            </div>
                            <!-- /.banner-content -->
                            <!-- /.promo-mockup -->
                        </div>
                        <div class="promo-mockup wow pixFadeUp text-center">
                            <img src="{{ asset('homePage/assets/img/dashboard.svg') }}" class="ban_img" alt="" />
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.container -->

            <!-- /.bg-shape-inner -->
        </section>

        <!-- Features -->
        <section class="features mt-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="featuresection-title color-two text-center">
                            <img class="managestar1" src="{{ asset('homePage/assets/img/managedecor3.svg') }}"
                                alt="" />
                            <h4 class="sub-title wow pixFadeUp">Included Capabilities</h4>
                            <h2 class="title wow pixFadeUp" data-wow-delay="0.3s">
                                Features designed for you
                            </h2>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-lg-6">
                        <div class="management two wow pixFadeUp" data-wow-delay="0.4s">
                            <img class="managestar2" src="{{ asset('homePage/assets/img/magedecor2.svg') }}"
                                alt="" />
                            <div class="icon_text">
                                <div class="icon_management">
                                    <img src="{{ asset('homePage/assets/img/manage_icon.svg') }}" alt="" />
                                </div>
                                <div class="text_management">
                                    <h4>Booking Management</h4>
                                    <p>
                                        Efficiently manage all aspects of your bookings for smooth
                                        operations.
                                    </p>
                                </div>
                            </div>
                            <div class="mangement_list">
                                <ul>
                                    <li>
                                        View the status and availability of all vehicles in one
                                        place
                                    </li>
                                    <li>
                                        Set up maintenance reminders for compliance and safety
                                    </li>
                                    <li>
                                        Track service history to manage warranties effectively
                                    </li>
                                    <li>
                                        Manage different fuel types for operational efficiency
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="management two wow pixFadeUp" data-wow-delay="0.5s">
                            <div class="icon_text">
                                <div class="icon_management">
                                    <img src="{{ asset('homePage/assets/img/manage_icon.svg') }}" alt="" />
                                </div>
                                <div class="text_management">
                                    <h4>Vendor Management</h4>
                                    <p>
                                        Efficiently manage all aspects of your bookings for smooth
                                        operations.
                                    </p>
                                </div>
                            </div>
                            <div class="mangement_list">
                                <ul>
                                    <li>
                                        View the status and availability of all vehicles in one
                                        place
                                    </li>
                                    <li>
                                        Set up maintenance reminders for compliance and safety
                                    </li>
                                    <li>
                                        Track service history to manage warranties effectively
                                    </li>
                                    <li>
                                        Manage different fuel types for operational efficiency
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="management two wow pixFadeUp" data-wow-delay="0.6s">
                            <div class="men">
                                <img class="men_img" src="{{ asset('homePage/assets/img/managemandecor.svg') }}"
                                    alt="" />
                            </div>
                            <div class="icon_text">
                                <div class="icon_management">
                                    <img src="{{ asset('homePage/assets/img/manage_icon.svg') }}" alt="" />
                                </div>
                                <div class="text_management">
                                    <h4>Vehicle Management</h4>
                                    <p>
                                        Efficiently manage all aspects of your bookings for smooth
                                        operations.
                                    </p>
                                </div>
                            </div>
                            <div class="mangement_list">
                                <ul>
                                    <li>
                                        View the status and availability of all vehicles in one
                                        place
                                    </li>
                                    <li>
                                        Set up maintenance reminders for compliance and safety
                                    </li>
                                    <li>
                                        Track service history to manage warranties effectively
                                    </li>
                                    <li>
                                        Manage different fuel types for operational efficiency
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="management two wow pixFadeUp" data-wow-delay="0.7s">
                            <img class="managestar3" src="{{ asset('homePage/assets/img/managedecor3.svg') }}"
                                alt="" />
                            <div class="icon_text">
                                <div class="icon_management">
                                    <img src="{{ asset('homePage/assets/img/manage_icon.svg') }}" alt="" />
                                </div>
                                <div class="text_management">
                                    <h4>Customer Management</h4>
                                    <p>
                                        Efficiently manage all aspects of your bookings for smooth
                                        operations.
                                    </p>
                                </div>
                            </div>
                            <div class="mangement_list">
                                <ul>
                                    <li>
                                        View the status and availability of all vehicles in one
                                        place
                                    </li>
                                    <li>
                                        Set up maintenance reminders for compliance and safety
                                    </li>
                                    <li>
                                        Track service history to manage warranties effectively
                                    </li>
                                    <li>
                                        Manage different fuel types for operational efficiency
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end features-->
        <!-- why choosw us -->
        <section class="whychooseus mt-5">
            <div class="container">
                <div class="row chooseus_row">
                    <div class="col-lg-7">
                        <div class="chooseus_img">
                            <!-- <img src="{{ asset('homePage/assets/img/chooseus.svg') }}" alt="" /> -->

                            <div class="carousel-frame">
                                <div class="carousel-slide">
                                    <img src="{{ asset('homePage/assets/img/chooseus.svg') }}" />
                                    <img src="{{ asset('homePage/assets/img/chooseus.svg') }}" />
                                    <img src="{{ asset('homePage/assets/img/chooseus.svg') }}" />
                                </div>
                                <i class="carousel-prev"></i>
                                <i class="carousel-next"></i>
                                <ol class="carousel-dots">
                                    <li></li>
                                    <li></li>
                                    <li></li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="chooseus_text">
                            <img class="managestar3" src="{{ asset('homePage/assets/img/managedecor3.svg') }}"
                                alt="" />
                            <h4 class="sub-title wow pixFadeUp">Why choose NexZen Go?</h4>
                        </div>
                        <div class="choosus_list">
                            <div class="list_one">
                                <h5>Proudly Made in India</h5>
                                <p>
                                    Developed specifically for Indian car rental agencies, our
                                    software addresses unique local challenges, ensuring you
                                    receive a solution tailored to your needs. Made by the
                                    Locals for the Locals.
                                </p>
                            </div>
                            <div class="list_two">
                                <h5>User-Friendly Interface</h5>
                                <p>
                                    Experience a seamless transition with an intuitive design
                                    that minimizes the learning curve for your team, making it
                                    easy to get started.
                                </p>
                            </div>
                            <div class="list_three">
                                <h5>24/7 Customer Support</h5>
                                <p>
                                    Our dedicated support team is always available to assist
                                    you, ensuring smooth and efficient operations at all times.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--end why choosw us -->
        <!--=============================-->
        <!--=        pricing         =-->
        <!--=============================-->
        <section class="pricing-two">
            <div class="container">
                <div class="section-title color-two text-center">
                    <h2 class="pricingtitle wow pixFadeUp" data-wow-delay="0.3s">
                        Simple, scalable pricing.
                    </h2>
                    <span class="pricingsub-title">No extra charges. No hidden fees.</span>
                </div>
                <!-- /.section-title -->
                <nav class="pricing-tab color-two wow pixFadeUp" data-wow-delay="0.4s">
                    <span class="monthly_tab_title tab-btn"> Monthly </span>
                    <span class="pricing-tab-switcher"></span>
                    <span class="annual_tab_title tab-btn"> Annual </span>
                </nav>

                <div class="row advanced-pricing-table">
                    <div class="col-lg-4">
                        <div class="pricing-table style-two price-two wow pixFadeLeft" data-wow-delay="0.5s">
                            <div class="pricing-header pricing-amount">
                                <h3 class="price-title">NexZen Starter</h3>
                                <p>For creating impressive tools that generate results.</p>

                                <div class="annual_price">
                                    <h2 class="price">₹14990 INR</h2>
                                    <span class="monthly">Per year</span>
                                </div>
                                <!-- /.annual_price -->

                                <div class="monthly_price">
                                    <h2 class="price">₹1499 INR</h2>
                                    <span class="monthly">Per month</span>
                                </div>

                                <!-- /.monthly_price -->
                            </div>
                            <!-- /.pricing-header -->
                            <div class="trail_btn">
                                <a href="{{ route('business.login') }}" class="pix-btn btn-outline-two">Start a free
                                    trial</a>
                                <span>No credit card required</span>
                            </div>
                            <p class="key-features">Key features:</p>
                            <ul class="price-feture">
                                <li class="have">Booking management</li>
                                <li class="have">Customer management</li>
                                <li class="have">Vehicle management</li>
                                <li class="not">Basic reporting features</li>
                                <li class="not">Add up to 20 vehicles</li>
                            </ul>

                            <div class="action text-left"></div>
                            <div class="pricing_dec">
                                <img class="dec_women" src="{{ asset('homePage/assets/img/price_decor.svg') }}"
                                    alt="" />
                            </div>
                        </div>
                        <!-- /.pricing-table -->
                    </div>
                    <!-- /.col-lg-4 -->

                    <div class="col-lg-4">
                        <div class="pricing-table color-two style-two price-two featured wow pixFadeLeft"
                            data-wow-delay="0.7s">
                            <span class="offer-tag"><span class="tag">Popular</span></span>

                            <div class="pricing-header pricing-amount">
                                <h3 class="price-title">NexZen Pro</h3>
                                <p>For seamless integrations and sending tools in bulk.</p>

                                <div class="annual_price">
                                    <h2 class="price">₹24990 INR</h2>
                                    <span class="monthly">Per year</span>
                                </div>
                                <!-- /.annual_price -->

                                <div class="monthly_price">
                                    <h2 class="price">₹2499 INR</h2>
                                    <span class="monthly">Per month</span>
                                </div>

                                <!-- /.monthly_price -->
                            </div>
                            <!-- /.pricing-header -->
                            <div class="trail_btn">
                                <a href="{{ route('business.login') }}" class="pix-btn btn-outline-two">Start a free
                                    trial</a>
                                <span>No credit card required</span>
                            </div>
                            <p class="key-features">Key features:</p>
                            <ul class="price-feture">
                                <li class="have">Vendor management</li>
                                <li class="have">Advanced reporting and analytics</li>
                                <li class="have">
                                    Vehicle management & maintenance reminders
                                </li>
                                <li class="have">Add up to 40 vehicles</li>
                                <li class="not">24/7 chat support</li>
                            </ul>
                            <div class="cash_svg">
                                <img src="{{ asset('homePage/assets/img/cash.svg') }}" alt="" />
                            </div>
                            <div class="action text-left"></div>
                        </div>
                        <!-- /.pricing-table -->
                    </div>
                    <!-- /.col-lg-4 -->

                    <div class="col-lg-4">
                        <div class="pricing-table color-three style-two price-two wow pixFadeLeft"
                            data-wow-delay="0.9s">
                            <div class="pricing-header pricing-amount">
                                <h3 class="price-title">NexZen Max</h3>
                                <p>For large companies with complex Tool workflows.</p>

                                <div class="annual_price">
                                    <h2 class="price">₹34990 INR</h2>
                                    <span class="monthly">Per year</span>
                                </div>

                                <!-- /.annual_price -->

                                <div class="monthly_price">
                                    <h2 class="price">₹3499 INR</h2>
                                    <span class="monthly">Per month</span>
                                </div>

                                <!-- /.monthly_price -->
                            </div>
                            <!-- /.pricing-header -->
                            <div class="trail_btn">
                                <a href="{{ route('business.login') }}" class="pix-btn btn-outline-two">Start a free
                                    trial</a>
                                <span>No credit card required</span>
                            </div>
                            <p class="key-features">Key features:</p>
                            <ul class="price-feture">
                                <li class="have">Extensive customization options</li>
                                <li class="have">
                                    Multi-user access with role-based permissions
                                </li>
                                <li class="have">Dedicated account management</li>
                                <li class="have">Add unlimited vehicles</li>
                                <li class="have">Enterprise-level 24/7 support</li>
                            </ul>

                            <div class="action text-left"></div>
                        </div>
                    </div>
                    <div class="price_include">
                        <p>Prices exclude any applicable taxes.</p>
                    </div>
                </div>
            </div>
        </section>
        <!-- /#pricing-close -->

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
                                <h3>Start your</h3>
                                <h3>14 days free trial</h3>
                                <img class="title_dec" src="{{ asset('homePage/assets/img/titledec.svg') }}"
                                    alt="" />
                            </div>
                            <div class="contact_sub">
                                <p>
                                    Experience the full power of NexZen Go with a 14-day free
                                    trial. Enjoy unrestricted access to all features and see how
                                    it streamlines your rental operations.
                                </p>
                            </div>
                            <div class="contact_list">
                                <ul class="price-feture">
                                    <li class="cont_list">
                                        Full access to all packages and features
                                    </li>
                                    <li class="cont_list">
                                        Explore booking management, vendor tracking, and reporting
                                    </li>
                                    <li class="cont_list">
                                        Improve efficiency with automated reminders and
                                        maintenance tools
                                    </li>
                                    <li class="cont_list">
                                        No commitment—just discover how NexZen Go benefits your
                                        business
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
                                <h4>Request for demo</h4>
                            </div>
                            <form id="contactForm" novalidate>
                                <div class="form-group">
                                    <input class="form-control" type="text" id="name" placeholder="Name" />
                                    <span class="error-msg" id="nameError"></span>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" type="email" id="email" placeholder="Email" />
                                    <span class="error-msg" id="emailError"></span>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" type="tel" id="phone" placeholder="Phone" length="10"
                                        pattern="[0-9]{10}" maxlength="10" />
                                    <span class="error-msg" id="phoneError"></span>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" type="text" id="company" placeholder="Company Name" />
                                    <span class="error-msg" id="companyError"></span>
                                </div>
                                <span class="error-msg" id="messageError"></span>
                                <div class="form-group">
                                    <button type="button" class="send_btn" onclick="submitForm()">Send Request</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- /.contactus -->

        <!-- accordions section -->
        <section class="accordions">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="accor_head">
                            <h2>Frequently asked questions:</h2>
                            <img class="managestar3" src="{{ asset('homePage/assets/img/managedecor3.svg') }}"
                                alt="" />
                            <img class="questindec two wow pixFadeUp" data-wow-delay="0.4s"
                                src="{{ asset('homePage/assets/img/questindec.svg') }}" alt="" />
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="accor_content">
                            <img class="managestar2" src="{{ asset('homePage/assets/img/magedecor2.svg') }}"
                                alt="" />
                            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                                <div class="panel panel-default">
                                    <div class="panel-heading" role="tab" id="headingOne">
                                        <h4 class="panel-title">
                                            <a role="button" data-toggle="collapse" data-parent="#accordion"
                                                href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                Do I need to know about how to code?
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel"
                                        aria-labelledby="headingOne">
                                        <div class="panel-body">
                                            <p>
                                                Yes, you need to have a fair amount of knowledge in
                                                dealing with HTML/CSS as well as JavaScript in order
                                                to be able to use Lexend.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel panel-default">
                                    <div class="panel-heading" role="tab" id="headingOne">
                                        <h4 class="panel-title">
                                            <a role="button" data-toggle="collapse" data-parent="#accordion"
                                                href="#collapsetwo" aria-expanded="true" aria-controls="collapsetwo">
                                                Can I use it for commercial projects?
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="collapsetwo" class="panel-collapse collapse in" role="tabpanel"
                                        aria-labelledby="headingOne">
                                        <div class="panel-body">
                                            <p>
                                                Lorem ipsum dolor sit amet, consectetur adipiscing
                                                elit. Praesent nisl lorem, dictum id pellentesque at,
                                                vestibulum ut arcu. Curabitur erat libero, egestas eu
                                                tincidunt ac, rutrum ac justo. Vivamus condimentum
                                                laoreet lectus, blandit posuere tortor aliquam vitae.
                                                Curabitur molestie eros.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel panel-default">
                                    <div class="panel-heading" role="tab" id="headingthree">
                                        <h4 class="panel-title">
                                            <a role="button" data-toggle="collapse" data-parent="#accordion"
                                                href="#collapsethree" aria-expanded="true"
                                                aria-controls="collapsethree">
                                                Can I use it for multiple projects?
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="collapsethree" class="panel-collapse collapse in" role="tabpanel"
                                        aria-labelledby="headingthree">
                                        <div class="panel-body">
                                            <p>
                                                Lorem ipsum dolor sit amet, consectetur adipiscing
                                                elit. Praesent nisl lorem, dictum id pellentesque at,
                                                vestibulum ut arcu. Curabitur erat libero, egestas eu
                                                tincidunt ac, rutrum ac justo. Vivamus condimentum
                                                laoreet lectus, blandit posuere tortor aliquam vitae.
                                                Curabitur molestie eros.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="panel panel-default">
                                    <div class="panel-heading" role="tab" id="headingthree">
                                        <h4 class="panel-title">
                                            <a role="button" data-toggle="collapse" data-parent="#accordion"
                                                href="#collapsefour" aria-expanded="true" aria-controls="collapsefour">
                                                Can I use this to create and sell a product?
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="collapsefour" class="panel-collapse collapse in" role="tabpanel"
                                        aria-labelledby="headingfour">
                                        <div class="panel-body">
                                            <p>
                                                Lorem ipsum dolor sit amet, consectetur adipiscing
                                                elit. Praesent nisl lorem, dictum id pellentesque at,
                                                vestibulum ut arcu. Curabitur erat libero, egestas eu
                                                tincidunt ac, rutrum ac justo. Vivamus condimentum
                                                laoreet lectus, blandit posuere tortor aliquam vitae.
                                                Curabitur molestie eros.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel panel-default">
                                    <div class="panel-heading" role="tab" id="headingthree">
                                        <h4 class="panel-title">
                                            <a role="button" data-toggle="collapse" data-parent="#accordion"
                                                href="#collapsefive" aria-expanded="true" aria-controls="collapsefive">
                                                What is your refund policy?
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="collapsefive" class="panel-collapse collapse in" role="tabpanel"
                                        aria-labelledby="headingfive">
                                        <div class="panel-body">
                                            <p>
                                                Lorem ipsum dolor sit amet, consectetur adipiscing
                                                elit. Praesent nisl lorem, dictum id pellentesque at,
                                                vestibulum ut arcu. Curabitur erat libero, egestas eu
                                                tincidunt ac, rutrum ac justo. Vivamus condimentum
                                                laoreet lectus, blandit posuere tortor aliquam vitae.
                                                Curabitur molestie eros.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- endaccordions section -->

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
                                        <li><a href="#">Contact Us</a></li>
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
    <script src="{{ asset('homePage/dependencies/wow/js/wow.min.js') }}"></script>
    
    <!-- Bootstrap 5 CSS and JS for dropdown -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom dropdown styles -->
    <style>
        /* Text decoration removal for all menus and texts */
        a, a:hover, a:focus, a:active, a:visited {
            text-decoration: none !important;
        }
        
        /* Navigation menu links */
        .site-main-menu a, 
        .site-main-menu a:hover, 
        .site-main-menu a:focus, 
        .site-main-menu a:active, 
        .site-main-menu a:visited {
            text-decoration: none !important;
        }
        
        /* Footer menu links */
        .footer-menu a, 
        .footer-menu a:hover, 
        .footer-menu a:focus, 
        .footer-menu a:active, 
        .footer-menu a:visited {
            text-decoration: none !important;
        }
        
        /* Social media links */
        .social-share-link a, 
        .social-share-link a:hover, 
        .social-share-link a:focus, 
        .social-share-link a:active, 
        .social-share-link a:visited {
            text-decoration: none !important;
        }
        
        /* All text elements */
        h1, h2, h3, h4, h5, h6, p, span, div, li {
            text-decoration: none !important;
        }
        
        /* Button links */
        .pix-btn, .pix-btn:hover, .pix-btn:focus, .pix-btn:active, .pix-btn:visited {
            text-decoration: none !important;
        }
        
        /* Accordion links */
        .panel-title a, 
        .panel-title a:hover, 
        .panel-title a:focus, 
        .panel-title a:active, 
        .panel-title a:visited {
            text-decoration: none !important;
        }
        
        .dropdown-toggle::after {
            display: inline-block !important;
            margin-left: 0.5em !important;
            vertical-align: 0.255em !important;
            content: "" !important;
            border-top: 0.3em solid #fff !important;
            border-right: 0.3em solid transparent !important;
            border-bottom: 0 !important;
            border-left: 0.3em solid transparent !important;
        }
        .nav-btn.style-two.dropdown-toggle::after {
            border-top-color: #fff !important;
        }
        
        /* Ensure dropdown is clickable and visible */
        .dropdown {
            position: relative !important;
        }
        .dropdown-menu {
            z-index: 1000 !important;
            min-width: 160px !important;
            padding: 0.5rem 0 !important;
            margin: 0 !important;
            background-color: #fff !important;
            border: 1px solid rgba(0,0,0,.15) !important;
            border-radius: 0.375rem !important;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,.175) !important;
        }
        .nav-btn.dropdown-toggle {
            cursor: pointer !important;
        }
        
        /* Ensure dropdown items are visible */
        .dropdown-menu li {
            list-style: none !important;
        }
        .dropdown-menu .dropdown-item {
            display: block !important;
            width: 100% !important;
            padding: 0.5rem 1rem !important;
            clear: both !important;
            font-weight: 400 !important;
            color: #212529 !important;
            text-align: inherit !important;
            text-decoration: none !important;
            white-space: nowrap !important;
            background-color: transparent !important;
            border: 0 !important;
            transition: background-color 0.15s ease-in-out !important;
        }
        .dropdown-menu .dropdown-item:hover {
            color: #1e2125 !important;
            background-color: #e9ecef !important;
            text-decoration: none !important;
        }
        
        /* Force dropdown to be visible when Bootstrap shows it */
        .dropdown-menu.show {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        /* Override any conflicting styles from other CSS files */
        .site-header .dropdown-menu {
            display: none !important;
        }
        .site-header .dropdown-menu.show {
            display: block !important;
        }
    </style>

    <script type="text/javascript"
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDk2HrmqE4sWSei0XdKGbOMOHN3Mm2Bf-M&amp;ver=2.1.6">
    </script>
    <!-- Site Scripts -->
    <script src="{{ asset('dist/js/common.js') }}"></script>
    <script src="{{ asset('homePage/assets/js/header.js') }}"></script>
    <script src="{{ asset('homePage/assets/js/app.js') }}"></script>
    <script src="{{ asset('homePage/assets/js/home.js') }}"></script>
    
    <!-- Initialize Bootstrap dropdowns -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Initializing dropdowns...');
            
            // Check if Bootstrap is loaded
            if (typeof bootstrap === 'undefined') {
                console.error('Bootstrap is not loaded!');
                return;
            }
            
            // Initialize all dropdowns using Bootstrap's built-in method
            var dropdownTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
            console.log('Found dropdown triggers:', dropdownTriggerList.length);
            
            var dropdownList = dropdownTriggerList.map(function (dropdownTriggerEl) {
                console.log('Initializing dropdown for:', dropdownTriggerEl.id);
                return new bootstrap.Dropdown(dropdownTriggerEl);
            });
            
            // Let Bootstrap handle the dropdown, but ensure it's properly initialized
            var loginBtn = document.getElementById('loginDropdown');
            if (loginBtn) {
                // Remove any existing event listeners and let Bootstrap handle it
                console.log('Login button found, Bootstrap should handle the dropdown');
                
                // Add a click event to debug Bootstrap's behavior
                loginBtn.addEventListener('click', function(e) {
                    console.log('Login button clicked - Bootstrap should handle this');
                });
            }
        });
    </script>
    </body>

</html>