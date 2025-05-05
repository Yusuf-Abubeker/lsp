<div class="bg-gray-50 py-10">
    <div class="container-custom">
        <h1 class="text-2xl font-bold mb-6">Add New Service</h1>
        <?= displayFlashMessage() ?>

        <form method="POST" action="<?= BASE_URL ?>/provider/services.php" class="space-y-6">
            <input type="hidden" name="action" value="create">

            <div class="mb-4">
                <label for="title" class="block text-sm font-semibold text-gray-700">Service Title</label>
                <input type="text" id="title" name="title" class="form-input mt-1 w-full" required>
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-semibold text-gray-700">Description</label>
                <textarea id="description" name="description" rows="4" class="form-input mt-1 w-full" required></textarea>
            </div>

            <div class="mb-4">
                <label for="category_id" class="block text-sm font-semibold text-gray-700">Category</label>
                <select id="category_id" name="category_id" class="form-select mt-1 w-full" required>
                    <option value="">Select Category</option>
                    <?php while ($cat = $categories->fetch(PDO::FETCH_ASSOC)): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-4">
                <label for="price" class="block text-sm font-semibold text-gray-700">Price</label>
                <input type="number" id="price" name="price" class="form-input mt-1 w-full" step="0.01" required>
            </div>

            <div class="mb-4">
                <label for="price_type" class="block text-sm font-semibold text-gray-700">Price Type</label>
                <select id="price_type" name="price_type" class="form-select mt-1 w-full" required>
                    <option value="fixed">Fixed Price</option>
                    <option value="hourly">Hourly Rate</option>
                    <option value="starting_at">Starting At</option>
                </select>
            </div>

            <div class="flex justify-end gap-2">
                <a href="<?= BASE_URL ?>/provider/services.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Add Service</button>
            </div>
        </form>
    </div>
</div>
