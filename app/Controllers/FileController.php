<?php

namespace App\Controllers;

use App\Models\SuratModel;
use App\Models\LampiranModel;
use CodeIgniter\Controller;

class FileController extends Controller
{
    protected $suratModel;
    protected $lampiranModel;
    protected $session;

    public function __construct()
    {
        $this->suratModel = new SuratModel();
        $this->lampiranModel = new LampiranModel();
        $this->session = \Config\Services::session();
        
        // Check authentication
        if (!$this->session->get('is_logged_in')) {
            return redirect()->to('/login');
        }
    }

    public function upload($suratId)
    {
        $surat = $this->suratModel->find($suratId);
        if (!$surat) {
            return $this->response->setJSON(['success' => false, 'message' => 'Surat tidak ditemukan']);
        }

        // Check permission
        $userId = $this->session->get('user_id');
        $userRole = $this->session->get('user_role');
        
        if (!$this->canUploadFile($surat, $userId, $userRole)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Anda tidak memiliki akses untuk upload file']);
        }

        $file = $this->request->getFile('file');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['success' => false, 'message' => 'File tidak valid']);
        }

        // Validate file
        $validation = $this->validateFile($file);
        if (!$validation['valid']) {
            return $this->response->setJSON(['success' => false, 'message' => $validation['message']]);
        }

        // Create upload directory
        $uploadPath = ROOTPATH . 'public/uploads/surat/' . $suratId;
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Generate unique filename
        $extension = $file->getClientExtension();
        $newName = $file->getRandomName();
        
        // Move file
        if (!$file->move($uploadPath, $newName)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal upload file']);
        }

        // Save to database
        $fileData = [
            'nama_file' => $newName,
            'nama_asli' => $file->getClientName(),
            'path_file' => 'uploads/surat/' . $suratId . '/' . $newName,
            'ukuran_file' => $file->getSize(),
            'tipe_file' => $file->getClientMimeType(),
            'keterangan' => $this->request->getPost('keterangan') ?? ''
        ];

        if ($this->lampiranModel->createNewVersion($suratId, $fileData, $userId)) {
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'File berhasil diupload',
                'file_name' => $file->getClientName()
            ]);
        } else {
            // Delete uploaded file if database insert failed
            unlink($uploadPath . '/' . $newName);
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan informasi file']);
        }
    }

    public function download($lampiranId)
    {
        $lampiran = $this->lampiranModel->find($lampiranId);
        if (!$lampiran) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File tidak ditemukan');
        }

        // Check permission
        $surat = $this->suratModel->find($lampiran['surat_id']);
        $userId = $this->session->get('user_id');
        $userRole = $this->session->get('user_role');
        
        if (!$this->canDownloadFile($surat, $userId, $userRole)) {
            return redirect()->to('/surat')->with('error', 'Anda tidak memiliki akses untuk download file ini');
        }

        $filePath = ROOTPATH . 'public/' . $lampiran['path_file'];
        
        if (!file_exists($filePath)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File tidak ditemukan di server');
        }

        return $this->response->download($filePath, null)->setFileName($lampiran['nama_asli']);
    }

    public function preview($lampiranId)
    {
        $lampiran = $this->lampiranModel->find($lampiranId);
        if (!$lampiran) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File tidak ditemukan');
        }

        $surat = $this->suratModel->find($lampiran['surat_id']);
        if (!$surat) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Surat tidak ditemukan');
        }

        $userId = $this->session->get('user_id');
        $userRole = $this->session->get('user_role');
        
        if (!$this->canDownloadFile($surat, $userId, $userRole)) {
            return $this->response->setJSON(['error' => 'Anda tidak memiliki akses untuk preview file ini']);
        }

        $filePath = ROOTPATH . 'public/' . $lampiran['path_file'];
        
        if (!file_exists($filePath)) {
            return $this->response->setJSON(['error' => 'File tidak ditemukan di server']);
        }

        $mimeType = mime_content_type($filePath);
        $fileExtension = strtolower(pathinfo($lampiran['nama_asli'], PATHINFO_EXTENSION));
        
        // Handle different file types
        if (in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
            // For images, return base64 encoded data
            $imageData = base64_encode(file_get_contents($filePath));
            return $this->response->setJSON([
                'type' => 'image',
                'data' => 'data:' . $mimeType . ';base64,' . $imageData,
                'filename' => $lampiran['nama_asli']
            ]);
        } elseif ($mimeType === 'application/pdf') {
            // For PDFs, return file URL for iframe
            return $this->response->setJSON([
                'type' => 'pdf',
                'url' => base_url($lampiran['path_file']),
                'filename' => $lampiran['nama_asli']
            ]);
        } elseif (in_array($fileExtension, ['txt', 'md', 'csv'])) {
            // For text files, return content
            $content = file_get_contents($filePath);
            return $this->response->setJSON([
                'type' => 'text',
                'content' => $content,
                'filename' => $lampiran['nama_asli']
            ]);
        } else {
            // For other files, suggest download
            return $this->response->setJSON([
                'type' => 'download',
                'message' => 'File ini tidak bisa dipreview. Silakan download untuk melihat isinya.',
                'filename' => $lampiran['nama_asli'],
                'downloadUrl' => base_url('file/download/' . $lampiranId)
            ]);
        }
    }

    public function delete($lampiranId)
    {
        $lampiran = $this->lampiranModel->find($lampiranId);
        if (!$lampiran) {
            return $this->response->setJSON(['success' => false, 'message' => 'File tidak ditemukan']);
        }

        $surat = $this->suratModel->find($lampiran['surat_id']);
        $userId = $this->session->get('user_id');
        $userRole = $this->session->get('user_role');
        
        if (!$this->canDeleteFile($surat, $userId, $userRole)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Anda tidak memiliki akses untuk menghapus file ini']);
        }

        // Delete physical file
        $filePath = ROOTPATH . 'public/' . $lampiran['path_file'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Delete from database
        if ($this->lampiranModel->delete($lampiranId)) {
            return $this->response->setJSON(['success' => true, 'message' => 'File berhasil dihapus']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menghapus file']);
        }
    }

    public function history($suratId)
    {
        $surat = $this->suratModel->find($suratId);
        if (!$surat) {
            return $this->response->setJSON(['success' => false, 'message' => 'Surat tidak ditemukan']);
        }

        $history = $this->lampiranModel->getFileHistory($suratId);
        $totalSize = $this->lampiranModel->getTotalFileSize($suratId);

        return $this->response->setJSON([
            'success' => true,
            'history' => $history,
            'total_size' => $totalSize,
            'total_size_formatted' => $this->lampiranModel->formatFileSize($totalSize)
        ]);
    }

    public function uploadForm($suratId)
    {
        $surat = $this->suratModel->find($suratId);
        if (!$surat) {
            return redirect()->to('/surat')->with('error', 'Surat tidak ditemukan');
        }

        $userId = $this->session->get('user_id');
        $userRole = $this->session->get('user_role');
        
        if (!$this->canUploadFile($surat, $userId, $userRole)) {
            return redirect()->to('/surat/' . $suratId)->with('error', 'Anda tidak memiliki akses untuk upload file');
        }

        $lampiran = $this->lampiranModel->getLampiranBySurat($suratId);

        $data = [
            'title' => 'Upload File - ' . $surat['nomor_surat'],
            'surat' => $surat,
            'lampiran' => $lampiran
        ];

        return view('file/upload', $data);
    }

    private function validateFile($file): array
    {
        // Maximum file size (10MB)
        $maxSize = 10 * 1024 * 1024;
        if ($file->getSize() > $maxSize) {
            return ['valid' => false, 'message' => 'Ukuran file maksimal 10MB'];
        }

        // Allowed file types
        $allowedTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif'
        ];

        if (!in_array($file->getClientMimeType(), $allowedTypes)) {
            return ['valid' => false, 'message' => 'Tipe file tidak didukung. Hanya PDF, DOC, DOCX, JPG, PNG, GIF yang diizinkan'];
        }

        // Check file extension
        $allowedExtensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'gif'];
        $extension = strtolower($file->getClientExtension());
        
        if (!in_array($extension, $allowedExtensions)) {
            return ['valid' => false, 'message' => 'Ekstensi file tidak didukung'];
        }

        return ['valid' => true];
    }

    private function canUploadFile($surat, $userId, $userRole): bool
    {
        // Creator can upload during DRAFT and NEED_REVISION
        if ($surat['created_by'] == $userId) {
            return in_array($surat['status'], [
                SuratModel::STATUS_DRAFT,
                SuratModel::STATUS_NEED_REVISION
            ]);
        }

        // Super admin can always upload
        if ($userRole === 'super_admin') {
            return true;
        }

        return false;
    }

    private function canDownloadFile($surat, $userId, $userRole): bool
    {
        // Creator can always download
        if ($surat['created_by'] == $userId) {
            return true;
        }

        // Admin and management roles can download
        if (in_array($userRole, [
            'super_admin', 'dekan', 'wd_akademik', 'wd_kemahasiswa', 'wd_umum',
            'kabag_tu', 'staff_umum', 'kaur_akademik', 'kaur_kemahasis', 
            'kaur_kepegawai', 'kaur_keuangan'
        ])) {
            return true;
        }

        return false;
    }

    private function canDeleteFile($surat, $userId, $userRole): bool
    {
        // Only creator can delete during DRAFT and NEED_REVISION
        if ($surat['created_by'] == $userId) {
            return in_array($surat['status'], [
                SuratModel::STATUS_DRAFT,
                SuratModel::STATUS_NEED_REVISION
            ]);
        }

        // Super admin can always delete
        if ($userRole === 'super_admin') {
            return true;
        }

        return false;
    }
}