<?php
ini_set('mssql.connect_timeout', 3);
ini_set('mssql.timeout', 3);

$sites = [
  'A' => 'mssql_site_a_123456',
  'B' => 'mssql_site_b_123456',
  'G' => 'mssql_global_123456'
];

$logFile = __DIR__ . '/monitor.log';

function logMsg($msg)
{
  global $logFile;
  $timestamp = date('Y-m-d H:i:s');
  file_put_contents($logFile, "[$timestamp] $msg\n", FILE_APPEND);
  echo "$msg\n";
}

function checkSite($siteName)
{
  $dsn = "sqlsrv:Server=$siteName;Database=master;TrustServerCertificate=1";
  $user = "sa";
  $pass = "Your@STROng!Pass#Word";

  $port = 1433;
  $timeout = 1.0; // 1 giây

  $ctx = stream_context_create([
    'socket' => ['connect_timeout' => $timeout]
  ]);
  $fp = @stream_socket_client("tcp://$siteName:$port", $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT, $ctx);
  if (!$fp) return false;
  fclose($fp);

  try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->query("SELECT 1");
    return true;
  } catch (Exception $e) {
    return false;
  }
}

function sync($site)
{
  $result = [];
  $exitCode = 0;
  exec("php /app/sync.php $site 2>&1", $result, $exitCode);
  logMsg("Sync result: " . implode(' ', $result));
  return $exitCode == 0;
}

$state = [];
$logIterator = 0;
$logBatch = 12;
$syncIterator = 0;
$syncBatch = 6;
$needSync = [];
$canSync = false;
while (true) {
  $logIterator = ($logIterator + 1) % $logBatch;
  $syncIterator = ($syncIterator + 1) % $syncBatch;
  foreach ($sites as $site => $container) {
    $alive = checkSite($container);
    $prev = $state[$site] ?? null;
    if ($alive) {
      if ($prev === null) {
        $state[$site] = true;
        logMsg("Initial check $site => $container: ok");
      } elseif ($prev === true) {
        if ($site === 'G') $canSync = true;
        if (!$logIterator) logMsg("Checked $site => $container: ok");
      } else {
        $state[$site] = true;
        if ($site == 'G') {
          $canSync = true;
          logMsg("Site $site => $container comeback.");
        } elseif (empty($needSync[$site])) {
          $needSync[$site] = true;
          logMsg("Site $site => $container comeback. Enqueued to sync.");
        }
      }
    } else {
      $state[$site] = false;
      if ($site == 'G') $canSync = false;
      if (!$logIterator) {
        logMsg("Site $site => $container no response.");
      }
    }
  }
  if (!$syncIterator && $canSync && $needSync) {
    foreach (array_keys($needSync) as $site) {
      if (sync($site)) {
        logMsg("Sync site $site success.");
        unset($needSync[$site]);
      } else {
        logMsg("Sync site $site failed.");
      }
    }
  }
  sleep(5);
}