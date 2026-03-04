<?php
/**
 * Script to add Link Approvals sidebar item to all admin pages that are missing it.
 * Inserts it right after the Payment Approvals section, matching indentation.
 */

$baseDir = __DIR__ . '/../resources/views';

// Find all blade files with "Payment Approvals" but without "link_approvals"
$files = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($baseDir));
foreach ($iterator as $file) {
    if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
        $content = file_get_contents($file->getPathname());
        if (str_contains($content, 'Payment Approvals') && !str_contains($content, 'link_approvals')) {
            $files[] = $file->getPathname();
        }
    }
}

echo "Found " . count($files) . " files to update:\n";

foreach ($files as $filePath) {
    $content = file_get_contents($filePath);
    $lines = explode("\n", $content);
    $newLines = [];
    $i = 0;
    $insertCount = 0;

    while ($i < count($lines)) {
        $newLines[] = $lines[$i];

        // Look for "Payment Approvals</span>" line
        if (preg_match('/^(\s*).*<span.*>Payment Approvals<\/span>/', $lines[$i], $matches)) {
            $baseIndent = $matches[1];
            
            // Find the closing </a> after this line
            $j = $i + 1;
            while ($j < count($lines)) {
                $newLines[] = $lines[$j];
                if (preg_match('/^\s*<\/a>/', $lines[$j])) {
                    // Determine the indentation of the </a> tag
                    preg_match('/^(\s*)/', $lines[$j], $closingMatch);
                    $indent = $closingMatch[1];
                    
                    // Build the Link Approvals block with matching indentation
                    $innerIndent = $indent . "    ";
                    $block = "\n" .
                        "{$indent}<!-- Link Approvals -->\n" .
                        "{$indent}<a class=\"group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.link_approvals.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}\" href=\"{{ route('admin.link_approvals.index') }}\">\n" .
                        "{$innerIndent}<div class=\"w-8 flex justify-center\">\n" .
                        "{$innerIndent}    <i class=\"fas fa-link text-lg {{ request()->routeIs('admin.link_approvals.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}\"></i>\n" .
                        "{$innerIndent}</div>\n" .
                        "{$innerIndent}<span class=\"text-sm font-medium\">Link Approvals</span>\n" .
                        "{$innerIndent}@php try { \$pendingLinks = \\App\\Models\\StudentLinkRequest::where('status','pending')->count(); } catch (\\Exception \$e) { \$pendingLinks = 0; } @endphp\n" .
                        "{$innerIndent}@if(\$pendingLinks > 0)\n" .
                        "{$innerIndent}  <span class=\"ml-auto bg-amber-100 text-amber-700 text-xs font-bold px-2 py-0.5 rounded-full\">{{ \$pendingLinks }}</span>\n" .
                        "{$innerIndent}@endif\n" .
                        "{$indent}</a>";
                    
                    $newLines[] = $block;
                    $insertCount++;
                    $i = $j;
                    break;
                }
                $j++;
            }
        }
        $i++;
    }

    if ($insertCount > 0) {
        file_put_contents($filePath, implode("\n", $newLines));
        $relativePath = str_replace(realpath($baseDir) . DIRECTORY_SEPARATOR, '', realpath($filePath));
        echo "  Updated: $relativePath ($insertCount insertion(s))\n";
    }
}

echo "\nDone!\n";
