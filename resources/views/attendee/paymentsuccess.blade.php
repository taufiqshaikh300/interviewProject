<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Payment Success</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Arial', sans-serif;
        }
        .success-container {
            text-align: center;
            padding: 60px 30px;
            max-width: 800px;
            margin: 0 auto;
        }
        .icon-success {
            font-size: 80px;
            color: #28a745;
            margin-bottom: 20px;
        }
        .success-message {
            font-size: 24px;
            color: #333;
            font-weight: bold;
        }
        .details {
            margin-top: 20px;
            font-size: 18px;
            color: #555;
        }
        .button-container {
            margin-top: 30px;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            text-transform: uppercase;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="success-container">
            <!-- Success Icon -->
            <div class="icon-success">
                <i class="fas fa-check-circle"></i>
            </div>

            <!-- Success Message -->
            <div class="success-message">
                Payment Successful!
            </div>

            <!-- Booking Details -->
            <div class="details">
                <p>Your payment has been processed successfully.</p>
              
            </div>

            <!-- Button for Further Actions -->
            <div class="button-container">
                <a href="{{ route('attendee.mypurchase') }}" class="btn btn-primary">View Tickets</a>
            
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and FontAwesome Icons -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
