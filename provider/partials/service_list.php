<div class="bg-gray-100 py-10 min-h-screen">
    <div class="max-w-6xl mx-auto px-4">
        <div class="bg-white rounded-2xl shadow-md p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-3xl font-semibold text-gray-800">Your Services</h1>
                <a href="<?= BASE_URL ?>/provider/services.php?action=add"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                    + Add New Service
                </a>
            </div>

            <?= displayFlashMessage() ?>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-700 border rounded-lg overflow-hidden shadow-sm">
                    <thead class="bg-gray-200 text-gray-800">
                        <tr>
                            <th class="px-5 py-3">Title</th>
                            <th class="px-5 py-3">Category</th>
                            <th class="px-5 py-3">Price</th>
                            <th class="px-5 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php while ($row = $services->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-5 py-3 font-medium"><?= htmlspecialchars($row['title']) ?></td>
                                <td class="px-5 py-3"><?= htmlspecialchars($row['category_name']) ?></td>
                                <td class="px-5 py-3">
                                    <?= number_format($row['price'], 2) ?><?= $row['price_type'] === 'hourly' ? ' /hr' : '' ?>
                                </td>
                                <td class="px-5 py-3 space-x-2">
                                    <a href="<?= BASE_URL ?>/provider/services.php?action=edit&id=<?= $row['id'] ?>"
                                       class="inline-block px-3 py-1 text-sm text-white bg-yellow-500 rounded hover:bg-yellow-600 transition">Edit</a>
                                    <a href="<?= BASE_URL ?>/provider/services.php?action=delete&id=<?= $row['id'] ?>"
                                       class="inline-block px-3 py-1 text-sm text-white bg-red-600 rounded hover:bg-red-700 transition"
                                       onclick="return confirm('Are you sure you want to delete this service?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
