document.addEventListener('DOMContentLoaded', () => {
    // =================================================================
    // VARIABEL GLOBAL & ELEMENT PENTING
    // =================================================================
    const monthYearElement = document.getElementById('monthYear');
    const datesElement = document.getElementById('dates');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const descriptionDateEl = document.getElementById('description-date');
    const descriptionTotalEl = document.getElementById('description-total');
    const descriptionTableEl = document.getElementById('description-table');
    const calendarSummaryEl = document.getElementById('calendar-summary');

    // Elemen Modal Tambah
    const addModal = document.getElementById('addTransactionModal');
    const addTransactionButton = document.getElementById('addTransactionBtn');
    const closeAddModalBtn = addModal.querySelector('.close-btn');
    const addForm = document.getElementById('transactionForm');
    const addCategorySelect = document.getElementById('transactionCategory');
    const addBillGroup = document.getElementById('bill-form-group');
    const addGoalGroup = document.getElementById('goal-form-group');

    // Elemen Modal Edit
    const editModal = document.getElementById('editTransactionModal');
    const closeEditModalBtn = editModal.querySelector('.close-btn');
    const editForm = document.getElementById('editTransactionForm');
    const deleteBtn = document.getElementById('deleteTransactionBtn');
    const editCategorySelect = document.getElementById('editTransactionCategory');
    const editBillGroup = document.getElementById('edit-bill-form-group');
    const editGoalGroup = document.getElementById('edit-goal-form-group');
    
    let currentDate = new Date();

    if (descriptionTableEl) {
        descriptionTableEl.addEventListener('click', (event) => {
            const row = event.target.closest('.description__row');
            if (row && row.dataset.transactionId) {
                openEditModal(row.dataset.transactionId);
            }
        });
    }

    // =================================================================
    // FUNGSI UTAMA KALENDER & RENDER TAMPILAN
    // =================================================================

    const formatCurrency = (number) => 'Rp' + new Intl.NumberFormat('id-ID').format(number);

    const updateDescriptionView = (data, dateString) => {
        if (descriptionDateEl) {
            const selectedDate = new Date(dateString + 'T00:00:00');
            descriptionDateEl.textContent = selectedDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
        }
        if (calendarSummaryEl && data.summary) {
            calendarSummaryEl.innerHTML = `
                <p class="calendar__text__outcomes">Pengeluaran: ${formatCurrency(data.summary.expense)}</p>
                <p class="calendar__text__incomes">Pendapatan: ${formatCurrency(data.summary.income)}</p>
                <p class="calendar__text__balance">Saldo: ${formatCurrency(data.summary.balance)}</p>
            `;
        }
        if (descriptionTotalEl && data.summary) {
            descriptionTotalEl.textContent = formatCurrency(data.summary.total);
        }
        if (descriptionTableEl) {
            let tableHTML = '';
            if (!data.transactions || data.transactions.length === 0) {
                tableHTML = '<p>Tidak ada transaksi pada tanggal ini.</p>';
            } else {
                data.transactions.forEach(transaction => {
                    const amountColor = (transaction.tipe.toLowerCase().includes('pemasukan') || transaction.tipe.toLowerCase().includes('income')) ? 'green' : '#c5172e';
                    // Baris di bawah ini diberi data-transaction-id dan cursor pointer
                    tableHTML += `
                        <div class="description__row" data-transaction-id="${transaction.transaksi_id}" style="cursor: pointer;" title="Klik untuk ubah">
                            <img src="resources/assets/car-icon.png" class="icon-small" />
                            <div class="description__item">${transaction.kategori}</div>
                            <div class="description__item" style="color: ${amountColor};">
                                ${formatCurrency(transaction.jumlah)}
                                ${transaction.keterangan ? `<div class="description__timestamp">${transaction.keterangan}</div>` : ''}
                            </div>
                        </div>
                    `;
                });
            }
            descriptionTableEl.innerHTML = tableHTML;
        }
    };

    const fetchAndDisplayTransactions = async (dateString) => {
        try {
            const response = await fetch(`?c=CalendarController&m=showTransactions&date=${dateString}`);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            const data = await response.json();
            updateDescriptionView(data, dateString);
        } catch (error) {
            console.error("Tidak dapat mengambil data transaksi:", error);
            if (descriptionTableEl) descriptionTableEl.innerHTML = '<p style="color: red;">Gagal memuat data.</p>';
        }
    };

    const updateCalendar = () => {
        const currentYear = currentDate.getFullYear();
        const currentMonth = currentDate.getMonth();
        const firstDay = new Date(currentYear, currentMonth, 1);
        const lastDay = new Date(currentYear, currentMonth + 1, 0);
        const totalDays = lastDay.getDate();
        const firstDayIndex = firstDay.getDay() === 0 ? 6 : firstDay.getDay() - 1;

        monthYearElement.textContent = currentDate.toLocaleString('id-ID', { month: 'long', year: 'numeric' });

        let datesHTML = '';
        const prevLastDay = new Date(currentYear, currentMonth, 0).getDate();
        for (let i = firstDayIndex; i > 0; i--) {
            datesHTML += `<div class="date inactive">${prevLastDay - i + 1}</div>`;
        }

        const today = new Date();
        const activeDateEl = document.querySelector('.date.active');
        let activeDay = null;

        if (activeDateEl && activeDateEl.dataset.fulldate) {
            const activeFullDate = new Date(activeDateEl.dataset.fulldate + 'T00:00:00');
            if (activeFullDate.getMonth() === currentMonth && activeFullDate.getFullYear() === currentYear) {
                activeDay = activeFullDate.getDate();
            }
        } else if (currentMonth === today.getMonth() && currentYear === today.getFullYear()) {
            activeDay = today.getDate();
        }

        for (let i = 1; i <= totalDays; i++) {
            const fullDate = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
            const activeClass = i === activeDay ? 'active' : '';
            datesHTML += `<div class="date ${activeClass}" data-fulldate="${fullDate}">${i}</div>`;
        }

        const remainingCells = 7 - ((firstDayIndex + totalDays) % 7);
        if (remainingCells < 7) {
            for (let i = 1; i <= remainingCells; i++) {
                datesHTML += `<div class="date inactive">${i}</div>`;
            }
        }
        datesElement.innerHTML = datesHTML;
    };

    // =================================================================
    // EVENT LISTENER KALENDER
    // =================================================================

    datesElement.addEventListener('click', (event) => {
        const target = event.target;
        if (target.classList.contains('date') && !target.classList.contains('inactive')) {
            const active = datesElement.querySelector('.active');
            if (active) active.classList.remove('active');
            target.classList.add('active');
            fetchAndDisplayTransactions(target.dataset.fulldate);
        }
    });

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            updateCalendar();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            updateCalendar();
        });
    }

    // =================================================================
    // LOGIKA MODAL TAMBAH TRANSAKSI (ADD)
    // =================================================================
    
    if (addTransactionButton) {
        addTransactionButton.addEventListener('click', () => addModal.style.display = 'block');
    }
    if (closeAddModalBtn) {
        closeAddModalBtn.addEventListener('click', () => addModal.style.display = 'none');
    }
    addCategorySelect.addEventListener('change', function() {
        addBillGroup.style.display = 'none';
        addGoalGroup.style.display = 'none';
        const selectedOption = this.options[this.selectedIndex];
        if (!selectedOption) return;
        const type = selectedOption.getAttribute('data-type');
        if (type === 'pengeluaran') {
            addBillGroup.style.display = 'block';
        } else if (type === 'pemasukan') {
            addGoalGroup.style.display = 'block';
        }
    });
    addForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(addForm);
        const submitButton = addForm.querySelector('.btn-submit-transaction');
        submitButton.textContent = 'Menyimpan...';
        submitButton.disabled = true;
        
        fetch('?c=TransactionController&m=addProcess', { method: 'POST', body: formData, })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.status === 'success') {
                    addModal.style.display = 'none';
                    addForm.reset();
                    addBillGroup.style.display = 'none';
                    addGoalGroup.style.display = 'none';
                    const activeDate = document.querySelector('.date.active');
                    if (activeDate) {
                        fetchAndDisplayTransactions(activeDate.dataset.fulldate);
                    } else {
                        location.reload();
                    }
                }
            })
            .catch(error => {
                console.error('Fetch Error:', error);
                alert('Terjadi kesalahan koneksi.');
            })
            .finally(() => {
                submitButton.textContent = 'Simpan';
                submitButton.disabled = false;
            });
    });

    // =================================================================
    // LOGIKA MODAL UBAH & HAPUS TRANSAKSI (EDIT/DELETE)
    // =================================================================

    const openEditModal = async (transactionId) => {
        try {
            const response = await fetch(`?c=TransactionController&m=getTransaction&id=${transactionId}`);
            if (!response.ok) throw new Error('Gagal mengambil data transaksi.');
            const result = await response.json();
            if (result.status === 'success') {
                const t = result.data;
                document.getElementById('editTransactionId').value = t.transaksi;
                document.getElementById('editDate').value = t.tanggal_transaksi;
                document.getElementById('editTransactionCategory').value = t.kategori_id;
                document.getElementById('editAmount').value = t.jumlah;
                document.getElementById('editNote').value = t.keterangan;
                document.getElementById('edit_bill_id').value = t.tanggungan_id || '';
                document.getElementById('edit_goal_id').value = t.target_id || '';
                editCategorySelect.dispatchEvent(new Event('change'));
                editModal.style.display = 'block';
            } else {
                alert(result.message);
            }
        } catch (error) {
            alert(error.message);
        }
    };
    
    if (descriptionTableEl) {
        descriptionTableEl.addEventListener('click', (event) => {
            const row = event.target.closest('.description__row');
            if (row && row.dataset.transactionId) {
                openEditModal(row.dataset.transactionId);
            }
        });
    }

    if (closeEditModalBtn) {
        closeEditModalBtn.addEventListener('click', () => editModal.style.display = 'none');
    }

    editCategorySelect.addEventListener('change', function() {
        editBillGroup.style.display = 'none';
        editGoalGroup.style.display = 'none';
        const selectedOption = this.options[this.selectedIndex];
        if (!selectedOption) return;
        const type = selectedOption.getAttribute('data-type');
        if (type === 'pengeluaran') {
            editBillGroup.style.display = 'block';
        } else if (type === 'pemasukan') {
            editGoalGroup.style.display = 'block';
        }
    });

    editForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(editForm);
        fetch('?c=TransactionController&m=updateProcess', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                if (data.status === 'success') {
                    editModal.style.display = 'none';
                    const activeDate = document.querySelector('.date.active');
                    if (activeDate) fetchAndDisplayTransactions(activeDate.dataset.fulldate);
                }
            })
            .catch(err => alert('Terjadi kesalahan.'));
    });

    deleteBtn.addEventListener('click', function() {
        if (confirm('Apakah Anda yakin ingin menghapus transaksi ini?')) {
            const transactionId = document.getElementById('editTransactionId').value;
            const formData = new FormData();
            formData.append('transaction_id', transactionId);
            fetch('?c=TransactionController&m=deleteProcess', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    alert(data.message);
                    if (data.status === 'success') {
                        editModal.style.display = 'none';
                        const activeDate = document.querySelector('.date.active');
                        if (activeDate) fetchAndDisplayTransactions(activeDate.dataset.fulldate);
                    }
                })
                .catch(err => alert('Terjadi kesalahan.'));
        }
    });

    // =================================================================
    // TUTUP MODAL KETIKA KLIK DI LUAR AREA
    // =================================================================

    window.addEventListener('click', (event) => {
        if (event.target == addModal) {
            addModal.style.display = 'none';
        }
        if (event.target == editModal) {
            editModal.style.display = 'none';
        }
    });

    // Inisialisasi tampilan kalender saat halaman dimuat
    updateCalendar();
});