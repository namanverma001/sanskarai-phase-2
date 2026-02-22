<?php
/**
 * Sanskar AI - User Model
 * ========================
 * User model with authentication methods
 */

namespace App\Models;

use App\Core\Model;
use App\Core\Auth;
use App\Config\App;

class User extends Model
{
    protected string $table = 'SAI_users';
    
    protected array $fillable = [
        'name',
        'email',
        'mobile',
        'community_name',
        'kul_devi_devta',
        'religion',
        'password_hash',
        'role',
        'status',
        'email_verified_at',
        'remember_token',
        'last_login_at',
    ];
    
    protected array $hidden = [
        'password_hash',
        'remember_token',
    ];
    
    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?array
    {
        return $this->findBy('email', $email);
    }
    
    /**
     * Find user by mobile
     */
    public function findByMobile(string $mobile): ?array
    {
        return $this->findBy('mobile', $mobile);
    }
    
    /**
     * Create new user with hashed password
     */
    public function createUser(array $data): int
    {
        // Hash password
        if (isset($data['password'])) {
            $data['password_hash'] = Auth::hashPassword($data['password']);
            unset($data['password']);
        }
        
        // Set default role if not provided
        if (!isset($data['role'])) {
            $data['role'] = App::ROLE_USER;
        }
        
        // Set default status
        if (!isset($data['status'])) {
            $data['status'] = App::STATUS_ACTIVE;
        }
        
        return $this->create($data);
    }
    
    /**
     * Get users by role
     */
    public function getByRole(string $role): array
    {
        return $this->where(['role' => $role], 'created_at', 'DESC');
    }
    
    /**
     * Get all admins
     */
    public function getAdmins(): array
    {
        return $this->getByRole(App::ROLE_ADMIN);
    }
    
    /**
     * Get all pandits
     */
    public function getPandits(): array
    {
        return $this->getByRole(App::ROLE_PANDIT);
    }
    
    /**
     * Get all regular users
     */
    public function getUsers(): array
    {
        return $this->getByRole(App::ROLE_USER);
    }
    
    /**
     * Get users with pandit profiles
     */
    public function getPanditsWithProfiles(): array
    {
        $sql = "
            SELECT u.*, p.specialization, p.experience_years, p.approval_status, p.average_rating
            FROM SAI_users u
            LEFT JOIN SAI_pandit_profiles p ON u.id = p.user_id
            WHERE u.role = 'pandit'
            ORDER BY u.created_at DESC
        ";
        
        return $this->raw($sql);
    }
    
    /**
     * Get pending pandits
     */
    public function getPendingPandits(): array
    {
        $sql = "
            SELECT u.*, p.specialization, p.experience_years, p.bio, p.created_at as profile_created_at
            FROM SAI_users u
            INNER JOIN SAI_pandit_profiles p ON u.id = p.user_id
            WHERE u.role = 'pandit' AND p.approval_status = 'pending'
            ORDER BY p.created_at ASC
        ";
        
        return $this->raw($sql);
    }
    
    /**
     * Get approved pandits
     */
    public function getApprovedPandits(): array
    {
        $sql = "
            SELECT u.*, p.specialization, p.experience_years, p.bio, p.average_rating, p.total_rituals_performed
            FROM SAI_users u
            INNER JOIN SAI_pandit_profiles p ON u.id = p.user_id
            WHERE u.role = 'pandit' AND p.approval_status = 'approved' AND u.status = 'active'
            ORDER BY p.average_rating DESC, p.experience_years DESC
        ";
        
        return $this->raw($sql);
    }
    
    /**
     * Get single pandit with profile
     */
    public function getPanditProfile(int $panditId): ?array
    {
        $sql = "
            SELECT u.*, p.specialization, p.experience_years, p.bio, p.average_rating, 
                   p.total_rituals_performed, p.languages, p.availability_days, p.hourly_rate
            FROM SAI_users u
            INNER JOIN SAI_pandit_profiles p ON u.id = p.user_id
            WHERE u.id = :id AND u.role = 'pandit' AND p.approval_status = 'approved' AND u.status = 'active'
        ";
        
        return $this->rawOne($sql, ['id' => $panditId]);
    }
    
    /**
     * Update last login
     */
    public function updateLastLogin(int $userId): bool
    {
        $sql = "UPDATE SAI_users SET last_login_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $userId]);
    }
    
    /**
     * Block user
     */
    public function block(int $userId): bool
    {
        return $this->update($userId, ['status' => App::STATUS_BLOCKED]);
    }
    
    /**
     * Activate user
     */
    public function activate(int $userId): bool
    {
        return $this->update($userId, ['status' => App::STATUS_ACTIVE]);
    }
    
    /**
     * Get user statistics
     */
    public function getStats(): array
    {
        return [
            'total_users' => $this->count(['role' => App::ROLE_USER]),
            'total_pandits' => $this->count(['role' => App::ROLE_PANDIT]),
            'total_admins' => $this->count(['role' => App::ROLE_ADMIN]),
            'active_users' => $this->count(['status' => App::STATUS_ACTIVE]),
            'blocked_users' => $this->count(['status' => App::STATUS_BLOCKED]),
        ];
    }
    
    /**
     * Store a password reset token for a user.
     * Deletes any previous tokens for that user first.
     *
     * @param int    $userId    The user's ID
     * @param string $tokenHash Hashed token (password_hash)
     * @param string $expiresAt Expiry datetime string (Y-m-d H:i:s)
     * @return bool
     */
    public function storeResetToken(int $userId, string $tokenHash, string $expiresAt): bool
    {
        // Remove any old tokens for this user
        $del = $this->db->prepare("DELETE FROM SAI_password_resets WHERE user_id = :uid");
        $del->execute(['uid' => $userId]);

        $ins = $this->db->prepare("
            INSERT INTO SAI_password_resets (user_id, token_hash, expires_at, created_at)
            VALUES (:uid, :token_hash, :expires_at, NOW())
        ");
        return $ins->execute([
            'uid'        => $userId,
            'token_hash' => $tokenHash,
            'expires_at' => $expiresAt,
        ]);
    }

    /**
     * Find a valid (non-expired) reset row by matching the raw token
     * against all stored hashes for the matching user lookup.
     *
     * Because we can't reverse a hash we scan recent rows by user email.
     *
     * @param string $rawToken The plain token from the URL
     * @return array|null      Full row including user_id on success
     */
    public function findValidResetToken(string $rawToken): ?array
    {
        // Fetch all non-expired rows (there should only be 1 per user)
        $stmt = $this->db->prepare("
            SELECT pr.*, u.email
            FROM SAI_password_resets pr
            INNER JOIN SAI_users u ON u.id = pr.user_id
            WHERE pr.expires_at > NOW()
            ORDER BY pr.created_at DESC
        ");
        $stmt->execute();
        $rows = $stmt->fetchAll();

        foreach ($rows as $row) {
            if (password_verify($rawToken, $row['token_hash'])) {
                return $row;
            }
        }

        return null;
    }

    /**
     * Reset user password and delete the used token.
     *
     * @param int    $userId      User to update
     * @param string $newPassword Plain-text new password
     * @param int    $resetRowId  ID of the SAI_password_resets row to delete
     * @return bool
     */
    public function consumeResetToken(int $userId, string $newPassword, int $resetRowId): bool
    {
        $hash = \App\Core\Auth::hashPassword($newPassword);

        $update = $this->db->prepare("
            UPDATE SAI_users SET password_hash = :hash, updated_at = NOW() WHERE id = :id
        ");
        $updated = $update->execute(['hash' => $hash, 'id' => $userId]);

        // Delete the used token
        $del = $this->db->prepare("DELETE FROM SAI_password_resets WHERE id = :id");
        $del->execute(['id' => $resetRowId]);

        return $updated;
    }

    /**
     * Delete expired reset tokens (housekeeping, optional)
     */
    public function deleteExpiredTokens(): void
    {
        $this->db->exec("DELETE FROM SAI_password_resets WHERE expires_at <= NOW()");
    }

    /**
     * Search users
     */
    public function search(string $query, ?string $role = null): array
    {
        $sql = "
            SELECT * FROM SAI_users
            WHERE (name LIKE :name OR email LIKE :email OR mobile LIKE :mobile)
        ";
        
        $searchTerm = "%$query%";
        $params = [
            'name' => $searchTerm,
            'email' => $searchTerm,
            'mobile' => $searchTerm
        ];
        
        if ($role) {
            $sql .= " AND role = :role";
            $params['role'] = $role;
        }
        
        $sql .= " ORDER BY name ASC LIMIT 50";
        
        return $this->raw($sql, $params);
    }

    /**
     * Get top communities by user count
     */
    public function getTopCommunities(int $limit = 6): array
    {
        $sql = "
            SELECT community_name, COUNT(*) as user_count
            FROM SAI_users
            WHERE community_name IS NOT NULL AND community_name != ''
            GROUP BY community_name
            ORDER BY user_count DESC
            LIMIT :limit
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
}
