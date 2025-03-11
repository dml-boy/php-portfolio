<?php
session_start();

// Check if user is logged in (optional)
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Handle errors (if any)
$error = isset($_GET['error']) ? $_GET['error'] : '';
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Post a Job | DML Dev</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        :root {
            --primary: #f39c12;
            --dark: #121212;
            --darker: #1a1a1a;
            --light: #f8f9fa;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--dark);
            color: var(--light);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .job-card {
            background: var(--darker);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            width: 100%;
            max-width: 600px;
            margin: auto;
        }

        .job-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .job-header h2 {
            color: var(--primary);
            margin: 0;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--light);
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(243, 156, 18, 0.1);
        }

        .btn-primary {
            background: var(--primary);
            border: none;
            padding: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(243, 156, 18, 0.3);
        }

        .alert-danger {
            background: rgba(255, 107, 107, 0.1);
            border: 1px solid rgba(255, 107, 107, 0.2);
            color: #ff6b6b;
            margin-bottom: 1.5rem;
        }

        .job-footer {
            margin-top: 1.5rem;
            text-align: center;
        }

        .job-footer a {
            color: var(--primary);
            text-decoration: none;
            transition: opacity 0.3s ease;
        }

        .job-footer a:hover {
            opacity: 0.8;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="job-card">
            <div class="job-header">
                <h2>Post a Job</h2>
                <p>Fill out the form below to submit your job details</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="../process/process_job.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="job_title" class="form-label">Job Title</label>
                    <input type="text"
                        name="job_title"
                        id="job_title"
                        class="form-control"
                        placeholder="e.g., Web Developer"
                        required>
                </div>

                <div class="mb-3">
                    <label for="job_description" class="form-label">Job Description</label>
                    <textarea name="job_description"
                        id="job_description"
                        class="form-control"
                        rows="5"
                        placeholder="Describe the job details..."
                        required></textarea>
                </div>

                <div class="mb-3">
                    <label for="job_budget" class="form-label">Budget (USD)</label>
                    <input type="number"
                        name="job_budget"
                        id="job_budget"
                        class="form-control"
                        placeholder="e.g., 500"
                        min="0"
                        required>
                </div>

                <div class="mb-3">
                    <label for="job_deadline" class="form-label">Deadline</label>
                    <input type="date"
                        name="job_deadline"
                        id="job_deadline"
                        class="form-control"
                        required>
                </div>

                <div class="mb-4">
                    <label for="job_files" class="form-label">Attach Files (Optional)</label>
                    <input type="file"
                        name="job_files[]"
                        id="job_files"
                        class="form-control"
                        multiple
                        accept=".pdf,.doc,.docx,.zip">
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    Submit Job
                </button>
            </form>

            <div class="job-footer mt-4">
                <p>Need help? <a href="contact.php">Contact me</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>