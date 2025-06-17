<div id="laporan_mingguan_section" class="mt-4">
    <div class="card shadow-sm">
        <div class="header_input_div card-header text-white bg-success">
            Laporan Mingguan
        </div>
        <div class="card-body scrollable-table">
            <table>
                <tr>
                    <th>Tanggal Awal</th>
                    <th>Tanggal Akhir</th>
                    <th>Total Pemasukan</th>
                    <th>Total Pengeluaran</th>
                    <th>Catatan</th>
                    <th colspan="3">aksi</th>
                </tr>
                <?php
                    // $tabel;
                    if(isset($listLaporanMingguan)){
                        while (true) {
                            $tabel = $listLaporanMingguan->fetch_object();

                            if (!$tabel) {
                                break;
                            }

                            printf("<tr>
                                <td>%s</td>
                                <td>%s</td>
                                <td>%s</td>
                                <td>%s</td>
                                <td>%s</td>
                                <td>
                                    <form method=\"post\" name=\"deleteReport\">
                                        <input type=\"hidden\" name=\"laporan_id\" value=\"%s\">
                                        <button type=\"submit\" class=\"btn btn-sm btn-outline-danger\">Hapus</button>
                                    </form>
                                </td>
                                <td>
                                    <form action=\"?c=LaporanController&m=editReport\" method=\"post\">
                                        <input type=\"hidden\" name=\"laporan_id\" value=\"%s\">
                                        <input type=\"hidden\" name=\"tanggal_awal\" value=\"%s\">
                                        <input type=\"hidden\" name=\"tanggal_akhir\" value=\"%s\">
                                        <input type=\"hidden\" name=\"catatan\" value=\"%s\">
                                        <button type=\"submit\" class=\"btn btn-sm btn-outline-primary\">Edit</button>
                                    </form>
                                </td>
                                <td>
                                    <form action=\"?c=LaporanController&m=report\" method=\"post\">
                                        <input type=\"hidden\" name=\"laporan_id\" value=\"%s\">
                                        <input type=\"hidden\" name=\"laporan_type\" value=\"mingguan\">
                                        <button type=\"submit\" class=\"btn btn-sm btn-outline-success\">Grafik</button>
                                    </form>
                                </td>
                            </tr>", 
                                $tabel->tanggal_awal, 
                                $tabel->tanggal_akhir, 
                                $tabel->jumlah_pemasukan, 
                                $tabel->jumlah_pengeluaran,
                                $tabel->catatan,
                                $tabel->laporan_id,
                                $tabel->laporan_id,
                                $tabel->tanggal_awal, 
                                $tabel->tanggal_akhir,
                                $tabel->catatan,
                                $tabel->laporan_id
                            );
                        }
                    }                 
                ?>
            </table>
        </div>
    </div>
</div>
