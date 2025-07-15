(function ($) {
    'use strict';

    $(function () {
        // State object to manage the process
        var automationState = {
            isProcessing: false,
            isCancelled: false,
            // ... other state properties
        };

        // jQuery objects for UI elements
        var $form = $('#emn-automation-form');
        var $button = $('#emn_automation_button');
        var $dropdown = $('#page_size');
        var $progressContainer = $('#emn-progress-container');
        var $progressBar = $('#emn-progress-bar');
        var $progressStatus = $('#emn-progress-status');

        $form.on('submit', function (e) {
            e.preventDefault();
            if (automationState.isProcessing) {
                cancelAutomation();
            } else {
                startAutomation();
            }
        });

        /**
         * Disables the "Cancel" button and changes its text to show it's working.
         * This prevents multiple clicks while waiting for the current batch to finish.
         */
        function cancelAutomation() {
            console.log('Attempting to cancel automation...');
            automationState.isCancelled = true;
            // ✅ Disable button and show loading state
            $button.val('Cancelling...').prop('disabled', true); 
        }

        /**
         * Sets the UI to its initial state, re-enabling controls.
         * Called when the process is complete or successfully cancelled.
         */
        function resetUi() {
            console.log('Resetting UI.');
            automationState.isProcessing = false;
            // ✅ Re-enable dropdown and reset button
            $dropdown.prop('disabled', false);
            $button.val('Run Automation').removeClass('button-secondary').addClass('button-primary').prop('disabled', false);
        }

        /**
         * Sets the UI to a "processing" state.
         * Disables dropdown and changes button to "Cancel".
         */
        function updateUiForProcessing() {
            // ✅ Disable dropdown and set button to "Cancel"
            $dropdown.prop('disabled', true);
            $button.val('Cancel').removeClass('button-primary').addClass('button-secondary').prop('disabled', false); // Ensure it's enabled
            $progressContainer.show();
            $progressBar.css('width', '0%').text('0%');
            $progressStatus.text('Initializing...');
        }

        function startAutomation() {
            console.log('Starting automation...');
            automationState.isProcessing = true;
            automationState.isCancelled = false;
            automationState.pageSize = parseInt($dropdown.val(), 10);
            
            updateUiForProcessing();
            $progressStatus.text('Clearing old files...');

            // Step 1: Clear the directory
            $.ajax({
                url: emn_ai_ajax.ajax_url,
                type: 'POST',
                data: { action: 'emn_ajax_clear_json_directory', nonce: emn_ai_ajax.nonce },
                success: function (response) {
                    if (response.success) {
                        console.log('Directory cleared.');
                        $progressStatus.text('Old files cleared. Getting product count...');
                        // Step 2: Get total product count
                        getTotalProducts();
                    } else {
                        alert('Could not clear the directory. Please check permissions.');
                        resetUi();
                    }
                },
                error: function () {
                    alert('An error occurred while clearing the directory. Check the console (F12).');
                    resetUi();
                }
            });
        }
        
        function getTotalProducts() {
            // ... (no changes in this function)
            console.log('Requesting total product count...');
            $.ajax({
                url: emn_ai_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'emn_ajax_get_total_products',
                    nonce: emn_ai_ajax.nonce,
                },
                success: function (response) {
                    if (response.success) {
                        automationState.totalProducts = response.data.total;
                        if (automationState.totalProducts === 0) {
                            $progressStatus.text('No products to process.');
                            resetUi();
                            return;
                        }
                        automationState.totalPages = Math.ceil(automationState.totalProducts / automationState.pageSize);
                        automationState.processedProducts = 0;
                        automationState.currentPage = 1;

                        updateProgressDisplay();
                        processNextBatch();
                    } else {
                        alert('Could not retrieve product count.');
                        resetUi();
                    }
                },
                error: function () {
                    alert('An error occurred while getting product count.');
                    resetUi();
                }
            });
        }
        
        function processNextBatch() {
            // Check for cancellation *before* making the next request
            if (automationState.isCancelled) {
                console.log('Process cancelled by user.');
                $progressStatus.text('Process cancelled by user.');
                resetUi(); // Reset UI to its initial state
                return;
            }

            if (automationState.currentPage > automationState.totalPages) {
                console.log('All batches complete.');
                $progressStatus.text('Completed. Processed ' + automationState.totalProducts + ' products.');
                resetUi();
                return;
            }
            
            var isLastBatch = (automationState.currentPage === automationState.totalPages);

            $.ajax({
                // ... (AJAX call is the same)
                url: emn_ai_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'emn_ajax_process_batch',
                    nonce: emn_ai_ajax.nonce,
                    page: automationState.currentPage,
                    page_size: automationState.pageSize,
                    is_last_batch: isLastBatch
                },
                success: function(response) {
                    if (response.success) {
                        automationState.processedProducts += response.data.processed;
                        if(automationState.processedProducts > automationState.totalProducts) {
                           automationState.processedProducts = automationState.totalProducts;
                        }
                        updateProgressDisplay();
                        automationState.currentPage++;
                        // Process the next batch
                        processNextBatch(); 
                    } else {
                        alert('An error occurred during processing.');
                        resetUi();
                    }
                },
                error: function() {
                    alert('A critical error occurred. Please check the server logs.');
                    resetUi();
                }
            });
        }
        
        function updateProgressDisplay() {
            // ... (no changes in this function)
            var percentage = 0;
            if (automationState.totalProducts > 0) {
                percentage = Math.round((automationState.processedProducts / automationState.totalProducts) * 100);
            }
            $progressBar.css('width', percentage + '%').text(percentage + '%');
            $progressStatus.text('Processed ' + automationState.processedProducts + ' of ' + automationState.totalProducts + ' products.');
        }

    });

})(jQuery);