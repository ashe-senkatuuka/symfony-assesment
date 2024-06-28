<?php
/**
 * Custom script that processes the PHPCS output to improve its readability.
 */

// Read input from stdin
$input = @file_get_contents('php://stdin');
if ($input === false) {
    fwrite(STDERR, "Failed to read input from php://stdin\n");
    exit(1);
}

// Debug: Output the raw input to verify it's being read correctly
file_put_contents('phpcs_raw_input.txt', $input);

// Split input into lines
$lines = explode("\n", trim($input));

$currentFile = '';
$errors = [];

foreach ($lines as $line) {
    // Debug: Output each line to verify parsing
    file_put_contents('phpcs_debug.txt', "Processing line: $line\n", FILE_APPEND);
    
    if (preg_match('/^FILE: (.+)$/', $line, $matches)) {
        $currentFile = $matches[1];
        $errors[$currentFile] = [];
    } elseif ($currentFile !== '' && preg_match('/^ (\d+) \| ERROR \| (.+)$/', $line, $matches)) {
        $errors[$currentFile][] = [
            'line' => $matches[1],
            'message' => $matches[2]
        ];
    }
}

$output = '';
foreach ($errors as $file => $fileErrors) {
    $output .= "\nFile: $file\n";
    $output .= str_repeat('-', strlen($file) + 6) . "\n";
    foreach ($fileErrors as $error) {
        $output .= "Line {$error['line']}: {$error['message']}\n";
    }
    $output .= "\n";
}

$totalFilesWithErrors = count($errors);
$totalErrors = array_sum(array_map('count', $errors));

$output .= "Total files with errors: $totalFilesWithErrors\n";
$output .= "Total errors: $totalErrors\n";

// Debug: Output the processed result to verify the output
file_put_contents('phpcs_debug_output.txt', $output);

// Write output to a file
if (file_put_contents('phpcs_results.txt', $output) === false) {
    fwrite(STDERR, "Failed to write output to phpcs_results.txt\n");
    exit(1);
}

echo "PHPCS results have been processed and saved to phpcs_results.txt\n";
?>
