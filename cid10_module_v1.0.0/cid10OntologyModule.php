<?php 
// namespace ExternalModules;
namespace UNIFESP\\cid10OntologyModule;

// Make sure to include the AbstractExternalModule class and the OntologyProvider interface
use ExternalModules\AbstractExternalModule;
use ExternalModules\OntologyProvider;

// class cid10OntologyModule extends AbstractExternalModule implements OntologyProvider {
class cid10OntologyModule extends AbstractExternalModule {

    // --- Implementation of OntologyProvider Interface ---

    public function getOntologyCategories(): array {
        // Define the ontology category your module provides
        return [
            ['value' => 'CID10', 'label' => 'Ontologia Unifesp']
        ];
    }

    public function getOntologyValues($category, $searchTerm): array {
        if ($category !== 'CID10') {
            return []; // Only handle our specific category
        }

        $fastapiUrl = $this->getSystemSetting('fastapi_url');
        if (empty($fastapiUrl)) {
            $this->log("FastAPI URL is not configured in module settings.");
            return [];
        }

        // Construct the URL for your FastAPI search endpoint
        // Make sure to URL-encode the search term
        $url = rtrim($fastapiUrl, '/') . '/cid/search?query=' . urlencode($searchTerm);

        $response = $this->callFastApiService($url);

        $results = [];
        if ($response && is_array($response)) {
            foreach ($response as $item) {
                // Assuming your FastAPI returns 'code' and 'display' for each item
                if (isset($item['code']) && isset($item['display'])) {
                    $results[] = [
                        'code' => $item['code'],
                        'display' => $item['display']
                    ];
                }
            }
        }
        return $results;
    }

    public function getOntologyValueDisplay($category, $code): ?string {
        if ($category !== 'CID10') {
            return null;
        }

        $fastapiUrl = $this->getSystemSetting('fastapi_url');
        if (empty($fastapiUrl)) {
            $this->log("URL FastAPI não configurada.");
            return null;
        }

        // Construct the URL for your FastAPI to get a specific code's display
        $url = rtrim($fastapiUrl, '/') . '/cid/code/' . urlencode($code);

        $response = $this->callFastApiService($url);

        if ($response && isset($response['display'])) {
            return $response['display'];
        }
        return null;
    }

    public function getOntologyValueMetadata($category, $code): ?array {
        // Optional: Implement this if your FastAPI service provides additional metadata
        // beyond 'code' and 'display' that you want to pass to REDCap.
        // For example, if your FastAPI returns a 'definition' field:
        // return ['definition' => $response['definition']];
        return null;
    }

    // --- Helper Function for FastAPI Communication ---

    private function callFastApiService($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); // Set a timeout for the request (e.g., 5 seconds)
        // Add any other cURL options as needed, e.g., for SSL verification if using HTTPS
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Be careful with this in production!

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            $this->log("cURL error for URL {$url}: {$error}");
            return null;
        }

        if ($httpCode >= 200 && $httpCode < 300) {
            // Attempt to decode JSON response
            $data = json_decode($response, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $data;
            } else {
                $this->log("Invalid JSON response from URL {$url}: {$response}");
                return null;
            }
        } else {
            $this->log("FastAPI service returned HTTP {$httpCode} for URL {$url}. Response: {$response}");
            return null;
        }
    }

    // --- REDCap Hooks (Optional but good for debugging/enhancements) ---

    public function redcap_every_page_top($project_id = null) {
        // Example: You could inject custom JavaScript here if needed for specific UI enhancements
        // For basic auto-suggest, REDCap handles the UI.
        // If you need to debug, you can log messages here:
        // $this->log("MyIcdOntologyModule is active on this page.");
    }
}