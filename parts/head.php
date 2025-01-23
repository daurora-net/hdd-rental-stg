<head>
  <meta charset='utf-8' />
  <link rel="stylesheet" type="text/css" href="assets/css/style.css">
  <link rel="stylesheet" type="text/css" href="assets/css/reset.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <title>
    <?= isset($isIndex) && $isIndex
      ? 'HDD Rental'
      : 'HDD Rental | ' . ($pageTitle ?? 'Default Title'); ?>
  </title>
  <script src="assets/js/calendar.js"></script>
  <script src="assets/js/modal.js"></script>
  <script src='https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.15/index.global.min.js'></script>
</head>