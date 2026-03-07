<section class="card">
    <h1>Employee List</h1>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Age</th>
                    <th>Permanent</th>
                    <th>Current</th>
                    <th>Photo</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($employees as $emp): ?>
                    <tr>
                        <td><?= (int) $emp['id'] ?></td>
                        <td><?= htmlspecialchars($emp['full_name'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($emp['email'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= (int) $emp['age'] ?></td>
                        <td><?= htmlspecialchars($emp['perm_city'] . ', ' . $emp['perm_state'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($emp['curr_city'] . ', ' . $emp['curr_state'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <?php if (!empty($emp['profile_picture'])): ?>
                                <img src="/Ems/public/uploads/profiles/<?= rawurlencode($emp['profile_picture']) ?>" alt="Profile" class="avatar small">
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($emp['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
