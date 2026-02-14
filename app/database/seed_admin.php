<?php
/**
 * Sanskar AI - Admin Seeding Script
 * ===================================
 * Seeds initial admin user and sample data
 * 
 * Usage: php app/database/seed_admin.php
 */

// Load configuration
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/app.php';

use App\Config\Database;
use App\Config\App;

echo "==============================================\n";
echo "  Sanskar AI - Admin Seeder\n";
echo "==============================================\n\n";

try {
    Database::loadEnv();
    $pdo = Database::getConnection();

    // ============================================================
    // SEED ADMIN USER
    // ============================================================
    echo "[1/5] Creating Admin User...\n";

    // Check if admin already exists
    $stmt = $pdo->prepare("SELECT id FROM SAI_users WHERE email = :email");
    $stmt->execute(['email' => 'admin@sanskarai.com']);
    $existingAdmin = $stmt->fetch();

    if ($existingAdmin) {
        echo "      ⚠ Admin user already exists (ID: {$existingAdmin['id']})\n";
    } else {
        $adminPassword = password_hash('Admin@123', PASSWORD_BCRYPT, ['cost' => 12]);

        $stmt = $pdo->prepare("
            INSERT INTO SAI_users (name, email, mobile, password_hash, role, status, email_verified_at, created_at, updated_at)
            VALUES (:name, :email, :mobile, :password_hash, :role, :status, NOW(), NOW(), NOW())
        ");

        $stmt->execute([
            'name' => 'System Administrator',
            'email' => 'admin@sanskarai.com',
            'mobile' => '9999999999',
            'password_hash' => $adminPassword,
            'role' => 'admin',
            'status' => 'active',
        ]);

        $adminId = $pdo->lastInsertId();
        echo "      ✓ Admin user created (ID: $adminId)\n";
        echo "      📧 Email: admin@sanskarai.com\n";
        echo "      🔑 Password: Admin@123\n";
    }

    // ============================================================
    // SEED SAMPLE PANDIT USER
    // ============================================================
    echo "\n[2/5] Creating Sample Pandit...\n";

    $stmt = $pdo->prepare("SELECT id FROM SAI_users WHERE email = :email");
    $stmt->execute(['email' => 'pandit@sanskarai.com']);
    $existingPandit = $stmt->fetch();

    if ($existingPandit) {
        echo "      ⚠ Sample pandit already exists (ID: {$existingPandit['id']})\n";
        $panditUserId = $existingPandit['id'];
    } else {
        $panditPassword = password_hash('Pandit@123', PASSWORD_BCRYPT, ['cost' => 12]);

        $stmt = $pdo->prepare("
            INSERT INTO SAI_users (name, email, mobile, password_hash, role, status, email_verified_at, created_at, updated_at)
            VALUES (:name, :email, :mobile, :password_hash, :role, :status, NOW(), NOW(), NOW())
        ");

        $stmt->execute([
            'name' => 'Pandit Sharma Ji',
            'email' => 'pandit@sanskarai.com',
            'mobile' => '9888888888',
            'password_hash' => $panditPassword,
            'role' => 'pandit',
            'status' => 'active',
        ]);

        $panditUserId = $pdo->lastInsertId();
        echo "      ✓ Pandit user created (ID: $panditUserId)\n";

        // Create pandit profile
        $stmt = $pdo->prepare("
            INSERT INTO SAI_pandit_profiles (user_id, specialization, experience_years, bio, languages, approval_status, approved_by, approved_at, created_at, updated_at)
            VALUES (:user_id, :specialization, :experience_years, :bio, :languages, :approval_status, :approved_by, NOW(), NOW(), NOW())
        ");

        $stmt->execute([
            'user_id' => $panditUserId,
            'specialization' => 'Vedic Rituals, Puja, Havan, Marriage Ceremonies',
            'experience_years' => 15,
            'bio' => 'Experienced Vedic pandit with expertise in traditional Hindu rituals and ceremonies. Trained in Sanskrit scriptures and mantras.',
            'languages' => 'Hindi, Sanskrit, English',
            'approval_status' => 'approved',
            'approved_by' => 1,
        ]);

        echo "      ✓ Pandit profile created (Approved)\n";
        echo "      📧 Email: pandit@sanskarai.com\n";
        echo "      🔑 Password: Pandit@123\n";
    }

    // ============================================================
    // SEED SAMPLE USER
    // ============================================================
    echo "\n[3/5] Creating Sample User...\n";

    $stmt = $pdo->prepare("SELECT id FROM SAI_users WHERE email = :email");
    $stmt->execute(['email' => 'user@sanskarai.com']);
    $existingUser = $stmt->fetch();

    if ($existingUser) {
        echo "      ⚠ Sample user already exists (ID: {$existingUser['id']})\n";
        $sampleUserId = $existingUser['id'];
    } else {
        $userPassword = password_hash('User@123', PASSWORD_BCRYPT, ['cost' => 12]);

        $stmt = $pdo->prepare("
            INSERT INTO SAI_users (name, email, mobile, password_hash, role, status, email_verified_at, created_at, updated_at)
            VALUES (:name, :email, :mobile, :password_hash, :role, :status, NOW(), NOW(), NOW())
        ");

        $stmt->execute([
            'name' => 'Rahul Verma',
            'email' => 'user@sanskarai.com',
            'mobile' => '9777777777',
            'password_hash' => $userPassword,
            'role' => 'user',
            'status' => 'active',
        ]);

        $sampleUserId = $pdo->lastInsertId();
        echo "      ✓ Sample user created (ID: $sampleUserId)\n";
        echo "      📧 Email: user@sanskarai.com\n";
        echo "      🔑 Password: User@123\n";

        // Create sample family
        $stmt = $pdo->prepare("
            INSERT INTO SAI_families (user_id, family_name, gotra, nakshatra, kul_devta, city, state, created_at, updated_at)
            VALUES (:user_id, :family_name, :gotra, :nakshatra, :kul_devta, :city, :state, NOW(), NOW())
        ");

        $stmt->execute([
            'user_id' => $sampleUserId,
            'family_name' => 'Verma Family',
            'gotra' => 'Kashyap',
            'nakshatra' => 'Rohini',
            'kul_devta' => 'Lord Shiva',
            'city' => 'New Delhi',
            'state' => 'Delhi',
        ]);

        $familyId = $pdo->lastInsertId();
        echo "      ✓ Sample family created\n";

        // Add family members
        $members = [
            ['name' => 'Rahul Verma', 'gender' => 'male', 'relation' => 'Self', 'is_primary' => true],
            ['name' => 'Priya Verma', 'gender' => 'female', 'relation' => 'Wife', 'is_primary' => false],
            ['name' => 'Aarav Verma', 'gender' => 'male', 'relation' => 'Son', 'is_primary' => false],
        ];

        foreach ($members as $member) {
            $stmt = $pdo->prepare("
                INSERT INTO SAI_family_members (family_id, name, gender, relation, is_primary, created_at)
                VALUES (:family_id, :name, :gender, :relation, :is_primary, NOW())
            ");
            $stmt->execute([
                'family_id' => $familyId,
                'name' => $member['name'],
                'gender' => $member['gender'],
                'relation' => $member['relation'],
                'is_primary' => $member['is_primary'] ? 1 : 0,
            ]);
        }
        echo "      ✓ Family members added\n";
    }

    // ============================================================
    // SEED SAMPLE RITUALS - SKIPPED
    // ============================================================
    // Sample rituals are no longer seeded by default.
    echo "\n[4/5] Skipping Sample Rituals...\n";

    // ============================================================
    // SEED CULTURAL INSIGHTS
    // ============================================================
    echo "\n[5/5] Creating Cultural Insights...\n";

    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM SAI_cultural_insights");
    $stmt->execute();
    $insightCount = $stmt->fetch()['count'];

    if ($insightCount > 0) {
        echo "      ⚠ Cultural insights already exist ($insightCount found)\n";
    } else {
        $insights = [
            [
                'title' => 'The Significance of Ganesh Chaturthi',
                'slug' => 'significance-of-ganesh-chaturthi',
                'category' => 'Festivals',
                'content' => 'Ganesh Chaturthi, also known as Vinayaka Chaturthi, is one of the most important Hindu festivals celebrating the birth of Lord Ganesha. The festival typically falls in August or September and lasts for 10 days. It begins with the installation of Ganesha clay idols in homes and public pandals, and concludes with the immersion of the idols in water bodies.',
                'summary' => 'A comprehensive guide to understanding the cultural and spiritual significance of Ganesh Chaturthi.',
                'region' => 'Maharashtra',
                'is_published' => true,
            ],
            [
                'title' => 'Understanding the Hindu Calendar',
                'slug' => 'understanding-hindu-calendar',
                'category' => 'Knowledge',
                'content' => 'The Hindu calendar, also known as Panchang, is based on lunar months and solar years. It includes important concepts like Tithi (lunar day), Nakshatra (lunar mansion), Yoga, Karana, and Var (weekday). Understanding the Panchang is essential for determining auspicious times for rituals, ceremonies, and important life events.',
                'summary' => 'Learn about the Hindu calendar system and its importance in determining auspicious times.',
                'region' => 'All India',
                'is_published' => true,
            ],
            [
                'title' => 'The Sacred Importance of Tulsi Plant',
                'slug' => 'sacred-importance-tulsi-plant',
                'category' => 'Traditions',
                'content' => 'Tulsi (Holy Basil) holds a special place in Hindu households and is considered sacred. It is believed to be an earthly manifestation of Goddess Lakshmi and is worshipped daily. The plant is known for its medicinal properties and spiritual significance. Many Hindu families maintain a Tulsi plant in their courtyard.',
                'summary' => 'Discover why Tulsi is revered as a sacred plant in Hindu tradition.',
                'region' => 'All India',
                'is_published' => true,
            ],
        ];

        foreach ($insights as $insight) {
            $stmt = $pdo->prepare("
                INSERT INTO SAI_cultural_insights (title, slug, category, content, summary, region, is_published, created_by, published_at, created_at, updated_at)
                VALUES (:title, :slug, :category, :content, :summary, :region, :is_published, 1, NOW(), NOW(), NOW())
            ");
            $stmt->execute($insight);
            echo "      ✓ Created: {$insight['title']}\n";
        }
    }

    // ============================================================
    // SEED INITIAL DASHBOARD STATS
    // ============================================================
    $today = date('Y-m-d');
    $stmt = $pdo->prepare("SELECT id FROM SAI_admin_dashboard_stats WHERE stat_date = :stat_date");
    $stmt->execute(['stat_date' => $today]);

    if (!$stmt->fetch()) {
        $stmt = $pdo->prepare("
            INSERT INTO SAI_admin_dashboard_stats (stat_date, total_users, total_pandits, total_rituals, created_at)
            VALUES (:stat_date, 3, 1, 5, NOW())
        ");
        $stmt->execute(['stat_date' => $today]);
    }

    echo "\n==============================================\n";
    echo "  ✓ Seeding completed successfully!\n";
    echo "==============================================\n\n";

    echo "Test Accounts:\n";
    echo "┌────────────────────────────────────────────┐\n";
    echo "│ ADMIN                                      │\n";
    echo "│   Email: admin@sanskarai.com              │\n";
    echo "│   Password: Admin@123                      │\n";
    echo "├────────────────────────────────────────────┤\n";
    echo "│ PANDIT                                     │\n";
    echo "│   Email: pandit@sanskarai.com             │\n";
    echo "│   Password: Pandit@123                     │\n";
    echo "├────────────────────────────────────────────┤\n";
    echo "│ USER                                       │\n";
    echo "│   Email: user@sanskarai.com               │\n";
    echo "│   Password: User@123                       │\n";
    echo "└────────────────────────────────────────────┘\n\n";

} catch (PDOException $e) {
    echo "\n✗ Seeding FAILED!\n";
    echo "Error: " . $e->getMessage() . "\n\n";
    exit(1);
}
