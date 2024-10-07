<?php
// Function to replace ads and analytics in an HTML file
function replaceAdsAndAnalytics($filePath) {
    // Load the HTML file
    $html = file_get_contents($filePath);
	
	//replace old analytics with my code
	


   $html = str_replace('G-6BPGNZNTLZ', 'G-NJ6457W9EC', $html);	

    // Create a new DOMDocument
$dom = new DOMDocument();

// Suppress errors due to malformed HTML
libxml_use_internal_errors(true);

// Load the HTML into the DOMDocument object
$dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

// Create an XPath object to query the DOM
$xpath = new DOMXPath($dom);

// 1. Remove the script that contains the DoubleClick script source
$doubleClickScript = $xpath->query('//script[@src="https://securepubads.g.doubleclick.net/tag/js/gpt.js"]');
if ($doubleClickScript->length > 0) {
    foreach ($doubleClickScript as $script) {
        $script->parentNode->removeChild($script);
    }
}

// 2. Remove the inline script that uses googletag commands
$googleTagScript = $xpath->query('//script[contains(text(), "googletag")]');
if ($googleTagScript->length > 0) {
    foreach ($googleTagScript as $script) {
        $script->parentNode->removeChild($script);
    }
}

// 3. Remove the div with ID 'div-gpt-ad-1722250890750-0'
$adDiv = $xpath->query('//div[@id="div-gpt-ad-1722250890750-0"]');
if ($adDiv->length > 0) {
    foreach ($adDiv as $div) {
        $div->parentNode->removeChild($div);
    }
}

// 4. Insert new complex HTML into the "banner-ad-content" div, placing it first
$adContentDiv = $xpath->query('//div[@class="banner-ad-content"]');
if ($adContentDiv->length > 0) {
    foreach ($adContentDiv as $contentDiv) {
        // Create a new DOMDocument fragment to handle complex HTML
        $newHTML = '<div>Your new complex HTML content here.</div>'; // Replace with your actual HTML
        $fragment = $dom->createDocumentFragment();
        $fragment->appendXML($newHTML);

        // Insert new content at the beginning of the "banner-ad-content" div
        $contentDiv->insertBefore($fragment, $contentDiv->firstChild);
    }
}

// Save or display the modified HTML
//echo $dom->saveHTML();


    // Save the modified content back to the file
    file_put_contents($filePath, $dom->saveHTML());
}

// Function to recursively process all HTML files in a directory
function processFiles($directory) {
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'html') {
            echo "Processing: " . $file . PHP_EOL;
            replaceAdsAndAnalytics($file);
        }
    }
}

// Execute the script on the specified directory
$directory = __DIR__; // . '/path/to/your/html/files'; // Set your directory path
processFiles($directory);

echo "Finished processing all files.";
