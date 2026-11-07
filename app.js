// Wait until the HTML document is fully loaded
document.addEventListener('DOMContentLoaded', () => {

    // Select all forms in the webpage
    document.querySelectorAll('form').forEach(form => {

        // Add a submit event listener to each form
        form.addEventListener('submit', event => {

            // Check whether the form inputs are valid
            if (!form.checkValidity()) {

                // Stop form submission if validation fails
                event.preventDefault();

                // Display the browser's validation messages
                form.reportValidity();
            }

        });

    });

});
