<!DOCTYPE html>
<html>
<head>
    <title>Congratulations! You have been selected</title>
</head>
<body>
    <h1>Congratulations {{ $candidate->name }}!</h1>
    <p>We are pleased to inform you that you have been selected for the position.</p>
    <p>Your overall score: {{ $candidate->overall_score }}</p>
    <p>Please contact us for next steps.</p>
    <p>Best regards,<br>Hiring Team</p>
</body>
</html>