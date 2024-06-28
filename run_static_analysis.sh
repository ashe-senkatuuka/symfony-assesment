#!/bin/bash

# Run static analysis
./vendor/bin/phpstan analyse > phpstan_results.txt
./vendor/bin/phpcs > phpcs_results.txt

# Apply automatic fixes
./vendor/bin/phpcbf > phpcbf_results.txt

# Run PHP_CodeSniffer
echo "Running PHP_CodeSniffer..."
./vendor/bin/phpcs > phpcs_results.txt

# Apply automatic fixes
echo "Applying automatic fixes..."
./vendor/bin/phpcbf > phpcbf_results.txt

# Get code changes
git diff > code_fixes.diff

# Combine results
echo "Static Analysis Results" > static_analysis_report.txt
echo "========================" >> static_analysis_report.txt
echo "" >> static_analysis_report.txt

echo "PHPStan Results:" >> static_analysis_report.txt
cat phpstan_results.txt >> static_analysis_report.txt
echo "" >> static_analysis_report.txt

echo "PHP_CodeSniffer Results:" >> static_analysis_report.txt
cat phpcs_results.txt >> static_analysis_report.txt
echo "" >> static_analysis_report.txt

echo "Automatic Fixes Applied:" >> static_analysis_report.txt
cat phpcbf_results.txt >> static_analysis_report.txt
echo "" >> static_analysis_report.txt

echo "Code Changes:" >> static_analysis_report.txt
cat code_fixes.diff >> static_analysis_report.txt

# Clean up temporary files
# rm phpstan_results.txt phpcs_results.txt phpcbf_results.txt code_fixes.diff

echo "Static analysis report generated: static_analysis_report.txt"