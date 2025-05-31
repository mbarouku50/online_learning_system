// Define base path for all AJAX requests
const BASE_PATH = '/online_learning_system/';

function addstu() {
    var studname = $("#studname").val();
    var studreg = $("#studreg").val();
    var stuemail = $("#stuemail").val();
    var stupass = $("#stupass").val();

    console.log(studname, studreg, stuemail, stupass);

    // Clear previous status message
    $("#statusRegMsg").html("");

    // Client-side validation
    if (!studname || !studreg || !stuemail || !stupass) {
        $("#statusRegMsg").html('<div class="alert alert-danger">All fields are required!</div>');
        return;
    }

    if (!/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(stuemail)) {
        $("#statusRegMsg").html('<div class="alert alert-danger">Invalid email format!</div>');
        return;
    }

    $.ajax({
        url: BASE_PATH + 'student/addstudent.php',
        type: 'POST',
        data: {
            stusignup: true,
            studname: studname,
            studreg: studreg,
            stuemail: stuemail,
            stupass: stupass
        },
        dataType: 'json',
        success: function(response) {
            console.log('Success:', response);
            if (response.status === 'success') {
                $("#statusRegMsg").html('<div class="alert alert-success">' + response.message + '</div>');
                $("#studRegForm")[0].reset();
                setTimeout(() => {
                    $("#studRegModalCenter").modal('hide');
                }, 2000);
            } else {
                $("#statusRegMsg").html('<div class="alert alert-danger">Error: ' + response.message + '</div>');
            }
        },
        error: function(xhr, status, error) {
            let errorMessage = 'An error occurred. Please try again.';
            if (xhr.responseText) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMessage = response.message || errorMessage;
                } catch (e) {
                    // If not JSON, use the raw response
                    errorMessage = xhr.responseText;
                }
            }
            $("#statusRegMsg").html('<div class="alert alert-danger">' + errorMessage + '</div>');
            console.error('Error:', error, 'Status:', status, 'Response:', xhr.responseText);
        }
    });
}

function checkStudLogin() {
    const stuemail = $("#stuLogemail").val().trim();
    const stupass = $("#stuLogpass").val();
    
    // Clear previous messages
    $("#statusStudLogMsg").empty();
    
    // Validate inputs
    if (!stuemail || !stupass) {
        showLoginMessage('Email and password are required', 'danger');
        return;
    }

    // Show loading state
    showLoginMessage('Authenticating...', 'info');
    $("#stuLoginBtn").prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Logging in');

    $.ajax({
        url: BASE_PATH + 'student/loginstudent.php',
        method: "POST",
        dataType: "json",
        data: {
            checkLogStemail: "checkLogStemail",
            stuemail: stuemail,
            stupass: stupass,
        },
        beforeSend: function() {
            console.log('Sending AJAX to:', BASE_PATH + 'student/loginstudent.php');
        },
        success: function(data) {
            console.log('Success Response:', data);
            if (data.status === "success") {
                showLoginMessage(data.message, 'success');
                setTimeout(() => {
                    window.location.href = data.redirect || "index.php";
                }, 1000);
            } else {
                showLoginMessage(data.message || 'Login failed. Please try again.', 'danger');
                resetLoginButton();
            }
        },
        error: function(xhr, status, error) {
            console.error('Error Details:', xhr, status, error);
            let errorMessage = 'Login failed. Please try again.';
            
            try {
                const response = JSON.parse(xhr.responseText);
                errorMessage = response.message || errorMessage;
            } catch (e) {
                if (xhr.status === 404) {
                    errorMessage = 'Server endpoint not found. Please check the URL path.';
                } else if (xhr.status === 500) {
                    errorMessage = 'Server error. Please try again later.';
                }
            }
            
            showLoginMessage(errorMessage, 'danger');
            resetLoginButton();
        }
    });
}

// Helper functions
function showLoginMessage(message, type) {
    const alertClass = `alert alert-${type} alert-dismissible fade show`;
    $("#statusStudLogMsg").html(`
        <div class="${alertClass}" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `);
}

function resetLoginButton() {
    $("#stuLoginBtn").prop('disabled', false).html('Login');
}

// Handle Enter key submission
$("#stuLogpass").keypress(function(e) {
    if (e.which === 13) {
        checkStudLogin();
    }
});