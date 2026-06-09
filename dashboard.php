<?php
$db = new PDO('sqlite:' . __DIR__ . '/data.db');

// Tổng events
$total = $db->query("SELECT COUNT(*) FROM events")->fetchColumn();

// Đếm theo event
$byEvent = $db->query("
    SELECT event, COUNT(*) as count 
    FROM events 
    GROUP BY event 
    ORDER BY count DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Đếm purchase_click theo plan
$byPlan = $db->query("
    SELECT payload, COUNT(*) as count 
    FROM events 
    WHERE event = 'purchase_click'
    GROUP BY payload
    ORDER BY count DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Đếm click_tool theo tool
$byTool = $db->query("
    SELECT payload, COUNT(*) as count 
    FROM events 
    WHERE event = 'click_tool'
    GROUP BY payload
    ORDER BY count DESC
")->fetchAll(PDO::FETCH_ASSOC);

// 10 events gần nhất
$recent = $db->query("
    SELECT event, payload, ip, created_at 
    FROM events 
    ORDER BY created_at DESC 
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 40px; background: #f9f9f9; color: #333; }
    h1 { text-align: center; }
    .cards { display: flex; gap: 20px; justify-content: center; margin: 30px 0; }
    .card { background: white; border: 1px solid #ddd; padding: 20px 40px; text-align: center; box-shadow: 0 2px 6px rgba(0,0,0,0.1); border-radius: 8px; }
    .card h2 { font-size: 48px; margin: 0; color: #0073e6; }
    .card p { margin: 5px 0 0; font-size: 14px; color: #666; }
    table { width: 80%; margin: 20px auto; border-collapse: collapse; background: white; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
    th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
    th { background: #333; color: white; }
    tr:hover { background: #f1f1f1; }
    h2.section { text-align: center; margin-top: 40px; }
    .refresh { display: block; text-align: center; margin: 20px; color: #0073e6; cursor: pointer; }
  </style>
</head>
<body>

<h1>📊 Dashboard</h1>
<a class="refresh" onclick="location.reload()">🔄 Refresh</a>

<!-- Tổng -->
<div class="cards">
  <div class="card">
    <h2><?= $total ?></h2>
    <p>Total Events</p>
  </div>
  <?php
  $purchases = 0; $clicks = 0;
  foreach ($byEvent as $e) {
    if ($e['event'] === 'purchase_click') $purchases = $e['count'];
    if ($e['event'] === 'click_tool') $clicks = $e['count'];
  }
  ?>
  <div class="card">
    <h2><?= $purchases ?></h2>
    <p>Buy Now Clicks</p>
  </div>
  <div class="card">
    <h2><?= $clicks ?></h2>
    <p>Tool Link Clicks</p>
  </div>
</div>

<!-- Purchase theo plan -->
<h2 class="section">💰 Buy Now by Plan</h2>
<table>
  <tr><th>Plan</th><th>Clicks</th></tr>
  <?php foreach ($byPlan as $row):
    $payload = json_decode($row['payload'], true);
    $plan = $payload['plan'] ?? $row['payload'];
  ?>
  <tr><td><?= htmlspecialchars($plan) ?></td><td><?= $row['count'] ?></td></tr>
  <?php endforeach; ?>
  <?php if (empty($byPlan)): ?>
  <tr><td colspan="2">No data yet</td></tr>
  <?php endif; ?>
</table>

<!-- Click tool -->
<h2 class="section">🔗 Tool Link Clicks</h2>
<table>
  <tr><th>Tool</th><th>Clicks</th></tr>
  <?php foreach ($byTool as $row):
    $payload = json_decode($row['payload'], true);
    $tool = $payload['tool'] ?? $row['payload'];
  ?>
  <tr><td><?= htmlspecialchars($tool) ?></td><td><?= $row['count'] ?></td></tr>
  <?php endforeach; ?>
  <?php if (empty($byTool)): ?>
  <tr><td colspan="2">No data yet</td></tr>
  <?php endif; ?>
</table>

<!-- Recent events -->
<h2 class="section">🕐 Recent Events</h2>
<table>
  <tr><th>Event</th><th>Payload</th><th>IP</th><th>Time</th></tr>
  <?php foreach ($recent as $row): ?>
  <tr>
    <td><?= htmlspecialchars($row['event']) ?></td>
    <td><?= htmlspecialchars($row['payload']) ?></td>
    <td><?= htmlspecialchars($row['ip']) ?></td>
    <td><?= $row['created_at'] ?></td>
  </tr>
  <?php endforeach; ?>
  <?php if (empty($recent)): ?>
  <tr><td colspan="4">No data yet</td></tr>
  <?php endif; ?>
</table>

</body>
</html>
