<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="views/styles/styleCalendar.css" />
    <title>Calendar</title>
  </head>
  <body>
    <?php include_once "header.php" ?>

    <div class="calendar">
      <div class="header">
        <button id="prevBtn">
          <i class="fa-solid fa-chevron-left"></i>
        </button>
        <div class="monthYear" id="monthYear"></div>
        <button id="nextBtn">
          <i class="fa-solid fa-chevron-right"></i>
        </button>
      </div>
      <div class="days">
        <div class="day">Mon</div>
        <div class="day">Tue</div>
        <div class="day">Wed</div>
        <div class="day">Thurs</div>
        <div class="day">Fri</div>
        <div class="day">Sat</div>
        <div class="day">Sun</div>
      </div>
      <div class="dates" id="dates"></div>
      <div class="calendar__text">
        <p class="calendar__text__outcomes">Pengeluaran: Rp1.6464.800</p>
        <p class="calendar__text__incomes">Pendapatan: Rp2.000.000</p>
        <p class="calendar__text__balance">Saldo: Rp353.200</p>
      </div>
    </div>

    <div class="description">
      <div class="description__container">
        <div class="description__header">
          <div class="description__money">20 Mei 2025</div>
          <div class="description__datenow">Rp18.000</div>
        </div>
        <div class="description__table">
          <div class="description__row">
            <img src="../assets/car-icon.png" class="icon-small" />
            <div class="description__item">Trans</div>
            <div class="description__item">
              Rp8.000
              <div class="description__timestamp">21.30</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?php include_once "footer.php" ?>
    <script src="views/scripts/calendar.js"></script>
  </body>
</html>
