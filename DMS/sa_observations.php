<?php
include 'config.php';

// Fetch SA Team members
$stmt = $pdo->prepare("SELECT UserID, Username FROM Users WHERE Role = 'SA Team'");
$stmt->execute();
$sa_team_members = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userID = $_POST['sa_team_member'];
    $details = $_POST['details'];
    $category = $_POST['category'] ?: null;

    // Fetch the Username for the selected UserID
    $stmt = $pdo->prepare("SELECT Username FROM Users WHERE UserID = ?");
    $stmt->execute([$userID]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $username = $user['Username'] ?? null;

    if ($username) {
        // Insert the observation, including the Name (Username)
        $stmt = $pdo->prepare("INSERT INTO SATeamObservations (UserID, Name, Details, Category) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userID, $username, $details, $category]);
    } else {
        // Handle the case where the Username is not found (optional)
        // For now, we'll still insert the record without the Name
        $stmt = $pdo->prepare("INSERT INTO SATeamObservations (UserID, Details, Category) VALUES (?, ?, ?)");
        $stmt->execute([$userID, $details, $category]);
    }

    header("Location: submission_confirmation.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SA Observations</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4cc9f0;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --success-color: #4CAF50;
            --warning-color: #ff9800;
            --danger-color: #f44336;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
            color: #333;
        }
        
        .navbar {
            background-color: white !important;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color) !important;
            font-size: 1.5rem;
        }
        
        .nav-link {
            font-weight: 500;
            color: #555 !important;
            margin: 0 5px;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            color: var(--primary-color) !important;
            transform: translateY(-2px);
        }
        
        .nav-link.active {
            color: var(--primary-color) !important;
            border-bottom: 3px solid var(--primary-color);
        }
        
        .observations-container {
            padding: 30px 0;
        }
        
        .page-title {
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 30px;
            border-left: 5px solid var(--primary-color);
            padding-left: 15px;
        }
        
        .card {
            border-radius: 10px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
            margin-bottom: 25px;
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            font-weight: 600;
            font-size: 1.1rem;
            padding: 15px 20px;
            display: flex;
            align-items: center;
        }
        
        .card-header i {
            margin-right: 10px;
            color: var(--primary-color);
        }
        
        .card-body {
            padding: 20px;
        }
        
        .form-label {
            font-weight: 500;
            color: #555;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }
        
        .form-label i {
            margin-right: 10px;
            color: var(--primary-color);
        }
        
        .form-control {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 12px 15px;
            transition: all 0.3s;
            background-color: #f8f9fa;
            font-size: 0.95rem;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
            background-color: #fff;
        }
        
        textarea.form-control {
            min-height: 150px;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 10px;
            padding: 12px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(67, 97, 238, 0.2);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(67, 97, 238, 0.3);
        }
        
        .category-tag {
            display: inline-block;
            padding: 6px 15px;
            background-color: rgba(67, 97, 238, 0.1);
            color: var(--primary-color);
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            margin-right: 10px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .category-tag:hover {
            background-color: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }
        
        .category-suggestions {
            display: flex;
            flex-wrap: wrap;
            margin-top: 10px;
        }
        
        .small-text {
            color: #777;
            font-size: 0.85rem;
            margin-top: 5px;
        }

        .header-banner {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 4px 20px rgba(22, 163, 74, 0.2);
        }
        
        .header-content {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .header-icon {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }
        
        .header-text h1 {
            font-weight: 700;
            margin-bottom: 0.5rem;
            font-size: 1.8rem;
        }
        
        .header-text p {
            opacity: 0.9;
            margin-bottom: 0;
            font-size: 1rem;
        }
        
        @media (max-width: 768px) {
            .category-suggestions {
                flex-direction: column;
            }
            
            .category-tag {
                margin-bottom: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="header-banner">
        <div class="container">
            <div class="header-content">
                <div class="header-icon">
                    <i class="fas fa-eye"></i>
                </div>
                <div class="header-text">
                    <h1>SA Team Observations</h1>
                    <p>Record and categorize your observations to help improve processes and communication.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container observations-container">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-clipboard-list"></i> Record New Observation
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-user"></i> SA Team Member
                        </label>
                        <select name="sa_team_member" class="form-control" required>
                            <option value="">Select SA Team Member</option>
                            <?php foreach ($sa_team_members as $member): ?>
                                <option value="<?php echo $member['UserID']; ?>">
                                    <?php echo htmlspecialchars($member['Username']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="small-text">
                            <i class="fas fa-info-circle me-1"></i> Select the SA Team member making this observation.
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-search"></i> Observation Details
                        </label>
                        <textarea name="details" class="form-control" placeholder="Enter detailed observations..." required></textarea>
                        <div class="small-text">
                            <i class="fas fa-info-circle me-1"></i> Describe what you observed, including context, behaviors, and outcomes.
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-tag"></i> Category
                        </label>
                        <input type="text" name="category" id="categoryInput" class="form-control" placeholder="Enter a category...">
                        
                        <div class="category-suggestions">
                            <span class="category-tag" onclick="selectCategory('Process Improvement')">Process Improvement</span>
                            <span class="category-tag" onclick="selectCategory('Communication')">Communication</span>
                            <span class="category-tag" onclick="selectCategory('Technical Issue')">Technical Issue</span>
                            <span class="category-tag" onclick="selectCategory('Training Need')">Training Need</span>
                            <span class="category-tag" onclick="selectCategory('Success Story')">Success Story</span>
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Submit Observation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function selectCategory(category) {
            document.getElementById('categoryInput').value = category;
        }
    </script>
</body>
</html>