    document.addEventListener('DOMContentLoaded', function() {
        const tabelDaftarTarget = document.getElementById('tabelDaftarTarget');
        const targetAdminForm = document.getElementById('targetAdminForm');

        // Function to toggle edit mode for a row
        function toggleEditMode(row) {
            row.classList.toggle('editing');
            const editButton = row.querySelector('.toggle-edit-btn');
            if (row.classList.contains('editing')) {
                editButton.textContent = 'Batal';
                // Store original values if needed for cancel, for now, inputs are always pre-filled
            } else {
                editButton.textContent = 'Edit';
                // Revert values if canceled (not implemented in this version, but good to note)
            }
        }

        // Event listener for "Edit" / "Batal" buttons
        tabelDaftarTarget.addEventListener('click', function(event) {
            const toggleBtn = event.target.closest('.toggle-edit-btn');
            if (toggleBtn) {
                const row = toggleBtn.closest('.editable-row');
                toggleEditMode(row);
            }
        });

        // Event listener for the global "Simpan Perubahan Data" button
        targetAdminForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent default form submission

            const rowsToUpdate = document.querySelectorAll('#tabelDaftarTarget .editable-row.editing');
            const updates = [];

            rowsToUpdate.forEach(function(row) {
                const targetId = row.dataset.targetId;
                const userId = row.querySelector(`[name="user_id_${targetId}"]`).value;
                const targetName = row.querySelector(`[name="target_name_${targetId}"]`).value;
                const jumlah = row.querySelector(`[name="jumlah_${targetId}"]`).value;

                updates.push({
                    target_id: targetId,
                    user_id: userId,
                    target: targetName,
                    jumlah: jumlah
                });
            });

            if (updates.length === 0) {
                alert('Tidak ada perubahan yang perlu disimpan.');
                return;
            }

            const formData = new FormData();
            formData.append('updates', JSON.stringify(updates)); // Send updates as a JSON string

            // Send data via Fetch API (without await/async as requested)
            fetch('?c=AdminTargetController&m=bulkUpdate', {
                method: 'POST',
                body: formData
            })
            .then(function(response) {
                if (!response.ok) {
                    // Handle HTTP errors
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                return response.json();
            })
            .then(function(data) {
                alert(data.message);
                if (data.status === 'success' || data.status === 'partial_success') {
                    // If successful, exit edit mode for all updated rows and reload to refresh display
                    window.location.reload(); 
                }
            })
            .catch(function(error) {
                console.error('There was a problem with the fetch operation:', error);
                alert('Terjadi kesalahan saat menyimpan perubahan: ' + error.message);
            });
        });

        // Function for deleting a single target (kept separate from bulk update)
        async function deleteTarget(buttonElement) {
            if (!confirm('Anda yakin ingin menghapus target ini? Tindakan ini tidak dapat dibatalkan.')) return;
            const rowElement = buttonElement.closest('tr');
            const targetId = rowElement.dataset.targetId;
            
            try {
                const formData = new URLSearchParams();
                formData.append('target_id', targetId);

                const response = await fetch(`?c=AdminTargetController&m=delete`, {
                    method: 'POST',
                    body: formData,
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                });
                const result = await response.json();
                alert(result.message);
                if (response.ok && result.status === 'success') {
                    window.location.reload();
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat berkomunikasi dengan server.');
            }
        }
        // Expose deleteTarget to the global scope if it's called from onclick in PHP
        window.deleteTarget = deleteTarget;
    });