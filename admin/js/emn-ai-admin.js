(function ($) {
    'use strict';

    $(function () {
        // Object to hold the state of the automation process
        var automationState = {
            totalProducts: 0,
            processedProducts: 0,
            currentPage: 1,
            pageSize: 100,
            totalPages: 0,
            isCancelled: false,
            isProcessing: false
        };

        var $button = $('#emn_automation_button');
        var $dropdown = $('#page_size');
        var $progressContainer = $('#emn-progress-container');
        var $progressBar = $('#emn-progress-bar');
        var $progressStatus = $('#emn-progress-status');

        $('#emn-automation-form').on('submit', function (e) {
            e.preventDefault();
            if (automationState.isProcessing) {
                cancelAutomation();
            } else {
                startAutomation();
            }
        });

        function startAutomation() {
            automationState.isProcessing = true;
            automationState.isCancelled = false;
            automationState.pageSize = parseInt($dropdown.val(), 10);
            
            updateUiForProcessing();

            // First, get the total number of products
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
                        
                        // Start processing the first batch
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
            if (automationState.isCancelled) {
                $progressStatus.text('Process cancelled by user.');
                resetUi();
                return;
            }

            if (automationState.currentPage > automationState.totalPages) {
                $progressStatus.text('Completed. Processed ' + automationState.totalProducts + ' products.');
                resetUi();
                // Reload to show the updated "Last Run" time
                location.reload(); 
                return;
            }
            
            var isLastBatch = (automationState.currentPage === automationState.totalPages);

            $.ajax({
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
                        // Ensure processed count doesn't exceed total
                        if(automationState.processedProducts > automationState.totalProducts) {
                           automationState.processedProducts = automationState.totalProducts;
                        }

                        updateProgressDisplay();

                        automationState.currentPage++;
                        processNextBatch(); // Process the next page
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
        
        function cancelAutomation() {
            automationState.isCancelled = true;
            $button.val('Cancelling...').prop('disabled', true);
        }

        function updateProgressDisplay() {
            var percentage = 0;
            if (automationState.totalProducts > 0) {
                percentage = Math.round((automationState.processedProducts / automationState.totalProducts) * 100);
            }
            $progressBar.css('width', percentage + '%').text(percentage + '%');
            $progressStatus.text('Processed ' + automationState.processedProducts + ' of ' + automationState.totalProducts + ' products.');
        }
        
        function updateUiForProcessing() {
            $button.val('Cancel').removeClass('button-primary').addClass('button-secondary');
            $dropdown.prop('disabled', true);
            $progressContainer.show();
        }

        function resetUi() {
            automationState.isProcessing = false;
            $button.val('Run Automation').removeClass('button-secondary').addClass('button-primary').prop('disabled', false);
            $dropdown.prop('disabled', false);
        }
    });

})(jQuery);