# HMS Bug Reporter — Remote Fetch Endpoint Task

> **Feature:** Open JSON endpoint to fetch all bug reports remotely  
> **Consumer:** Separate PHP site (your personal bug viewer)  
> **Storage:** Existing bugs.sqlite (unchanged)  
> **Auth:** None

---

## Concept

```
[bugs.sqlite on HMS server]
        ↓
[GET /api/bugs]
        ↓
[Your PHP site hits it → displays bugs]
```

---

## STEP 1 — Add the Route

In `routes/api.php`:

```php
Route::get('/bugs', [BugReportController::class, 'fetchRemote']);
```

No middleware. No auth group. Naked route.

---

## STEP 2 — Controller Method `fetchRemote()`

Add to `BugReportController.php`:

```php
public function fetchRemote(Request $request)
{
    $bugs = BugReport::on('bugs')
        ->when($request->status,   fn($q) => $q->where('status', $request->status))
        ->when($request->severity, fn($q) => $q->where('severity', $request->severity))
        ->when($request->module,   fn($q) => $q->where('module', $request->module))
        ->orderBy('created_at', 'desc')
        ->get();

    return response()->json([
        'total' => $bugs->count(),
        'bugs'  => $bugs
    ])->header('Access-Control-Allow-Origin', '*');
}
```

**Optional query params your PHP site can pass:**

| Param | Example | Effect |
|---|---|---|
| `status` | `?status=open` | filter by status |
| `severity` | `?severity=critical` | filter by severity |
| `module` | `?module=Billing` | filter by module |

All optional — no params returns everything.

---

## STEP 3 — JSON Response Shape

```json
{
  "total": 12,
  "bugs": [
    {
      "id": 1,
      "title": "Room billing shows wrong total",
      "details": "When checking out, the extra charges are doubled",
      "module": "Billing",
      "severity": "high",
      "page_url": "https://hms.yourdomain.com/billing/checkout/5",
      "reported_by": "Tester Juma",
      "status": "open",
      "created_at": "2026-05-27T14:32:00"
    }
  ]
}
```

---

## STEP 4 — PHP Viewer Site

Simple `index.php`:

```php
<?php
$url  = 'https://hms.yourdomain.com/api/bugs';

// optional filters
$params = http_build_query([
    'status'   => $_GET['status']   ?? '',
    'severity' => $_GET['severity'] ?? '',
    'module'   => $_GET['module']   ?? '',
]);

$json = file_get_contents($url . '?' . $params);
$data = json_decode($json, true);
?>

<!DOCTYPE html>
<html>
<head>
    <title>HMS Bug Reports</title>
</head>
<body>
    <h1>Bug Reports (<?= $data['total'] ?>)</h1>
    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Module</th>
            <th>Severity</th>
            <th>Reported By</th>
            <th>Page</th>
            <th>Status</th>
            <th>Date</th>
        </tr>
        <?php foreach ($data['bugs'] as $bug): ?>
        <tr>
            <td><?= $bug['id'] ?></td>
            <td><?= $bug['title'] ?></td>
            <td><?= $bug['module'] ?></td>
            <td><?= $bug['severity'] ?></td>
            <td><?= $bug['reported_by'] ?></td>
            <td><a href="<?= $bug['page_url'] ?>" target="_blank">link</a></td>
            <td><?= $bug['status'] ?></td>
            <td><?= $bug['created_at'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
```

---

## File Changes Checklist

```
routes/api.php                        ← add GET /bugs route
app/Http/Controllers/
  └── BugReportController.php         ← add fetchRemote() method
[separate PHP site]
  └── index.php                       ← your viewer
```

3 touches. Done.

---

## Implementation Order

1. Add route in `api.php`
2. Add `fetchRemote()` to controller
3. Test with browser / curl: `GET /api/bugs`
4. Build PHP viewer site
