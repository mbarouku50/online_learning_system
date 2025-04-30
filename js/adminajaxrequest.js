function checkAdminLogin() {
    const adminLogemail = $("#adminLogemail").val().trim();
    const adminLogpass = $("#adminLogpass").val();
    
    // Clear previous messages
    $("#statusAdminLogMsg").empty();
    
    // Validate inputs
    if (!adminLogemail || !adminLogpass) {
        showLoginMessage('Email and password are required', 'danger');
        return;
    }

    // Show loading state
    showLoginMessage('Authenticating...', 'info');
    $("#loginBtn").prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Logging in');

    $.ajax({
        url: 'admin/admin.php',
        method: "POST",
        dataType: "json",
        data: {
            checkLogemail: "checkLogemail",
            adminLogemail: adminLogemail,
            adminLogpass: adminLogpass,
        },
        success: function(data) {
            if (data.status === "success") {
                showLoginMessage(data.message, 'success');
                // Redirect after short delay
                setTimeout(() => {
                    window.location.href = data.redirect || "admin/adminDashbord.php";
                }, 1000);
            } else {
                showLoginMessage(data.message || 'Login failed. Please try again.', 'danger');
                resetLoginButton();
            }
        },
        error: function(xhr, status, error) {
            let errorMessage = 'Login failed. Please try again.';
            
            // Try to parse error response
            try {
                const response = JSON.parse(xhr.responseText);
                errorMessage = response.message || errorMessage;
            } catch (e) {
                console.error("Error parsing response:", e);
            }
            
            showLoginMessage(errorMessage, 'danger');
            console.error("AJAX Error:", status, error, "Response:", xhr.responseText);
            resetLoginButton();
        }
    });
}

    // Helper functions
function showLoginMessage(message, type) {
    const alertClass = `alert alert-${type} alert-dismissible fade show`;
    $("#statusadminLogMsg").html(`
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

