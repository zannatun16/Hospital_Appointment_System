// Main JavaScript file
// Location: HospitalAppointmentSystem/assets/js/main.js

// Form validation helper functions
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validatePhone(phone) {
    const re = /^[0-9]{10,11}$/;
    return re.test(phone);
}

function showMessage(message, type) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `alert alert-${type}`;
    messageDiv.innerHTML = message;
    
    const container = document.querySelector('.container');
    if (container) {
        container.insertBefore(messageDiv, container.firstChild);
        setTimeout(() => {
            messageDiv.remove();
        }, 5000);
    }
}

// AJAX helper function
function makeAjaxRequest(url, method, data, callback) {
    const xhr = new XMLHttpRequest();
    xhr.open(method, url, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            callback(JSON.parse(xhr.responseText));
        }
    };
    
    xhr.send(data);
}

// Live search functionality
function liveSearch(inputId, resultsContainer, ajaxUrl) {
    const input = document.getElementById(inputId);
    if (input) {
        input.addEventListener('keyup', function() {
            const searchTerm = this.value;
            if (searchTerm.length > 2) {
                makeAjaxRequest(ajaxUrl, 'POST', 'search=' + searchTerm, function(response) {
                    const container = document.getElementById(resultsContainer);
                    if (container) {
                        container.innerHTML = response.html;
                    }
                });
            }
        });
    }
}