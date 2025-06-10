<?php
    // Memberikan nilai default untuk mencegah warning "Undefined variable"
    $selected_date = $selected_date ?? date('Y-m-d');
    $summary = $summary ?? ['expense' =>
0, 'income' => 0, 'balance' => 0, 'total' => 0]; $transactions = $transactions
?? []; ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
    href="https://fonts.googleapis.com/icon?family=Material+Icons"
    rel="stylesheet"
    />
    <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    />
    <link rel="stylesheet" href="./views/styles/styleCalendar.css" />
    <title>Calendar</title>
  </head>
  <body>
    <!-- <?php include_once "header.php" ?> -->

    <main class="container py-4">
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
        <div class="calendar__text" id="calendar-summary">
          <p class="calendar__text__outcomes">
            Pengeluaran: Rp<?= number_format($summary['expense'] ?? 0, 0, ',', '.') ?>
          </p>
          <p class="calendar__text__incomes">
            Pendapatan: Rp<?= number_format($summary['income'] ?? 0, 0, ',', '.') ?>
          </p>
          <p class="calendar__text__balance">
            Saldo: Rp<?= number_format($summary['balance'] ?? 0, 0, ',', '.') ?>
          </p>
        </div>
      </div>

      <div class="description">
        <div class="description__container" id="description-container">
          <div class="description__header">
            <div class="description__money" id="description-date">
              <?php
                    $date = new DateTime($selected_date);
                    echo $date->format('d F Y'); ?>
            </div>
            <div class="description__datenow" id="description-total">
              Rp<?= number_format($summary['total'] ?? 0, 0, ',', '.') ?>
            </div>
          </div>
          <div class="description__table" id="description-table">
            <?php if (empty($transactions)): ?>
            <p>Tidak ada transaksi pada tanggal ini.</p>
            <?php else: ?>
            <?php foreach ($transactions as $transaction): ?>
            <div class="description__row">
              <img src="resources/assets/car-icon.png" class="icon-small" />
              <div class="description__item">
                <?= htmlspecialchars($transaction['category']) ?>
              </div>
              <div
                class="description__item"
                style="color: <?= $transaction['type'] == 'income' ? 'green' : '#c5172e' ?>;"
              >
                Rp<?= number_format($transaction['amount'], 0, ',', '.') ?>
                <?php if (!empty($transaction['note'])): ?>
                <div class="description__timestamp">
                  <?= htmlspecialchars($transaction['note']) ?>
                </div>
                <?php endif; ?>
              </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <button
        type="button"
        class="btn btn-dark rounded-circle d-flex justify-content-center align-items-center"
        style="width: 60px; height: 60px; font-size: 32px; position: fixed"
        id="addTransactionBtn"
        aria-label="Tambah Transaksi Baru"
      >
        +
      </button>
    </main>

    <!-- ADD -->
    <div id="addTransactionModal" class="modal">
      <div class="modal-content">
        <span class="close-btn">&times;</span>
        <h2>Tambah Transaksi Baru</h2>

        <form
          id="transactionForm"
          action="?c=TransactionController&m=addProcess"
          method="post"
        >
          <div class="form-group">
            <label for="date">Tanggal:</label>
            <input
              type="date"
              id="date"
              name="date"
              value="<?= date('Y-m-d') ?>"
              required
            />
          </div>
          <div class="form-group">
            <label for="transactionCategory">Kategori Transaksi:</label>
            <select id="transactionCategory" name="category_id" required>
              <option value="" disabled selected>Pilih Kategori</option>
              <?php foreach ($categories as $category): ?>
              <option
                value="<?= $category['category_id'] ?>"
                data-type="<?= htmlspecialchars(strtolower(trim($category['type']))) ?>"
              >
                <?= htmlspecialchars($category['category']) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group" id="bill-form-group" style="display: none">
            <label for="bill_id">Pilih Tagihan (Opsional):</label>
            <select id="bill_id" name="bill_id">
              <option value="">Tidak ada</option>
              <?php foreach ($bills as $bill): ?>
              <option value="<?= $bill['bill_id'] ?>">
                <?= htmlspecialchars($bill['bill']) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group" id="goal-form-group" style="display: none">
            <label for="goal_id">Pilih Target (Opsional):</label>
            <select id="goal_id" name="goal_id">
              <option value="">Tidak ada</option>
              <?php foreach ($goals as $goal): ?>
              <option value="<?= $goal['goal_id'] ?>">
                <?= htmlspecialchars($goal['goal']) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label for="transactionAmount">Jumlah (Rp):</label>
            <input
              type="number"
              id="transactionAmount"
              name="amount"
              placeholder="Contoh: 50000"
              required
              min="0"
            />
          </div>
          <div class="form-group">
            <label for="transactionNote">Catatan:</label>
            <textarea
              id="transactionNote"
              name="note"
              rows="3"
              placeholder="Contoh: Bayar tagihan listrik bulan Juni"
            ></textarea>
          </div>
          <button type="submit" class="btn-submit-transaction">Simpan</button>
        </form>
      </div>
    </div>

    <!-- EDIT -->
    <div id="editTransactionModal" class="modal">
      <div class="modal-content">
        <span class="close-btn">&times;</span>
        <h2>Ubah Transaksi</h2>

        <form
          id="editTransactionForm"
          action="?c=TransactionController&m=updateProcess"
          method="post"
        >
          <input type="hidden" id="editTransactionId" name="transaction_id" />

          <div class="form-group">
            <label for="editDate">Tanggal:</label>
            <input type="date" id="editDate" name="date" required />
          </div>

          <div class="form-group">
            <label for="editTransactionCategory">Kategori Transaksi:</label>
            <select id="editTransactionCategory" name="category_id" required>
              <option value="" disabled>Pilih Kategori</option>
              <?php foreach ($categories as $category): ?>
              <option
                value="<?= $category['category_id'] ?>"
                data-type="<?= htmlspecialchars(strtolower(trim($category['type']))) ?>"
              >
                <?= htmlspecialchars($category['category']) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div
            class="form-group"
            id="edit-bill-form-group"
            style="display: none"
          >
            <label for="edit_bill_id">Pilih Tagihan (Opsional):</label>
            <select id="edit_bill_id" name="bill_id">
              <option value="">Tidak ada</option>
              <?php foreach ($bills as $bill): ?>
              <option value="<?= $bill['bill_id'] ?>">
                <?= htmlspecialchars($bill['bill']) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div
            class="form-group"
            id="edit-goal-form-group"
            style="display: none"
          >
            <label for="edit_goal_id">Pilih Target (Opsional):</label>
            <select id="edit_goal_id" name="goal_id">
              <option value="">Tidak ada</option>
              <?php foreach ($goals as $goal): ?>
              <option value="<?= $goal['goal_id'] ?>">
                <?= htmlspecialchars($goal['goal']) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label for="editAmount">Jumlah (Rp):</label>
            <input
              type="number"
              id="editAmount"
              name="amount"
              placeholder="Contoh: 50000"
              required
              min="0"
            />
          </div>

          <div class="form-group">
            <label for="editNote">Catatan:</label>
            <textarea id="editNote" name="note" rows="3"></textarea>
          </div>

          <div class="form-buttons">
            <button type="button" id="deleteTransactionBtn" class="btn-delete">
              Hapus
            </button>
            <button type="submit" class="btn-submit-transaction">
              Simpan Perubahan
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- <?php include_once "footer.php" ?> -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./views/scripts/calendar.js"></script>
  </body>
</html>
