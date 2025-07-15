(function ($) {
    'use strict';

    $(function () {
        console.log('EMN AI Admin script loaded.'); // 1. เช็คว่าไฟล์ถูกโหลดหรือไม่

        // State object
        var automationState = {
            totalProducts: 0,
            processedProducts: 0,
            currentPage: 1,
            pageSize: 100,
            totalPages: 0,
            isCancelled: false,
            isProcessing: false
        };

        // jQuery objects
        var $form = $('#emn-automation-form');
        var $button = $('#emn_automation_button');
        var $dropdown = $('#page_size');
        var $progressContainer = $('#emn-progress-container');
        var $progressBar = $('#emn-progress-bar');
        var $progressStatus = $('#emn-progress-status');

        $form.on('submit', function (e) {
            e.preventDefault();
            console.log('Form submitted.'); // 2. เช็คว่าอีเวนต์ submit ทำงานหรือไม่

            if (automationState.isProcessing) {
                cancelAutomation();
            } else {
                startAutomation();
            }
        });

        function startAutomation() {
            console.log('Starting automation...');
            automationState.isProcessing = true;
            automationState.isCancelled = false;
            automationState.pageSize = parseInt($dropdown.val(), 10);
            
            updateUiForProcessing();

            console.log('Requesting total product count...');
            $.ajax({
                url: emn_ai_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'emn_ajax_get_total_products',
                    nonce: emn_ai_ajax.nonce,
                },
                success: function (response) {
                    console.log('Get total products response:', response); // 3. เช็คการตอบกลับจากเซิร์ฟเวอร์
                    if (response.success) {
                        automationState.totalProducts = response.data.total;
                        console.log('Total products:', automationState.totalProducts);

                        if (automationState.totalProducts === 0) {
                            $progressStatus.text('No products to process.');
                            resetUi();
                            return;
                        }
                        automationState.totalPages = Math.ceil(automationState.totalProducts / automationState.pageSize);
                        automationState.processedProducts = 0;
                        automationState.currentPage = 1;

                        updateProgressDisplay();
                        
                        console.log('Starting batch processing...');
                        processNextBatch();
                    } else {
                        alert('Could not retrieve product count. Server response: ' + response.data);
                        resetUi();
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error('AJAX Error (get_total_products):', textStatus, errorThrown);
                    alert('An error occurred while getting product count. Check the developer console (F12) for more details.');
                    resetUi();
                }
            });
        }
        
        function processNextBatch() {
            if (automationState.isCancelled) {
                console.log('Process cancelled by user.');
                $progressStatus.text('Process cancelled by user.');
                resetUi();
                return;
            }

            if (automationState.currentPage > automationState.totalPages) {
                console.log('All batches complete.');
                $progressStatus.text('Completed. Processed ' + automationState.totalProducts + ' products.');
                resetUi();
                // We can uncomment the reload once everything works
                // location.reload(); 
                return;
            }
            
            var isLastBatch = (automationState.currentPage === automationState.totalPages);
            console.log('Processing batch. Page: ' + automationState.currentPage + ' of ' + automationState.totalPages);

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
                    console.log('Process batch response:', response); // 4. เช็คการตอบกลับของแต่ละ batch
                    if (response.success) {
                        automationState.processedProducts += response.data.processed;
                        if(automationState.processedProducts > automationState.totalProducts) {
                           automationState.processedProducts = automationState.totalProducts;
                        }
                        updateProgressDisplay();
                        automationState.currentPage++;
                        processNextBatch();
                    } else {
                        alert('An error occurred during processing. Server response: ' + response.data);
                        resetUi();
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('AJAX Error (process_batch):', textStatus, errorThrown);
                    alert('A critical error occurred while processing. Check the developer console (F12).');
                    resetUi();
                }
            });
        }
        
        function cancelAutomation() {
            console.log('Attempting to cancel automation...');
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
            $button.val('Cancel').removeClass('button-primary').addClass('button-secondary').prop('disabled', false);
            $dropdown.prop('disabled', true);
            $progressContainer.show();
            // Reset progress bar at the start
            $progressBar.css('width', '0%').text('0%');
            $progressStatus.text('Initializing...');
        }

        function resetUi() {
            automationState.isProcessing = false;
            $button.val('Run Automation').removeClass('button-secondary').addClass('button-primary').prop('disabled', false);
            $dropdown.prop('disabled', false);
        }
    });

})(jQuery);