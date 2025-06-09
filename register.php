<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Register - Supply Office Inventory</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="assets/img/favicon.ico" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
    <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <!-- Template Main CSS File -->
    <link href="assets/css/style.css" rel="stylesheet">

</head>

<body>
    <?php 
include 'alert.php';
?>
    <main>
        <div class="container">

            <section
                class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5 col-md-6 d-flex flex-column align-items-center justify-content-center">

                            <div class="d-flex justify-content-center py-4">
                                <a href="index.php" class="logo d-flex align-items-center w-auto">
                                    <img src="assets/img/ua-logo.png" alt="" />
                                </a>
                            </div>
                            <!-- End Logo -->

                            <div class="text-center">
                                <h1 class="text-center fw-bold">Supply Office Inventory</h1>
                                <span class="d-none d-lg-block">University of Antique Hamtic Campus</span>
                            </div>

                            <form action="code.php" method="POST" class="row g-3 needs-validation mt-2" novalidate>

                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="text" name="name" class="form-control" id="yourName"
                                            placeholder="Your Name" required>
                                        <label for="yourName">Your Name</label>
                                        <div class="invalid-feedback">Please, enter your name!</div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="email" name="email" class="form-control" id="yourEmail"
                                            placeholder="Your Email" required>
                                        <label for="yourEmail">Your Email</label>
                                        <div class="invalid-feedback">Please enter a valid Email address!</div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-floating has-validation">
                                        <select name="role" class="form-select" id="yourRole" required>
                                            <option value="" selected disabled>Select role</option>
                                            <option value="Admin">Admin</option>
                                            <option value="Staff">Staff</option>
                                        </select>
                                        <label for="yourRole">Role</label>
                                        <div class="invalid-feedback">Please choose a role.</div>
                                    </div>
                                </div>

                                <!-- New Department Field -->
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="text" name="department" class="form-control" id="yourDepartment"
                                            placeholder="Department" required>
                                        <label for="yourDepartment">Department</label>
                                        <div class="invalid-feedback">Please enter your department!</div>
                                    </div>

                                </div>

                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="password" name="password" class="form-control" id="yourPassword"
                                            placeholder="Password" required>
                                        <label for="yourPassword">Password</label>
                                        <div class="invalid-feedback">Please enter your password!</div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="password" name="confirm_password" class="form-control"
                                            id="yourConfirmPassword" placeholder="Password" required>
                                        <label for="yourConfirmPassword">Confirm Password</label>
                                        <div class="invalid-feedback">Please enter your confirm password!</div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <button class="btn btn-danger w-100" type="submit" name="registerBtn">Create
                                        Account</button>
                                </div>

                                <div class="col-12">
                                    <p class="small mb-0">Already have an account? <a href="login.php">Log in</a></p>
                                </div>
                            </form>


                        </div>
                    </div>

                </div>
            </section>

        </div>
    </main><!-- End #main -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/chart.js/chart.umd.js"></script>
    <script src="assets/vendor/echarts/echarts.min.js"></script>
    <script src="assets/vendor/quill/quill.js"></script>
    <script src="assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>

    <!-- Template Main JS File -->
    <script src="assets/js/main.js"></script>

</body>

</html>