<?php
// Datenstrom Yellow extensions, https://github.com/datenstrom/yellow-extensions

if (PHP_SAPI!="cli") {
    echo "ERROR making test environment: Please run at the command line!\n";
} else {
    if (!is_dir("tests")) {
        echo "\rMaking test environment 0%... ";
        mkdir("tests/system/extensions", 0777, true);
        $zip = new ZipArchive();
        if ($zip->open("downloads/install.zip")===true) {
            $fileData = $zip->getFromName("install/yellow.php");
            file_put_contents("tests/yellow.php", $fileData);
            $zip->close();
        }
        if ($zip->open("downloads/core.zip")===true) {
            $fileData = $zip->getFromName("core/core.php");
            file_put_contents("tests/system/extensions/core.php", $fileData);
            $zip->close();
        }
        if ($zip->open("downloads/update.zip")===true) {
            $fileData = $zip->getFromName("update/update.php");
            file_put_contents("tests/system/extensions/update.php", $fileData);
            $zip->close();
        }
        echo "\rMaking test environment 10%... ";
        $directoryHandle = opendir("downloads");
        if ($directoryHandle) {
            while (($entry = readdir($directoryHandle))!==false) {
                if (substr($entry, 0, 1)==".") continue;
                copy("downloads/$entry", "tests/system/extensions/$entry");
            }
            closedir($directoryHandle);
        }
        echo "\rMaking test environment 20%... ";
        $fileData = date("Y-m-d H:i:s")." info Make test environment for Datenstrom Yellow extensions\n";
        file_put_contents("tests/system/extensions/yellow-website.log", $fileData);
        $fileData = "# Datenstrom Yellow system settings\n\nSitename: Tests\n";
        file_put_contents("tests/system/extensions/yellow-system.ini", $fileData);
        exec("cd tests; php yellow.php update; php yellow.php skip installation", $outputLines, $returnStatus);
        if ($returnStatus!=0) {
            foreach ($outputLines as $line) echo "$line\n";
            exit($returnStatus);
        }
        file_put_contents("tests/content/contact/page.md", "exclude\n");   //TODO: remove later, exclude contact page for now
        file_put_contents("tests/content/search/page.md", "exclude\n");    //TODO: remove later, exclude search page for now
        echo "\rMaking test environment 100%... done\n";
    }
}
