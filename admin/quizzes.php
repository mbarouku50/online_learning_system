<?php
if(!isset($_SESSION)){
    session_start();
}

include("./admininclude/header.php");
include("../dbconnection.php");

if(isset($_SESSION['is_admin_login'])){
    $adminEmail = $_SESSION['admin_email'];
} else {
    echo "<script> location.href='../index.php'; </script>";
}

// Get course ID from URL
$course_id = isset($_GET['course_id']) ? $_GET['course_id'] : 0;

// Fetch course details
$course_sql = "SELECT course_name FROM course WHERE course_id = ?";
$course_stmt = $conn->prepare($course_sql);
$course_stmt->bind_param("i", $course_id);
$course_stmt->execute();
$course_result = $course_stmt->get_result();
$course_row = $course_result->fetch_assoc();
$course_name = $course_row['course_name'] ?? 'Unknown Course';

// Handle quiz creation with questions
if(isset($_POST['submit_quiz'])){
    $quiz_title = $_POST['quiz_title'];
    $quiz_desc = $_POST['quiz_desc'];
    $total_questions = $_POST['total_questions'];
    $time_limit = $_POST['time_limit'];
    $passing_score = $_POST['passing_score'];
    
    // Insert quiz
    $sql = "INSERT INTO quizzes (course_id, quiz_title, quiz_desc, total_questions, time_limit, passing_score) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issiii", $course_id, $quiz_title, $quiz_desc, $total_questions, $time_limit, $passing_score);
    
    if($stmt->execute()){
        $quiz_id = $conn->insert_id;
        
        // Handle questions
        for($i = 0; $i < $total_questions; $i++){
            $question_text = $_POST['question_text'][$i];
            $question_type = $_POST['question_type'][$i];
            $points = $_POST['points'][$i];
            $sequence_number = $_POST['sequence_number'][$i];
            $options = [$_POST['option_a'][$i], $_POST['option_b'][$i], $_POST['option_c'][$i], $_POST['option_d'][$i]];
            $correct_option = $_POST['correct_option'][$i];
            
            // Insert question
            $question_sql = "INSERT INTO questions (quiz_id, question_text, question_type, points, sequence_number) 
                            VALUES (?, ?, ?, ?, ?)";
            $question_stmt = $conn->prepare($question_sql);
            $question_stmt->bind_param("issii", $quiz_id, $question_text, $question_type, $points, $sequence_number);
            $question_stmt->execute();
            $question_id = $conn->insert_id;
            
            // Insert options
            $option_sql = "INSERT INTO question_options (question_id, option_text, is_correct) VALUES (?, ?, ?)";
            $option_stmt = $conn->prepare($option_sql);
            
            foreach($options as $index => $option_text){
                $is_correct = ($index == $correct_option) ? 1 : 0;
                $option_stmt->bind_param("isi", $question_id, $option_text, $is_correct);
                $option_stmt->execute();
            }
            
            // Handle file upload
            if(isset($_FILES['question_file']['name'][$i]) && $_FILES['question_file']['name'][$i] != ''){
                $file_name = $_FILES['question_file']['name'][$i];
                $file_tmp = $_FILES['question_file']['tmp_name'][$i];
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                $allowed_exts = ['jpg', 'jpeg', 'png', 'pdf'];
                
                if(in_array($file_ext, $allowed_exts)){
                    $new_file_name = uniqid() . '.' . $file_ext;
                    $upload_dir = '../Uploads/';
                    $file_path = $upload_dir . $new_file_name;
                    
                    if(move_uploaded_file($file_tmp, $file_path)){
                        $file_sql = "INSERT INTO question_files (question_id, file_path, file_name) VALUES (?, ?, ?)";
                        $file_stmt = $conn->prepare($file_sql);
                        $file_stmt->bind_param("iss", $question_id, $file_path, $file_name);
                        $file_stmt->execute();
                    } else {
                        $msg = '<div class="alert alert-danger">Error uploading file for question ' . ($i + 1) . '</div>';
                    }
                } else {
                    $msg = '<div class="alert alert-danger">Invalid file type for question ' . ($i + 1) . '. Allowed types: JPG, JPEG, PNG, PDF</div>';
                }
            }
        }
        
        $msg = '<div class="alert alert-success">Quiz and questions created successfully!</div>';
    } else {
        $msg = '<div class="alert alert-danger">Error creating quiz: ' . $conn->error . '</div>';
    }
}

// Handle additional question submission
if(isset($_POST['submit_question'])){
    $quiz_id = $_POST['quiz_id'];
    $question_text = $_POST['question_text'];
    $question_type = $_POST['question_type'];
    $points = $_POST['points'];
    $sequence_number = $_POST['sequence_number'];
    $options = [$_POST['option_a'], $_POST['option_b'], $_POST['option_c'], $_POST['option_d']];
    $correct_option = $_POST['correct_option'];
    
    // Insert question
    $question_sql = "INSERT INTO questions (quiz_id, question_text, question_type, points, sequence_number) 
                    VALUES (?, ?, ?, ?, ?)";
    $question_stmt = $conn->prepare($question_sql);
    $question_stmt->bind_param("issii", $quiz_id, $question_text, $question_type, $points, $sequence_number);
    
    if($question_stmt->execute()){
        $question_id = $conn->insert_id;
        
        // Insert options
        $option_sql = "INSERT INTO question_options (question_id, option_text, is_correct) VALUES (?, ?, ?)";
        $option_stmt = $conn->prepare($option_sql);
        
        foreach($options as $index => $option_text){
            $is_correct = ($index == $correct_option) ? 1 : 0;
            $option_stmt->bind_param("isi", $question_id, $option_text, $is_correct);
            $option_stmt->execute();
        }
        
        // Handle file upload
        if(isset($_FILES['question_file']['name']) && $_FILES['question_file']['name'] != ''){
            $file_name = $_FILES['question_file']['name'];
            $file_tmp = $_FILES['question_file']['tmp_name'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_exts = ['jpg', 'jpeg', 'png', 'pdf'];
            
            if(in_array($file_ext, $allowed_exts)){
                $new_file_name = uniqid() . '.' . $file_ext;
                $upload_dir = '../Uploads/';
                $file_path = $upload_dir . $new_file_name;
                
                if(move_uploaded_file($file_tmp, $file_path)){
                    $file_sql = "INSERT INTO question_files (question_id, file_path, file_name) VALUES (?, ?, ?)";
                    $file_stmt = $conn->prepare($file_sql);
                    $file_stmt->bind_param("iss", $question_id, $file_path, $file_name);
                    $file_stmt->execute();
                } else {
                    $msg = '<div class="alert alert-danger">Error uploading file for new question</div>';
                }
            } else {
                $msg = '<div class="alert alert-danger">Invalid file type for new question. Allowed types: JPG, JPEG, PNG, PDF</div>';
            }
        }
        
        // Update total_questions in quizzes
        $update_sql = "UPDATE quizzes SET total_questions = total_questions + 1 WHERE quiz_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("i", $quiz_id);
        $update_stmt->execute();
        
        $msg = '<div class="alert alert-success">Question added successfully!</div>';
    } else {
        $msg = '<div class="alert alert-danger">Error adding question: ' . $conn->error . '</div>';
    }
}

// Handle additional document upload
if(isset($_POST['submit_document'])){
    $quiz_id = $_POST['quiz_id'];
    $document_name = $_POST['document_name'];
    
    if(isset($_FILES['document_file']['name']) && $_FILES['document_file']['name'] != ''){
        $file_name = $_FILES['document_file']['name'];
        $file_tmp = $_FILES['document_file']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_exts = ['jpg', 'jpeg', 'png', 'pdf'];
        
        if(in_array($file_ext, $allowed_exts)){
            $new_file_name = uniqid() . '.' . $file_ext;
            $upload_dir = '../Uploads/';
            $file_path = $upload_dir . $new_file_name;
            
            if(move_uploaded_file($file_tmp, $file_path)){
                $sql = "INSERT INTO quiz_documents (quiz_id, document_path, document_name) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iss", $quiz_id, $file_path, $document_name);
                
                if($stmt->execute()){
                    $msg = '<div class="alert alert-success">Document uploaded successfully!</div>';
                } else {
                    $msg = '<div class="alert alert-danger">Error uploading document: ' . $conn->error . '</div>';
                }
            } else {
                $msg = '<div class="alert alert-danger">Error moving document file!</div>';
            }
        } else {
            $msg = '<div class="alert alert-danger">Invalid document file type! Allowed types: JPG, JPEG, PNG, PDF</div>';
        }
    } else {
        $msg = '<div class="alert alert-danger">No document file selected!</div>';
    }
}

// Handle question deletion
if(isset($_POST['delete_question'])){
    $question_id = $_POST['question_id'];
    $quiz_id = $_POST['quiz_id'];
    
    // Delete associated files
    $file_sql = "SELECT file_path FROM question_files WHERE question_id = ?";
    $file_stmt = $conn->prepare($file_sql);
    $file_stmt->bind_param("i", $question_id);
    $file_stmt->execute();
    $file_result = $file_stmt->get_result();
    
    while($row = $file_result->fetch_assoc()){
        if($row['file_path'] && file_exists($row['file_path'])){
            unlink($row['file_path']);
        }
    }
    
    // Delete question (cascades to options and files)
    $sql = "DELETE FROM questions WHERE question_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $question_id);
    
    if($stmt->execute()){
        // Update total_questions in quizzes
        $update_sql = "UPDATE quizzes SET total_questions = total_questions - 1 WHERE quiz_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("i", $quiz_id);
        $update_stmt->execute();
        
        $msg = '<div class="alert alert-success">Question deleted successfully!</div>';
    } else {
        $msg = '<div class="alert alert-danger">Error deleting question: ' . $conn->error . '</div>';
    }
}

// Handle quiz deletion
if(isset($_POST['delete_quiz'])){
    $quiz_id = $_POST['quiz_id'];
    
    // Delete associated files
    $file_sql = "SELECT qf.file_path 
                 FROM question_files qf 
                 JOIN questions q ON qf.question_id = q.question_id 
                 WHERE q.quiz_id = ?";
    $file_stmt = $conn->prepare($file_sql);
    $file_stmt->bind_param("i", $quiz_id);
    $file_stmt->execute();
    $file_result = $file_stmt->get_result();
    
    while($row = $file_result->fetch_assoc()){
        if($row['file_path'] && file_exists($row['file_path'])){
            unlink($row['file_path']);
        }
    }
    
    $doc_sql = "SELECT document_path FROM quiz_documents WHERE quiz_id = ?";
    $doc_stmt = $conn->prepare($doc_sql);
    $doc_stmt->bind_param("i", $quiz_id);
    $doc_stmt->execute();
    $doc_result = $doc_stmt->get_result();
    
    while($row = $doc_result->fetch_assoc()){
        if($row['document_path'] && file_exists($row['document_path'])){
            unlink($row['document_path']);
        }
    }
    
    // Delete quiz (cascades to questions, options, and files)
    $sql = "DELETE FROM quizzes WHERE quiz_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $quiz_id);
    if($stmt->execute()){
        $msg = '<div class="alert alert-success">Quiz and associated files deleted successfully!</div>';
    } else {
        $msg = '<div class="alert alert-danger">Error deleting quiz: ' . $conn->error . '</div>';
    }
}
?>
<div class="col-sm-9 mt-5">
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h3 class="mt-4"><?php echo htmlspecialchars($course_name); ?> - Quizzes</h3>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="courses.php">Courses</a></li>
                    <li class="breadcrumb-item active">Quizzes</li>
                </ol>
                
                <?php if(isset($msg)) echo $msg; ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-plus-circle mr-1"></i>
                                Create New Quiz
                            </div>
                            <div class="card-body">
                                <form method="POST" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label for="quiz_title">Quiz Title</label>
                                        <input type="text" class="form-control" id="quiz_title" name="quiz_title" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="quiz_desc">Description</label>
                                        <textarea class="form-control" id="quiz_desc" name="quiz_desc" rows="3" required></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="total_questions">Total Questions</label>
                                        <input type="number" class="form-control" id="total_questions" name="total_questions" min="1" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="time_limit">Time Limit (minutes)</label>
                                        <input type="number" class="form-control" id="time_limit" name="time_limit" min="1" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="passing_score">Passing Score (%)</label>
                                        <input type="number" class="form-control" id="passing_score" name="passing_score" min="1" max="100" required>
                                    </div>
                                    
                                    <!-- Dynamic Questions -->
                                    <div id="questions_container">
                                        <h5>Questions</h5>
                                        <!-- JavaScript will add question fields -->
                                    </div>
                                    
                                    <button type="submit" name="submit_quiz" class="btn btn-primary">Create Quiz</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-question-circle mr-1"></i>
                                Current Quizzes
                            </div>
                            <div class="card-body">
                                <?php
                                $sql = "SELECT * FROM quizzes WHERE course_id = ? ORDER BY quiz_id DESC";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("i", $course_id);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                
                                if($result->num_rows > 0){
                                    while($row = $result->fetch_assoc()){
                                        echo '<div class="quiz-item mb-3 p-3 border rounded">';
                                        echo '<h5>' . htmlspecialchars($row['quiz_title']) . '</h5>';
                                        echo '<p>' . htmlspecialchars($row['quiz_desc']) . '</p>';
                                        echo '<p><strong>Questions:</strong> ' . $row['total_questions'] . '</p>';
                                        echo '<p><strong>Time Limit:</strong> ' . $row['time_limit'] . ' minutes</p>';
                                        echo '<p><strong>Passing Score:</strong> ' . $row['passing_score'] . '%</p>';
                                        
                                        // Action buttons
                                        echo '<div class="btn-group mb-2">';
                                        echo '<button class="btn btn-sm btn-info mr-2" onclick="toggleQuestions(' . $row['quiz_id'] . ')">View Questions</button>';
                                        echo '<button class="btn btn-sm btn-primary mr-2" onclick="toggleAddQuestion(' . $row['quiz_id'] . ')">Add Question</button>';
                                        echo '<form method="POST" class="d-inline">';
                                        echo '<input type="hidden" name="quiz_id" value="' . $row['quiz_id'] . '">';
                                        echo '<button type="submit" name="delete_quiz" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure you want to delete this quiz and all associated questions and documents?\')">Delete Quiz</button>';
                                        echo '</form>';
                                        echo '</div>';
                                        
                                        // Questions display (hidden by default)
                                        echo '<div id="questions_' . $row['quiz_id'] . '" style="display: none;">';
                                        echo '<p><strong>Questions:</strong></p>';
                                        $question_sql = "SELECT * FROM questions WHERE quiz_id = ? ORDER BY sequence_number";
                                        $question_stmt = $conn->prepare($question_sql);
                                        $question_stmt->bind_param("i", $row['quiz_id']);
                                        $question_stmt->execute();
                                        $question_result = $question_stmt->get_result();
                                        
                                        if($question_result->num_rows > 0){
                                            while($question_row = $question_result->fetch_assoc()){
                                                echo '<div class="ml-3 mb-2 border p-2">';
                                                echo '<p><strong>' . htmlspecialchars($question_row['question_text']) . '</strong> (Type: ' . htmlspecialchars($question_row['question_type']) . ', Points: ' . $question_row['points'] . ', Sequence: ' . $question_row['sequence_number'] . ')</p>';
                                                
                                                // Display file
                                                $file_sql = "SELECT file_path, file_name FROM question_files WHERE question_id = ?";
                                                $file_stmt = $conn->prepare($file_sql);
                                                $file_stmt->bind_param("i", $question_row['question_id']);
                                                $file_stmt->execute();
                                                $file_result = $file_stmt->get_result();
                                                if($file_result->num_rows > 0){
                                                    $file_row = $file_result->fetch_assoc();
                                                    $ext = pathinfo($file_row['file_path'], PATHINFO_EXTENSION);
                                                    echo '<p>';
                                                    if(in_array($ext, ['jpg', 'jpeg', 'png'])){
                                                        echo '<img src="' . htmlspecialchars($file_row['file_path']) . '" class="img-fluid" style="max-width: 200px;">';
                                                    } else {
                                                        echo '<a href="' . htmlspecialchars($file_row['file_path']) . '" target="_blank">' . htmlspecialchars($file_row['file_name']) . '</a>';
                                                    }
                                                    echo '</p>';
                                                }
                                                
                                                // Display options
                                                $option_sql = "SELECT option_text, is_correct FROM question_options WHERE question_id = ? ORDER BY option_id";
                                                $option_stmt = $conn->prepare($option_sql);
                                                $option_stmt->bind_param("i", $question_row['question_id']);
                                                $option_stmt->execute();
                                                $option_result = $option_stmt->get_result();
                                                $option_letters = ['A', 'B', 'C', 'D'];
                                                $option_index = 0;
                                                while($option_row = $option_result->fetch_assoc()){
                                                    $correct_mark = $option_row['is_correct'] ? ' (Correct)' : '';
                                                    echo '<p class="ml-3"><strong>' . $option_letters[$option_index] . ':</strong> ' . htmlspecialchars($option_row['option_text']) . $correct_mark . '</p>';
                                                    $option_index++;
                                                }
                                                
                                                // Delete question button
                                                echo '<form method="POST" class="d-inline">';
                                                echo '<input type="hidden" name="question_id" value="' . $question_row['question_id'] . '">';
                                                echo '<input type="hidden" name="quiz_id" value="' . $row['quiz_id'] . '">';
                                                echo '<button type="submit" name="delete_question" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure you want to delete this question?\')">Delete Question</button>';
                                                echo '</form>';
                                                echo '</div>';
                                            }
                                        } else {
                                            echo '<p class="ml-3">No questions found.</p>';
                                        }
                                        echo '</div>';
                                        
                                        // Add question form (hidden by default)
                                        echo '<div id="add_question_' . $row['quiz_id'] . '" style="display: none;">';
                                        echo '<form method="POST" enctype="multipart/form-data" class="mt-2">';
                                        echo '<input type="hidden" name="quiz_id" value="' . $row['quiz_id'] . '">';
                                        echo '<h6>Add New Question</h6>';
                                        echo '<div class="form-group">';
                                        echo '<label for="question_text_' . $row['quiz_id'] . '">Question Text</label>';
                                        echo '<textarea class="form-control" id="question_text_' . $row['quiz_id'] . '" name="question_text" rows="3" required></textarea>';
                                        echo '</div>';
                                        echo '<div class="form-group">';
                                        echo '<label for="question_type_' . $row['quiz_id'] . '">Question Type</label>';
                                        echo '<select class="form-control" id="question_type_' . $row['quiz_id'] . '" name="question_type" required>';
                                        echo '<option value="multiple_choice">Multiple Choice</option>';
                                        echo '</select>';
                                        echo '</div>';
                                        echo '<div class="form-group">';
                                        echo '<label for="points_' . $row['quiz_id'] . '">Points</label>';
                                        echo '<input type="number" class="form-control" id="points_' . $row['quiz_id'] . '" name="points" min="1" required>';
                                        echo '</div>';
                                        echo '<div class="form-group">';
                                        echo '<label for="sequence_number_' . $row['quiz_id'] . '">Sequence Number</label>';
                                        echo '<input type="number" class="form-control" id="sequence_number_' . $row['quiz_id'] . '" name="sequence_number" min="1" required>';
                                        echo '</div>';
                                        echo '<div class="form-group">';
                                        echo '<label for="option_a_' . $row['quiz_id'] . '">Option A</label>';
                                        echo '<input type="text" class="form-control" id="option_a_' . $row['quiz_id'] . '" name="option_a" required>';
                                        echo '</div>';
                                        echo '<div class="form-group">';
                                        echo '<label for="option_b_' . $row['quiz_id'] . '">Option B</label>';
                                        echo '<input type="text" class="form-control" id="option_b_' . $row['quiz_id'] . '" name="option_b" required>';
                                        echo '</div>';
                                        echo '<div class="form-group">';
                                        echo '<label for="option_c_' . $row['quiz_id'] . '">Option C</label>';
                                        echo '<input type="text" class="form-control" id="option_c_' . $row['quiz_id'] . '" name="option_c" required>';
                                        echo '</div>';
                                        echo '<div class="form-group">';
                                        echo '<label for="option_d_' . $row['quiz_id'] . '">Option D</label>';
                                        echo '<input type="text" class="form-control" id="option_d_' . $row['quiz_id'] . '" name="option_d" required>';
                                        echo '</div>';
                                        echo '<div class="form-group">';
                                        echo '<label for="correct_option_' . $row['quiz_id'] . '">Correct Option</label>';
                                        echo '<select class="form-control" id="correct_option_' . $row['quiz_id'] . '" name="correct_option" required>';
                                        echo '<option value="0">A</option>';
                                        echo '<option value="1">B</option>';
                                        echo '<option value="2">C</option>';
                                        echo '<option value="3">D</option>';
                                        echo '</select>';
                                        echo '</div>';
                                        echo '<div class="form-group">';
                                        echo '<label for="question_file_' . $row['quiz_id'] . '">Upload Image/Document (Optional)</label>';
                                        echo '<input type="file" class="form-control-file" id="question_file_' . $row['quiz_id'] . '" name="question_file" accept=".jpg,.jpeg,.png,.pdf">';
                                        echo '</div>';
                                        echo '<button type="submit" name="submit_question" class="btn btn-sm btn-primary">Add Question</button>';
                                        echo '</form>';
                                        echo '</div>';
                                        
                                        // Additional documents
                                        echo '<p><strong>Additional Documents:</strong></p>';
                                        $doc_sql = "SELECT document_name, document_path FROM quiz_documents WHERE quiz_id = ? ORDER BY uploaded_at";
                                        $doc_stmt = $conn->prepare($doc_sql);
                                        $doc_stmt->bind_param("i", $row['quiz_id']);
                                        $doc_stmt->execute();
                                        $doc_result = $doc_stmt->get_result();
                                        if($doc_result->num_rows > 0){
                                            while($doc_row = $doc_result->fetch_assoc()){
                                                $ext = pathinfo($doc_row['document_path'], PATHINFO_EXTENSION);
                                                echo '<p class="ml-3">';
                                                if(in_array($ext, ['jpg', 'jpeg', 'png'])){
                                                    echo '<img src="' . htmlspecialchars($doc_row['document_path']) . '" class="img-fluid" style="max-width: 200px;">';
                                                } else {
                                                    echo '<a href="' . htmlspecialchars($doc_row['document_path']) . '" target="_blank">' . htmlspecialchars($doc_row['document_name']) . '</a>';
                                                }
                                                echo '</p>';
                                            }
                                        } else {
                                            echo '<p class="ml-3">No additional documents.</p>';
                                        }
                                        
                                        // Upload document form
                                        echo '<form method="POST" enctype="multipart/form-data" class="mt-2">';
                                        echo '<input type="hidden" name="quiz_id" value="' . $row['quiz_id'] . '">';
                                        echo '<div class="form-group">';
                                        echo '<label for="document_name_' . $row['quiz_id'] . '">Document Name</label>';
                                        echo '<input type="text" class="form-control" id="document_name_' . $row['quiz_id'] . '" name="document_name" required>';
                                        echo '</div>';
                                        echo '<div class="form-group">';
                                        echo '<label for="document_file_' . $row['quiz_id'] . '">Upload Document</label>';
                                        echo '<input type="file" class="form-control-file" id="document_file_' . $row['quiz_id'] . '" name="document_file" accept=".jpg,.jpeg,.png,.pdf" required>';
                                        echo '</div>';
                                        echo '<button type="submit" name="submit_document" class="btn btn-sm btn-primary">Upload Document</button>';
                                        echo '</form>';
                                        
                                        echo '</div>';
                                    }
                                } else {
                                    echo '<p>No quizzes found for this course.</p>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<!-- JavaScript for dynamic question fields and toggling -->
<script>
document.getElementById('total_questions').addEventListener('change', function(){
    const total = parseInt(this.value) || 0;
    const container = document.getElementById('questions_container');
    container.innerHTML = '<h5>Questions</h5>';
    
    for(let i = 0; i < total; i++){
        container.innerHTML += `
            <div class="card mb-3">
                <div class="card-body">
                    <h6>Question ${i + 1}</h6>
                    <div class="form-group">
                        <label for="question_text_${i}">Question Text</label>
                        <textarea class="form-control" id="question_text_${i}" name="question_text[${i}]" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="question_type_${i}">Question Type</label>
                        <select class="form-control" id="question_type_${i}" name="question_type[${i}]" required>
                            <option value="multiple_choice">Multiple Choice</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="points_${i}">Points</label>
                        <input type="number" class="form-control" id="points_${i}" name="points[${i}]" min="1" required>
                    </div>
                    <div class="form-group">
                        <label for="sequence_number_${i}">Sequence Number</label>
                        <input type="number" class="form-control" id="sequence_number_${i}" name="sequence_number[${i}]" min="1" required>
                    </div>
                    <div class="form-group">
                        <label for="option_a_${i}">Option A</label>
                        <input type="text" class="form-control" id="option_a_${i}" name="option_a[${i}]" required>
                    </div>
                    <div class="form-group">
                        <label for="option_b_${i}">Option B</label>
                        <input type="text" class="form-control" id="option_b_${i}" name="option_b[${i}]" required>
                    </div>
                    <div class="form-group">
                        <label for="option_c_${i}">Option C</label>
                        <input type="text" class="form-control" id="option_c_${i}" name="option_c[${i}]" required>
                    </div>
                    <div class="form-group">
                        <label for="option_d_${i}">Option D</label>
                        <input type="text" class="form-control" id="option_d_${i}" name="option_d[${i}]" required>
                    </div>
                    <div class="form-group">
                        <label for="correct_option_${i}">Correct Option</label>
                        <select class="form-control" id="correct_option_${i}" name="correct_option[${i}]" required>
                            <option value="0">A</option>
                            <option value="1">B</option>
                            <option value="2">C</option>
                            <option value="3">D</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="question_file_${i}">Upload Image/Document (Optional)</label>
                        <input type="file" class="form-control-file" id="question_file_${i}" name="question_file[${i}]" accept=".jpg,.jpeg,.png,.pdf">
                    </div>
                </div>
            </div>
        `;
    }
});

function toggleQuestions(quizId) {
    const questionsDiv = document.getElementById('questions_' + quizId);
    questionsDiv.style.display = questionsDiv.style.display === 'none' ? 'block' : 'none';
}

function toggleAddQuestion(quizId) {
    const addQuestionDiv = document.getElementById('add_question_' + quizId);
    addQuestionDiv.style.display = addQuestionDiv.style.display === 'none' ? 'block' : 'none';
}
</script>
<?php
include("./admininclude/footer.php");
?>