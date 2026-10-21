<?php
function tampil_view($conn, $view) {
    $hasil = mysqli_query($conn, "SELECT * FROM $view");
    echo "<table><tr>";
    foreach (mysqli_fetch_fields($hasil) as $kolom) {
        echo "<th>" . htmlspecialchars($kolom->name) . "</th>";
    }
    echo "</tr>";
    while ($baris = mysqli_fetch_assoc($hasil)) {
        echo "<tr>";
        foreach ($baris as $isi) {
            echo "<td>" . htmlspecialchars($isi ?? '') . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
}