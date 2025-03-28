<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submission Confirmation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .confirmation-container {
            background: white;
            padding: 2rem 3rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
        }
        .success-icon {
            color: #4CAF50;
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        h1 {
            color: #4361ee;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        p {
            color: #555;
            margin-bottom: 2rem;
        }
        .btn-primary {
            background-color: #4361ee;
            border-color: #4361ee;
            padding: 0.75rem 2rem;
            border-radius: 10px;
        }
        .btn-primary:hover {
            background-color: #3f37c9;
            border-color: #3f37c9;
        }
    </style>
</head>
<body>
    <div class="confirmation-container">
        <i class="fas fa-check-circle success-icon"></i>
        <h1>Submission Successful</h1>
        <p>Your form data has been successfully submitted.</p>
        <!-- <a href="index.php" class="btn btn-primary">Return to Dashboard</a> -->
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>