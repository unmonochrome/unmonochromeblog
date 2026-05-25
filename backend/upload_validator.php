<?php
// Funções de validação de upload

class UploadValidator {
    const MAX_FILE_SIZE = 5242880; // 5MB
    const ALLOWED_MIMES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    
    public static function validate($file) {
        // Validar existência
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['valid' => false, 'error' => 'Arquivo inválido'];
        }
        
        // Validar tamanho
        if ($file['size'] > self::MAX_FILE_SIZE) {
            return ['valid' => false, 'error' => 'Arquivo muito grande (máximo 5MB)'];
        }
        
        // Validar extensão
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, self::ALLOWED_EXTENSIONS)) {
            return ['valid' => false, 'error' => 'Formato inválido'];
        }
        
        // Validar MIME type real
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mime, self::ALLOWED_MIMES)) {
            return ['valid' => false, 'error' => 'Tipo de arquivo não permitido'];
        }
        
        // Gerar nome único + seguro
        $uniqueName = bin2hex(random_bytes(16)) . '.' . $ext;
        
        return ['valid' => true, 'filename' => $uniqueName];
    }
}
?>
