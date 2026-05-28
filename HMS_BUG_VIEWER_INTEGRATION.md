# HMS Bug Viewer — Remote Integration Plan

> **Production HMS:** http://hms.dadyprojects.systems/  
> **API Endpoint:** http://hms.dadyprojects.systems/api/bugs  
> **Consumer:** Separate PHP site (bug viewer dashboard)  
> **Auth:** None (public endpoint)

---

## Architecture

```
[http://hms.dadyprojects.systems/api/bugs]
                ↓ JSON
    [Your PHP Bug Viewer Site]
                ↓
    [Displays bugs in a table]
```

---

## STEP 1 — Verify Production Endpoint

Before building the viewer, confirm the endpoint is live:

```bash
curl http://hms.dadyprojects.systems/api/bugs
```

Expected response:
```json
{
  "total": 12,
  "bugs": [
    {
      "id": 1,
      "title": "Room billing shows wrong total",
      "details": "When checking out, extra charges are doubled",
      "module": "Billing",
      "severity": "high",
      "page_url": "http://hms.dadyprojects.systems/billing/checkout/5",
      "reported_by": "Tester Juma",
      "status": "open",
      "created_at": "2026-05-27T14:32:00.000000Z",
      "updated_at": "2026-05-27T14:32:00.000000Z"
    }
  ]
}
```

---

## STEP 2 — PHP Viewer Site Structure

```
bug-viewer/
├── index.php          ← main dashboard
├── style.css          ← optional styling
└── .htaccess          ← clean URLs (optional)
```

---

## STEP 3 — `index.php` Implementation

```php
<?php
define('HMS_API', 'http://hms.dadyprojects.systems/api/bugs');

// Build query from user filters
$params = array_filter([
    'status'   => $_GET['status']   ?? '',
    'severity' => $_GET['severity'] ?? '',
    'module'   => $_GET['module']   ?? '',
]);

$url = HMS_API . ($params ? '?' . http_build_query($params) : '');

// Fetch with error handling
$json = @file_get_contents($url);
$data = $json ? json_decode($json, true) : ['total' => 0, 'bugs' => []];

// Severity badge colors
$severityColors = [
    'low'      => 'bg-green-100 text-green-800',
    'medium'   => 'bg-yellow-100 text-yellow-800',
    'high'     => 'bg-orange-100 text-orange-800',
    'critical' => 'bg-red-100 text-red-800',
];

// Status badge colors
$statusColors = [
    'open'         => 'bg-blue-100 text-blue-800',
    'acknowledged' => 'bg-purple-100 text-purple-800',
    'fixed'        => 'bg-green-100 text-green-800',
];

// Get unique modules for filter dropdown
$modules = array_unique(array_column($data['bugs'], 'module'));
sort($modules);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HMS Bug Reports</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">HMS Bug Reports</h1>
            <p class="text-gray-600 mt-1">
                Total: <span class="font-semibold"><?= $data['total'] ?></span> bugs
                &middot; Source: <code class="bg-gray-200 px-2 py-0.5 rounded text-sm">hms.dadyprojects.systems</code>
            </p>
        </div>

        <!-- Filters -->
        <form method="GET" class="bg-white rounded-lg shadow p-4 mb-6 flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="border rounded-md px-3 py-2 text-sm">
                    <option value="">All</option>
                    <option value="open" <?= ($_GET['status'] ?? '') === 'open' ? 'selected' : '' ?>>Open</option>
                    <option value="acknowledged" <?= ($_GET['status'] ?? '') === 'acknowledged' ? 'selected' : '' ?>>Acknowledged</option>
                    <option value="fixed" <?= ($_GET['status'] ?? '') === 'fixed' ? 'selected' : '' ?>>Fixed</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Severity</label>
                <select name="severity" class="border rounded-md px-3 py-2 text-sm">
                    <option value="">All</option>
                    <option value="low" <?= ($_GET['severity'] ?? '') === 'low' ? 'selected' : '' ?>>Low</option>
                    <option value="medium" <?= ($_GET['severity'] ?? '') === 'medium' ? 'selected' : '' ?>>Medium</option>
                    <option value="high" <?= ($_GET['severity'] ?? '') === 'high' ? 'selected' : '' ?>>High</option>
                    <option value="critical" <?= ($_GET['severity'] ?? '') === 'critical' ? 'selected' : '' ?>>Critical</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Module</label>
                <select name="module" class="border rounded-md px-3 py-2 text-sm">
                    <option value="">All</option>
                    <?php foreach ($modules as $mod): ?>
                        <option value="<?= htmlspecialchars($mod) ?>" <?= ($_GET['module'] ?? '') === $mod ? 'selected' : '' ?>>
                            <?= htmlspecialchars($mod) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700">
                Filter
            </button>
            <a href="?" class="text-gray-500 text-sm hover:text-gray-700">Clear</a>
        </form>

        <!-- Bug Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Module</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Severity</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reported By</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Page</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if (empty($data['bugs'])): ?>
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">No bugs found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data['bugs'] as $bug): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-mono">#<?= $bug['id'] ?></td>
                                <td class="px-4 py-3 text-sm font-medium"><?= htmlspecialchars($bug['title']) ?></td>
                                <td class="px-4 py-3 text-sm"><?= htmlspecialchars($bug['module']) ?></td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $severityColors[$bug['severity']] ?? '' ?>">
                                        <?= ucfirst($bug['severity']) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm"><?= htmlspecialchars($bug['reported_by'] ?? '—') ?></td>
                                <td class="px-4 py-3 text-sm">
                                    <a href="<?= htmlspecialchars($bug['page_url'] ?? '#') ?>" target="_blank" class="text-blue-600 hover:underline">
                                        Link
                                    </a>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusColors[$bug['status']] ?? '' ?>">
                                        <?= ucfirst($bug['status']) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    <?= date('M d, Y H:i', strtotime($bug['created_at'])) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <p class="text-center text-gray-400 text-sm mt-6">
            Auto-refreshes on filter change &middot; Data from HMS Production
        </p>
    </div>
</body>
</html>
```

---

## STEP 4 — CORS Check

If you get CORS errors from the browser, ensure `hms.dadyprojects.systems` returns:

```
Access-Control-Allow-Origin: *
```

Already set in `fetchRemote()` controller method. Verify with:

```bash
curl -I http://hms.dadyprojects.systems/api/bugs | grep -i access-control
```

---

## STEP 5 — Deployment Checklist

```
[ ] Deploy HMS with /api/bugs endpoint (already done)
[ ] Verify endpoint returns JSON: curl http://hms.dadyprojects.systems/api/bugs
[ ] Upload bug-viewer/ to your PHP hosting
[ ] Open bug-viewer in browser
[ ] Test filters: status, severity, module
[ ] Test empty state (no bugs matching filter)
```

---

## STEP 6 — Optional Enhancements

| Feature | How |
|---|---|
| Auto-refresh every 30s | Add `<meta http-equiv="refresh" content="30">` |
| Export to CSV | Add a button that calls `/api/bugs` and outputs CSV |
| Bug detail modal | Click row → show details in a popup (Alpine.js) |
| Pagination | HMS endpoint returns all bugs; add `?limit=` param later |

---

## Files to Create

```
bug-viewer/
└── index.php    ← copy from STEP 3 above
```

1 file. Done.
