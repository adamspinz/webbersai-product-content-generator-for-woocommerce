jQuery(document).ready(function($) {
    function updateModels() {
        var endpoint = $('#webbersaicg_endpoint_url').val();
        var modelSelect = $('#webbersaicg_ai_model');
        var selectedModel = modelSelect.val(); // Save the currently selected model
        modelSelect.empty();
        if (endpoint === 'https://api.openai.com/v1/chat/completions') {
            var openaiModels = [
                { value: 'gpt-4o', label: 'gpt-4o' },
                { value: 'gpt-4-turbo', label: 'gpt-4-turbo' },
                { value: 'gpt-3.5-turbo', label: 'gpt-3.5-turbo' },
                { value: 'dall-e-3', label: 'Image - DALL E 3' },
                { value: 'dall-e-2', label: 'Image - DALL E 2' }
            ];
            openaiModels.forEach(function(model) {
                var option = $('<option>').val(model.value).text(model.label);
                modelSelect.append(option);
            });
            $('#organization_id_field').show();
        } else {
            var openrouterModels = [
                { value: 'openrouter/auto', label: 'Auto (best for prompt)' },
                { value: 'nousresearch/nous-capybara-7b:free', label: 'Nous: Capybara 7B (free)' },
                { value: 'openchat/openchat-7b:free', label: 'OpenChat 3.5 (free)' },
                { value: 'gryphe/mythomist-7b:free', label: 'MythoMist 7B (free)' },
                { value: 'undi95/toppy-m-7b:free', label: 'Toppy M 7B (free)' },
                { value: 'openrouter/cinematika-7b:free', label: 'Cinematika 7B (alpha) (free)' },
                { value: 'google/gemma-7b-it:free', label: 'Google: Gemma 7B (free)' },
                { value: 'meta-llama/llama-3-8b-instruct:free', label: 'Meta: Llama 3 8B Instruct (free)' },
                { value: 'microsoft/phi-3-medium-128k-instruct:free', label: 'Phi-3 Medium Instruct (free)' },
                { value: 'mistralai/mistral-7b-instruct:free', label: 'Mistral AI Model' }
            ];
            openrouterModels.forEach(function(model) {
                var option = $('<option>').val(model.value).text(model.label);
                modelSelect.append(option);
            });
            $('#organization_id_field').hide();
        }
        // Restore the previously selected model or set a default value if none is selected
        if (selectedModel) {
            modelSelect.val(selectedModel);
        } else {
            modelSelect.val(modelSelect.find('option:first').val());
        }
    }

    function toggleOrgIdField() {
        var endpoint = $('#webbersaicg_endpoint_url').val();
        if (endpoint === 'https://api.openai.com/v1/chat/completions') {
            $('#organization_id_field').show();
        } else {
            $('#organization_id_field').hide();
        }
    }

    // Initial calls to set the correct display and models on page load
    updateModels();
    toggleOrgIdField();

    // Add event listener to toggle the display and update models when the endpoint is changed
    $('#webbersaicg_endpoint_url').change(function() {
        updateModels();
        toggleOrgIdField();
    });

    // Ensure selected model is retained on form submit
    $('form').on('submit', function() {
        var selectedModel = $('#webbersaicg_ai_model').val();
        $('#webbersaicg_ai_model option[value="' + selectedModel + '"]').attr('selected', 'selected');
    });
});