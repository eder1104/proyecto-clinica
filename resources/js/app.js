import './bootstrap'
import Alpine from 'alpinejs'

window.Alpine = Alpine

Alpine.data('citasComponent', () => ({
    open: true,
    openPaciente: false,
    openNuevoPaciente: false,
    openEditar: false,
    search: '',
    citaSeleccionada: {},

    setCita(cita) {
        this.citaSeleccionada = { ...cita }
        this.openEditar = true
    }
}))

Alpine.start()

window.navigationComponent = () => ({
    open: false,
});
