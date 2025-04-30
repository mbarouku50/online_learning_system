function addstu(){
    var studname = $("#studname").val();
    var studreg = $("#studreg").val();
    var stuemail = $("#stuemail").val();
    var stupass = $("#stupass").val();

    $.ajax({
        url:'student/addstudent.php',
        method: 'POST',
        dataType: "json",
        data:{
            stusignup:"stusignup",
            studname: studname,
            studreg: studreg,
            stuemail: stuemail,
            stupass: stupass,
        },
        success:function(data){
            console.log(data);
            if(data.status === "success"){
                $('#successMsg').html("<span class='alert alert-success'>" + data.message + "</span>");
                $("#studRegModalCenter form")[0].reset(); // Clear form after success
            } else {
                $('#successMsg').html("<span class='alert alert-danger'>" + data.message + "</span>");
            }
        },
        error: function(xhr, status, error){
            console.error(error);
        }
    });
}

function checkStuLogin() {
    const stuLogemail = $("#stuLogemail").val().trim();
    const stuLogpass = $("#stuLogpass").val();
    
    // Validate inputs
    if (!stuLogemail || !stuLogpass) {
        showLoginMessage('Email and password are required', 'danger');
        return;
    }

    // Show loading state
    showLoginMessage('Authenticating...', 'info');
    $("#loginBtn").prop('disabled', true)
                 .html('<span class="spinner-border spinner-border-sm"></span> Logging in');

    $.ajax({
        url: 'student/addstudent.php',
        method: "POST",
        data: {
            checkLogemail: "checkLogemail",
            stuLogemail: stuLogemail,
            stuLogpass: stuLogpass,
        },
        success: function(data) {
            if (data.status === "success") {
                showLoginMessage(data.message, 'success');
                
                // Smooth redirect after message is visible
                setTimeout(() => {
                    window.location.href = data.redirect || "index.php";
                }, 1500); // 1.5 second delay
            } else {
                showLoginMessage(data.message, 'danger');
                resetLoginButton();
            }
        },
        error: function(xhr, status, error) {
            showLoginMessage('Login failed. Please try again.', 'danger');
            console.error("AJAX Error:", status, error);
            resetLoginButton();
        }
    });
}

// Helper functions
function showLoginMessage(message, type) {
    const alertClass = `alert alert-${type} alert-dismissible fade show`;
    $("#statusLogMsg").html(`
        <div class="${alertClass}" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `);
}

function resetLoginButton() {
    $("#loginBtn").prop('disabled', false).html('Login');
}

// Handle Enter key submission
$("#stuLogpass").keypress(function(e) {
    if (e.which === 13) {
        checkStuLogin();
    }
});