<?php
// Function to replace ads and analytics in an HTML file
function replaceAdsAndAnalytics($filePath) {
    // Load the HTML file
    $html = file_get_contents($filePath);
	
	//replace old analytics with my code
	


   $html = str_replace('G-6BPGNZNTLZ', 'G-NJ6457W9EC', $html);	

  $html = str_replace('https://faf-games.github.io/', 'https://67unblockedgames.pages.dev/', $html);

 

	
  //removing index.html, to mathc cloudflare redirect to base url
  $html = str_replace('index.html', '', $html);
 //for game pages
 $html = str_replace('.html', '', $html);
	
	

    // Create a new DOMDocument
$dom = new DOMDocument();

// Suppress errors due to malformed HTML
libxml_use_internal_errors(true);

// Load the HTML into the DOMDocument object
$dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

// Create an XPath object to query the DOM
$xpath = new DOMXPath($dom);

// Fix the canonical URL
$canonicalLink = $xpath->query('//link[@rel="canonical"]');
if ($canonicalLink->length > 0) {
    foreach ($canonicalLink as $link) {
        $link->setAttribute('href', 'https://67unblockedgames.pages.dev' . $link->getAttribute('href'));
    }
}


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

// 4. Insert new complex HTML into the "banner-ad-content" div, allowing updates by removing old content first
$adContentDiv = $xpath->query('//div[@class="banner-ad-content"]');
if ($adContentDiv->length > 0) {
    foreach ($adContentDiv as $contentDiv) {
        // Check if the content with a unique ID or class exists
        $existingContent = $xpath->query('.//div[@class="custom-html"]', $contentDiv);
        if ($existingContent->length > 0) {
            // Remove the old content if found
            foreach ($existingContent as $oldNode) {
                $oldNode->parentNode->removeChild($oldNode);
            }
        }

        // Create a new DOMDocument fragment to handle complex HTML
        $newHTML = '<div class="custom-html">3kh0 Games</div>'; // Replace with your actual HTML
        $fragment = $dom->createDocumentFragment();
        $fragment->appendXML($newHTML);

        // Insert the new content at the beginning of the "banner-ad-content" div
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
