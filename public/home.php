<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - RENTCAR</title>
    <link rel="icon" href="images/mainLogo.svg" type="image/x-icon" />
    
    <!-- Bootstrap CSS -->
    <link href="dist/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="dist/css/common.css">
    <link rel="stylesheet" href="dist/css/dashboard/dashboard.css">
    <script src="dist/jquery.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="dist/js/common.js"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Racing+Sans+One&display=swap"
        rel="stylesheet">
    
    <style>
        /* Global Font */
        * {
            font-family: "Poppins", sans-serif !important;
        }

        /* Sidebar Styling */
        .sidebar {
            background-color: #ffffff;
            width: 250px;
            box-shadow: -6px 5px 14px 3px #0000002e;
            font-family: "Poppins", sans-serif !important;
        }

        /* Sidebar Links */
        .nav-link {
            display: flex;
            align-items: center;
            color: #fff;
            font-size: 16px;
            padding: 10px 15px;
            text-decoration: none;
            margin-bottom: 5px;
            transition: background-color 0.3s ease;
        }

        .nav-link i {
            margin-right: 10px;
            font-size: 18px;
        }

        /* Hover Effect */
        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        /* Individual Colors */
        .dashboard-link {
            color: #4caf50;
            /* Green */
        }

        .customer-link {
            color: #2196f3;
            /* Blue */
        }

        .vendor-link {
            color: #ff9800;
            /* Orange */
        }

        .vehicle-link {
            color: #e91e63;
            /* Pink */
        }

        .data-link {
            color: #9c27b0;
            /* Purple */
        }

        /* Header */
        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .logout-btn {
            color: #fff;
            background-color: #f44336;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #d32f2f;
        }

        .border_logo {
            position: absolute;
            bottom: -8px;
            left: 0px;
        }

        .logo {
            position: relative;
        }

        ::-webkit-scrollbar {
            width: 8px;
        }

        /* Track */
        ::-webkit-scrollbar-track {
            box-shadow: inset 0 0 5px grey;
            border-radius: 10px;
        }

        /* Handle */
        ::-webkit-scrollbar-thumb {
            background: #6b6ade87;
            border-radius: 7px;
        }

        /* Handle on hover */
        ::-webkit-scrollbar-thumb:hover {
            background: #6b6ade87;
        }

        .menuActive {
            background: #6b6ade1f;
            border-radius: 8px;
        }

        .menuActive:hover {
            background: #6b6ade1f;
            border-radius: 8px;
        }

        .notificationSnooze {
            background: #f3f4f6;
            color: #333;
            padding: 2px 16px !important;
        }

        .notificationSnooze:hover {
            background: #f3f4f6;
            color: #333;
        }

        .notificationView {
            background: #4f46e5;
            color: white;
            padding: 2px 16px !important;
        }

        .notificationView:hover {
            background: #4f46e5;
            color: white;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="logo">
            <img src="images/mainLogo.svg" alt="Logo">
            <h3>RENTCAR</h3>
        </div>
        
        <a href="#" class="nav-link menuActive">
            <svg width="31" height="30" viewBox="0 0 31 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="0.5" width="30" height="30" rx="8" fill="white" />
                <path d="M15.6619 10.4674C15.6183 10.4256 15.5603 10.4023 15.4999 10.4023C15.4395 10.4023 15.3815 10.4256 15.3379 10.4674L9.94434 15.6198C9.92143 15.6417 9.90321 15.668 9.89077 15.6972C9.87833 15.7263 9.87194 15.7577 9.87197 15.7894L9.87109 20.6252C9.87109 20.8738 9.96987 21.1123 10.1457 21.2881C10.3215 21.4639 10.56 21.5627 10.8086 21.5627H13.624C13.7483 21.5627 13.8676 21.5133 13.9555 21.4254C14.0434 21.3375 14.0928 21.2182 14.0928 21.0939V17.1095C14.0928 17.0474 14.1175 16.9878 14.1614 16.9438C14.2054 16.8999 14.265 16.8752 14.3271 16.8752H16.6709C16.7331 16.8752 16.7927 16.8999 16.8366 16.9438C16.8806 16.9878 16.9053 17.0474 16.9053 17.1095V21.0939C16.9053 21.2182 16.9547 21.3375 17.0426 21.4254C17.1305 21.5133 17.2497 21.5627 17.374 21.5627H20.1883C20.4369 21.5627 20.6754 21.4639 20.8512 21.2881C21.027 21.1123 21.1258 20.8738 21.1258 20.6252V15.7894C21.1258 15.7577 21.1194 15.7263 21.107 15.6972C21.0945 15.668 21.0763 15.6417 21.0534 15.6198L15.6619 10.4674Z" fill="#6B6ADE" />
            </svg>
            Dashboard
        </a>

        <a href="#" class="nav-link">
            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="30" height="30" rx="8" fill="white" />
                <path d="M11.0469 22.5312H10.1094C9.92289 22.5312 9.74405 22.4572 9.61219 22.3253C9.48033 22.1934 9.40625 22.0146 9.40625 21.8281V17.6094C9.40625 17.4229 9.48033 17.2441 9.61219 17.1122C9.74405 16.9803 9.92289 16.9062 10.1094 16.9062H11.0469C11.2334 16.9062 11.4122 16.9803 11.5441 17.1122C11.6759 17.2441 11.75 17.4229 11.75 17.6094V21.8281C11.75 22.0146 11.6759 22.1934 11.5441 22.3253C11.4122 22.4572 11.2334 22.5312 11.0469 22.5312V22.5312Z" fill="#6B6ADE" />
            </svg>
            Bookings
        </a>

        <a href="#" class="nav-link">
            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="30" height="30" rx="8" fill="white" />
                <path d="M6.58536 14.2619L7.36094 11.9288C7.36094 11.9089 7.36094 11.9089 7.38033 11.889C7.67117 11.1911 8.33041 10.7324 9.06721 10.7324H13.7982C15.4269 10.7524 16.9781 11.3905 18.1415 12.5869L19.6345 13.903H20.5458C22.5235 13.903 24.1328 15.558 24.1328 17.5919V18.3497C24.1328 18.6288 23.9195 18.8482 23.6481 18.8482H22.1357C21.8642 19.7455 21.0499 20.4035 20.0998 20.4035C19.1303 20.4035 18.316 19.7455 18.0639 18.8482H12.0144C11.743 19.7455 10.9286 20.4035 9.97851 20.4035C9.00904 20.4035 8.19469 19.7455 7.94262 18.8482H6.68231C6.31391 18.8482 6.00368 18.5291 6.00368 18.1503V15.5979C5.9649 15.0595 6.19757 14.5809 6.58536 14.2619Z" fill="#6B6ADE" />
            </svg>
            Vehicles
        </a>

        <a href="#" class="nav-link">
            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="30" height="30" rx="8" fill="white" />
                <path d="M20.7563 11.5314C20.7226 11.4657 20.6738 11.409 20.6139 11.3657C20.5541 11.3225 20.4849 11.294 20.412 11.2826C20.3391 11.2712 20.2645 11.2771 20.1943 11.3C20.1241 11.3229 20.0603 11.362 20.0081 11.4142L18.2081 13.2153C18.1197 13.3024 18.0006 13.3512 17.8766 13.3512C17.7525 13.3512 17.6334 13.3024 17.5451 13.2153L16.767 12.436C16.7234 12.3925 16.6889 12.3408 16.6653 12.284C16.6418 12.2271 16.6296 12.1661 16.6296 12.1046C16.6296 12.043 16.6418 11.982 16.6653 11.9251C16.6889 11.8683 16.7234 11.8166 16.767 11.7731L18.5593 9.98038C18.6131 9.92666 18.6529 9.86068 18.6755 9.78812C18.6981 9.71557 18.7027 9.63861 18.6889 9.56389C18.6751 9.48917 18.6434 9.41892 18.5964 9.35918C18.5494 9.29945 18.4887 9.25203 18.4193 9.22101V9.22101C17.0655 8.61573 15.3757 8.93155 14.3101 9.98917C13.4049 10.888 13.135 12.2925 13.5704 13.8426C13.5939 13.9252 13.594 14.0128 13.5708 14.0955C13.5476 14.1783 13.5019 14.253 13.4388 14.3113L8.5615 18.7659C8.37149 18.9365 8.21823 19.144 8.11107 19.3758C8.0039 19.6076 7.94508 19.8588 7.93819 20.1141C7.93129 20.3693 7.97647 20.6233 8.07098 20.8605C8.16548 21.0978 8.30732 21.3132 8.48784 21.4938C8.66836 21.6745 8.88379 21.8164 9.12097 21.911C9.35816 22.0056 9.61213 22.0509 9.86739 22.0441C10.1227 22.0373 10.3739 21.9786 10.6057 21.8716C10.8375 21.7645 11.0451 21.6113 11.2158 21.4214L15.7181 16.5332C15.7757 16.471 15.8491 16.4256 15.9305 16.402C16.0119 16.3784 16.0982 16.3775 16.1801 16.3993C16.6215 16.5202 17.0767 16.583 17.5342 16.5859C18.5128 16.5859 19.3715 16.2692 19.9908 15.659C21.1378 14.529 21.3127 12.6124 20.7563 11.5314Z" fill="#6B6ADE" />
            </svg>
            Vendors
        </a>

        <a href="#" class="nav-link">
            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="30" height="30" rx="8" fill="white" />
                <path d="M16.7453 9.39199C16.1752 8.77646 15.3789 8.4375 14.5 8.4375C13.6164 8.4375 12.8175 8.77441 12.25 9.38613C11.6764 10.0046 11.3969 10.8451 11.4625 11.7527C11.5926 13.5434 12.9552 15 14.5 15C16.0448 15 17.4051 13.5437 17.5372 11.7533C17.6037 10.8539 17.3225 10.0151 16.7453 9.39199Z" fill="#6B6ADE" />
                <path d="M19.6564 21.5625H9.34392C9.20894 21.5643 9.07526 21.5359 8.95262 21.4795C8.82997 21.4231 8.72145 21.34 8.63493 21.2364C8.4445 21.0088 8.36775 20.6979 8.42458 20.3836C8.67185 19.0119 9.44353 17.8597 10.6564 17.0508C11.734 16.3327 13.0989 15.9375 14.5002 15.9375C15.9014 15.9375 17.2664 16.333 18.3439 17.0508C19.5568 17.8594 20.3285 19.0116 20.5757 20.3833C20.6326 20.6977 20.5558 21.0085 20.3654 21.2361C20.2789 21.3398 20.1704 21.4229 20.0478 21.4794C19.9251 21.5358 19.7914 21.5642 19.6564 21.5625Z" fill="#6B6ADE" />
            </svg>
            Customers
        </a>

        <a href="#" class="nav-link">
            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="30" height="30" rx="8" fill="white" />
                <path d="M19.5391 14.5625H15.4375C15.0645 14.5625 14.7069 14.4143 14.4431 14.1506C14.1794 13.8869 14.0312 13.5292 14.0312 13.1562V9.05469C14.0312 9.02361 14.0189 8.9938 13.9969 8.97182C13.9749 8.94985 13.9451 8.9375 13.9141 8.9375H11.2187C10.7215 8.9375 10.2446 9.13504 9.89292 9.48667C9.54129 9.83831 9.34375 10.3152 9.34375 10.8125V20.1875C9.34375 20.6848 9.54129 21.1617 9.89292 21.5133C10.2446 21.865 10.7215 22.0625 11.2187 22.0625H17.7812C18.2785 22.0625 18.7554 21.865 19.1071 21.5133C19.4587 21.1617 19.6562 20.6848 19.6562 20.1875V14.6797C19.6562 14.6486 19.6439 14.6188 19.6219 14.5968C19.5999 14.5748 19.5701 14.5625 19.5391 14.5625Z" fill="#6B6ADE" />
            </svg>
            Reports
        </a>

        <a href="#" class="nav-link">
            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="30" height="30" rx="8" fill="white" />
                <path d="M20.9933 9.12129C20.9879 9.09538 20.9753 9.07153 20.9569 9.05254C20.9384 9.03355 20.915 9.0202 20.8892 9.01406C19.1733 8.59453 15.2086 10.0896 13.0605 12.2367C12.6774 12.6167 12.3281 13.0294 12.0167 13.4701C11.3543 13.4115 10.6919 13.4604 10.1273 13.7065C8.53447 14.4076 8.0707 16.2369 7.9415 17.0238C7.93417 17.0669 7.93694 17.1111 7.94958 17.1529C7.96222 17.1947 7.98439 17.233 8.01434 17.2648C8.0443 17.2967 8.08123 17.3211 8.12222 17.3362C8.16321 17.3513 8.20715 17.3567 8.25058 17.352L10.8085 17.0698C10.8103 17.2627 10.822 17.4553 10.8434 17.647C10.8562 17.7801 10.9153 17.9045 11.0103 17.9985L12.0009 18.9867C12.095 19.0816 12.2194 19.1407 12.3524 19.1537C12.543 19.175 12.7346 19.1867 12.9264 19.1886L12.6457 21.7433C12.641 21.7867 12.6465 21.8306 12.6616 21.8716C12.6767 21.9125 12.7011 21.9494 12.7329 21.9794C12.7647 22.0093 12.803 22.0315 12.8448 22.0441C12.8866 22.0568 12.9308 22.0596 12.9738 22.0523C13.7593 21.9264 15.5918 21.4626 16.2888 19.8697C16.5349 19.3052 16.5852 18.646 16.5284 17.9868C16.9702 17.6753 17.3839 17.326 17.765 16.9427C19.9198 14.7987 21.4063 10.9225 20.9933 9.12129Z" fill="#6B6ADE" />
            </svg>
            Notifications
        </a>
    </nav>

    <!-- Header -->
    <header class="header">
        <div class="row w-100">
            <div class="col-md-6">
                <div class="head_left">
                    <div class="header_title">
                        <h1>Dashboard</h1>
                    </div>
                </div>
            </div>
            <div class="col-md-6 profileCol">
                <div class="head_right">
                    <div class="profile">
                        <div class="select-container" id="globalRecordsFilterContainer">
                            <button class="select-button" id="globalRecordsFilterButton">This week</button>
                            <div class="dropdown" id="globalRecordsDropdown">
                                <div class="option globalFilterOption selected" data-value="1">This week</div>
                                <div class="option globalFilterOption" data-value="2">Today</div>
                                <div class="option globalFilterOption" data-value="3">Yesterday</div>
                                <div class="option globalFilterOption" data-value="4">Last 7 days</div>
                                <div class="option globalFilterOption" data-value="5">Last 30 days</div>
                                <div class="option globalFilterOption" data-value="6">This Month</div>
                                <div class="option globalFilterOption" data-value="7">This Year</div>
                                <div class="option globalFilterOption" data-value="8">All Time</div>
                            </div>
                        </div>
                        <div class="scan" id="viewFullScreen">
                            <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_160_572)">
                                    <path opacity="0.2" fill-rule="evenodd" clip-rule="evenodd" d="M20 40C31.0457 40 40 31.0457 40 20C40 8.9543 31.0457 0 20 0C8.9543 0 0 8.9543 0 20C0 31.0457 8.9543 40 20 40Z" fill="white" />
                                    <path d="M28.25 15.5C28.25 15.9142 28.5858 16.25 29 16.25C29.4142 16.25 29.75 15.9142 29.75 15.5H28.25ZM24.5 10.25C24.0858 10.25 23.75 10.5858 23.75 11C23.75 11.4142 24.0858 11.75 24.5 11.75V10.25ZM11.75 24.5C11.75 24.0858 11.4142 23.75 11 23.75C10.5858 23.75 10.25 24.0858 10.25 24.5H11.75ZM15.5 29.75C15.9142 29.75 16.25 29.4142 16.25 29C16.25 28.5858 15.9142 28.25 15.5 28.25V29.75ZM15.5 11.75C15.9142 11.75 16.25 11.4142 16.25 11C16.25 10.5858 15.9142 10.25 15.5 10.25V11.75ZM10.25 15.5C10.25 15.9142 10.5858 16.25 11 16.25C11.4142 16.25 11.75 15.9142 11.75 15.5H10.25ZM24.5 28.25C24.0858 28.25 23.75 28.5858 23.75 29C23.75 29.4142 24.0858 29.75 24.5 29.75V28.25ZM29.75 24.5C29.75 24.0858 29.4142 23.75 29 23.75C28.5858 23.75 28.25 24.0858 28.25 24.5H29.75ZM29.75 15.5C29.75 12.6005 27.3995 10.25 24.5 10.25V11.75C26.5711 11.75 28.25 13.4289 28.25 15.5H29.75ZM10.25 24.5C10.25 27.3995 12.6005 29.75 15.5 29.75V28.25C13.4289 28.25 11.75 26.5711 11.75 24.5H10.25ZM15.5 10.25C12.6005 10.25 10.25 12.6005 10.25 15.5H11.75C11.75 13.4289 13.4289 11.75 15.5 11.75V10.25ZM24.5 29.75C27.3995 29.75 29.75 27.3995 29.75 24.5H28.25C28.25 26.5711 26.5711 28.25 24.5 28.25V29.75Z" fill="white" />
                                </g>
                                <defs>
                                    <clipPath id="clip0_160_572">
                                        <rect width="40" height="40" fill="white" />
                                    </clipPath>
                                </defs>
                            </svg>
                        </div>
                        <div class="notify">
                            <span class="notification-trigger">
                                <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_160_568)">
                                        <path opacity="0.2" fill-rule="evenodd" clip-rule="evenodd" d="M20 40C31.0457 40 40 31.0457 40 20C40 8.9543 31.0457 0 20 0C8.9543 0 0 8.9543 0 20C0 31.0457 8.9543 40 20 40Z" fill="white" />
                                        <path d="M14.0278 16.6326L14.7517 16.8287L14.7517 16.8287L14.0278 16.6326ZM18.1584 12.3022L17.9271 11.5888L18.1584 12.3022ZM13.983 16.798L13.2591 16.6019L13.2591 16.6019L13.983 16.798ZM13.7896 20.8674L14.5135 21.0635L13.7896 20.8674ZM26.2104 20.8674L25.4865 21.0635L26.2104 20.8674ZM25.8416 12.3022L26.0729 11.5888L25.8416 12.3022ZM26.017 16.798L26.7409 16.6019L26.017 16.798ZM20 8C15.5817 8 12 11.5817 12 16C12 18.5 13 20.5 14 22H26C27 20.5 28 18.5 28 16C28 11.5817 24.4183 8 20 8ZM20 32C21.1046 32 22 31.1046 22 30H18C18 31.1046 18.8954 32 20 32Z" fill="white" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_160_568">
                                            <rect width="40" height="40" fill="white" />
                                        </clipPath>
                                    </defs>
                                </svg>
                            </span>
                        </div>
                        <div class="profile_img">
                            <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_160_569)">
                                    <path opacity="0.2" fill-rule="evenodd" clip-rule="evenodd" d="M20 40C31.0457 40 40 31.0457 40 20C40 8.9543 31.0457 0 20 0C8.9543 0 0 8.9543 0 20C0 31.0457 8.9543 40 20 40Z" fill="white" />
                                    <path d="M20 12C22.2091 12 24 13.7909 24 16C24 18.2091 22.2091 20 20 20C17.7909 20 16 18.2091 16 16C16 13.7909 17.7909 12 20 12ZM20 22C24.4183 22 28 25.5817 28 30H12C12 25.5817 15.5817 22 20 22Z" fill="white" />
                                </g>
                                <defs>
                                    <clipPath id="clip0_160_569">
                                        <rect width="40" height="40" fill="white" />
                                    </clipPath>
                                </defs>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <img class="border_bg" src="images/header_bg.svg" alt="Logo">
    </header>

    <!-- Main Content -->
    <div class="main-content">
        <div class="top_card_sec">
            <div class="erning_card">
                <div class="multicards">
                    <div class="earn_text">
                        <p>Net Earnings</p>
                        <svg width="46" height="46" viewBox="0 0 46 46" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path opacity="0.21" fill-rule="evenodd" clip-rule="evenodd" d="M0 23V32.5301C0 39.9693 6.03068 46 13.4699 46H23H32.5301C39.9693 46 46 39.9693 46 32.5301V23V13.4699C46 6.03068 39.9693 0 32.5301 0H23H13.4699C6.03068 0 0 6.03068 0 13.4699V23Z" fill="#8280FF" />
                            <path d="M25.6308 17.1731H27.7573L27.7207 15.8738H25.9231L27.6126 12.3714L25.7012 12.211L24.0225 13.5018L24.5822 12.0421L22.623 11.8184L21.9804 13.6424L20.9245 12.2302L18.0197 12.1291L19.9418 15.8744L18.2067 15.8185V17.1726H19.8787C16.3128 19.2661 13.7437 24.7384 13.7437 27.6945C13.7437 31.4003 17.7781 33.4848 22.7542 33.4848C27.7303 33.4848 31.7648 31.4003 31.7648 27.6945C31.7654 24.7384 29.1968 19.2667 25.6308 17.1731ZM23.5077 29.4723V30.7552H22.2784V29.5604C21.4381 29.5237 20.6227 29.2955 20.1462 29.0198L20.5224 27.5488C21.049 27.8386 21.7895 28.1018 22.6044 28.1018C23.3191 28.1018 23.8085 27.825 23.8085 27.3228C23.8085 26.8432 23.4075 26.541 22.4794 26.2275C21.1368 25.775 20.2217 25.1451 20.2217 23.926C20.2217 22.8188 20.9994 21.95 22.3414 21.6867V20.4914H23.5708V21.5986C24.4105 21.637 24.9759 21.8116 25.3892 22.0133L25.0254 23.4351C24.6994 23.2973 24.1221 23.0069 23.2194 23.0069C22.4039 23.0069 22.1404 23.3589 22.1404 23.7108C22.1404 24.1266 22.5796 24.3904 23.6457 24.7932C25.1375 25.3231 25.7401 26.0134 25.7401 27.1466C25.7407 28.2645 24.95 29.2209 23.5077 29.4723Z" fill="#8280FF" />
                        </svg>
                    </div>
                    <div class="earn_grow">
                        <h4>₹1,245,846</h4>
                        <p><span class="grow_per">
                            <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.37289 3.60352L10.714 4.97886L7.85607 7.90972L5.51348 5.50737L1.17383 9.96372L1.99959 10.8105L5.51348 7.20703L7.85607 9.60938L11.5456 5.83169L12.8868 7.20703V3.60352H9.37289Z" fill="#1DAA7B" />
                            </svg>
                            1.8%</span> Up from yesterday</p>
                    </div>
                </div>

                    <div class="multicards">
                        <div class="earn_text">
                            <p>Bookings</p>
                            <svg width="46" height="46" viewBox="0 0 46 46" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.21" fill-rule="evenodd" clip-rule="evenodd" d="M0 23V32.5301C0 39.9693 6.03068 46 13.4699 46H23H32.5301C39.9693 46 46 39.9693 46 32.5301V23V13.4699C46 6.03068 39.9693 0 32.5301 0H23H13.4699C6.03068 0 0 6.03068 0 13.4699V23Z" fill="#FEC53D" />
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M18.9729 11.4805C19.4249 11.4805 19.7914 11.8555 19.7914 12.318V12.5899C19.8447 12.576 19.8983 12.5626 19.952 12.5497C22.1792 12.0151 24.4971 12.0151 26.7242 12.5497C26.778 12.5626 26.8315 12.576 26.8849 12.5899V12.318C26.8849 11.8555 27.2513 11.4805 27.7034 11.4805C28.1554 11.4805 28.5218 11.8555 28.5218 12.318V13.2006C30.8553 14.3541 32.5994 16.535 33.2063 19.1829C33.7288 21.4619 33.7288 23.8338 33.2063 26.1129C32.4519 29.4041 29.9406 31.974 26.7242 32.746C24.4971 33.2806 22.1792 33.2806 19.952 32.746C16.7357 31.974 14.2244 29.4041 13.4699 26.1129C12.9475 23.8338 12.9475 21.4619 13.4699 19.1829C14.0769 16.535 15.821 14.3541 18.1544 13.2006V12.318C18.1544 11.8555 18.5209 11.4805 18.9729 11.4805Z" fill="#FEC53D" />
                            </svg>
                        </div>
                        <div class="earn_grow">
                            <h4>180</h4>
                            <p><span class="grow_down">
                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9.37289 10.8105L10.714 9.43521L7.85607 6.50435L5.51348 8.90669L1.17383 4.45034L1.99959 3.60352L5.51348 7.20703L7.85607 4.80469L11.5456 8.58237L12.8868 7.20703V10.8105H9.37289Z" fill="#F93C65" />
                                </svg>
                                4.3%</span> Down from yesterday</p>
                        </div>
                    </div>

                    <div class="multicards">
                        <div class="earn_text">
                            <p>Vendors</p>
                            <svg width="46" height="46" viewBox="0 0 46 46" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.21" fill-rule="evenodd" clip-rule="evenodd" d="M0 23V32.5301C0 39.9693 6.03068 46 13.4699 46H23H32.5301C39.9693 46 46 39.9693 46 32.5301V23V13.4699C46 6.03068 39.9693 0 32.5301 0H23H13.4699C6.03068 0 0 6.03068 0 13.4699V23Z" fill="#4AD991" />
                                <path d="M19.6668 23.9242C18.8449 24.8799 17.6545 25.437 16.3711 25.4409L16.3709 25.4409C15.5995 25.4409 14.912 25.2836 14.3254 24.9722L14.2887 24.9721V24.9513C12.8774 24.1849 12.0004 22.7115 11.9988 21.0988L11.9988 21.0988C11.9988 20.6383 12.0716 20.1795 12.2164 19.7327C12.2251 19.6911 12.2355 19.649 12.2457 19.6191L12.2469 19.6158L12.247 19.6158L14.2251 14.9277C14.5387 13.9907 15.4026 13.4012 16.506 13.4012H29.426H29.4261C30.5146 13.4036 31.4514 13.998 31.7785 14.9738L33.6182 19.5677C33.6183 19.5678 33.6184 19.568 33.6184 19.5682C33.6379 19.614 33.6512 19.6609 33.661 19.7085C33.6633 19.7177 33.6657 19.7286 33.6674 19.7405C33.8048 20.161 33.8798 20.6223 33.8774 21.0822C33.8774 21.0823 33.8774 21.0824 33.8774 21.0825L33.8274 21.0822C33.8282 22.6694 32.9668 24.1282 31.5784 24.8891L19.6668 23.9242Z" fill="#1AB969" stroke="#D9F7E8" stroke-width="0.1" />
                            </svg>
                        </div>
                        <div class="earn_grow">
                            <h4>489</h4>
                            <p><span class="grow_per">
                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9.37289 3.60352L10.714 4.97886L7.85607 7.90972L5.51348 5.50737L1.17383 9.96372L1.99959 10.8105L5.51348 7.20703L7.85607 9.60938L11.5456 5.83169L12.8868 7.20703V3.60352H9.37289Z" fill="#1DAA7B" />
                                </svg>
                                1.8%</span> Up from yesterday</p>
                        </div>
                    </div>

                    <div class="multicards">
                        <div class="earn_text">
                            <p>Vehicles</p>
                            <svg width="46" height="46" viewBox="0 0 46 46" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.3" fill-rule="evenodd" clip-rule="evenodd" d="M0 23V32.5301C0 39.9693 6.03068 46 13.4699 46H23H32.5301C39.9693 46 46 39.9693 46 32.5301V23V13.4699C46 6.03068 39.9693 0 32.5301 0H23H13.4699C6.03068 0 0 6.03068 0 13.4699V23Z" fill="#FF9066" />
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M32.5 31.3333C31.9847 31.3319 31.4825 31.1709 31.0625 30.8724C30.6424 30.574 30.3251 30.1528 30.1542 29.6667H31.05C32.4786 29.6661 33.8528 29.119 34.8908 28.1375C34.9575 28.3608 35 28.5925 35 28.8333C35 30.2117 33.8783 31.3333 32.5 31.3333ZM15.8333 31.3333C15.3181 31.3319 14.8159 31.1709 14.3958 30.8724C13.9758 30.574 13.6585 30.1528 13.4875 29.6667H18.1792C18.0082 30.1528 17.6909 30.574 17.2708 30.8724C16.8508 31.1709 16.3486 31.3319 15.8333 31.3333Z" fill="#ED774C" />
                            </svg>
                        </div>
                        <div class="earn_grow">
                            <h4>187</h4>
                            <p><span class="grow_per">
                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9.37289 3.60352L10.714 4.97886L7.85607 7.90972L5.51348 5.50737L1.17383 9.96372L1.99959 10.8105L5.51348 7.20703L7.85607 9.60938L11.5456 5.83169L12.8868 7.20703V3.60352H9.37289Z" fill="#1DAA7B" />
                                </svg>
                                1.8%</span> Up from yesterday</p>
                        </div>
                    </div>

                    <div class="multicards">
                        <div class="earn_text">
                            <p>Customers</p>
                            <svg width="46" height="46" viewBox="0 0 46 46" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.2" fill-rule="evenodd" clip-rule="evenodd" d="M0 23V32.5301C0 39.9693 6.03068 46 13.4699 46H23H32.5301C39.9693 46 46 39.9693 46 32.5301V23V13.4699C46 6.03068 39.9693 0 32.5301 0H23H13.4699C6.03068 0 0 6.03068 0 13.4699V23Z" fill="#8280FF" />
                                <path d="M14.8846 17.8571C14.8846 15.1746 17.0801 13 19.7885 13C22.4968 13 24.6923 15.1746 24.6923 17.8571C24.6923 20.5397 22.4968 22.7143 19.7885 22.7143C17.0801 22.7143 14.8846 20.5397 14.8846 17.8571Z" fill="#1B1B6A" />
                                <path d="M16.689 24.9077L16.8945 24.8752C18.8117 24.5721 20.7652 24.5721 22.6824 24.8752L22.8879 24.9077C25.5892 25.3347 27.5769 27.6426 27.5769 30.352C27.5769 31.8145 26.38 33 24.9035 33H14.6734C13.1969 33 12 31.8145 12 30.352C12 27.6426 13.9877 25.3347 16.689 24.9077Z" fill="#1B1B6A" />
                            </svg>
                        </div>
                        <div class="earn_grow">
                            <h4>269</h4>
                            <p><span class="grow_per">
                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9.37289 3.60352L10.714 4.97886L7.85607 7.90972L5.51348 5.50737L1.17383 9.96372L1.99959 10.8105L5.51348 7.20703L7.85607 9.60938L11.5456 5.83169L12.8868 7.20703V3.60352H9.37289Z" fill="#1DAA7B" />
                                </svg>
                                1.8%</span> Up from yesterday</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="chart_sec mt-4">
                <div class="row">
                    <div class="col-md-7">
                        <div class="chart-container" id="netEarnings">
                            <div class="chart-header">
                                <h2 class="chart-title">Net Earnings</h2>
                                <div class="earnings-value" id="earningsValue"></div>
                            </div>
                            <canvas id="earningsChart"></canvas>
                            <div class="custom-tooltip" id="customTooltip">
                                <div class="tooltip-title">Net Earnings</div>
                                <div class="tooltip-value"></div>
                                <div class="tooltip-date"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5" id="chartContainer">
                        <canvas id="doughnutChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Bookings Table -->
            <div class="table_sec mt-4">
                <div class="col-md-12" id="ongoing">
                    <table id="ongoingTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Customer Name</th>
                                <th>Vehicle Details</th>
                                <th>Start Date & Time</th>
                                <th>End Date & Time</th>
                                <th>Status</th>
                                <th>Amount Due</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#256325</td>
                                <td>Eleanor Pena</td>
                                <td>
                                    <div class="vehicle-details">
                                        <img src="images/manufacturer-logo.svg" alt="Logo">
                                        Suzuki Swift Dezire
                                    </div>
                                </td>
                                <td>Dec 30, 2019 05:18</td>
                                <td>Jan 05, 2020 05:18</td>
                                <td>
                                    <span style="display: flex; align-items: center;">
                                        <span style="width: 8px; height: 8px; background: #007bff; border-radius: 50%; margin-right: 8px;"></span>
                                        In route
                                    </span>
                                </td>
                                <td>₹ 3,500.00</td>
                                <td><a href="#" class="action-link">VIEW</a></td>
                            </tr>
                            <tr>
                                <td>#256322</td>
                                <td>Marvin McKinney</td>
                                <td>
                                    <div class="vehicle-details">
                                        <img src="images/manufacturer-logo.svg" alt="Logo">
                                        Hyundai Grand i10
                                    </div>
                                </td>
                                <td>Dec 30, 2019 05:18</td>
                                <td>Jan 05, 2020 05:18</td>
                                <td>
                                    <span style="display: flex; align-items: center;">
                                        <span style="width: 8px; height: 8px; background: #007bff; border-radius: 50%; margin-right: 8px;"></span>
                                        In route
                                    </span>
                                </td>
                                <td>₹ 11,880.00</td>
                                <td><a href="#" class="action-link">VIEW</a></td>
                            </tr>
                            <tr>
                                <td>#256365</td>
                                <td>Darlene Robertson</td>
                                <td>
                                    <div class="vehicle-details">
                                        <img src="images/manufacturer-logo.svg" alt="Logo">
                                        KIA Sonet
                                    </div>
                                </td>
                                <td>Dec 30, 2019 05:18</td>
                                <td>Jan 05, 2020 05:18</td>
                                <td>
                                    <span style="display: flex; align-items: center;">
                                        <span style="width: 8px; height: 8px; background: #007bff; border-radius: 50%; margin-right: 8px;"></span>
                                        In route
                                    </span>
                                </td>
                                <td>₹ 12,500.00</td>
                                <td><a href="#" class="action-link">VIEW</a></td>
                            </tr>
                            <tr>
                                <td>#256371</td>
                                <td>Esther Howard</td>
                                <td>
                                    <div class="vehicle-details">
                                        <img src="images/manufacturer-logo.svg" alt="Logo">
                                        Hyundai Creta
                                    </div>
                                </td>
                                <td>Dec 30, 2019 05:18</td>
                                <td>Jan 05, 2020 05:18</td>
                                <td>
                                    <span style="display: flex; align-items: center;">
                                        <span style="width: 8px; height: 8px; background: #007bff; border-radius: 50%; margin-right: 8px;"></span>
                                        In route
                                    </span>
                                </td>
                                <td>₹ 8,500.00</td>
                                <td><a href="#" class="action-link">VIEW</a></td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- Pagination controls -->
                    <nav aria-label="Customer pagination">
                        <ul class="pagination" id="ongoing-pagination">
                            <!-- Pagination buttons will be dynamically inserted here -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <script>
        const CURRENCY = '₹';
        const BOOKING_STATUS_ARRAY = ['PENDING', 'CONFIRMED', 'ONGOING', 'COMPLETED', 'CANCELLED'];
        const RECORDS_PER_PAGE = 10;
        const RECORDS_PER_PAGE_OPTIONS = [5, 10, 20, 50];
    </script>
    <script src="dist/chartjs/chart.min.js"></script>
    <script src="dist/js/Dashboard/dashboard.js"></script>
</body>
</html>