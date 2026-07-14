import { ref, onMounted } from 'vue'

const isDark = ref(false)

export function useDarkMode() {
    const toggleDarkMode = () => {
        isDark.value = !isDark.value
        updateDOM()
    }

    const updateDOM = () => {
        if (typeof window === 'undefined') return
        
        if (isDark.value) {
            document.documentElement.classList.add('dark')
            localStorage.setItem('solarlink-theme', 'dark')
        } else {
            document.documentElement.classList.remove('dark')
            localStorage.setItem('solarlink-theme', 'light')
        }
    }

    const initDarkMode = () => {
        if (typeof window === 'undefined') return
        
        const savedTheme = localStorage.getItem('solarlink-theme')
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
        
        isDark.value = savedTheme === 'dark' || (!savedTheme && prefersDark)
        updateDOM()
    }

    return {
        isDark,
        toggleDarkMode,
        initDarkMode
    }
}
