import './bootstrap'
import Alpine from 'alpinejs'

window.Alpine = Alpine

// Componente para citas
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

Alpine.data('navigationComponent', () => ({
    open: false,
}))

Alpine.start()
