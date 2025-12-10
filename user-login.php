<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />

    <title>Login- Supply Office Inventory</title>
    <meta content="" name="description" />
    <meta content="" name="keywords" />

    <!-- Favicons -->
    <link href="assets/img/favicon.ico" rel="icon" />
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon" />

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect" />
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet" />

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet" />
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet" />
    <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet" />
    <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet" />
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet" />

    <!-- Template Main CSS File -->
    <link href="assets/css/style.css" rel="stylesheet" />
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
                        <div class="col-lg-5 col-md-12 d-flex flex-column justify-content-center">
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

                            <form action="code.php" method="POST" class="row g-3 needs-validation mt-4" novalidate>
                                <div class="mt-5 mb-5">
                                    <div class="form-floating mb-3">
                                        <input type="text" name="email" class="form-control" id="yourEmail"
                                            placeholder="Email Address" required />
                                        <label for="yourEmail">Email Address</label>
                                    </div>

                                    <div class="form-floating mb-4 position-relative">
                                        <input type="password" name="password" class="form-control" id="yourPassword"
                                            placeholder="Password" required />
                                        <label for="yourPassword">Password</label>

                                        <!-- Eye Icon -->
                                        <i class="bi bi-eye-slash toggle-password"
                                            style="position:absolute; top:50%; right:15px; transform:translateY(-50%); cursor:pointer;"></i>
                                    </div>


                                    <button class="btn btn-danger w-100 mb-4" name="loginBtn" type="submit">
                                        Login
                                    </button>

                                    <div class="text-center col-12">
                                        <p class="small mb-0">
                                            Don't have account?
                                            <a href="register.php">Create an account</a>
                                        </p>
                                    </div>
                                </div>

                            </form>


                            <!-- <div class="text-center credits">
                                Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
                            </div> -->
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>
    <!-- End #main -->

    <script>
    document.querySelector(".toggle-password").addEventListener("click", function() {
        const passwordInput = document.getElementById("yourPassword");

        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            this.classList.remove("bi-eye-slash");
            this.classList.add("bi-eye");
        } else {
            passwordInput.type = "password";
            this.classList.remove("bi-eye");
            this.classList.add("bi-eye-slash");
        }
    });
    </script>


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