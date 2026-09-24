<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Student Registration Form</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5">

        <div class="row justify-content-center">

            <div class="col-md-6">

                <div class="card shadow">

                    <!-- Card Header -->
                    <div class="card-header bg-primary text-white">

                        <h3 class="mb-0">
                            Student Registration Form
                        </h3>

                    </div>

                    <!-- Card Body -->
                    <div class="card-body">

                        <!-- Error Message Example -->
                        <div class="alert alert-danger">
                            Please correct the errors in the form.
                        </div>

                        <!-- Student Form -->
                        <form method="post" action="#">

                            <!-- First Name -->
                            <div class="mb-3">

                                <label class="form-label">
                                    First Name
                                </label>

                                <input
                                    type="text"
                                    name="firstname"
                                    class="form-control"
                                    placeholder="Enter First Name"
                                >

                            </div>


                            <!-- Second Name -->
                            <div class="mb-3">

                                <label class="form-label">
                                    Second Name
                                </label>

                                <input
                                    type="text"
                                    name="secondname"
                                    class="form-control"
                                    placeholder="Enter Second Name"
                                >

                            </div>


                            <!-- Email -->
                            <div class="mb-3">

                                <label class="form-label">
                                    Email Address
                                </label>

                                <input
                                    type="email"
                                    name="email"
                                    class="form-control"
                                    placeholder="Enter Email"
                                >

                            </div>


                            <!-- Phone Number -->
                            <div class="mb-3">

                                <label class="form-label">
                                    Phone Number
                                </label>

                                <input
                                    type="text"
                                    name="phoneno"
                                    class="form-control"
                                    placeholder="Enter Phone Number"
                                >

                            </div>


                            <!-- Course -->
                            <div class="mb-3">

                                <label class="form-label">
                                    Course
                                </label>

                                <input
                                    type="text"
                                    name="course"
                                    class="form-control"
                                    placeholder="Enter Course"
                                >

                            </div>


                            <!-- Submit Button -->
                            <div class="d-grid mb-2">

                                <input
                                    type="submit"
                                    value="Submit"
                                    class="btn btn-primary"
                                >

                            </div>


                            <!-- Go Back Button -->
                            <a href="#" class="btn btn-light w-100">
                                Go Back
                            </a>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

</body>
</html>

