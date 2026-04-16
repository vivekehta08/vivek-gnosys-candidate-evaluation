<!DOCTYPE html>
<html>
<head>
    <title>Application Update</title>
</head>
<body>
    <h1>Dear {{ $candidate->name }},</h1>
    <p>Thank you for your interest in the position. After careful evaluation, we regret to inform you that we will not be proceeding with your application at this time.</p>
    <p>Your overall score: {{ $candidate->overall_score }}</p>
    <p>We wish you the best in your future endeavors.</p>
    <p>Best regards,<br>Hiring Team</p>
</body>
</html>