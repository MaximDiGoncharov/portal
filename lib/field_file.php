<?php

class field_file
{

    static function header_access_denied()
    {
        http_response_code(403);
    }


    static function header_not_found()
    {
        http_response_code(404);
    }

    // отдает файл расширенния
    static function downloader(array $input)
    {
        $extension_name = (string) $input[2];
        $extension_dir = EXTENSION_ROOT . $extension_name;

        $file_path = $extension_dir . '/' . self::get_path($input);
        if (file_exists($file_path)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_path));

            readfile($file_path);
        } else {
            self::header_not_found();
        }
    }


    static public function uploader($input)
    {
        $extension_name = $input[2];

        if ($extension_name && isset($_FILES['file'])) {
            $file = $_FILES['file'];

            if ($file['error'] === UPLOAD_ERR_OK) {
                $tempFile = $file['tmp_name'];
                $targetDir = EXTENSION_ROOT . $extension_name . '/';
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                $targetFile = $targetDir . $extension_name;
                if (move_uploaded_file($tempFile, $targetFile)) {
                    echo "GZIP-архив успешно сохранен: " . $targetFile;
                    $zip = new ZipArchive();
                    if ($zip->open($targetFile) === TRUE) {
                        $zip->extractTo($targetDir);
                        $zip->close();
                        return "Файлы успешно загружены." . $targetDir;
                    } else {
                        logger::add('Ошибка при загрузке файлов.');
                        return false;
                    }
                } else {
                    self::header_access_denied();
                }
            } else {
                self::header_access_denied();
            }
        } else {
            self::header_access_denied();
        }
    }

    static function get_path($array)
    {
        $slice = array_slice($array, 3);

        $result = implode("/", $slice);
        return $result;
    }
}
