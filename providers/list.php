<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Provider.php';
require_once __DIR__ . '/../utils/helpers.php';

$db = (new Database())->getConnection();
$provider = new Provider($db);

// Pagination
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

$stmt = $provider->getAll($limit, $offset);
$total = $provider->countAll();
$total_pages = ceil($total / $limit);

// Header for public layout
include_once __DIR__ . '/../includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 py-8 min-h-screen">
    <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">Find a Service Provider</h2>

    <div class="grid gap-6 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) : ?>
            <div class="bg-white rounded-2xl shadow hover:shadow-md transition p-4 flex flex-col">
                <div class="flex items-center space-x-4">
                    <img src="<?= htmlspecialchars($row['profile_image'] ?? '/assets/default-avatar.png') ?>"
                         alt="Profile"
                         class="w-14 h-14 rounded-full object-cover border">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900"><?= htmlspecialchars($row['name']) ?></h3>
                        <div class="text-sm text-gray-600">
                            <?= htmlspecialchars($row['city']) ?>, <?= htmlspecialchars($row['state']) ?>
                        </div>
                    </div>
                </div>

                <p class="mt-3 text-gray-700 text-sm line-clamp-3"><?= nl2br(htmlspecialchars($row['bio'])) ?></p>

                <div class="mt-auto pt-4 text-sm text-gray-600 flex items-center justify-between">
                    <div>
                        <?= generateStarRating($row['avg_rating']); ?> (<?= $row['total_reviews'] ?> reviews)
                    </div>
                    <?php if ($row['is_verified']) : ?>
                        <span class="text-green-700 text-xs bg-green-100 px-2 py-0.5 rounded-full">Verified</span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <div class="mt-8 flex justify-center">
            <nav class="inline-flex space-x-2 text-sm">
                <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                    <a href="?page=<?= $p ?>"
                       class="px-4 py-2 border rounded-lg <?= $p == $page ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-blue-50' ?>">
                        <?= $p ?>
                    </a>
                <?php endfor; ?>
            </nav>
        </div>
    <?php endif; ?>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
