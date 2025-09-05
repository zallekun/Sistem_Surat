<?php

namespace App\Models;

use CodeIgniter\Model;

class UserDelegationsModel extends Model
{
    protected $table = 'user_delegations';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'from_user_id', 'to_user_id', 'start_date', 'end_date',
        'reason', 'status', 'is_active'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'from_user_id' => 'integer',
        'to_user_id' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];
    
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'from_user_id' => 'required|integer|is_not_unique[users.id]',
        'to_user_id' => 'required|integer|is_not_unique[users.id]|differs[from_user_id]',
        'start_date' => 'required|valid_date',
        'end_date' => 'required|valid_date',
        'reason' => 'required|min_length[10]',
        'status' => 'required|in_list[ACTIVE,INACTIVE,EXPIRED]',
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    public function getActiveDelegation(int $userId): ?array
    {
        return $this->select('user_delegations.*, 
                            from_user.nama as from_user_name,
                            to_user.nama as to_user_name')
                   ->join('users from_user', 'from_user.id = user_delegations.from_user_id')
                   ->join('users to_user', 'to_user.id = user_delegations.to_user_id')
                   ->where('user_delegations.from_user_id', $userId)
                   ->where('user_delegations.is_active', true)
                   ->where('user_delegations.start_date <=', date('Y-m-d'))
                   ->where('user_delegations.end_date >=', date('Y-m-d'))
                   ->first();
    }

    public function getEffectiveUser(int $originalUserId): int
    {
        $delegation = $this->getActiveDelegation($originalUserId);
        return $delegation ? $delegation['to_user_id'] : $originalUserId;
    }
}