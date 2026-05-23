document.addEventListener('DOMContentLoaded', function() {
    const starRating = document.querySelector('.star-rating');
    const stars = starRating.querySelectorAll('i');
    const ratingInput = document.getElementById('rating');
    const feedbackForm = document.getElementById('feedbackForm');

    // Handle star rating selection
    stars.forEach(star => {
        star.addEventListener('click', () => {
            const rating = star.getAttribute('data-rating');
            ratingInput.value = rating;
            
            // Update stars visual
            stars.forEach(s => {
                if (s.getAttribute('data-rating') <= rating) {
                    s.classList.add('active');
                } else {
                    s.classList.remove('active');
                }
            });
        });
    });

    // Handle form submission
    // Handle form submission
feedbackForm.addEventListener('submit', function(e) {
    e.preventDefault();

    // Get form data
    const formData = new FormData();
    formData.append('rating', ratingInput.value);
    formData.append('category', document.getElementById('category').value);
    formData.append('comment', document.getElementById('comment').value);

    // Show loading state
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;
    submitButton.disabled = true;
    submitButton.textContent = 'Submitting...';

    // Send feedback to server
    fetch('feedback.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new TypeError('Response was not JSON');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Create and show popup
            const popup = document.createElement('div');
            popup.style.cssText = 'position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: #4CAF50; color: white; padding: 20px; border-radius: 5px; text-align: center; z-index: 1000;';
            popup.textContent = 'Feedback submitted successfully!';
            document.body.appendChild(popup);
            
            // Clear form
            feedbackForm.reset();
            stars.forEach(s => s.classList.remove('active'));
            ratingInput.value = '';
            
            // Redirect to home page after 3 seconds
            setTimeout(() => {
                window.location.href = 'index.html';
            }, 3000);
        } else {
            throw new Error(data.message || 'Failed to submit feedback');
        }
    })
    .catch(error => {
        // Create and show error popup
        const popup = document.createElement('div');
        popup.style.cssText = 'position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: #dc3545; color: white; padding: 20px; border-radius: 5px; text-align: center; z-index: 1000;';
        popup.textContent = error.message;
        document.body.appendChild(popup);
        
        // Remove popup after 3 seconds
        setTimeout(() => {
            popup.remove();
        }, 3000);
    })
    .finally(() => {
        // Reset button state
        submitButton.disabled = false;
        submitButton.textContent = originalText;
    });
});

});