<div id="laporan_mingguan_section" class="mt-4">
    <div class="card shadow-sm">
        <div class="header_input_div card-header text-white bg-success">
            Laporan Mingguan
        </div>
        <div class="card-body scrollable-table">
            <table>
                <tr>
                    <th>ID User</th>
                    <th>ID Laporan</th>
                    <th>Tanggal Awal</th>
                    <th>Tanggal Akhir</th>
                    <th>Catatan</th>
                    <th colspan="2">aksi</th>
                </tr>
                <?php
                    // $tabel;
                    if(isset($tabelLaporan)){
                        while (true) {
                            $tabel = $tabelLaporan->fetch_object();
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
                                    <form name=\"deleteReport\" method=\"post\">
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
                            </tr>",
                                $tabel->user_id,
                                $tabel->laporan_id, 
                                $tabel->tanggal_awal, 
                                $tabel->tanggal_akhir, 
                                htmlspecialchars($tabel->catatan),
                                $tabel->laporan_id,
                                $tabel->laporan_id,
                                $tabel->tanggal_awal, 
                                $tabel->tanggal_akhir,
                                htmlspecialchars($tabel->catatan),
                            );
                        }
                    }                 
                ?>
            </table>
        </div>
    </div>
</div>