<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION)) {
    session_start();
}

include_once(__DIR__ . '/../dbconnection.php');

if (!isset($_SESSION['is_login'])) {
    header("Location: ../index.php");
    exit;
}

$stuemail = $_SESSION['stuemail'] ?? '';
$course_id = $_GET['course_id'] ?? 0;

// Verify the student is enrolled in this course
if ($course_id) {
    $enrollment_check = $conn->prepare("SELECT order_id FROM courseorder WHERE stuemail = ? AND course_id = ?");
    $enrollment_check->bind_param("si", $stuemail, $course_id);
    $enrollment_check->execute();
    $enrollment_check->store_result();
    
    if ($enrollment_check->num_rows === 0) {
        header("Location: myCourse.php");
        exit;
    }
    $enrollment_check->close();
} else {
    header("Location: myCourse.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Watch Course - E-Learning</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #2c3e50;
            --light-gray: #f8f9fa;
            --dark-gray: #6c757d;
        }
        
        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .course-container {
            max-width: 1400px;
            margin: 20px auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .course-header {
            background-color: var(--secondary-color);
            color: white;
            padding: 15px 20px;
            margin-bottom: 0;
        }
        
        .lesson-container {
            display: flex;
            min-height: 600px;
        }
        
        .lesson-sidebar {
            width: 300px;
            background-color: var(--light-gray);
            border-right: 1px solid #dee2e6;
            overflow-y: auto;
        }
        
        .lesson-content {
            flex: 1;
            padding: 20px;
        }
        
        .lesson-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .lesson-item {
            padding: 12px 15px;
            border-bottom: 1px solid #dee2e6;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .lesson-item:hover {
            background-color: rgba(78, 115, 223, 0.1);
        }
        
        .lesson-item.active {
            background-color: var(--primary-color);
            color: white;
        }
        
        .video-container {
            width: 100%;
            background-color: #000;
            border-radius: 5px;
            overflow: hidden;
        }
        
        #videoarea {
            width: 100%;
            height: auto;
            max-height: 500px;
        }
        
        .lesson-title {
            margin-top: 20px;
            color: var(--secondary-color);
        }
        
        .no-lessons {
            padding: 40px;
            text-align: center;
            color: var(--dark-gray);
        }
        
        @media (max-width: 768px) {
            .lesson-container {
                flex-direction: column;
            }
            
            .lesson-sidebar {
                width: 100%;
                max-height: 300px;
            }
        }
    </style>
</head>
<body>
    <?php include('./stuInclude/header.php'); ?>
    
    <div class="course-container">
        <h4 class="course-header">Course Lessons</h4>
        
        <div class="lesson-container">
            <div class="lesson-sidebar">
                <ul class="lesson-list" id="playlist">
                    <?php
                    // Modified query to remove lesson_order if it doesn't exist
                    $lesson_query = $conn->prepare("SELECT * FROM lesson WHERE course_id = ?");
                    $lesson_query->bind_param("i", $course_id);
                    $lesson_query->execute();
                    $result = $lesson_query->get_result();
                    
                    if ($result->num_rows > 0) {
                        $first_lesson = null;
                        while ($row = $result->fetch_assoc()) {
                            if ($first_lesson === null) {
                                $first_lesson = $row;
                            }
                            echo '<li class="lesson-item" 
                                    data-video="'.htmlspecialchars($row['lesson_link']).'"
                                    data-title="'.htmlspecialchars($row['lesson_name']).'">
                                    <i class="fas fa-play-circle me-2"></i>'
                                    .htmlspecialchars($row['lesson_name']).
                                 '</li>';
                        }
                    } else {
                        echo '<div class="no-lessons">
                                <i class="fas fa-video-slash fa-3x mb-3"></i>
                                <p>No lessons available for this course yet.</p>
                              </div>';
                    }
                    $lesson_query->close();
                    ?>
                </ul>
            </div>
            
            <div class="lesson-content">
                <div class="video-container">
                    <video id="videoarea" controls autoplay>
                        <?php if (isset($first_lesson)): ?>
                            <source src="<?php echo htmlspecialchars($first_lesson['lesson_link']); ?>" type="video/mp4">
                            Your browser does not support the video tag.
                        <?php endif; ?>
                    </video>
                </div>
                <h4 class="lesson-title" id="video-title">
                    <?php echo isset($first_lesson) ? htmlspecialchars($first_lesson['lesson_name']) : 'Select a lesson'; ?>
                </h4>
            </div>
        </div>
    </div>
    
    <?php include('./stuInclude/footer.php'); ?>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Handle lesson selection
            $('.lesson-item').click(function() {
                // Update active state
                $('.lesson-item').removeClass('active');
                $(this).addClass('active');
                
                // Get video data
                const videoUrl = $(this).data('video');
                const videoTitle = $(this).data('title');
                
                // Update video player
                const videoPlayer = $('#videoarea')[0];
                videoPlayer.src = videoUrl;
                videoPlayer.load();
                videoPlayer.play();
                
                // Update title
                $('#video-title').text(videoTitle);
                
                // Track lesson progress (optional)
                trackLessonProgress($(this).index());
            });
            
            // Auto-select first lesson
            if ($('.lesson-item').length > 0) {
                $('.lesson-item').first().addClass('active');
            }
            
            // Function to track lesson progress (example implementation)
            function trackLessonProgress(lessonIndex) {
                // You would typically send this to your server via AJAX
                console.log('Tracking progress for lesson index:', lessonIndex);
                // $.ajax({
                //     url: 'track_progress.php',
                //     method: 'POST',
                //     data: {
                //         course_id: <?php echo $course_id; ?>,
                //         lesson_index: lessonIndex,
                //         stuemail: '<?php echo $stuemail; ?>'
                //     }
                // });
            }
            
            // Handle video ended event to auto-play next lesson
            $('#videoarea').on('ended', function() {
                const currentItem = $('.lesson-item.active');
                const nextItem = currentItem.next('.lesson-item');
                
                if (nextItem.length) {
                    nextItem.click();
                }
            });
        });
    </script>
</body>
</html>