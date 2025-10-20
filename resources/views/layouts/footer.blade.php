<footer class="bg-dark text-white mt-5 py-4">
    <div class="container text-center">
        <p class="mb-1">© {{ date('Y') }} — Todos los derechos reservados.</p>
        <p class="mb-0">
            <a href="https://www.coca-cola.com/co/es/legal/terms-of-service" class="text-decoration-none text-light mx-2">Términos de uso</a> |
            <a href="https://www.coca-cola.com/co/es/legal/privacy-policy" class="text-decoration-none text-light mx-2">Política de privacidad</a> |
            <a href="#" id="copyNumber" class="text-decoration-none text-light mx-2">Contacto</a>
        </p>
        <hr class="border-light my-3" style="opacity: 0.2;">
        <p class="mb-0 small text-secondary">Desarrollado por <strong>RIOM S.A.S</strong></p>
        <div id="copyAlert" class="mt-2 text-success fw-bold" style="display:none;">📋 ¡Número copiado al portapapeles!</div>
    </div>
</footer>

<style>
footer {
    background-color: black;
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', cursive, sans-serif;
    text-align: center;
}

footer .container {
    max-width: 800px;
}

footer a:hover {
    color: #0dcaf0;
    transition: color 0.3s ease;
}
</style>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const copyBtn = document.getElementById("copyNumber");
    const alertMsg = document.getElementById("copyAlert");
    const numero = "3164795110";

    copyBtn.addEventListener("click", (e) => {
        e.preventDefault();
        navigator.clipboard.writeText(numero).then(() => {
            alertMsg.style.display = "block";
            setTimeout(() => alertMsg.style.display = "none", 2000);
        });
    });
});
</script>
