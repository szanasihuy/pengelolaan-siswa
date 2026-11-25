
document.addEventListener("DOMContentLoaded", function () {

    // Konfirmasi hapus
    const tombolHapus = document.querySelectorAll(".btn-delete");

    tombolHapus.forEach(function (tombol) {
        if (tombol.tagName === "A") {
            tombol.addEventListener("click", function (event) {
                const yakin = confirm(
                    "Yakin ingin menghapus data ini?"
                );

                if (!yakin) {
                    event.preventDefault();
                }
            });
        }
    });

    // Efek fokus pada input
    const inputs = document.querySelectorAll(
        "input, select, textarea"
    );

    inputs.forEach(function (input) {
        input.addEventListener("focus", function () {
            this.style.transition = "0.2s";
        });
    });

});
