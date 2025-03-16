<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment Dashboard</title>

    <!-- Bootstrap for Styling -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        body {
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .dashboard {
            max-width: 600px;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .dashboard h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }
        .btn-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .btn-dashboard {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
            font-size: 18px;
            font-weight: bold;
            transition: all 0.3s ease-in-out;
        }
        .btn-dashboard i {
            margin-right: 10px;
            font-size: 20px;
        }
        .btn-dashboard:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>

<div class="dashboard">
    <h2>Assessment Dashboard</h2>
    
    <div class="btn-container">
        <a href="create_assessment.php" class="btn btn-primary btn-dashboard">
            <i class="fa-solid fa-plus"></i> Create Assessment
        </a>
        
        <a href="manage_assessment.php" class="btn btn-success btn-dashboard">
            <i class="fa-solid fa-list"></i> Manage Assessments
        </a>
        
        <a href="submitted_assessment.php" class="btn btn-info btn-dashboard">
            <i class="fa-solid fa-users"></i> View Student Responses
        </a>
        <a href="assign_assessment.php" class="btn btn-info btn-dashboard">
            <i class="fa-solid fa-users"></i> assign assessment
        </a>
    </div>
</div>

</body>
</html>
