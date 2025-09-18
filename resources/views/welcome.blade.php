<!-- resources/views/welcome.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to My Laravel App</title>
    
    <link rel="shortcut icon" href="lrvl icon.png" type="image/png">
    <link rel="stylesheet" href="_assets/BS4/css/bootstrap.min.css">
    <link rel="stylesheet" href="_assets/css/fa-all.css">
    <link rel="stylesheet" href="_assets/css/styles.css">
    <link rel="stylesheet" href="_assets/css/w3.css">
    <link rel="stylesheet" href="_assets/css/coryG_base.css">
</head>
<body class="modebg">
    <div class="flow centroid vh-100">
        <div class="holder">
            <div class="card panelbg shadow-lg border-0 rounded-lg">
                <div class="card-body text-center p-5">
                    <span class="display-4 mb-3 h2">
                        Welcome!
                    </span>
                    <p class="lead text-muted">
                        You've successfully set up your Laravel application.
                    </p>
                    <a href="{{ url('/') }}" class="btn outline">
                        Get started <i class="fa fa-angle-double-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (Optional) -->
    <script src="_assets/js/jquery-3.5.1.slim.min.js"></script>
    <script src="_assets/bs4/js/bootstrap.bundle.min.js"></script>
</body>
</html>
