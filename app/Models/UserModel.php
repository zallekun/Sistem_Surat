<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = [
        'nama', 'email', 'password', 'role', 'prodi_id', 'divisi_id',
        'nip', 'jabatan', 'telepon', 'foto', 'workload', 'is_active',
        'last_login', 'email_verified_at', 'remember_token'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'is_active' => 'boolean',
        'workload' => 'integer',
        'prodi_id' => '?integer',
        'divisi_id' => '?integer',
    ];
    
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'nama' => 'required|min_length[3]|max_length[100]',
        'email' => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password' => 'required|min_length[6]',
        'role' => 'required|in_list[super_admin,dekan,wd_akademik,wd_kemahasiswa,wd_umum,kabag_tu,staff_umum,kaur_akademik,kaur_kemahasis,kaur_kepegawai,kaur_keuangan,admin_prodi]',
        'nip' => 'permit_empty|is_unique[users.nip,id,{id}]|max_length[30]',
        'telepon' => 'permit_empty|max_length[20]',
    ];

    protected $validationMessages = [
        'email' => [
            'is_unique' => 'Email sudah terdaftar dalam sistem'
        ],
        'nip' => [
            'is_unique' => 'NIP sudah terdaftar dalam sistem'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    protected $callbacks = [
        'beforeInsert' => ['hashPassword'],
        'beforeUpdate' => ['hashPassword']
    ];

    protected function hashPassword(array $data)
    {
        if (!isset($data['data']['password'])) {
            return $data;
        }

        $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_BCRYPT);
        return $data;
    }

    public function authenticate(string $email, string $password): ?array
    {
        $user = $this->where('email', $email)
                    ->where('is_active', true)
                    ->first();

        if ($user && password_verify($password, $user['password'])) {
            // Update last login
            $this->update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);
            
            // Remove password from return data
            unset($user['password']);
            return $user;
        }

        return null;
    }

    public function getUserWithRelations(int $userId): ?array
    {
        $user = $this->select('users.*, prodi.nama_prodi, prodi.kode_prodi, fakultas.nama_fakultas, divisi.nama_divisi')
                    ->join('prodi', 'prodi.id = users.prodi_id', 'left')
                    ->join('fakultas', 'fakultas.id = prodi.fakultas_id', 'left')
                    ->join('divisi', 'divisi.id = users.divisi_id', 'left')
                    ->where('users.id', $userId)
                    ->where('users.is_active', true)
                    ->first();

        if ($user) {
            unset($user['password']);
        }

        return $user;
    }

    public function getUsersByRole(string $role, bool $activeOnly = true): array
    {
        $builder = $this->where('role', $role);
        
        if ($activeOnly) {
            $builder->where('is_active', true);
        }

        return $builder->findAll();
    }

    public function getAvailableStaff(string $role = 'staff_umum'): array
    {
        return $this->where('role', $role)
                   ->where('is_active', true)
                   ->orderBy('workload', 'ASC')
                   ->findAll();
    }

    public function incrementWorkload(int $userId): bool
    {
        $user = $this->find($userId);
        if ($user) {
            return $this->update($userId, ['workload' => $user['workload'] + 1]);
        }
        return false;
    }

    public function decrementWorkload(int $userId): bool
    {
        $user = $this->find($userId);
        if ($user && $user['workload'] > 0) {
            return $this->update($userId, ['workload' => $user['workload'] - 1]);
        }
        return false;
    }

    public function getUsersByDivisi($divisiId, $roles = null)
    {
        $builder = $this->where('divisi_id', $divisiId)
                        ->where('is_active', true);
        
        if ($roles && is_array($roles)) {
            $builder->whereIn('role', $roles);
        }
        
        return $builder->findAll();
    }

    public function getAllActiveUsers($excludeRoles = [])
    {
        $builder = $this->where('is_active', true);
        
        if (!empty($excludeRoles)) {
            $builder->whereNotIn('role', $excludeRoles);
        }
        
        return $builder->findAll();
    }
}