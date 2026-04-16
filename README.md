# Smart Candidate Evaluation System

## Project Description

The Smart Candidate Evaluation System is a role-based candidate management and evaluation platform built on Laravel 9. It automates the hiring workflow from resume submission to final selection, incorporating multi-stage evaluations and AI-driven insights to streamline the recruitment process.

The primary purpose of this project is to enhance efficiency in candidate hiring by providing a structured, automated workflow that reduces manual effort and improves decision-making through data-driven evaluations.

## Features

### Candidate Module
- **Add Candidate**: Capture essential details including Name, Email, Phone, and Resume Upload.
- **Candidate Listing**: View a comprehensive list of all candidates.
- **Candidate Detail View**: Access detailed information for individual candidates.

### Resume Screening
- **Status Management**: Update candidate status to Pending, Shortlisted, or Rejected.
- **Remarks**: Add comments and notes during the screening process.

### Multi-Round Evaluation
- **HR Round**: Provide feedback and assign scores.
- **Technical Round**: Conduct technical assessments with feedback and scoring.
- **Task Manual Round**: Manual evaluation of tasks with feedback and scores.
- **Task AI Round**: Automated evaluation generated using AI integration.

### Final Evaluation
- **Automatic Overall Score Calculation**: Compute aggregate scores from all rounds.
- **Final Status**: Determine and assign Selected or Rejected status.

### Dashboard
- **Candidate Overview**: Display all candidates with their current stage, overall score, and final status.
- **Analytics**: Provide insights into the evaluation process.

## Role-Based Access System

### Admin
- Full access to the system, including managing candidates, viewing dashboards, and analytics.
- Control and oversee all evaluation stages.

### HR
- Add and manage candidates.
- Perform resume screening.
- Conduct HR and Technical evaluations.
- Generate AI-based Task evaluations.

## AI Integration (Google Gemini)

The system integrates the Google Gemini API to automate the Task AI Round evaluation.

- **AI Analysis**: The AI evaluates HR feedback, Technical feedback, and overall candidate performance.
- **Automated Generation**: Produces Task evaluation feedback and assigns a score on a scale of 1–10.
- **User Interaction**: Includes a "Generate AI Task Evaluation" button that sends data to the Gemini API, parses the response, and saves it to the database.

## Technologies Used

- **Laravel 9** (PHP 8.0): Framework for backend development.
- **MySQL**: Database management.
- **Bootstrap 5**: Frontend styling and responsiveness.
- **AJAX**: Asynchronous data handling.
- **REST API**: API development for integrations.
- **Google Gemini API**: AI-powered evaluations.

## Installation Steps

1. Clone the repository: `git clone repository-url`
2. Navigate to the project directory: `cd candidate-system`
3. Install dependencies: `composer install`
4. Set up the environment file: Copy `.env.example` to `.env` and configure database and API keys.
5. Run migrations: `php artisan migrate`
6. Run Seeder: `php artisan make:seeder` 
6. Start the server: `php artisan serve`

## API Endpoints

- **Add Candidate API**: `POST /api/candidates` - Create a new candidate.
- **Get Candidate List API**: `GET /api/candidates` - Retrieve list of candidates.
- **Update Candidate Status API**: `PUT /api/candidates/{id}/status` - Update candidate status.

## Project Structure

- **Models**: Candidate, Evaluation, Screening – Define data structures.
- **Controllers**: Handle CRUD operations and AI logic.
- **Services**: Manage Gemini API integration.
- **Blade Views**: Render user interfaces.
- **Routes**: Define web and API routes.

## Future Improvements

- Implement an email notification system for status updates.
- Enhance AI scoring with more advanced algorithms.
- Add charts and analytics for better insights.
- Enable export of reports in PDF or Excel formats.