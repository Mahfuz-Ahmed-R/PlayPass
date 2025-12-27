<?php
include __DIR__ . '/../../../../Backend/PHP/connection.php';

function fetchEventsFromDatabase($conn, $eventDetailsPath) {
    $events = [];
    
    $defaultSql = "SELECT m.*, h.name AS home_team_name, a.name AS away_team_name, s.name AS stadium_name
                   FROM match_table m
                   LEFT JOIN team h ON m.home_team_id = h.team_id
                   LEFT JOIN team a ON m.away_team_id = a.team_id
                   LEFT JOIN stadium s ON m.stadium_id = s.stadium_id
                   ORDER BY m.match_date DESC
                   LIMIT 10";

    $result = mysqli_query($conn, $defaultSql);
    
    if ($result && mysqli_num_rows($result) > 0) {
      while ($row = mysqli_fetch_assoc($result)) {
        $status = strtolower($row['status'] ?? 'upcoming');
        $isLive = ($status === 'live');
        $rawPoster = $row['poster_url'] ?? '';
        if (empty($rawPoster)) {
          $rawPoster = 'assets/img/img3.jpg';
        }

        if (preg_match('#^(https?:)?//#', $rawPoster) || strpos($rawPoster, '/') === 0) {
          $posterUrl = $rawPoster;
        } else {
          $includingScript = $_SERVER['PHP_SELF'] ?? $_SERVER['SCRIPT_NAME'] ?? '';
          $prefix = (strpos($includingScript, '/pages/') !== false) ? '../../' : '';

          $clean = preg_replace('#^(\./|\.\./)+#', '', $rawPoster);
          $posterUrl = $prefix . ltrim($clean, '/');
        }

        $event = [
          'id' => $row['match_id'],
          'date' => $row['match_date'],
          'poster_url' => $posterUrl,
          'category' => 'Football', 
          'title' => ($row['home_team_name'] ?? 'Team A') . ' vs ' . ($row['away_team_name'] ?? 'Team B'),
          'location' => $row['stadium_name'] ?? 'Stadium',
          'status' => $status,
          'url' => $eventDetailsPath
        ];

        $events[] = $event;
      }
    }
    
    return $events;
}

$currentPath = $_SERVER['REQUEST_URI'];
$isIndexPage = ($currentPath === '/' || str_contains($currentPath, 'index.php'));

$currentScript = $_SERVER['PHP_SELF'] ?? $_SERVER['SCRIPT_NAME'];
if (strpos($currentScript, '/pages/') !== false) {
  $eventDetailsPath = '../EventDetails/eventdetails.php';
} else {
  $eventDetailsPath = 'pages/EventDetails/eventdetails.php';
}

$events = fetchEventsFromDatabase($conn, $eventDetailsPath);

$limit = $isIndexPage ? 6 : count($events);

function formatDate($date)
{
  return [
    'day' => date('d', strtotime($date)),
    'month' => date('M', strtotime($date))
  ];
}
?>

<link
  href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
  rel="stylesheet"
  integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
  crossorigin="anonymous" />
<link
  rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
  integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
  crossorigin="anonymous"
  referrerpolicy="no-referrer" />

<?php
$currentScript = $_SERVER['PHP_SELF'] ?? $_SERVER['SCRIPT_NAME'];
if (strpos($currentScript, '/pages/') !== false) {
  $cssPath = '../../components/Card/card.css';
} else {
  $cssPath = 'components/Card/card.css';
}
?>
<link rel="stylesheet" href="<?php echo $cssPath; ?>">

<div class="row mt-4" id="cards-container">

  <?php if (empty($events)): ?>
    <div class="col-12 text-center py-5">
      <p class="text-muted">No events available at the moment.</p>
    </div>
  <?php endif; ?>

  <?php foreach (array_slice($events, 0, $limit) as $event): ?>
    <?php
    $date = formatDate($event['date']);
    ?>
    <div class="col-lg-4 col-md-6">
      <div class="card event-card">
        <div class="img-container">
          <img src="<?= htmlspecialchars($event['poster_url']) ?>"
            class="card-img-top"
            alt="<?= htmlspecialchars($event['category']) ?>" />

          <span class="event-category">
            <?= htmlspecialchars($event['category']) ?>
          </span>

          <?php if ($event['status'] === 'live'): ?>
            <span class="live-badge">Live Now</span>
          <?php endif; ?>
        </div>

        <div class="card-body">
          <h5 class="event-title">
            <?= htmlspecialchars($event['title']) ?>
          </h5>

          <p class="event-location">
            <span class="location-icon">📍</span>
            <?= htmlspecialchars($event['location']) ?>
          </p>

          <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="date-badge">
              <span class="day"><?= $date['day'] ?></span>
              <span class="month"><?= $date['month'] ?></span>
            </div>

            <a href="<?= $event['url'] ?>?id=<?= $event['id'] ?>"
              class="btn price-tag bg-success">
              Get Tickets
            </a>
          </div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>

</div>