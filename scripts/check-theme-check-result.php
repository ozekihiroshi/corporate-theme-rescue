<?php
// Theme Check can return exit 0 with warnings. Fail explicitly on non-INFO results.
$findings = json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR);
if (!is_array($findings)) { throw new RuntimeException('Invalid Theme Check report'); }
foreach ($findings as $finding) {
    if (($finding['type'] ?? '') !== 'INFO') {
        fwrite(STDERR, "THEME_CHECK_FAILED\n");
        exit(1);
    }
}
echo "\nTHEME_CHECK_OK: no non-INFO findings\n";
